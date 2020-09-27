<?php namespace App\Http\Controllers;

	use Psy\Util\Json;
    use Session;
	use Request;
	use DB;
	use CRUDBooster;
    use DateTime;
    use Illuminate\Support\Facades\Log;
    use JasperPHP\JasperPHP;
    use Illuminate\Support\Facades\File;
    use Response;

	class AdminGoldSaleOrdersController extends CBExtendController {

	    public function cbInit() {
			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "order_no";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon_text";
			$counter = DB::table('gold_counters')
				->whereRaw('deleted_at is null AND closed_at is null')
				->where('saler_id', CRUDBooster::myId())
				->first();
			if($counter){
				$this->button_add = true;
			}else{
				$this->button_add = false;
			}
            $this->button_edit = true;
            $this->button_delete = true;				
			$this->button_detail = true;
			$this->button_show = false;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "gold_sale_orders";
            $this->is_search_form = true;
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Số đơn hàng","name"=>"order_no","width"=>"100"];
            $this->col[] = ["label"=>"Ngày đơn hàng","name"=>"order_date","callback_php"=>'date_time_format($row->order_date, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
            $this->col[] = ["label"=>"Trạng thái","name"=>"order_type","callback_php"=>'get_input_status($row->order_type);'];
			$this->col[] = ["label"=>"Mã khách hàng","name"=>"customer_id","join"=>"gold_customers,code"];
            $this->col[] = ["label"=>"Tên khách hàng","name"=>"customer_id","join"=>"gold_customers,name"];
            $this->col[] = ["label"=>"Phone","name"=>"customer_id","join"=>"gold_customers,phone"];
            $this->col[] = ["label"=>"Zalo phone","name"=>"customer_id","join"=>"gold_customers,zalo_phone"];
            $this->col[] = ["label"=>"Số tiền","name"=>DB::raw('(gold_amount + fee - discount_amount - reduce) as amount'),"callback_php"=>'number_format($row->amount)'];
            $this->col[] = ["label"=>"HTTT","name"=>"payment_method","callback_php"=>'get_payment_method($row->payment_method);'];
            $this->col[] = ["label"=>"Nhân viên BH","name"=>"saler_id","join"=>"cms_users,name"];
            $this->col[] = ["label"=>"Cửa hàng","name"=>"brand_id","join"=>"gold_brands,name"];
			# END COLUMNS DO NOT REMOVE THIS LINE

            // Nguen add new for search

            $this->search_form = [];
            $this->search_form[] = ["label"=>"Từ ngày", "name"=>"order_date_from_date", "data_column"=>"order_date", "search_type"=>"between_from","type"=>"date","width"=>"col-sm-2"];
            $this->search_form[] = ["label"=>"Đến ngày", "name"=>"order_date_to_date", "data_column"=>"order_date", "search_type"=>"between_to","type"=>"date","width"=>"col-sm-2"];
            $this->search_form[] = ["label"=>"Loại đơn hàng", "name"=>"order_type_search", "data_column"=>"gold_sale_orders.order_type", "search_type"=>"equals_raw","type"=>"select","width"=>"col-sm-2", 'dataenum'=>"\"0\"|<lable class='label label-warning'>Đang nhập</lable>;\"1\"|<lable class='label label-danger'>Hoàn tất</lable>"];
            if(CRUDBooster::myPrivilegeId() == 2) {
                $this->search_form[] = ["label" => "Khách hàng/ Số ĐT", "name" => "customer", "data_column"=>"gold_sale_orders.customer_id", "search_type"=>"equals_raw", "type" => "select2", "width" => "col-sm-6", 'datatable' => 'gold_customers,name', 'datatable_where' => 'deleted_at is null', 'datatable_format' => "code,' - ',name,' - ',IFNULL(phone,''),' - ',IFNULL(zalo_phone,'')"];
            }else{
                $this->search_form[] = ["label" => "Khách hàng/ Số ĐT", "name" => "customer", "data_column"=>"gold_sale_orders.customer_id", "search_type"=>"equals_raw", "type" => "select2", "width" => "col-sm-6", 'datatable' => 'gold_customers,name', 'datatable_where' => 'deleted_at is null', 'datatable_format' => "code,' - ',name,' - ',IFNULL(phone,''),' - ',IFNULL(zalo_phone,'')"];
            }
            //$this->search_form[] = ["label"=>"Xuống dòng", "name"=>"break_line", "type"=>"break_line"];
            $this->search_form[] = ["label"=>"Nhân viên BH", "name"=>"saler", "data_column"=>"gold_sale_orders.saler_id", "search_type"=>"equals_raw","type"=>"select2","width"=>"col-sm-2", 'datatable'=>'cms_users,name', 'datatable_where'=>CRUDBooster::myPrivilegeId() == 2 ? 'id = '.CRUDBooster::myId() : 'id_cms_privileges in (2,3,4,5)', 'datatable_format'=>"employee_code,' - ',name,' (',email,')'"];
            $this->search_form[] = ["label"=>"Trọng lượng tổng", "name"=>"total_weight_search","type"=>"text","width"=>"col-sm-2", "search_type"=>"in_details", "mark_value"=>"[value_search]",
                "sub_query"=>"(select D.id from gold_sale_order_details as D left join gold_items as I on D.item_id = I.id where ".$this->table.".id = D.order_id and CONVERT(I.total_weight, CHAR) = '[value_search]' limit 1) is not null"];
            $this->search_form[] = ["label"=>"Trọng lượng đá", "name"=>"gem_weight_search","type"=>"text","width"=>"col-sm-2", "search_type"=>"in_details", "mark_value"=>"[value_search]",
                "sub_query"=>"(select D.id from gold_sale_order_details as D left join gold_items as I on D.item_id = I.id where ".$this->table.".id = D.order_id and CONVERT(I.gem_weight, CHAR) = '[value_search]' limit 1) is not null"];
//            if(CRUDBooster::myPrivilegeId() == 1 || CRUDBooster::myPrivilegeId() == 4){
                $this->search_form[] = ["label" => "Cửa hàng", "name" => "brand_id", "data_column"=>$this->table.".brand_id", "search_type"=>"equals_raw", "type" => "select2", "width" => "col-sm-2", 'datatable' => 'gold_brands,name', 'datatable_where' => 'deleted_at is null'];
//            }

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Số đơn hàng','name'=>'order_no','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
            $this->form[] = ['label'=>'Ngày đơn hàng','name'=>'order_date','type'=>'date','validation'=>'required|date_format:Y-m-d','width'=>'col-sm-10','help'=>'Số đơn hàng sẽ tự phát sinh khi bạn lưu','readonly'=>'true'];
			# END FORM DO NOT REMOVE THIS LINE

			/* 
	        | ---------------------------------------------------------------------- 
	        | Sub Module
	        | ----------------------------------------------------------------------     
			| @label          = Label of action 
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class  
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        | 
	        */
	        $this->sub_module = array();


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)     
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        | 
	        */
	        $this->addaction = array();
            $this->addaction[] = ['label'=>'Hóa đơn','url'=>CRUDBooster::mainpath('print-invoice/[id]'),'icon'=>'fa fa-print','color'=>'info', 'showIf'=>"[order_type] != 0"];
            // $this->addaction[] = ['label'=>'In hóa đơn trắng','url'=>CRUDBooster::mainpath('print-invoice-blank/[id]'),'icon'=>'fa fa-newspaper-o','color'=>'info'];
            // $this->addaction[] = ['label'=>'Sửa','url'=>CRUDBooster::mainpath('edit/[id]'),'icon'=>'fa fa-pencil','color'=>'success', 'showIf'=>"[order_type] == 0"]; // ĐH đang nhập
            // $this->addaction[] = ['label'=>'Chi tiết','url'=>CRUDBooster::mainpath('detail/[id]'),'icon'=>'fa fa-eye','color'=>'primary', 'showIf'=>"[order_type] != 0"]; // ĐH đang nhập
            // if(CRUDBooster::myPrivilegeId() != 1 && CRUDBooster::myPrivilegeId() != 4){
                // $this->addaction[] = ['label'=>'Xóa','url'=>CRUDBooster::mainpath('delete/[id]'),'icon'=>'fa fa-trash-o','color'=>'warning', 'showIf'=>"[order_type] == 0"]; // ĐH đang nhập
            // }

	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Button Selected
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button 
	        | Then about the action, you should code at actionButtonSelected method 
	        | 
	        */
	        $this->button_selected = array();

	                
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------     
	        | @message = Text of message 
	        | @type    = warning,success,danger,info        
	        | 
	        */
	        $this->alert        = array();
	                
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add more button to header button 
	        | ----------------------------------------------------------------------     
	        | @label = Name of button 
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        | 
	        */
	        $this->index_button = array();

	        /* 
	        | ---------------------------------------------------------------------- 
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------     
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
	        | 
	        */
	        $this->table_row_color = array();     	          

	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
	        $this->index_statistic = array();

	        /*
	        | ---------------------------------------------------------------------- 
	        | Add javascript at body 
	        | ---------------------------------------------------------------------- 
	        | javascript code in the variable 
	        | $this->script_js = "function() { ... }";
	        |
	        */
            // $this->script_js = NULL;
            $this->script_js = "$(function() {
                    control_primary_button(".CRUDBooster::myPrivilegeId().");
                });";


            /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code before index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code after index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        $this->post_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = array();
            $this->load_js[] = asset("plugins/autoNumeric/autoNumeric.min.js");
//            $this->load_js[] = asset("plugins/jQuery-Scanner-Detection/jquery.scannerdetection.js");
            $this->load_js[] = asset("vendor/crudbooster/assets/datetimepicker-master/build/jquery.datetimepicker.full.min.js");
            $this->load_js[] = asset("vendor/crudbooster/assets/select2/dist/js/select2.min.js");
            $this->load_js[] = asset("js/jewelry.js");
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
            $this->style_css = NULL;

	        /*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = array();
            $this->load_css[] = asset("css/loading.css");
            $this->load_css[] = asset("css/site.customize.css");
            $this->load_css[] = asset("vendor/crudbooster/assets/datetimepicker-master/jquery.datetimepicker.css");
            $this->load_css[] = asset("vendor/crudbooster/assets/select2/dist/css/select2.min.css");
	    }

        public function getAdd() {
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            // Log::debug('Tạo mới đơn hàng bán');
            // Log::debug('$user = ' . Json::encode($user));
            // Log::debug('stock_ids = ' . $user->stock_id);
            $data = [];
            $data['page_title'] = 'Tạo mới đơn hàng bán';
            $data += ['mode' => 'new', 'stock_ids' => $user->stock_id];
            $this->cbView('sale_order_form', $data);
        }

        public function getEdit($id)
        {
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $data = [];
            $data['page_title'] = 'Sửa đơn hàng bán';
            $data += ['mode' => 'edit', 'stock_ids' => $user->stock_id, 'resume_id' => $id];
            $this->cbView('sale_order_form', $data);
        }

        public function getDetail($id)
        {
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $data = [];
            $data['page_title'] = 'Xem đơn hàng bán';
            $data += ['mode' => 'view', 'stock_ids' => $user->stock_id, 'resume_id' => $id];
            $this->cbView('sale_order_form', $data);
        }

        public function getSales()
        {
            $data = [];
			$data['page_title'] = 'Báo cáo doanh số';
			$this->cbView('rpt_sales_form', $data);
        }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for button selected
	    | ---------------------------------------------------------------------- 
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
	            
	    }


	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	    public function hook_query_index(&$query) {
	        //Your code here
            if(CRUDBooster::myPrivilegeId() == 2)// Nhân viên bán hàng
            {
                $query->where('gold_sale_orders.saler_id', CRUDBooster::myId());
            }
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hook_row_index($column_index,&$column_value) {	        
	    	//Your code here

	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before add data is execute
	    | ---------------------------------------------------------------------- 
	    | @arr
	    |
	    */
//	    public function hook_before_add(&$postdata) {
//	        //Your code here
//
//	    }

        public function postAddSave() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            $order_detail_ids = [];
            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
                $new_order = $para['order'];
                $order_details = $para['order_details'];
                $order_pays = $para['order_pays'];
                $new_customer = $para['customer'];

                $customer_id = $new_order['customer_id'];
                if (!$customer_id && $new_customer['name']) {
                    $last_code = DB::table('gold_customers')->orderBy('code', 'desc')->first();
                    $new_code = '';
                    if ($last_code) {
                        $new_code = (intval(substr($last_code->code, 2, strlen($last_code->code) - 2)) + 1);
                        if(strlen($new_code) < 6){
                            $new_code = '00000'.$new_code;
                            $new_code = substr($new_code, strlen($new_code) - 6, 6);
                        }
                        $new_code = 'KH'.$new_code;
                    } else {
                        $new_code = 'KH000001';
                    }
                    $new_customer['code'] = $new_code;
                    $new_customer['created_at'] = date('Y-m-d H:i:s');
                    $new_customer['created_by'] = CRUDBooster::myId();
                    $customer_id = DB::table('gold_customers')->insertGetId($new_customer);
                    $new_order['customer_id'] = $customer_id;
                }

                $order_date_str = $new_order['order_date'];
                $order_date = DateTime::createFromFormat('Y-m-d H:i:s', $order_date_str);

                if( $new_order['id'] && intval($new_order['id']) > 0) // update order
                {
                    $order_id = $this->updateOrderHeader($new_order);
                }else{
                    // get new order no
                    $last_order = DB::table('gold_sale_orders as SO')
                        ->whereRaw('SO.deleted_at is null')
                        ->where('SO.order_date', '>=', $order_date->format('Y-m-d') . ' 00:00:00')
                        ->where('SO.order_date', '<=', $order_date->format('Y-m-d') . ' 23:59:59')
                        ->orderBy('SO.order_no', 'desc')
                        ->first();
                    $new_order_no = '';
                    if ($last_order) {
                        $sno = explode('-', $last_order->order_no);
                        $old_no = intval($sno[count($sno)-1]); 
                        $new_order_no = '000' . ($old_no + 1);
                        $new_order_no = substr($new_order_no, strlen($new_order_no) - 3, 3);
                        $new_order_no = 'BH' . $order_date->format('ymd') . '-'. CRUDBooster::myBrand() . '-' . $new_order_no;
                    } else {
                        $new_order_no = 'BH' . $order_date->format('ymd') . '-'. CRUDBooster::myBrand() . '-001';
                    }
                    $new_order['order_no'] = $new_order_no;
                    // $created_at = date('Y-m-d H:i:s');
                    // $new_order['created_at'] = $created_at;
                    $new_order['created_by'] = CRUDBooster::myId();
                    $new_order['brand_id'] = CRUDBooster::myBrand();
                    unset($new_order['id']);
                    $order_id = DB::table('gold_sale_orders')->insertGetId($new_order);
                    Log::debug('$order_id = ' . $order_id);
                }

                $q10 = 0;
                $age = 0;
                $total_q10 = 0;
                $points = ($new_customer['points'] ? $new_customer['points'] : 0) - $new_order['use_points'];
                DB::table('gold_sale_order_details')->where('order_id', $order_id)->delete();
                if ($order_details && count($order_details)) {
                    if(intval($new_order['order_type']) != 0) // Hoàn tất
                    {
                        foreach ($order_details as $detail) {
                            $item = DB::table('gold_items as I')
                                ->leftJoin('gold_product_types as T', 'I.product_type_id', '=', 'T.id')
                                ->where('I.id', $detail['id'])
                                ->select('I.id', 'I.gold_weight', 'T.age')
                                ->first();
                            if($item){
                                $age = $item->age;
                                $q10 = round(($item->gold_weight - $detail['edit_weight']) * $age / 100, 4);
                                $total_q10 += $q10;
                            }
                            $new_detail = [
                                'order_id' => $order_id,
                                'sort_no' => $detail['no'],
                                'item_id' => $detail['id'],
                                'edit_weight' => $detail['edit_weight'],
                                'age' => $age,
                                'q10' => $q10,
                                'qty' => 1,
                                'price' => $detail['price'],
                                'gold_amount' => $detail['gold_amount'],
                                'fee' => $detail['fee'],
                                'discount' => $detail['discount_amount'],
                                'amount' => $detail['amount'],
                                'created_by' => CRUDBooster::myId()
                            ];
                            $new_detail_id = DB::table('gold_sale_order_details')->insertGetId($new_detail);
                            array_push($order_detail_ids, $new_detail_id);
                            $order = DB::table('gold_sale_orders')->where('id', $order_id)->first();
                            DB::table('gold_items')->where('id', $detail['id'])->update([
                                'qty'=>0, 
                                'status'=>2, 
                                'updated_at'=>$created_at, 
                                'updated_by'=>CRUDBooster::myId(), 
                                'notes' => 'Bán trong đơn hàng '.$order->order_no
                            ]);
                        }

                        $order_point = $this->calcOrderPoint($order_id, $order_date);
                        $points += $order_point;
                        // Log::debug('$points = ' . $points);
                        // Log::debug('$order_point = ' . $order_point);
                        // Log::debug('$new_order[customer_id] = ' . $new_order['customer_id']);
                        // Log::debug('$order_id = ' . $order_id);
                        DB::table($this->table)->where('id', $order_id)->update(['points'=>$points, 'order_point'=>$order_point]);
                    } else {
                        foreach ($order_details as $detail) {
                            $new_detail = [
                                'order_id' => $order_id,
                                'sort_no' => $detail['no'],
                                'item_id' => $detail['id'],
                                'edit_weight' => $detail['edit_weight'],
                                'qty' => 1,
                                'price' => $detail['price'],
                                'gold_amount' => $detail['gold_amount'],
                                'fee' => $detail['fee'],
                                'discount' => $detail['discount_amount'],
                                'amount' => $detail['amount'],
                                'created_by' => CRUDBooster::myId()
                            ];
                            $new_detail_id = DB::table('gold_sale_order_details')->insertGetId($new_detail);
                            array_push($order_detail_ids, $new_detail_id);
                        }
                    }
                }

                DB::table('gold_sale_order_pays')->where('order_id', $order_id)->delete();
                if ($order_pays && count($order_pays)) {
                    $new_order_pays = [];
                    foreach ($order_pays as $pay) {
                        if($pay['description']){
                            $total_q10 += $pay['q10'];
                            $new_pay = [
                                'order_id' => $order_id,
                                'description' => $pay['description'],
                                'product_type_id' => $pay['product_type_id'],
                                'total_weight' => $pay['total_weight'],
                                'gem_weight' => $pay['gem_weight'],
                                'abate_weight' => $pay['abate_weight'],
                                'gold_weight' => $pay['gold_weight'],
                                'price' => $pay['price'],
                                'fee' => $pay['fee'],
                                'amount' => $pay['amount'],
                                'age' => $pay['age'],
                                'q10' => $pay['q10'],
                                'notes' => $pay['notes'],
                                'created_by' => CRUDBooster::myId()
                            ];
                            array_push($new_order_pays, $new_pay);
                        }
                    }
                    // Log::debug('$new_order_pays = ' . Json::encode($new_order_pays));
                    DB::table('gold_sale_order_pays')->insert($new_order_pays);
                }

                if($new_order['order_type'] == 1){
                    $total_sales = $new_order['gold_amount'] + $new_order['fee'] + $new_order['balance'] - $new_order['discount_amount'] - $new_order['reduce'] - $new_order['use_points'];
                    $counter = DB::table('gold_counters')
                        ->whereRaw('deleted_at is null AND closed_at is null')
                        ->where('saler_id', CRUDBooster::myId())
                        ->first();
                    if($counter){
                        DB::table($this->table)->where('id', $order_id)->update(['counter_id' => $counter->id]);
                        $user = DB::table('cms_users')->where('id', $counter->saler_id)->first();
                        // Log::debug('$counter = ' . Json::encode($counter));
                        if($new_order['payment_method'] == 0){
                            DB::table('gold_counters')->where('id', $counter->id)->update([
                                'sales_amount'=>$counter->sales_amount + $total_sales,
                                'purchase_amount'=>$counter->purchase_amount + $new_order['pay_gold_amount']
                            ]);

                            DB::table('cms_users')->where('id', $user->id)->update([
                                'balance'=>$user->balance + $total_sales - $new_order['pay_gold_amount']
                            ]);
                        }else{
                            DB::table('gold_counters')->where('id', $counter->id)->update([
                                'bank_amount'=>$counter->bank_amount + ($total_sales - $new_order['pay_gold_amount']),
                                'sales_amount'=>$counter->sales_amount + $new_order['pay_gold_amount'],
                                'purchase_amount'=>$counter->purchase_amount + $new_order['pay_gold_amount']
                            ]);
                        }

                        if($total_q10 != 0){
                            DB::table('cms_users')->where('id', $user->id)->update([
                                'q10'=>$user->q10 + $total_q10
                            ]);
                        }
                    }

                    DB::table('gold_customers')->where('id', $customer_id)->update([
                        'balance' => $new_customer['balance'] - $new_order['balance'],
                        'points' => $points
                    ]);
                }
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['id'=>$order_id, 'order_no'=>$new_order_no, 'order_detail_ids'=>$order_detail_ids, 'customer_id'=>$customer_id]);
        }
        public function getResumeOrder(){
            $this->cbLoader();
            if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            $para = Request::all();
            $order_id = $para['order_id'];
            
            $order = DB::table('gold_sale_orders as SO')->where('SO.id', $order_id)->first();
            $customer = DB::table('gold_customers as C')->where('C.id', $order->customer_id)->first();
            $order_details = DB::table('gold_sale_order_details as SOD')
                ->leftJoin('gold_items as I', 'SOD.item_id', '=', 'I.id')
                ->leftJoin('gold_products as P', 'I.product_id', '=', 'P.id')
                ->leftJoin('gold_product_types as PT', 'I.product_type_id', '=', 'PT.id')
                ->whereRaw('SOD.deleted_at is null')
                ->where('SOD.order_id', $order_id)
                ->select('I.id',
                    'SOD.id as order_detail_id',
                    'SOD.sort_no as no',
                    'I.bar_code',
                    'P.product_code',
                    'P.product_name',
                    'I.total_weight',
                    'I.gem_weight',
                    'I.gold_weight',
                    'SOD.qty',
                    'I.retail_fee',
                    'I.whole_fee',
                    'I.product_type_id',
                    'PT.name as product_type_name',
                    'SOD.edit_weight',
                    'SOD.price',
                    'SOD.fee',
                    'SOD.gold_amount',
                    'SOD.discount as discount_amount',
                    'SOD.amount')
                ->get();
            $order_pays = DB::table('gold_sale_order_pays as SOP')
                ->leftJoin('gold_product_types as PT', 'SOP.product_type_id', '=', 'PT.id')
                ->whereRaw('SOP.deleted_at is null')
                ->where('SOP.order_id', $order_id)
                ->select('SOP.id',
                    'SOP.description',
                    'SOP.product_type_id',
                    'PT.name as product_type_name',
                    'SOP.total_weight',
                    'SOP.gem_weight',
                    'SOP.gold_weight',
                    'SOP.abate_weight',
                    'SOP.price',
                    'SOP.fee',
                    'SOP.amount',
                    'SOP.age',
                    'SOP.q10',
                    'SOP.notes')
                ->get();
            return ['order'=>$order, 'customer'=>$customer, 'order_details'=>$order_details, 'order_pays'=>$order_pays];
        }

        private function calcOrderPoint($order_id, $order_date){
            $point = 0;
            $order_details = DB::table('gold_sale_order_details as SOD')
                ->leftJoin('gold_items as I', 'SOD.item_id', '=', 'I.id')
                ->whereRaw('SOD.deleted_at is null')
                ->where('SOD.order_id', $order_id)
                ->groupBy('I.product_type_id')
                ->select('I.product_type_id', DB::raw('SUM(SOD.amount) as amount'))
                ->get();
            Log::debug('$order_details = ' . Json::encode($order_details));
            foreach ($order_details as $detail) {
                $point_rule = DB::table('gold_point_rule')
                    ->whereRaw('deleted_at is null')
                    ->where('apply_time', '<=', $order_date->format('Y-m-d H:i:s'))
                    ->where('product_type_id', $detail->product_type_id)
                    ->orderBy('apply_time', 'desc')
                    ->first();
                // Log::debug('$detail->product_type_id = ' . $detail->product_type_id);
                // Log::debug('$order_date->format(Y-m-d H:i:s) = ' . $order_date->format('Y-m-d H:i:s'));
                // Log::debug('$point_rule = ' . Json::encode($point_rule));
                if($point_rule && $point_rule->sales_amount > 0){
                    $point += floor($detail->amount / $point_rule->sales_amount) * $point_rule->point;
                }
            }
            return $point;
        }

        public function postResetPoint(){
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            $updated_at = date('Y-m-d H:i:s');
            DB::beginTransaction();
            try {
                DB::table('gold_customers')->update(['points'=>0]);
                $orders = DB::table($this->table)->whereRaw('deleted_at is null and order_type = 1')->orderBy('order_date')->get();    
                // Log::debug('$orders = ' . Json::encode($orders));
                if ($orders && count($orders)) {
                    foreach ($orders as $detail) {
                        $order_point = $this->calcOrderPoint($detail->id, DateTime::createFromFormat('Y-m-d H:i:s', $detail->order_date));
                        $points = $order_point - $detail->use_points;
                        Log::debug('$points = ' . $points);
                        Log::debug('$order_point = ' . $order_point);
                        $customer = DB::table('gold_customers')->where('id', $detail->customer_id)->first();    
                        if($customer){
                            $points += $customer->points;
                            DB::table('gold_customers')->where('id', $customer->id)->update(['points'=>$points]);
                        }
                        DB::table($this->table)->where('id', $detail->id)->update(['points'=>$points, 'order_point'=>$order_point]);
                    }
                }
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['result'=>true]);
	    }

        private function updateOrderHeader($update_order) {
            $order_id = intval($update_order['id']);
            if($order_id > 0) // update order
            {
                $order_id = intval($update_order['id']);
                $update_order['updated_at'] = date('Y-m-d H:i:s');
                $update_order['updated_by'] = CRUDBooster::myId();
                unset($update_order['id']);
                unset($update_order['created_at']);
                unset($update_order['created_by']);
                DB::table($this->table)->where($this->primary_key, $order_id)->update($update_order);
                // Log::debug('$order_id = ' . $order_id);
            }
            return $order_id;
        }

        public function postUpdateOrderHeader() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            $updated_at = date('Y-m-d H:i:s');
            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
                $update_order = $para['order'];
                $this->updateOrderHeader($update_order);
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['result'=>true]);
        }

        public function postAddNewOrderDetail() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            $updated_at = date('Y-m-d H:i:s');
            $order_detail_ids = [];
            $order_id = null;
            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
                $update_order = $para['order'];
                $order_details = $para['order_details'];

                $order_id = $this->updateOrderHeader($update_order);

                if ($order_details && count($order_details)) {
//                    $new_sale_order_details = [];
                    foreach ($order_details as $detail) {
                        $new_detail = [
                            'order_id' => $order_id,
                            'sort_no' => $detail['no'],
                            'item_id' => $detail['id'],
                            'edit_weight' => $detail['edit_weight'],
                            'qty' => 1,
                            'price' => $detail['price'],
                            'gold_amount' => $detail['gold_amount'],
                            'discount' => $detail['discount_amount'],
                            'fee' => $detail['fee'],
                            'amount' => $detail['amount'],
                            'created_by' => CRUDBooster::myId()
                        ];
                        // Log::debug('postAddNewOrderDetail gold_sale_order_details = ' . Json::encode($new_detail));
                        $new_detail_id = DB::table('gold_sale_order_details')->insertGetId($new_detail);
                        array_push($order_detail_ids, $new_detail_id);
                    }
//                    $order_detail_ids = DB::table('gold_sale_order_details')->insertGetId($new_sale_order_details);
                }
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['id'=>$order_id, 'order_detail_ids' => $order_detail_ids]);
        }

        public function postRemoveOrderDetail() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
                $update_order = $para['order'];
                $remove_order_detail_id = $para['remove_order_detail_id'];
                if (intval($remove_order_detail_id) > 0) {
                    DB::table('gold_sale_order_details')->where('id', $remove_order_detail_id)->delete();
                    $this->updateOrderHeader($update_order);
                }
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['result' => true]);
        }
        private function sendNotificationViaEmail($order_no, $order_date, $customer_id, $saler_id){
            try {
                $email = CRUDBooster::getSetting('email_sender');
                $customer = DB::table('gold_customers')->where('id', $customer_id)->first();
                $saler = DB::table('cms_users')->where('id', $saler_id)->first();
                $data = [
                    'order_no' => $order_no,
                    'order_date' => $order_date,
                    'customer_name' => $customer->name,
                    'saler_name' => $saler->name
                ];
                CRUDBooster::sendEmail(['to' => $email, 'data' => $data, 'template' => 'sale_notification_accountant']);
            }
            catch (exception $e) {
                $e = (string) $e;
                Log::debug('sendNotificationViaEmail error $e = '.$e);
            }
        }

        public function getPrintInvoice($id){
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'BH_'.time();
            $parameter = [
                'id'=>$id,
                'logo'=>storage_path().'/app/'.CRUDBooster::getSetting('logo'), 
                'background'=>storage_path().'/app/'.CRUDBooster::getSetting('favicon'),
                // 'comp_name'=>CRUDBooster::getSetting('ten'),
                // 'comp_prefix'=>CRUDBooster::getSetting('ten_thuong_mai'),
                // 'comp_phone'=>CRUDBooster::getSetting('dien_thoai'),
                // 'comp_address'=>CRUDBooster::getSetting('dia_chi')
            ];
            $output = public_path().'/output_reports/'.$filename;

            $details = DB::table('gold_sale_order_details')->where('order_id', $id)->get();
            $pays = DB::table('gold_sale_order_pays')->where('order_id', $id)->get();
            if(count($details) + count($pays) > 5){
                $input = base_path().'/app/Reports/rpt_invoice_a4.jasper';
            }else{
                $input = base_path().'/app/Reports/rpt_invoice.jasper';
            }
            // Log::debug('$logo = ' . $logo);
            // Log::debug('$input = ' . $input);
            // Log::debug('$output = ' . $output);
            // $command = $jasper->process($input, $output, array('pdf'), $parameter, $database)->output();
            // Log::debug('$database = ' , $database);
            // Log::debug('$command = ' . $command);
            $jasper->process($input, $output, array('pdf'), $parameter, $database)->execute();

            while (!file_exists($output.'.pdf' )){
                sleep(1);
            }

            $file = File::get( $output.'.pdf' );

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/pdf',
                    'Content-Disposition' => 'filename="'.$filename.'.pdf"'
                )
            );
        }

        public function getPrintInvoiceBlank(){
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'BHB_'.time();
            $parameter = [
                'logo'=>storage_path().'/app/'.CRUDBooster::getSetting('logo'), 
                'background'=>storage_path().'/app/'.CRUDBooster::getSetting('favicon'),
            ];
            $output = public_path().'/output_reports/'.$filename;
            $input = base_path().'/app/Reports/rpt_invoice_blank.jasper';
            $jasper->process($input, $output, array('pdf'), $parameter, $database)->execute();

            while (!file_exists($output.'.pdf' )){
                sleep(1);
            }

            $file = File::get( $output.'.pdf' );

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/pdf',
                    'Content-Disposition' => 'filename="'.$filename.'.pdf"'
                )
            );
        }

        public function getPrintSales($para){
        //public function getPrintSales($type, $from_date, $to_date, $user_ids, $brand_id){
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'SS_'.time();
            
            $parameter = [
                'brand_id'=>substr($para, 2, 1),
				'from_date'=>substr($para, 4, 10),
				'to_date'=>substr($para, 15, 10),
				'user_ids'=>substr($para, 26, strlen($para)),
                'logo'=>storage_path().'/app/uploads/logo.png'
			];
            $input = base_path().'/app/Reports/rpt_sales.jasper';
            $output = public_path().'/output_reports/'.$filename;
            $ext = substr($para, 0, 1) == 'X' ? 'xlsx' : 'pdf';
            $jasper->process($input, $output, array($ext), $parameter, $database)->execute();

            while (!file_exists($output . '.' . $ext)){
                sleep(1);
            }

            $file = File::get($output . '.' . $ext);
            if($ext == 'xlsx'){
                unlink($output . '.xlsx');
            }

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/' . ($ext == 'xlsx' ? 'vnd.openxmlformats-officedocument.spreadsheetml.sheet' : $ext),
                    'Content-Disposition' => 'filename="' . $filename . '.' . $ext . '"'
                )
            );
        }
        
        public function getPrintSalesDetail($para){
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SD_'.time();
            $parameter = [
				'brand_id'=>substr($para, 2, 1),
				'from_date'=>substr($para, 4, 10),
				'to_date'=>substr($para, 15, 10),
				'user_ids'=>substr($para, 26, strlen($para)),
                'logo'=>storage_path().'/app/uploads/logo.png'
			];
            $input = base_path().'/app/Reports/rpt_sales_detail.jasper';
            $output = public_path().'/output_reports/'.$filename;
            $ext = substr($para, 0, 1) == 'X' ? 'xlsx' : 'pdf';
            $jasper->process($input, $output, array($ext), $parameter, $database)->execute();

            while (!file_exists($output . '.' . $ext)){
                sleep(1);
            }

            $file = File::get($output . '.' . $ext);
            if($ext == 'xlsx'){
                unlink($output . '.xlsx');
            }

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/' . ($ext == 'xlsx' ? 'vnd.openxmlformats-officedocument.spreadsheetml.sheet' : $ext),
                    'Content-Disposition' => 'filename="' . $filename . '.' . $ext . '"'
                )
            );
        }

        public function getExportDetail($para){
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'ED_'.time();
            $parameter = [
				'from_date'=>substr($para, 2, 10),
				'to_date'=>substr($para, 13, 10),
				'user_ids'=>substr($para, 24, strlen($para)),
                'logo'=>storage_path().'/app/uploads/logo.png'
			];
            $input = base_path().'/app/Reports/' . (substr($para, 0, 1) == 'S' ? 'exp_sales_detail' : 'exp_purchase_detail') . '.jasper';
            $output = public_path().'/output_reports/'.$filename;
            $jasper->process($input, $output, array('xlsx'), $parameter, $database)->execute();

            while (!file_exists($output . '.xlsx')){
                sleep(1);
            }

            $file = File::get($output . '.xlsx');
            unlink($output . '.xlsx');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'filename="' . $filename . '.xlsx"'
                )
            );
        }

        public function getReportDetails($para){
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'ED_'.time();
            $paras = explode('@', $para);
            $parameter = [
                'from_date'=>$paras[1],
                'to_date'=>$paras[2],
                'user_ids'=>$paras[3],
                'branch_id'=>$paras[4],
                'logo'=>storage_path().'/app/uploads/logo.png',
                'IS_IGNORE_PAGINATION' => true,
            ];
            $input = base_path().'/app/Reports/' . ($paras[0] == 'S' ? 'rpt_report_sales_details' : 'rpt_report_purchase_details') . '.jasper';
            $output = public_path().'/output_reports/'.$filename;
            $jasper->process($input, $output, array('xlsx'), $parameter, $database)->execute();

            while (!file_exists($output . '.xlsx')){
                sleep(1);
            }

            $file = File::get($output . '.xlsx');
            unlink($output . '.xlsx');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'filename="' . $filename . '.xlsx"'
                )
            );
        }
	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
//	    public function hook_after_add($id) {
//	        //Your code here
//
//	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_edit(&$postdata,$id) {        
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_edit($id) {
	        //Your code here 

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_before_delete($id) {
            // Your code here
	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hook_after_delete($id) {
	        //Your code here
            $order = DB::table($this->table)->where('id', $id)->first();    
            // Log::debug('$order = ' . Json::encode($order));
            if($order && $order->order_type == 1){
                $q10 = 0;
                $order_details  = DB::table('gold_sale_order_details')->whereRaw('deleted_at is null')->where('order_id',$id)->get();
                foreach ($order_details as $detail) {
                    $item = DB::table('gold_items')->where('id', $detail->item_id)->first();
                    if($item){
                        $q10 += $detail->q10;
                        DB::table('gold_items')->where('id', $item->id)->update(['qty'=>1, 'status'=>1, 'notes' => '']);
                    }
                }

                $pays = DB::table('gold_sale_order_pays')->whereRaw('deleted_at is null')
                    ->where('order_id', $order->id)->select(DB::raw('SUM(q10) as q10'))->first();
                if($pays && $pays->q10){
                    $q10 += $pays->q10;
                }

                $counter = DB::table('gold_counters')->where('id', $order->counter_id)->first();    
                // Log::debug('$counter = ' . Json::encode($counter));
                if($counter){
                    $user = DB::table('cms_users')->where('id', $counter->saler_id)->first();
                    $total_sales = $order->gold_amount + $order->fee + $order->balance - $order->discount_amount - $order->reduce - $order->use_points;
                    if($order->payment_method == 0){
                        DB::table('gold_counters')->where('id', $counter->id)->update([
                            'sales_amount' => $counter->sales_amount - $total_sales,
                            'purchase_amount' => $counter->purchase_amount - $order->pay_gold_amount
                        ]);

                        DB::table('cms_users')->where('id', $user->id)->update([
                            'balance' => $user->balance - ($total_sales - $order->pay_gold_amount)
                        ]);
                    }else{
                        DB::table('gold_counters')->where('id', $counter->id)->update([
                            'bank_amount' => $counter->bank_amount - ($total_sales - $order->pay_gold_amount),
                            'sales_amount' => $counter->sales_amount - $order->pay_gold_amount,
                            'purchase_amount' => $counter->purchase_amount - $order->pay_gold_amount
                        ]);
                    }

                    if($q10 != 0){
                        DB::table('cms_users')->where('id', $user->id)->update(['q10' => $user->q10 - $q10]);
                    }
                }

                $customer = DB::table('gold_customers')->where('id', $order->customer_id)->first();    
                if($customer){
                    DB::table('gold_customers')->where('id', $customer->id)->update([
                        'points' => $customer->points - $order->order_point + $order->use_point,
                        'balance' => $customer->balance + $order->balance
                    ]);
                }
            }
	    }

	    //By the way, you can still create your own method in here... :) 
	}