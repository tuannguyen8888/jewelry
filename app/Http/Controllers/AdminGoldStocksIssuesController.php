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

	class AdminGoldStocksIssuesController extends CBExtendController {

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
			$this->table = "gold_stocks_issues";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Số phiếu","name"=>"order_no"];
			$this->col[] = ["label"=>"T/g xuất","name"=>"order_date","callback_php"=>'date_time_format($row->order_date, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"NCC","name"=>"supplier_id","join"=>"gold_suppliers,name"];
			$this->col[] = ["label"=>"Trạng thái","name"=>"status","callback_php"=>'get_input_status($row->status);'];
			$this->col[] = ["label"=>"Lý do","name"=>"notes"];
			$this->col[] = ["label"=>"Cửa hàng","name"=>"brand_id","join"=>"gold_brands,name"];
			$this->col[] = ["label"=>"Người tạo","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g tạo","name"=>"created_at","callback_php"=>'date_time_format($row->created_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Người sửa","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g sửa","name"=>"updated_at","callback_php"=>'date_time_format($row->updated_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Số phiếu','name'=>'order_no','type'=>'text','validation'=>'required|min:1|max:20','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'T/g kiểm kê','name'=>'order_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Kho','name'=>'supplier_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'gold_suppliers,name'];
			$this->form[] = ['label'=>'Lý do','name'=>'notes','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
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
            $this->addaction[] = ['label'=>'Phiếu xuất','url'=>CRUDBooster::mainpath('print-order/[id]'),'icon'=>'fa fa-print','color'=>'info', 'showIf'=>"[status] != 0"];

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
            $para = Request::all();
			$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			$data = [];
			$data['page_title'] = 'Tạo mới phiếu xuất';
			$data += ['mode' => 'new', 'stock_ids' => $user->stock_id];
			$this->cbView('stock_issues_form', $data);
        }

        public function getEdit($id)
        {
            $para = Request::all();
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $data = [];
            $data['page_title'] = 'Sửa phiếu xuất';
            $data += ['mode' => 'edit', 'stock_ids' => $user->stock_id, 'resume_id' => $id];
            $this->cbView('stock_issues_form', $data);
        }

        public function getDetail($id)
        {
            $para = Request::all();
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $data = [];
            $data['page_title'] = 'Xem phiếu xuất';
            $data += ['mode' => 'view', 'stock_ids' => $user->stock_id, 'resume_id' => $id];
            $this->cbView('stock_issues_form', $data);
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
            $supplier = DB::table('gold_suppliers')->where('id', $order->supplier_id)->first();
			$details = DB::table('gold_stocks_issue_detail as D')
				->leftJoin('gold_items as I', 'D.item_id', '=', 'I.id')
                ->leftJoin('gold_products as P', 'I.product_id', '=', 'P.id')
                ->leftJoin('gold_product_types as PT', 'I.product_type_id', '=', 'PT.id')
                ->whereRaw('D.deleted_at is null')
                ->where('D.order_id', $id)
				->select('D.id',
					'D.item_id',
                    'I.bar_code',
                    'P.product_code',
                    'P.product_name',
                    'I.total_weight',
                    'I.gem_weight',
					'I.gold_weight',
					'D.age',
					'D.q10',
                    'D.fee',
                    'D.stock_id',
                    'I.product_type_id',
					'PT.name as product_type_name',
					'D.notes')
                ->get();
            return ['order'=>$order, 'supplier'=>$supplier, 'details'=>$details];
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

        public function postUpdateOrder() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
				$order = $para['order'];
				$details = $para['details'];
				
				$this->updateOrder($order);

				if($details && count($details)){
					$q10 = 0;
					$fee = 0;
					foreach ($details as $detail) {
						$q10 += $detail['q10'];
						$fee += $detail['fee'];
						DB::table('gold_items')->where('id', $detail['item_id'])->update([
							'qty' => 0, 
							'status' => 3, 
							'notes' => 'Xuất trả số phiếu [' . $order['order_no'] . ']', 
							'updated_at' => date('Y-m-d H:i:s'), 
							'updated_by' => CRUDBooster::myId()
						]);
					}
					$supplier = DB::table('gold_suppliers')->where('id', $order['supplier_id'])->first();
					if($supplier){
						DB::table('gold_suppliers')->where('id', $supplier->id)->update([
							'q10' => $supplier->q10 - $q10, 
							'balance' => $supplier->balance - $fee, 
							'updated_at' => date('Y-m-d H:i:s'), 
							'updated_by' => CRUDBooster::myId()
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
            return response()->json(['result'=>true]);
        }

        public function postAddNewItem() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            
            $detail_id = null;
            $id = null;
            $order_no = '';

            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
                $order = $para['order'];
				$item = $para['item'];
				Log::debug('$order = ' . Json::encode($order));

                if( $order['id'] && intval($order['id']) > 0) // update order
                {
                    $id = intval($order['id']);
                    $order_no = $order['order_no'];
                    $this->updateOrder($order);
                }else{
					$order_date_str = $order['order_date'];
					$order_date = DateTime::createFromFormat('Y-m-d H:i:s', $order_date_str);
					$order_no = 'PX' . $order_date->format('ymd');
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

				$detail['order_id'] = $id;
				$detail['id'] = $item['id'];
                $detail['item_id'] = $item['item_id'];
				$detail['stock_id'] = $item['stock_id'];
				$detail['age'] = $item['age'];
				$detail['q10'] = $item['q10'];
				$detail['fee'] = $item['fee'];
				$detail['notes'] = $item['notes'];
				
                if( $detail['id'] && intval($detail['id']) > 0) // update order
                {
					$detail_id = intval($detail['id']);
					$detail['updated_at'] = date('Y-m-d H:i:s');
                	$detail['updated_by'] = CRUDBooster::myId();
					unset($detail['id']);
					// Log::debug('Update gold_stocks_issue_detail = ' . Json::encode($detail));
					DB::table('gold_stocks_issue_detail')->where('id', $detail_id)->update($detail);
                }else{
                    $detail['created_by'] = CRUDBooster::myId();
                    // Log::debug('Add gold_items = ' . Json::encode($item));
                    $detail_id = DB::table('gold_stocks_issue_detail')->insertGetId($detail);
                }
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            // Log::debug('$total = ' . Json::encode($total));
            return response()->json(['id'=>$id, 'order_no'=>$order_no, 'detail_id'=>$detail_id]);
        }

        public function postRemoveItem() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
                $detail_id = intval($para['detail_id']);

				DB::table('gold_stocks_issue_detail')->where('id', $detail_id)->delete();
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['result'=>true]);
		}

		public function postUpdateDetail() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
				$detail = $para['detail'];

				$id = intval($detail['id']);
                $detail['updated_at'] = date('Y-m-d H:i:s');
                $detail['updated_by'] = CRUDBooster::myId();
                unset($detail['id']);
				DB::table('gold_stocks_issue_detail')->where('id', $id)->update($detail);
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['result'=>true]);
        }

        public function getSearchItem(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $para = Request::all();
			$bar_code = $para['bar_code'];
			
            $item = DB::table('gold_items as I')
                ->leftJoin('gold_products as P', 'I.product_id', '=', 'P.id')
				->leftJoin('gold_product_types as PT', 'P.product_type_id', '=', 'PT.id')
				->leftJoin('gold_stocks as S', 'I.stock_id', '=', 'S.id')
				->leftJoin('gold_suppliers as SU', 'I.supplier_id', '=', 'SU.id')
				->whereRaw('I.deleted_at is null')
                ->where('I.bar_code', ''.$bar_code)
                ->select('I.id AS item_id',
                    'I.bar_code',
                    'P.product_code',
                    'P.product_name',
                    'I.total_weight',
                    'I.gem_weight',
					'I.gold_weight',
					'I.age',
					'I.q10',
                    'I.fund_fee AS fee',
					'I.qty',
					'I.stock_id',
					'S.name as stock_name',
					'I.supplier_id',
                    'SU.name as supplier_name',
                    'P.product_type_id',
                    'PT.name as product_type_name')
                ->first();
            return ['item'=>$item];
        }
		
        public function getPrintOrder($id) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'XT_'.time();
            $parameter = [
                'id'=>$id,
                'logo'=>storage_path().'/app/uploads/logo.png'
			];
            $input = base_path().'/app/Reports/rpt_issues.jasper';
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
            $order = DB::table('gold_stocks_issues')->where('id', $id)->first();    
            // Log::debug('$order = ' . Json::encode($order));
            if($order && $order->status == 1){
				$details = DB::table('gold_stocks_issue_detail')->whereRaw('deleted_at is null')->where('order_id', $order->id)->get();
				if($details && count($details)){
					foreach ($details as $detail) {
						DB::table('gold_items')->where('id', $detail->item_id)->update([
							'qty' => 1, 
							'status' => 1, 
							'notes' => 'Hủy phiếu xuất [' . $order->order_no . ']'
						]);

						$supplier = DB::table('gold_suppliers')->where('id', $order->supplier_id)->first();
						if($supplier){
							DB::table('gold_suppliers')->where('id', $supplier->id)->update([
								'q10' => $supplier->q10 + $detail->q10, 
								'balance' => $supplier->balance + $detail->fee
							]);
						}
					}
				}
            }
	    }

	    //By the way, you can still create your own method in here... :) 
	}