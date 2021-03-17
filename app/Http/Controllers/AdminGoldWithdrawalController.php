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

	class AdminGoldWithdrawalController extends CBExtendController {

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
			$this->button_filter = false;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "gold_vouchers";
            $this->is_search_form = true;
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Số phiếu","name"=>"order_no"];
			$this->col[] = ["label"=>"T/g rút","name"=>"order_date","callback_php"=>'date_time_format($row->order_date, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Trạng thái","name"=>"status","callback_php"=>'get_input_status($row->status);'];
			$this->col[] = ["label"=>"Khách hàng","name"=>"object_id","join"=>"gold_customers,name"];
            $this->col[] = ["label"=>"Phone","name"=>"object_id","join"=>"gold_customers,phone"];
            $this->col[] = ["label"=>"Zalo phone","name"=>"object_id","join"=>"gold_customers,zalo_phone"];
			$this->col[] = ["label"=>"Loại thẻ","name"=>"card_type_id","join"=>"gold_bank_card,name"];
			$this->col[] = ["label"=>"Số tiền","name"=>"in_amount","callback_php"=>'number_format($row->in_amount)'];
			$this->col[] = ["label"=>"% phí NH","name"=>"bank_fee","callback_php"=>'number_format($row->bank_fee, 2)'];
			$this->col[] = ["label"=>"% phí DV","name"=>"fee","callback_php"=>'number_format($row->fee, 2)'];
			$this->col[] = ["label"=>"Nội dung","name"=>"notes"];
			$this->col[] = ["label"=>"Cửa hàng","name"=>"brand_id","join"=>"gold_brands,name"];
			$this->col[] = ["label"=>"Người tạo","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g tạo","name"=>"created_at","callback_php"=>'date_time_format($row->created_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Người sửa","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g sửa","name"=>"updated_at","callback_php"=>'date_time_format($row->updated_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			# END COLUMNS DO NOT REMOVE THIS LINE
            $this->search_form = [];
            $this->search_form[] = ["label"=>"Từ ngày", "name"=>"order_date_from_date", "data_column"=>"order_date", "search_type"=>"between_from","type"=>"date","width"=>"col-sm-2"];
            $this->search_form[] = ["label"=>"Đến ngày", "name"=>"order_date_to_date", "data_column"=>"order_date", "search_type"=>"between_to","type"=>"date","width"=>"col-sm-2"];
            if(CRUDBooster::myPrivilegeId() == 1 || CRUDBooster::myPrivilegeId() == 4){
                $this->search_form[] = ["label" => "Cửa hàng", "name" => "brand_id", "data_column"=>$this->table.".brand_id", "search_type"=>"equals_raw", "type" => "select2", "width" => "col-sm-2", 'datatable' => 'gold_brands,name', 'datatable_where' => 'deleted_at is null'];
            }

            # START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Số phiếu','name'=>'order_no','type'=>'text','validation'=>'required|min:1|max:20','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'T/g thu','name'=>'order_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Nội dung','name'=>'notes','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
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
            $this->addaction[] = ['label'=>'Phiếu rút tiền','url'=>CRUDBooster::mainpath('print-order/[id]'),'icon'=>'fa fa-print','color'=>'info', 'showIf'=>"[status] != 0"];

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
			$data['page_title'] = 'Tạo mới phiếu rút tiền';
			$data += ['mode' => 'new'];
			$this->cbView('withdrawal_form', $data);
        }

        public function getEdit($id)
        {
            $data = [];
            $data['page_title'] = 'Sửa phiếu rút tiền';
            $data += ['mode' => 'edit', 'resume_id' => $id];
            $this->cbView('withdrawal_form', $data);
        }

        public function getDetail($id)
        {
            $data = [];
            $data['page_title'] = 'Xem phiếu rút tiền';
            $data += ['mode' => 'view', 'resume_id' => $id];
            $this->cbView('withdrawal_form', $data);
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
			$query->where('gold_vouchers.order_type', 2);
			if(CRUDBooster::myPrivilegeId() == 2)// Nhân viên bán hàng
            {
//                $query->where('gold_vouchers.created_by', CRUDBooster::myId());
                $query->where('gold_vouchers.brand_id', CRUDBooster::myBrand());
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
	    public function hook_before_add(&$postdata) {        
	        //Your code here

		}
		
        public function getResumeOrder(){
            $this->cbLoader();
            if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            $para = Request::all();
            $id = $para['id'];

			$order = DB::table($this->table)->where($this->primary_key, $id)->first();
			$customer = DB::table('gold_customers')->where('id', $order->object_id)->first();
            return['order'=>$order, 'customer'=>$customer];
        }

        public function postAddSave() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            $id = null;
            $order_no = '';

            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
				$order = $para['order'];
				$new_customer = $para['customer'];

				$customer_id = $order['object_id'];
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
                    $order['object_id'] = $customer_id;
				}
				
				$amount = round($order['amount'] * (1 - ($order['bank_fee'] + $order['fee']) / 100));
				$counter = DB::table('gold_counters')->whereRaw('deleted_at is null AND closed_at is null')
					->where('saler_id', CRUDBooster::myId())->first();
				if($counter){
					$order['counter_id'] = $counter->id;
					DB::table('gold_counters')->where('id', $counter->id)->update([
						'withdrawal_in' => $counter->withdrawal_in + round($order['amount'] * (1 - $order['bank_fee'] / 100)),
						'withdrawal_out' => $counter->withdrawal_out + $amount
					]);

					$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
					if($user){
						DB::table('cms_users')->where('id', $user->id)->update(['balance' => $user->balance - $amount]);
					}
				}

				$order['status'] = 1;
				$order['in_amount'] = $order['amount'];
				$order['method'] = 1;
				$order['order_type'] = 2;

                if($order['id'] && intval($order['id']) > 0) // update order
                {
                    $id = intval($order['id']);
                    $order_no = $order['order_no'];
                    $this->updateOrder($order);
                }else{
					$order_date_str = $order['order_date'];
					$order_date = DateTime::createFromFormat('Y-m-d H:i:s', $order_date_str);
					$order_no = 'RT' . $order_date->format('ymd');
                    // get new order no
                    $last_order = DB::table($this->table)
						->where('order_date', '>=', $order_date->format('Y-m-d') . ' 00:00:00')
						->where('order_date', '<=', $order_date->format('Y-m-d') . ' 23:59:59')
						->where('order_no', 'like', $order_no . '%')
                        ->orderBy('order_no', 'desc')
                        ->first();
                    if($last_order) {
						$old_no = intval(explode('-', $last_order->order_no)[1]) + 1;
						if(strlen($old_no) < 3){
							$old_no = '000'.$old_no;
							$old_no = substr($old_no, strlen($old_no) - 3, 3);
						}
                        $order_no = $order_no.'-'.$old_no;
                    }else{
                        $order_no = $order_no.'-001';
                    }
                    $order['order_no'] = $order_no;
					$order['created_by'] = CRUDBooster::myId();
					$order['brand_id'] = CRUDBooster::myBrand();
                    unset($order['id']);
                    Log::debug('Add order = ' . Json::encode($order));
					$id = DB::table($this->table)->insertGetId($order);
                }
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            // Log::debug('$total = ' . Json::encode($total));
            return response()->json(['id'=>$id, 'order_no'=>$order_no]);
		}
		
		private function updateOrder($order) {
            if( $order['id'] && intval($order['id']) > 0) // update order
            {
                $id = intval($order['id']);
                $order['updated_at'] = date('Y-m-d H:i:s');
                $order['updated_by'] = CRUDBooster::myId();
                unset($order['id']);
                unset($order['created_at']);
                unset($order['created_by']);
                DB::table($this->table)->where($this->primary_key, $id)->update($order);
                // Log::debug('$id = ' . $id);
            }
            return $id;
        }

        public function getPrintOrder($id) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'RT_'.time();
            $parameter = [
				'id'=>$id,
				'user'=>CRUDBooster::myName(),
				'logo'=>storage_path().'/app/uploads/logo.png',
                'background'=>storage_path().'/app/uploads/favicon.png'
			];
            $input = base_path().'/app/Reports/rpt_withdrawal.jasper';
            $output = public_path().'/output_reports/'.$filename;
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
		
		public function getPrintOrderBlank(){
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'RTT_'.time();
            $parameter = [
                'logo'=>storage_path().'/app/uploads/logo.png',
                'background'=>storage_path().'/app/uploads/favicon.png'
            ];
            $output = public_path().'/output_reports/'.$filename;
            $input = base_path().'/app/Reports/rpt_withdrawal_blank.jasper';
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
            $order = DB::table('gold_vouchers')->where('id', $id)->first();    
            // Log::debug('$order = ' . Json::encode($order));
            if($order && $order->status == 1){
				$amount = round($order->amount * (1 - ($order->bank_fee + $order->fee) / 100));
				$counter = DB::table('gold_counters')->where('id', $order->counter_id)->first();
				if($counter){
					DB::table('gold_counters')->where('id', $counter->id)->update([
						'withdrawal_in' => $counter->withdrawal_in - round($order->amount * (1 - $order->bank_fee / 100)),
						'withdrawal_out' => $counter->withdrawal_out - $amount
					]);

					$user = DB::table('cms_users')->where('id', $counter->saler_id)->first();
					if($user){
						DB::table('cms_users')->where('id', $user->id)->update(['balance' => $user->balance + $amount]);
					}
				}
            }
	    }

	    //By the way, you can still create your own method in here... :) 
	}