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

	class AdminGoldPawnOrderInterestedController extends CBExtendController {

	    public function cbInit() {
			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "interested_no";
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
			$this->table = "gold_pawn_order_interested";
            $this->is_search_form = true;
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Số phiếu","name"=>"interested_no","width"=>"100"];
			$this->col[] = ["label"=>"T/g đóng lãi","name"=>"interested_date","callback_php"=>'date_time_format($row->interested_date, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Tiền lãi","name"=>"amount","callback_php"=>'number_format($row->amount)'];
			$this->col[] = ["label"=>"Số hợp đồng","name"=>"order_id","join"=>"gold_pawn_orders,order_no","width"=>"100"];
			$this->col[] = ["label"=>"T/g cầm","name"=>DB::raw('gold_pawn_orders.order_date'),"join"=>"gold_pawn_orders,order_date","callback_php"=>'date_time_format($row->interested_date, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Tiền cầm","name"=>DB::raw('gold_pawn_orders.amount as pawn_amount'),"join"=>"gold_pawn_orders,amount","callback_php"=>'number_format($row->pawn_amount)'];
			$this->col[] = ["label"=>"Mã KH","name"=>"customer_id","join"=>"gold_customers,code"];
			$this->col[] = ["label"=>"Tên KH","name"=>"customer_id","join"=>"gold_customers,name"];
			$this->col[] = ["label"=>"Nhân viên","name"=>"saler_id","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Cửa hàng","name"=>"brand_id","join"=>"gold_brands,name"];
			# END COLUMNS DO NOT REMOVE THIS LINE

            // Nguen add new for search

            $this->search_form = [];
            if(CRUDBooster::myPrivilegeId() == 2) {
                $this->search_form[] = ["label" => "Khách hàng/ Số ĐT", "name" => "customer", "data_column"=>"gold_pawn_order_interested.customer_id", "search_type"=>"equals_raw", "type" => "select2", "width" => "col-sm-6", 'datatable' => 'gold_customers,name', 'datatable_where' => 'deleted_at is null', 'datatable_format' => "code,' - ',name,' - ',IFNULL(phone,''),' - ',IFNULL(zalo_phone,'')"];
            }else{
                $this->search_form[] = ["label" => "Khách hàng/ Số ĐT", "name" => "customer", "data_column"=>"gold_pawn_order_interested.customer_id", "search_type"=>"equals_raw", "type" => "select2", "width" => "col-sm-6", 'datatable' => 'gold_customers,name', 'datatable_where' => 'deleted_at is null', 'datatable_format' => "code,' - ',name,' - ',IFNULL(phone,''),' - ',IFNULL(zalo_phone,'')"];
            }
            $this->search_form[] = ["label"=>"Nhân viên", "name"=>"saler", "data_column"=>"gold_pawn_order_interested.saler_id", "search_type"=>"equals_raw","type"=>"select2","width"=>"col-sm-2", 'datatable'=>'cms_users,name', 'datatable_where'=>CRUDBooster::myPrivilegeId() == 2 ? 'id = '.CRUDBooster::myId() : 'id_cms_privileges in (2,3,4,5)', 'datatable_format'=>"employee_code,' - ',name,' (',email,')'"];
            $this->search_form[] = ["label"=>"Từ ngày", "name"=>"interested_date_from_date", "data_column"=>"interested_date", "search_type"=>"between_from","type"=>"date","width"=>"col-sm-2"];
            $this->search_form[] = ["label"=>"Đến ngày", "name"=>"interested_date_to_date", "data_column"=>"interested_date", "search_type"=>"between_to","type"=>"date","width"=>"col-sm-2"];
            if(CRUDBooster::myPrivilegeId() == 1 || CRUDBooster::myPrivilegeId() == 4){
                $this->search_form[] = ["label" => "Cửa hàng", "name" => "brand_id", "data_column"=>$this->table.".brand_id", "search_type"=>"equals_raw", "type" => "select2", "width" => "col-sm-2", 'datatable' => 'gold_brands,name', 'datatable_where' => 'deleted_at is null'];
            }
			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Số phiếu','name'=>'interested_no','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
            $this->form[] = ['label'=>'Ngày cầm','name'=>'interested_date','type'=>'date','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10','help'=>'Số đơn hàng sẽ tự phát sinh khi bạn lưu','readonly'=>'true'];
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
			$this->addaction[] = ['label'=>'In phiếu','url'=>CRUDBooster::mainpath('print-invoice/[id]'),'icon'=>'fa fa-newspaper-o','color'=>'info', 'showIf'=>"[status] != 0"];

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
            $this->index_statistic[] = ['label'=>'Tổng số tiền cầm','use_main_query'=>true,'operator'=>'sum','field'=>'gold_pawn_order_interested.pawn_amount','icon'=>'fa fa-usd','color'=>'danger'];
            $this->index_statistic[] = ['label'=>'Tổng số tiền lãi','use_main_query'=>true,'operator'=>'sum','field'=>'gold_pawn_order_interested.interested_amount','icon'=>'fa fa-money','color'=>'warning'];
            $this->index_statistic[] = ['label'=>'Số khách hàng','use_main_query'=>true,'operator'=>'count_distinct','field'=>'gold_pawn_order_interested.customer_id','icon'=>'fa fa-users','color'=>'info'];



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
            $para = Request::all();
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $data = [];
            $data['page_title'] = 'Tạo mới phiếu đóng lãi';
            $data += ['mode' => 'new', 'stock_ids' => $user->stock_id];
            $this->cbView('pawn_interested_form', $data);
        }

        public function getEdit($id)
        {
            $para = Request::all();
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $data = [];
            $data['page_title'] = 'Sửa phiếu đóng lãi';
            $data += ['mode' => 'edit', 'stock_ids' => $user->stock_id, 'resume_id' => $id];
            $this->cbView('pawn_interested_form', $data);
        }

        public function getDetail($id)
        {
            $para = Request::all();
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $data = [];
            $data['page_title'] = 'Xem phiếu đóng lãi';
            $data += ['mode' => 'view', 'stock_ids' => $user->stock_id, 'resume_id' => $id];
            $this->cbView('pawn_interested_form', $data);
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
                $query->where('gold_pawn_order_interested.saler_id', CRUDBooster::myId());
            }
            $current_brand = CRUDBooster::myBrand();
            $privilegeId = CRUDBooster::myPrivilegeId();
            if($current_brand && $privilegeId != 1 && $privilegeId != 4){
                $query->where($this->table.'.brand_id', '=', $current_brand);
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
                $order = $para['order'];
				$counter = $para['counter'];
				$interested = $para['interested'];

				$amount = $interested['amount'];
				if($order['liquidation_method'] == "0"){
					$counter['liquidation_amount'] = $counter['liquidation_amount'] + $order['amount'];
					$amount = $interested['amount'] - $order['amount'];
					$order['status'] = 3;
                }
				if($order['liquidation_method'] == "0" || $order['liquidation_method'] == "1"){
					$order['liquidation_at'] = $interested['interested_date'];
					$order['liquidation_by'] = CRUDBooster::myId();

					if($order['liquidation_method'] == "1"){
						$order['status'] = 2;
					}
				}else{
					$order['liquidation_method'] = null;
				}
				$order['last_interested_by'] = CRUDBooster::myId();
				$order['last_interested_at'] = $interested['interested_date'];

                $interested_date_str = $interested['interested_date'];
				$interested_date = DateTime::createFromFormat('Y-m-d H:i:s', $interested_date_str);
				$new_interested_no = 'DL' . $interested_date->format('ymd');
				$last_order = DB::table($this->table)
					->whereRaw('deleted_at is null')
					->where('interested_date', '>=', $interested_date->format('Y-m-d') . ' 00:00:00')
					->where('interested_date', '<=', $interested_date->format('Y-m-d') . ' 23:59:59')
					->where('interested_no', 'like', $new_interested_no . '%')
					->orderBy('interested_no', 'desc')
					->first();
				if ($last_order) {
					$old_no = intval(explode('-', $last_order->interested_no)[1]);
					$old_no_str = '' . ($old_no + 1);
					if(strlen($old_no_str) < 3){
						$old_no_str = '000' . $old_no_str;
						$old_no_str = substr($old_no_str, strlen($old_no_str) - 3, 3);
					}
					$new_interested_no = $new_interested_no . '-' . $old_no_str;
				} else {
					$new_interested_no = $new_interested_no . '-001';
				}
				$interested['counter_id'] = $counter['id'];
				$interested['order_id'] = $order['id'];
				$interested['customer_id'] = $order['customer_id'];
				$interested['interested_no'] = $new_interested_no;
				$interested['saler_id'] = CRUDBooster::myId();
				$interested['created_by'] = CRUDBooster::myId();
				$interested['brand_id'] = CRUDBooster::myBrand();
				unset($interested['id']);
				$interested_id = DB::table($this->table)->insertGetId($interested);
				Log::debug('$interested_id = ' . $interested_id);
				
				$counter['interested_amount'] += $amount;
				$counter['updated_at'] = date('Y-m-d H:i:s');
				$counter['updated_by'] = CRUDBooster::myId();
				$counter_id = $counter['id'];
				unset($counter['id']);
                unset($counter['created_at']);
                unset($counter['created_by']);
				DB::table('gold_counters')->where('id', $counter_id)->update($counter);

				$investor = DB::table('gold_investors')->where('id', $order['investor_id'])->first();
				if($investor){
					DB::table('gold_investors')->where('id', $investor->id)->update([
						'balance' => $investor->balance + $amount
					]);
				}

				$user = DB::table('cms_users')->where('id', $counter['saler_id'])->first();
				if($user){
					DB::table('cms_users')->where('id', $user->id)->update([
						'balance' => $user->balance + $interested['amount']
					]);
				}
				
				$order['interested_amount'] += $amount;
				$order['updated_at'] = date('Y-m-d H:i:s');
				$order['updated_by'] = CRUDBooster::myId();
				$order_id = $order['id'];
				unset($order['id']);
                unset($order['created_at']);
                unset($order['created_by']);
				DB::table('gold_pawn_orders')->where('id', $order_id)->update($order);
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['id'=>$interested_id, 'interested_no'=>$new_interested_no]);
        }

        public function getResumeOrder(){
            $this->cbLoader();
            if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            $para = Request::all();
            $id = $para['id'];
			$interested = DB::table($this->table)->where($this->primary_key, $id)->first();
            $order = DB::table('gold_pawn_orders')->where('id', $interested->order_id)->first();
			$customer = DB::table('gold_customers')->where('id', $interested->customer_id)->first();
			$investor = DB::table('gold_investors')->where('id', $order->investor_id)->first();
            $detail = DB::table('gold_pawn_order_details')
                ->whereRaw('deleted_at is null')
                ->where('order_id', $interested->order_id)
				->orderBy('id')
				->select('id',
                    'description',
					'amount',
					'rate',
					'interested',
                    'notes')
				->get();
			$order_interested = DB::table('gold_pawn_order_interested')
                ->whereRaw('deleted_at is null')
				->where('order_id', $interested->order_id)
				->where('id', '<>', $id)
				->orderBy('id')
                ->get();
            return ['interested'=>$interested, 'order'=>$order, 'customer'=>$customer, 'investor'=>$investor, 'detail'=>$detail, 'order_interested'=>$order_interested];
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
            $filename = 'DL_'.time();
            $parameter = [
                'id'=>$id,
                'logo'=>storage_path().'/app/'.CRUDBooster::getSetting('logo'), 
                'background'=>storage_path().'/app/'.CRUDBooster::getSetting('favicon'),
            ];
            $output = public_path().'/output_reports/'.$filename;
			$input = base_path().'/app/Reports/rpt_pawn_interested.jasper';
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
            $filename = 'DLT_'.time();
            $parameter = [
                'logo'=>storage_path().'/app/'.CRUDBooster::getSetting('logo'), 
                'background'=>storage_path().'/app/'.CRUDBooster::getSetting('favicon'),
            ];
            $output = public_path().'/output_reports/'.$filename;
			$input = base_path().'/app/Reports/rpt_pawn_interested_blank.jasper';
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
            $interested = DB::table($this->table)->where($this->primary_key, $id)->first();    
            Log::debug('$interested = ' . Json::encode($interested));
            if($interested){
				$order = DB::table('gold_pawn_orders')->where('id', $interested->order_id)->first();    
                $counter = DB::table('gold_counters')->where('id', $interested->counter_id)->first();    
				Log::debug('$order = ' . Json::encode($order));
				Log::debug('$counter = ' . Json::encode($counter));
				if($order){
					$investor = DB::table('gold_investors')->where('id', $order->investor_id)->first();    
					$liquidation_method = ''.$order->liquidation_method;
					Log::debug('$order->liquidation_method = ' . $liquidation_method);
					if($liquidation_method == "0"){
						Log::debug('$order->liquidation_method = ' . ($interested->amount - $order->amount));
						DB::table('gold_counters')->where('id', $counter->id)->update([
							'liquidation_amount' => $counter->liquidation_amount - $order->amount,
							'interested_amount' => $counter->interested_amount - ($interested->amount - $order->amount)
						]);

						DB::table('gold_investors')->where('id', $investor->id)->update([
							'balance' => $investor->balance - ($interested->amount - $order->amount)
						]);

						DB::table('gold_pawn_orders')->where('id', $order->id)->update([
							'interested_amount' => $order->interested_amount - ($interested->amount - $order->amount),
							'last_interested_at' => $interested->last_interested_at,
							'status' => 1,
							'liquidation_at' => null,
							'liquidation_by' => null,
							'liquidation_method' => null,
							'liquidation_notes' => null
						]);
					}else{
						DB::table('gold_counters')->where('id', $counter->id)->update([
							'interested_amount' => $counter->interested_amount - $interested->amount
						]);

						DB::table('gold_investors')->where('id', $investor->id)->update([
							'balance' => $investor->balance - $interested->amount
						]);

						if($liquidation_method == "1"){
							DB::table('gold_pawn_orders')->where('id', $order->id)->update([
								'interested_amount' => $order->interested_amount - $interested->amount,
								'last_interested_at' => $interested->last_interested_at,
								'status' => 1,
								'liquidation_at' => null,
								'liquidation_by' => null,
								'liquidation_method' => null,
								'liquidation_notes' => null
							]);
						}else{
							DB::table('gold_pawn_orders')->where('id', $order->id)->update([
								'interested_amount' => $order->interested_amount - $interested->amount,
								'last_interested_at' => $interested->last_interested_at
							]);
						}
					}	

					$user = DB::table('cms_users')->where('id', $counter->saler_id)->first();
					if($user){
						DB::table('cms_users')->where('id', $user->id)->update([
							'balance' => $user->balance - $interested->amount
						]);
					}
				}
            }
	    }

	    //By the way, you can still create your own method in here... :) 
	}