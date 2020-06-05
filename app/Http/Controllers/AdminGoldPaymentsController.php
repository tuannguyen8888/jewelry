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

	class AdminGoldPaymentsController extends CBExtendController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "order_no";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon_text";
			$this->button_add = true;
			$this->button_edit = true;
            $this->button_delete = true;				
			$this->button_detail = true;
			$this->button_show = false;
			$this->button_filter = false;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "gold_vouchers";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Số phiếu","name"=>"order_no"];
			$this->col[] = ["label"=>"T/g chi","name"=>"order_date","callback_php"=>'date_time_format($row->order_date, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Trạng thái","name"=>"status","callback_php"=>'get_input_status($row->status);'];
			$this->col[] = ["label"=>"Đối tượng","name"=>"object_id","join"=>"gold_suppliers,name"];
			$this->col[] = ["label"=>"Số tiền","name"=>"out_amount","callback_php"=>'number_format($row->out_amount)'];
			$this->col[] = ["label"=>"Hình thức chi","name"=>"method","callback_php"=>'get_payment_method($row->method);'];
			$this->col[] = ["label"=>"Nội dung","name"=>"notes"];
			$this->col[] = ["label"=>"Cửa hàng","name"=>"brand_id","join"=>"gold_brands,name"];
			$this->col[] = ["label"=>"Người tạo","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g tạo","name"=>"created_at","callback_php"=>'date_time_format($row->created_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Người sửa","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g sửa","name"=>"updated_at","callback_php"=>'date_time_format($row->updated_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			# END COLUMNS DO NOT REMOVE THIS LINE

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
            $this->addaction[] = ['label'=>'Phiếu chi','url'=>CRUDBooster::mainpath('print-order/[id]'),'icon'=>'fa fa-print','color'=>'info', 'showIf'=>"[status] != 0"];

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
            $this->load_css[] = asset("vendor/crudbooster/assets/datetimepicker-master/jquery.datetimepicker.css");
            $this->load_css[] = asset("vendor/crudbooster/assets/select2/dist/css/select2.min.css");
	    }

		public function getAdd() {
			$data = [];
			$data['page_title'] = 'Tạo mới phiếu chi';
			$data += ['mode' => 'new'];
			$this->cbView('payments_form', $data);
        }

        public function getEdit($id)
        {
            $data = [];
            $data['page_title'] = 'Sửa phiếu chi';
            $data += ['mode' => 'edit', 'resume_id' => $id];
            $this->cbView('payments_form', $data);
        }

        public function getDetail($id)
        {
            $data = [];
            $data['page_title'] = 'Xem phiếu chi';
            $data += ['mode' => 'view', 'resume_id' => $id];
            $this->cbView('payments_form', $data);
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
			$query->where('gold_vouchers.order_type', 1);
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
			if($order->object_type == 0){
				$object = DB::table('gold_customers')->where('id', $order->object_id)->select('code', 'name')->first();
			}else if($order->object_type == 1){
				$object = DB::table('gold_suppliers')->where('id', $order->object_id)->select('code', 'name')->first();
			}else if($order->object_type == 2){
				$object = DB::table('gold_investors')->where('id', $order->object_id)->select('code', 'name')->first();
			}else{
				$object = DB::table('cms_users')->where('id', $order->object_id)->select('employee_code as code', 'name')->first();
			}
            return['order'=>$order, 'object'=>$object];
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
				// Log::debug('$order = ' . Json::encode($order));

				$order['status'] = 1;
				$order['amount'] = -$order['out_amount'];
				$order['order_type'] = 1;
                if($order['id'] && intval($order['id']) > 0) // update order
                {
                    $id = intval($order['id']);
                    $order_no = $order['order_no'];
                    $this->updateOrder($order);
                }else{
					if(intval($order['is_balance']) == 1){
						$object_type = intval($order['object_type']);
						if($object_type == 0){
							$customer = DB::table('gold_customers')->where('id', $order['object_id'])->first();
							if($customer){
								DB::table('gold_customers')->where('id', $customer->id)->update([
									'balance' => $customer->balance + $order['out_amount'], 
									'updated_at' => date('Y-m-d H:i:s'), 
									'updated_by' => CRUDBooster::myId()
								]);
							}
						}else if($object_type == 1){
							$supplier = DB::table('gold_suppliers')->where('id', $order['object_id'])->first();
							if($supplier){
								DB::table('gold_suppliers')->where('id', $supplier->id)->update([
									'balance' => $supplier->balance - $order['out_amount'], 
									'updated_at' => date('Y-m-d H:i:s'), 
									'updated_by' => CRUDBooster::myId()
								]);
							}
						}else if($object_type == 2){
							$investor = DB::table('gold_investors')->where('id', $order['object_id'])->first();
							if($investor){
								DB::table('gold_investors')->where('id', $investor->id)->update([
									'balance' => $investor->balance - $order['out_amount'], 
									'updated_at' => date('Y-m-d H:i:s'), 
									'updated_by' => CRUDBooster::myId()
								]);
							}
						}else{
							$user = DB::table('cms_users')->where('id', $order['object_id'])->first();
							if($user){
								DB::table('cms_users')->where('id', $user->id)->update([
									'balance' => $user->balance + $order['out_amount'], 
									'updated_at' => date('Y-m-d H:i:s')
								]);

								$counter = DB::table('gold_counters')->whereRaw('deleted_at is null AND closed_at is null')
									->where('saler_id', $user->id)->first();
								if($counter){
									$order['counter_id'] = $counter->id;
									DB::table('gold_counters')->where('id', $counter->id)->update([
										'advance_amount' => $counter->advance_amount + $order['out_amount']
									]);
								}
							}
						}
					}

					$order_date_str = $order['order_date'];
					$order_date = DateTime::createFromFormat('Y-m-d H:i:s', $order_date_str);
					$order_no = 'PC' . $order_date->format('ymd');
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

		public function getSearchObject(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
			$para = Request::all();
			$object_type = intval($para['object_type']);
            $object_code = $para['object_code'];

			// Log::debug('$para = '.json_encode($para));
			if($object_type == 0){
				$object = DB::table('gold_customers')->whereRaw('deleted_at is null')->where('code', $object_code)
					->select('id', 'name', 'balance')->first();
			}else if($object_type == 1){
				$object = DB::table('gold_suppliers')->whereRaw('deleted_at is null')->where('code', $object_code)
                ->select('id', 'name', 'balance')->first();
			}else if($object_type == 2){
				$object = DB::table('gold_investors')->whereRaw('deleted_at is null')->where('code', $object_code)
                ->select('id', 'name', 'balance')->first();
			}else{
				$object = DB::table('cms_users')->whereRaw('status = 0')->where('employee_code', $object_code)
                ->select('id', 'name', 'balance')->first();
			}
			return ['object'=>$object];
        }

        public function getPrintOrder($id) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'PC_'.time();
            $parameter = [
				'id'=>$id,
				'user'=>CRUDBooster::myName(),
				'logo'=>storage_path().'/app/uploads/logo.png',
                'background'=>storage_path().'/app/uploads/favicon.png'
			];
            $input = base_path().'/app/Reports/rpt_payments.jasper';
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
            $filename = 'PCT_'.time();
            $parameter = [
                'logo'=>storage_path().'/app/uploads/logo.png',
                'background'=>storage_path().'/app/uploads/favicon.png'
            ];
            $output = public_path().'/output_reports/'.$filename;
            $input = base_path().'/app/Reports/rpt_payments_blank.jasper';
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
            if($order && $order->status == 1 && $order->is_balance == 1){
				if($order->object_type == 0){
					$customer = DB::table('gold_customers')->where('id', $order->object_id)->first();
					if($customer){
						DB::table('gold_customers')->where('id', $customer->id)->update([
							'balance' => $customer->balance - $order->out_amount, 
							'updated_at' => date('Y-m-d H:i:s'), 
							'updated_by' => CRUDBooster::myId()
						]);
					}
				}else if($order->object_type == 1){
					$supplier = DB::table('gold_suppliers')->where('id', $order->object_id)->first();
					if($supplier){
						DB::table('gold_suppliers')->where('id', $supplier->id)->update([
							'balance' => $supplier->balance + $order->out_amount, 
							'updated_at' => date('Y-m-d H:i:s'), 
							'updated_by' => CRUDBooster::myId()
						]);
					}
				}else if($order->object_type == 2){
					$investor = DB::table('gold_investors')->where('id', $order->object_id)->first();
					if($investor){
						DB::table('gold_investors')->where('id', $investor->id)->update([
							'balance' => $investor->balance + $order->out_amount, 
							'updated_at' => date('Y-m-d H:i:s'), 
							'updated_by' => CRUDBooster::myId()
						]);
					}
				}else{
					$user = DB::table('cms_users')->where('id', $order->object_id)->first();
					if($user){
						DB::table('cms_users')->where('id', $user->id)->update([
							'balance' => $user->balance - $order->out_amount, 
							'updated_at' => date('Y-m-d H:i:s')
						]);
					}

					$counter = DB::table('gold_counters')->where('id', $order->counter_id)->first();
					if($counter){
						DB::table('gold_counters')->where('id', $counter->id)->update([
							'advance_amount' => $counter->advance_amount - $order->out_amount
						]);
					}
				}
            }
	    }

	    //By the way, you can still create your own method in here... :) 
	}