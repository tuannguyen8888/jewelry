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

	class AdminGoldPawnOrdersController extends CBExtendController {

	    public function cbInit() {
			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "order_no";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon_text";
			$rows = DB::table('gold_counters')
				->whereRaw('deleted_at is null AND closed_at is null')
				->where('saler_id', CRUDBooster::myId())
				->first();
			if($rows){
				$this->button_add = true;
			}else{
				$this->button_add = false;
			}
			$this->button_edit = false;
			if(CRUDBooster::myPrivilegeId() != 1 && CRUDBooster::myPrivilegeId() != 4){
				$this->button_delete = false;
			}else{
				$this->button_delete = true;
			}
			$this->button_detail = true;
			$this->button_show = false;
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "gold_pawn_orders";
            $this->is_search_form = true;
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Số phiếu","name"=>"order_no","width"=>"100"];
			$this->col[] = ["label"=>"T/g cầm","name"=>"order_date","callback_php"=>'date_time_format($row->order_date, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Trạng thái","name"=>"status","callback_php"=>'get_pawn_status($row->status).get_due_status($row->due_status)'];
			$this->col[] = ["label"=>"Mã KH","name"=>"customer_id","join"=>"gold_customers,code"];
			$this->col[] = ["label"=>"Tên KH","name"=>"customer_id","join"=>"gold_customers,name"];
            $this->col[] = ["label"=>"Phone","name"=>"customer_id","join"=>"gold_customers,phone"];
            $this->col[] = ["label"=>"Zalo phone","name"=>"customer_id","join"=>"gold_customers,zalo_phone"];
			$this->col[] = ["label"=>"Số tiền","name"=>"amount", "callback_php"=>'number_format($row->amount)'];
			$this->col[] = ["label"=>"Thời hạn","name"=>"due_date", "callback_php"=>'number_format($row->due_date)'];
			$this->col[] = ["label"=>"T/h tối thiểu","name"=>"min_days", "callback_php"=>'number_format($row->min_days)'];
			$this->col[] = ["label"=>"Nhà ĐT","name"=>"investor_id","join"=>"gold_investors,name"];
			$this->col[] = ["label"=>"Đóng lãi lần cuối","name"=>"last_interested_at","callback_php"=>'date_time_format($row->last_interested_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"T/g thanh lý","name"=>"liquidation_at","callback_php"=>'date_time_format($row->liquidation_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			// $this->col[] = ["label"=>"HTTL","name"=>"liquidation_method","callback_php"=>'get_liquidation_method($row->liquidation_method);'];
			$this->col[] = ["label"=>"Nhân viên","name"=>"saler_id","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Cửa hàng","name"=>"brand_id","join"=>"gold_brands,name"];
			# END COLUMNS DO NOT REMOVE THIS LINE

            // Nguen add new for search

            $this->search_form = [];
            if(CRUDBooster::myPrivilegeId() == 2) {
                $this->search_form[] = ["label" => "Khách hàng/ Số ĐT", "name" => "customer", "data_column"=>"gold_pawn_orders.customer_id", "search_type"=>"equals_raw", "type" => "select2", "width" => "col-sm-6", 'datatable' => 'gold_customers,name', 'datatable_where' => 'deleted_at is null', 'datatable_format' => "code,' - ',name,' - ',IFNULL(phone,''),' - ',IFNULL(zalo_phone,'')"];
            }else{
                $this->search_form[] = ["label" => "Khách hàng/ Số ĐT", "name" => "customer", "data_column"=>"gold_pawn_orders.customer_id", "search_type"=>"equals_raw", "type" => "select2", "width" => "col-sm-6", 'datatable' => 'gold_customers,name', 'datatable_where' => 'deleted_at is null', 'datatable_format' => "code,' - ',name,' - ',IFNULL(phone,''),' - ',IFNULL(zalo_phone,'')"];
            }
            //$this->search_form[] = ["label"=>"Xuống dòng", "name"=>"break_line", "type"=>"break_line"];
            $this->search_form[] = ["label"=>"Nhân viên", "name"=>"saler", "data_column"=>"gold_pawn_orders.saler_id", "search_type"=>"equals_raw","type"=>"select2","width"=>"col-sm-2", 'datatable'=>'cms_users,name', 'datatable_where'=>CRUDBooster::myPrivilegeId() == 2 ? 'id = '.CRUDBooster::myId() : 'id_cms_privileges in (2,3,4,5)', 'datatable_format'=>"employee_code,' - ',name,' (',email,')'"];
            $this->search_form[] = ["label"=>"Từ ngày", "name"=>"order_date_from_date", "data_column"=>"order_date", "search_type"=>"between_from","type"=>"date","width"=>"col-sm-2"];
            $this->search_form[] = ["label"=>"Đến ngày", "name"=>"order_date_to_date", "data_column"=>"order_date", "search_type"=>"between_to","type"=>"date","width"=>"col-sm-2"];

            if(CRUDBooster::myPrivilegeId() == 1 || CRUDBooster::myPrivilegeId() == 4){
                $this->search_form[] = ["label" => "Cửa hàng", "name" => "brand_id", "data_column"=>$this->table.".brand_id", "search_type"=>"equals_raw", "type" => "select2", "width" => "col-sm-2", 'datatable' => 'gold_brands,name', 'datatable_where' => 'deleted_at is null'];
            }
            $this->search_form[] = ["label"=>"Xuống dòng", "name"=>"break_line", "type"=>"break_line"];

			$this->search_form[] = ["label"=>"Trạng thái","name"=>"status_search","data_column"=>'gold_pawn_orders.status', "search_type"=>"equals_raw", "type"=>"select","width"=>"col-sm-2",'dataenum'=>\Enums::$PAWN_STATUS];
            $this->search_form[] = ["label"=>"Đúng hạn/Quá hạn", "name"=>"due_date","type"=>"select","width"=>"col-sm-2",'dataenum'=>\Enums::$DUE_STATUS, "search_type"=>"in_details", "mark_value"=>"[value_search]",
                "sub_query"=>"CASE WHEN gold_pawn_orders.status = 1 THEN (CASE WHEN ((gold_pawn_orders.last_interested_at is null and DATEDIFF(NOW(),gold_pawn_orders.order_date) > 30) or (gold_pawn_orders.last_interested_at is not null and  DATEDIFF(NOW(),gold_pawn_orders.last_interested_at) > 30)) THEN 2 ELSE 1 END) ELSE 0 END = [value_search]"
            ];

            # START FORM DO NOT  REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Số phiếu','name'=>'order_no','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
            $this->form[] = ['label'=>'Ngày cầm','name'=>'order_date','type'=>'date','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10','help'=>'Số đơn hàng sẽ tự phát sinh khi bạn lưu','readonly'=>'true'];
            $this->form[] = ['label'=>'Khách hàng','name'=>'customer_id','type'=>'datamodal','validation'=>'required|integer|min:0','width'=>'col-sm-10','datamodal_table'=>'gold_customers','datamodal_columns'=>'code,tmp_code,name,address,phone','datamodal_size'=>'large','datamodal_where'=>'deleted_at is null','datamodal_module_path'=>'gold_customers/add','datamodal_columns_alias_name'=>'Mã khách hàng,Mã tạm,Tên khách hàng,Địa chỉ,Số điện thoại','help'=>'Chọn khách hàng'];
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
			$this->addaction[] = ['label'=>'In nhãn','url'=>CRUDBooster::mainpath('print-label/[id]'),'icon'=>'fa fa-buysellads','color'=>'info', 'showIf'=>"[status] != 0"];
			$this->addaction[] = ['label'=>'In hợp đồng','url'=>CRUDBooster::mainpath('print-invoice/[id]'),'icon'=>'fa fa-newspaper-o','color'=>'info', 'showIf'=>"[status] != 0"];

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
            $this->index_statistic[] = ['label'=>'Tổng số phiếu','use_main_query'=>true,'operator'=>'count','icon'=>'fa fa-newspaper-o','color'=>'success'];
            $this->index_statistic[] = ['label'=>'Tổng số tiền','use_main_query'=>true,'operator'=>'sum','field'=>'amount','icon'=>'fa fa-usd','color'=>'danger'];
            $this->index_statistic[] = ['label'=>'Số khách hàng','use_main_query'=>true,'operator'=>'count_distinct','field'=>'customer_id','icon'=>'fa fa-users','color'=>'info'];
            $this->index_statistic[] = ['label'=>'Số nhà đầu tư','use_main_query'=>true,'operator'=>'count_distinct','field'=>'investor_id','icon'=>'fa fa-user','color'=>'primary'];


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
            $data = [];
            $data['page_title'] = 'Tạo mới hợp đồng cầm';
            $data += ['mode' => 'new'];
            $this->cbView('pawn_order_form', $data);
        }

        public function getEdit($id)
        {
            $data = [];
            $data['page_title'] = 'Sửa hợp đồng cầm';
            $data += ['mode' => 'edit', 'resume_id' => $id];
            $this->cbView('pawn_order_form', $data);
        }

        public function getDetail($id)
        {
            $data = [];
            $data['page_title'] = 'Xem hợp đồng cầm';
            $data += ['mode' => 'view', 'resume_id' => $id];
            $this->cbView('pawn_order_form', $data);
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
//                $query->where('gold_pawn_orders.saler_id', CRUDBooster::myId());
                $query->where('gold_pawn_orders.brand_id', CRUDBooster::myBrand());
            }
            $current_brand = CRUDBooster::myBrand();
            $privilegeId = CRUDBooster::myPrivilegeId();
            if($current_brand && $privilegeId != 1 && $privilegeId != 4){
                $query->where($this->table.'.brand_id', '=', $current_brand);
            }
            $query->addSelect(DB::raw('CASE WHEN gold_pawn_orders.status = 1 THEN (CASE WHEN ((gold_pawn_orders.last_interested_at is null and DATEDIFF(NOW(),gold_pawn_orders.order_date) > 30) or (gold_pawn_orders.last_interested_at is not null and  DATEDIFF(NOW(),gold_pawn_orders.last_interested_at) > 30)) THEN 2 ELSE 1 END) ELSE 0 END as due_status'));
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
                $order_pawns = $para['order_pawns'];
				$counter = $para['counter'];
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

                if($new_order['status'] == 1 && $counter){
                    $new_order['counter_id'] = $counter['id'];
                    DB::table('gold_counters')->where('id', $counter['id'])->update([
						'pawn_amount' => $counter['pawn_amount'] + $new_order['amount'],
						'updated_at' => date('Y-m-d H:i:s'),
                		'updated_by' => CRUDBooster::myId()
					]);

					$user = DB::table('cms_users')->where('id', $counter['saler_id'])->first();
					if($user){
						DB::table('cms_users')->where('id', $user->id)->update([
							'balance' => $user->balance - $new_order['amount']
						]);
					}
                }

                $order_date_str = $new_order['order_date'];
                $order_date = DateTime::createFromFormat('Y-m-d H:i:s', $order_date_str);

                if( $new_order['id'] && intval($new_order['id']) > 0) // update order
                {
                    $order_id = intval($new_order['id']);
                    $this->updateOrderHeader($new_order);
                }
                else // new order
                {
					// $investor = DB::table('gold_investors')->where('id', $new_order['investor_id'])->first();    
					// if($new_order['status'] == 1 && $investor){
					// 	DB::table('gold_investors')->where('id', $investor->id)->update([
					// 		'balance' => $investor->balance + $new_order['amount']
					// 	]);
					// }
                    // get new order no
					$new_order_no = 'CV' . $order_date->format('ymd');
					$last_order = DB::table('gold_pawn_orders as SO')
                        ->whereRaw('SO.deleted_at is null')
                        ->where('SO.order_date', '>=', $order_date->format('Y-m-d') . ' 00:00:00')
						->where('SO.order_date', '<=', $order_date->format('Y-m-d') . ' 23:59:59')
						->where('SO.order_no', 'like', $new_order_no . '%')
                        ->orderBy('SO.id', 'desc')
                        ->first();
                    if ($last_order) {
						$old_no = intval(explode('-', $last_order->order_no)[1]);
						$old_no_str = '' . ($old_no + 1);
						if(strlen($old_no_str) < 3){
							$old_no_str = '000' . $old_no_str;
							$old_no_str = substr($old_no_str, strlen($old_no_str) - 3, 3);
						}
                        $new_order_no = $new_order_no . '-' . $old_no_str;
                    } else {
                        $new_order_no = $new_order_no . '-001';
                    }
                    $new_order['order_no'] = $new_order_no;
					$new_order['created_by'] = CRUDBooster::myId();
					$new_order['brand_id'] = CRUDBooster::myBrand();
                    unset($new_order['id']);
                    $order_id = DB::table($this->table)->insertGetId($new_order);
                    Log::debug('$order_id = ' . $order_id);
                }

                $new_order_pawns = [];
                if ($order_pawns && count($order_pawns)) {
                    foreach ($order_pawns as $pay) {
                        if($pay['description']){
                            $new_pay = [
                                'order_id' => $order_id,
                                'description' => $pay['description'],
								'amount' => $pay['amount'],
								'rate' => $pay['rate'],
								'interested' => $pay['interested'],
                                'notes' => $pay['notes'],
                                'created_by' => CRUDBooster::myId()
                            ];
                            array_push($new_order_pawns, $new_pay);
                        }
                    }
                    DB::table('gold_pawn_order_details')->where('order_id', $order_id)->delete();
                    Log::debug('$new_order_pawns = ' . Json::encode($new_order_pawns));
                    DB::table('gold_pawn_order_details')->insert($new_order_pawns);
                }
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['id'=>$order_id, 'order_no'=>$new_order_no, 'customer_id'=>$customer_id]);
        }

        public function getResumeOrder(){
            $this->cbLoader();
            if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
			$para = Request::all();
			$order_id = $para['order_id'];
            $order = DB::table($this->table)->where($this->primary_key, $order_id)->first();
			$customer = DB::table('gold_customers')->where('id', $order->customer_id)->first();
			$investor = DB::table('gold_investors')->where('id', $order->investor_id)->first();
			$liquidation = DB::table('cms_users')->where('id', $order->liquidation_by)->first();
            $order_pawns = DB::table('gold_pawn_order_details')
                ->whereRaw('deleted_at is null')
                ->where('order_id', $order_id)
                ->select('id',
                    'description',
					'amount',
					'rate',
					'interested',
                    'notes')
				->get();
			$order_interested = DB::table('gold_pawn_order_interested')
                ->whereRaw('deleted_at is null')
				->where('order_id', $order_id)
				->orderBy('id')
                ->get();
            return ['order'=>$order, 'customer'=>$customer, 'investor'=>$investor, 'liquidation'=>$liquidation, 'order_pawns'=>$order_pawns, 'order_interested'=>$order_interested];
		}
		
        private function updateOrderHeader($update_order) {
			$order_id = intval($update_order['id']);
            if($order_id > 0) // update order
            {
                $update_order['updated_at'] = date('Y-m-d H:i:s');
                $update_order['updated_by'] = CRUDBooster::myId();
                unset($update_order['id']);
                unset($update_order['created_at']);
                unset($update_order['created_by']);
                DB::table($this->table)->where($this->primary_key, $order_id)->update($update_order);
            }
            return $order_id;
		}
		
        public function getPrintInvoice($id){
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'CV_'.time();
            $parameter = [
                'id'=>$id,
                'logo'=>storage_path().'/app/'.CRUDBooster::getSetting('logo'), 
                'background'=>storage_path().'/app/'.CRUDBooster::getSetting('favicon'),
            ];
            $output = public_path().'/output_reports/'.$filename;
			$input = base_path().'/app/Reports/rpt_pawn_invoice.jasper';
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
            $filename = 'CVB_'.time();
            $parameter = [
                'logo'=>storage_path().'/app/'.CRUDBooster::getSetting('logo'), 
                'background'=>storage_path().'/app/'.CRUDBooster::getSetting('favicon'),
            ];
            $output = public_path().'/output_reports/'.$filename;
			$input = base_path().'/app/Reports/rpt_pawn_invoice_blank.jasper';
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
		
		public function getPrintLabel($id){
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'NCV_'.time();
            $parameter = ['id'=>$id];
            $output = public_path().'/output_reports/'.$filename;
			$input = base_path().'/app/Reports/rpt_pawn_label.jasper';
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
		
		public function getSearchOrder(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $para = Request::all();
            $order_id = $para['order_id'];
            $order_no = $para['order_no'];

           	Log::debug('$para = '.json_encode($para));
           	if($order_id){
                $order = DB::table('gold_pawn_orders')->whereRaw('deleted_at is null')->where('id', $order_id)->first();
            }else {
                $current_brand = CRUDBooster::myBrand();
                $order = DB::table('gold_pawn_orders')
                    ->whereRaw('deleted_at is null')
                    ->where('order_no', $order_no)
                    ->where('brand_id', $current_brand)
                    ->first();
            }
			$customer = DB::table('gold_customers')->where('id', $order->customer_id)->first();
			$investor = DB::table('gold_investors')->where('id', $order->investor_id)->first();
			$detail = DB::table('gold_pawn_order_details')
				->whereRaw('deleted_at is null')
				->where('order_id', $order->id)
				->get();
			$interested = DB::table('gold_pawn_order_interested')
				->whereRaw('deleted_at is null')
				->where('order_id', $order->id)
				->get();
			return ['order'=>$order, 'customer'=>$customer, 'investor'=>$investor, 'detail'=>$detail, 'interested'=>$interested];
        }
		
	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hook_after_add($id) {
	        //Your code here

	    }

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
	        //Your code here

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
            $order = DB::table($this->table)->where($this->primary_key, $id)->first();    
            // Log::debug('$order = ' . Json::encode($order));
            if($order && $order->status == 1){
                $counter = DB::table('gold_counters')->where('id', $order->counter_id)->first();    
                // Log::debug('$counter = ' . Json::encode($counter));
                if($counter){
                    DB::table('gold_counters')->where('id', $counter->id)->update([
                        'pawn_amount' => $counter->pawn_amount - $order->amount
					]);
					
					$user = DB::table('cms_users')->where('id', $counter->saler_id)->first();
					if($user){
						DB::table('cms_users')->where('id', $user->id)->update([
							'balance' => $user->balance + $order->amount
						]);
					}
				}
				
				// $investor = DB::table('gold_investors')->where('id', $order->investor_id)->first();    
                // // Log::debug('$investor = ' . Json::encode($investor));
                // if($investor){
                //     DB::table('gold_investors')->where('id', $investor->id)->update([
                //         'balance' => $investor->balance - $order->amount
                //     ]);
                // }
            }
	    }

	    //By the way, you can still create your own method in here... :) 
	}