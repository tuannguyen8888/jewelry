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

	class AdminGoldPurchaseOrdersController extends CBExtendController {

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
			$this->table = "gold_purchase_orders";
            $this->is_search_form = true;
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Số đơn hàng","name"=>"order_no","width"=>"100"];
            $this->col[] = ["label"=>"Ngày đơn hàng","name"=>"order_date","callback_php"=>'date_time_format($row->order_date, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Mã khách hàng","name"=>"customer_id","join"=>"gold_customers,code"];
			$this->col[] = ["label"=>"Tên khách hàng","name"=>"customer_id","join"=>"gold_customers,name"];
			$this->col[] = ["label"=>"Nhân viên","name"=>"purchase_id","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Cửa hàng","name"=>"brand_id","join"=>"gold_brands,name"];
			# END COLUMNS DO NOT REMOVE THIS LINE

            // Nguen add new for search

            $this->search_form = [];
            if(CRUDBooster::myPrivilegeId() == 2) {
                $this->search_form[] = ["label" => "Khách hàng", "name" => "customer_id", "type" => "select2", "width" => "col-sm-6", 'datatable' => 'gold_customers,name','datatable_ajax'=>true, 'datatable_where' => 'deleted_at is null', 'datatable_format' => "code,' - ',name,' - ',IFNULL(phone,'')"];
            }else{
                $this->search_form[] = ["label" => "Khách hàng", "name" => "customer_id", "type" => "select2", "width" => "col-sm-6", 'datatable' => 'gold_customers,name', 'datatable_where' => 'deleted_at is null', 'datatable_format' => "code,' - ',name,' - ',IFNULL(phone,'')"];
            }
            //$this->search_form[] = ["label"=>"Xuống dòng", "name"=>"break_line", "type"=>"break_line"];
            $this->search_form[] = ["label"=>"Nhân viên", "name"=>"purchase_id","type"=>"select2","width"=>"col-sm-2", 'datatable'=>'cms_users,name', 'datatable_where'=>CRUDBooster::myPrivilegeId() == 2 ? 'id = '.CRUDBooster::myId() : 'id_cms_privileges = 2', 'datatable_format'=>"employee_code,' - ',name,' (',email,')'"];
            $this->search_form[] = ["label"=>"Từ ngày", "name"=>"order_date_from_date", "data_column"=>"order_date", "search_type"=>"between_from","type"=>"date","width"=>"col-sm-2"];
            $this->search_form[] = ["label"=>"Đến ngày", "name"=>"order_date_to_date", "data_column"=>"order_date", "search_type"=>"between_to","type"=>"date","width"=>"col-sm-2"];

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Số đơn hàng','name'=>'order_no','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
            $this->form[] = ['label'=>'Ngày đơn hàng','name'=>'order_date','type'=>'date','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10','help'=>'Số đơn hàng sẽ tự phát sinh khi bạn lưu','readonly'=>'true'];
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
            $this->addaction[] = ['label'=>'Hóa đơn','url'=>CRUDBooster::mainpath('print-invoice/[id]'),'icon'=>'fa fa-newspaper-o','color'=>'info', 'showIf'=>"[status] != 0"];

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
            $this->load_css[] = asset("vendor/crudbooster/assets/datetimepicker-master/jquery.datetimepicker.css");
            $this->load_css[] = asset("vendor/crudbooster/assets/select2/dist/css/select2.min.css");
	    }

        public function getAdd() {
            $data = [];
            $data['page_title'] = 'Tạo mới đơn hàng mua';
            $data += ['mode' => 'new'];
            $this->cbView('purchase_order_form', $data);
        }

        public function getEdit($id)
        {
            $data = [];
            $data['page_title'] = 'Sửa đơn hàng mua';
            $data += ['mode' => 'edit', 'resume_id' => $id];
            $this->cbView('purchase_order_form', $data);
        }

        public function getDetail($id)
        {
            $data = [];
            $data['page_title'] = 'Xem đơn hàng mua';
            $data += ['mode' => 'view', 'resume_id' => $id];
            $this->cbView('purchase_order_form', $data);
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
                $query->where('gold_purchase_orders.purchase_id', CRUDBooster::myId());
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
                $order_pays = $para['order_pays'];
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
						'purchase_amount'=>($counter['purchase_amount'] + ($new_order['amount'] - $new_order['fee']))
					]);
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
                    // get new order no
                    $last_order = DB::table('gold_purchase_orders as SO')
                        ->whereRaw('SO.deleted_at is null')
                        ->where('SO.order_date', '>=', $order_date->format('Y-m-d') . ' 00:00:00')
                        ->where('SO.order_date', '<=', $order_date->format('Y-m-d') . ' 23:59:59')
                        ->orderBy('SO.order_no', 'desc')
                        ->first();
                    $new_order_no = '';
                    if ($last_order) {
                        $old_no = intval(explode('-', $last_order->order_no)[1]);
                        $new_order_no = '000' . ($old_no + 1);
                        $new_order_no = substr($new_order_no, strlen($new_order_no) - 3, 3);
                        $new_order_no = 'MH' . $order_date->format('ymd') . '-' . $new_order_no;
                    } else {
                        $new_order_no = 'MH' . $order_date->format('ymd') . '-001';
                    }
                    $new_order['order_no'] = $new_order_no;
					$new_order['created_by'] = CRUDBooster::myId();
					$new_order['brand_id'] = CRUDBooster::myBrand();
                    unset($new_order['id']);
                    $order_id = DB::table('gold_purchase_orders')->insertGetId($new_order);
                    Log::debug('$order_id = ' . $order_id);
                }

				$q10 = 0;
				$new_order_pays = [];
				DB::table('gold_purchase_order_details')->where('order_id', $order_id)->delete();
                if ($order_pays && count($order_pays)) {
                    foreach ($order_pays as $pay) {
                        if($pay['description']){
							$q10 += $pay['q10'];
                            $new_pay = [
                                'order_id' => $order_id,
                                'description' => $pay['description'],
                                'product_type_id' => $pay['product_type_id'],
                                'total_weight' => $pay['total_weight'],
                                'gem_weight' => $pay['gem_weight'],
                                'abate_weight' => $pay['abate_weight'],
								'gold_weight' => $pay['gold_weight'],
								'age' => $pay['age'],
								'q10' => $pay['q10'],
                                'price' => $pay['price'],
                                'amount' => $pay['amount'],
                                'notes' => $pay['notes'],
                                'created_by' => CRUDBooster::myId()
                            ];
                            array_push($new_order_pays, $new_pay);
                        }
                    }
                    Log::debug('$new_order_pays = ' . Json::encode($new_order_pays));
                    DB::table('gold_purchase_order_details')->insert($new_order_pays);
				}
				
				if($new_order['status'] == 1){
					$user = DB::table('cms_users')->where('id', $counter['saler_id'])->first();
					if($user){
						DB::table('cms_users')->where('id', $user->id)->update([
							'balance' => $user->balance - ($new_order['amount'] - $new_order['fee']),
							'q10' => $user->q10 + $q10
						]);
					}
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
            $order = DB::table($this->table)->where('id', $order_id)->first();
            $customer = DB::table('gold_customers')->where('id', $order->customer_id)->first();
            $order_pays = DB::table('gold_purchase_order_details as SOP')
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
					'SOP.age',
					'SOP.q10',
                    'SOP.price',
                    'SOP.amount',
                    'SOP.notes')
                ->get();
            return ['order'=>$order, 'customer'=>$customer, 'order_pays'=>$order_pays];
        }
        private function updateOrderHeader($update_order) {
            if( $update_order['id'] && intval($update_order['id']) > 0) // update order
            {
                $update_order['updated_at'] = date('Y-m-d H:i:s');
                $update_order['updated_by'] = CRUDBooster::myId();
                unset($update_order['id']);
                unset($update_order['created_at']);
                unset($update_order['created_by']);
                DB::table('gold_purchase_orders')->where('id', $order_id)->update($update_order);
            }
            return $order_id;
        }

        public function getPrintInvoice($id) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'MH_'.time();
            $parameter = [
				'id' => $id,
				'logo'=>storage_path().'/app/'.CRUDBooster::getSetting('logo'), 
                'background'=>storage_path().'/app/'.CRUDBooster::getSetting('favicon'),
			];
            $output = public_path().'/output_reports/'.$filename;
			$input = base_path().'/app/Reports/rpt_po_invoice.jasper';
			// Log::debug('$input = ' . $input);
            // Log::debug('$output = ' . $output);
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

		public function getPrintInvoiceBlank() {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'MHB_'.time();
			$parameter = [
                'logo'=>storage_path().'/app/'.CRUDBooster::getSetting('logo'), 
                'background'=>storage_path().'/app/'.CRUDBooster::getSetting('favicon'),
            ];
            $output = public_path().'/output_reports/'.$filename;
			$input = base_path().'/app/Reports/rpt_po_invoice_blank.jasper';
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
            $order = DB::table($this->table)->where('id', $id)->first();    
            // Log::debug('$order = ' . Json::encode($order));
            if($order && $order->status == 1){
                $counter = DB::table('gold_counters')->where('id', $order->counter_id)->first();    
                // Log::debug('$counter = ' . Json::encode($counter));
                if($counter){
                    DB::table('gold_counters')->where('id', $counter->id)->update([
                        'purchase_amount' => $counter->purchase_amount - ($order->amount - $order->fee)
					]);

					$q10 = DB::table('gold_purchase_order_details')->whereRaw('deleted_at is null')
						->where('order_id', $order->id)->select(DB::Raw('SUM(q10) AS q10'))->first();
					$user = DB::table('cms_users')->where('id', $counter->saler_id)->first();
					if($user){
						DB::table('cms_users')->where('id', $user->id)->update([
							'balance' => $user->balance + ($order->amount - $order->fee),
							'q10' => $user->q10 - ($q10->q10 ? $q10->q10 : 0)
						]);
					}
				}
            }
	    }

	    //By the way, you can still create your own method in here... :) 
	}