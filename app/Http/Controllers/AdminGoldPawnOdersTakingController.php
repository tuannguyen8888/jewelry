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

	class AdminGoldPawnOdersTakingController extends CBExtendController {

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
			$this->button_detail = false;
			$this->button_show = false;
			$this->button_filter = false;
			$this->button_import = false;
			$this->button_export = false;
			$this->table = "gold_pawn_oders_taking";
            $this->is_search_form = true;
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
            $this->col[] = ["label"=>"Số phiếu","name"=>"order_no"];
            $this->col[] = ["label"=>"T/g kiểm kê","name"=>"order_date","callback_php"=>'date_time_format($row->order_date, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
            $this->col[] = ["label"=>"Số lượng HĐ","name"=>DB::raw('(SELECT count(*) FROM gold_pawn_oders_taking_details as D where D.deleted_at is null and D.order_id = gold_pawn_oders_taking.id) as total_count'),"callback_php"=>'number_format($row->total_count)'];
            $this->col[] = ["label"=>"Tổng số tiền","name"=>DB::raw('(SELECT SUM(D.amount) FROM gold_pawn_oders_taking_details as D where D.deleted_at is null and D.order_id = gold_pawn_oders_taking.id) as total_amount'),"callback_php"=>'number_format($row->total_amount)'];
            $this->col[] = ["label"=>"Lý do","name"=>"notes"];
            $this->col[] = ["label"=>"Cửa hàng","name"=>"brand_id","join"=>"gold_brands,name"];
            $this->col[] = ["label"=>"Trạng thái","name"=>"status","callback_php"=>'get_input_status($row->status);'];
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
            $this->form[] = ['label'=>'T/g kiểm kê','name'=>'order_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-4'];
//            $this->form[] = ['label'=>'Kho','name'=>'stock_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'gold_stocks,name'];
            $this->form[] = ['label'=>'Lý do','name'=>'notes','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Order No","name"=>"order_no","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Order Date","name"=>"order_date","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Brand Id","name"=>"brand_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"brand,id"];
			//$this->form[] = ["label"=>"Status","name"=>"status","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Notes","name"=>"notes","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Created By","name"=>"created_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Updated By","name"=>"updated_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Deleted By","name"=>"deleted_by","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			# OLD END FORM

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
            $this->addaction[] = ['label'=>'Bảng kê','url'=>CRUDBooster::mainpath('print-order/[id]'),'icon'=>'fa fa-print','color'=>'info'];
            $this->addaction[] = ['label'=>'Bảng kê','url'=>CRUDBooster::mainpath('print-order-xlsx/[id]'),'icon'=>'fa fa-file-excel-o','color'=>'success'];


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
            // Log::debug('Tạo mới phiếu nhập');
            // Log::debug('$user = ' . Json::encode($user));
            // Log::debug('stock_ids = ' . $user->stock_id);
            $data = [];
            $data['page_title'] = 'Tạo mới biên bảng kiểm kê HĐ cầm';
            $data += ['mode' => 'new', 'brand_id' => $user->brand_id];
            $this->cbView('pawn_order_taking_form', $data);
        }

        public function getEdit($id)
        {
            $para = Request::all();
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $data = [];
            $data['page_title'] = 'Sửa biên bảng kiểm kê HĐ cầm';
            $data += ['mode' => 'edit', 'brand_id' => $user->brand_id, 'resume_id' => $id];
            $this->cbView('pawn_order_taking_form', $data);
        }

        public function getDetail($id)
        {
            $para = Request::all();
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $data = [];
            $data['page_title'] = 'Xem biên bảng kiểm kê HĐ cầm';
            $data += ['mode' => 'view', 'brand_id' => $user->brand_id, 'resume_id' => $id];
            $this->cbView('pawn_order_taking_form', $data);
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
                    $order_no = 'KKC' . $order_date->format('ymd');
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
                $detail['pawn_order_id'] = $item['pawn_order_id'];
                $detail['pawn_order_no'] = $item['pawn_order_no'];
                $detail['amount'] = $item['amount'];
                $detail['interested_amount'] = $item['interested_amount'];

                if( $detail['id'] && intval($detail['id']) > 0) // update order
                {
                    $detail_id = intval($detail['id']);
                    $detail['updated_at'] = date('Y-m-d H:i:s');
                    $detail['updated_by'] = CRUDBooster::myId();
                    unset($detail['id']);
                    // Log::debug('Update gold_pawn_oders_taking_details = ' . Json::encode($detail));
                    DB::table('gold_pawn_oders_taking_details')->where('id', $detail_id)->update($detail);
                }else{
                    $detail['created_by'] = CRUDBooster::myId();
                    // Log::debug('Add item = ' . Json::encode($item));
                    $detail_id = DB::table('gold_pawn_oders_taking_details')->insertGetId($detail);
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
        public function getResumeOrder(){
            $this->cbLoader();
            if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            $para = Request::all();
            $id = $para['id'];

            $order = DB::table($this->table)->where($this->primary_key, $id)->first();
            $details = DB::table('gold_pawn_oders_taking_details as D')
                ->leftJoin('gold_pawn_orders as PO', 'D.pawn_order_id', '=', 'PO.id')
                ->whereRaw('D.deleted_at is null')
                ->whereRaw('PO.deleted_at is null')
                ->where('D.order_id', $id)
                ->select('PO.id AS pawn_order_id',
                    'PO.order_no as pawn_order_no',
                    'PO.order_date as pawn_order_date',
                    'PO.due_date',
                    'PO.min_days',
                    'PO.status',
                    'PO.description',
                    'PO.amount',
                    'PO.interested_amount',
                    'PO.interest_reduced_amount',
                    'PO.brand_id')
                ->get();
            return ['order'=>$order, 'details'=>$details];
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

                DB::table('gold_pawn_oders_taking_details')->where('id', $detail_id)->delete();
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['result'=>true]);
        }

//        public function postUpdateItemNotes() {
//            $this->cbLoader();
//            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
//                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
//                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
//            }
//            DB::beginTransaction();
//            try {
//                $para = Request::all();
//                Log::debug('$para = ' . Json::encode($para));
//                $detail_id = intval($para['detail_id']);
//
//                DB::table('gold_pawn_oders_taking_details')->where('id', $detail_id)->update(['notes'=>$para['notes']]);
//            }
//            catch( \Exception $e){
//                DB::rollback();
//                Log::debug('PostAdd error $e = ' . Json::encode($e));
//                throw $e;
//            }
//            DB::commit();
//            return response()->json(['result'=>true]);
//        }

        public function getSearchItem(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $para = Request::all();
            $bar_code = $para['bar_code'];

            $item = DB::table('gold_pawn_orders as PO')
                ->whereRaw('PO.deleted_at is null')
//                ->whereRaw('PO.status = 1')
                ->where('PO.order_no', ''.$bar_code)
                ->select('PO.id AS pawn_order_id',
                    'PO.order_no as pawn_order_no',
                    'PO.order_date as pawn_order_date',
                    'PO.due_date',
                    'PO.min_days',
                    'PO.status',
                    'PO.description',
                    'PO.amount',
                    'PO.interested_amount',
                    'PO.interest_reduced_amount',
                    'PO.brand_id')
                ->first();
            return ['item'=>$item];
        }

        public function getPrintOrder($id) {
            Log::debug(CRUDBooster::getCurrentMethod() . ' $id = ' . $id);
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'KKC_'.time();
            $parameter = [
                'id'=>$id,
                'logo'=>storage_path().'/app/uploads/logo.png',
                'qr_code'=>storage_path().'/app/'.CRUDBooster::getSetting('qr_code'),
            ];
            $input = base_path().'/app/Reports/rpt_pawn_oders_taking.jasper';
            $output = public_path().'/output_reports/'.$filename;
            Log::debug(CRUDBooster::getCurrentMethod() . ' $input = ' . $input);
            Log::debug(CRUDBooster::getCurrentMethod() . ' $output = ' . $output);
            Log::debug('rpt_pawn_oders_taking.jasper cmd = '. $jasper->process($input, $output, array('pdf'), $parameter, $database)->output());
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

        public function getPrintOrderXlsx($id){
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'KKC-'.time();
            $parameter = ['id'=>$id];
            $input = base_path().'/app/Reports/rpt_pawn_oders_taking_xlsx.jasper';
            $output = public_path() . '/output_reports/' . $filename;
            Log::debug(CRUDBooster::getCurrentMethod() . ' $input = ' . $input);
            Log::debug(CRUDBooster::getCurrentMethod() . ' $output = ' . $output);
            Log::debug(CRUDBooster::getCurrentMethod() .' cmd = '. $jasper->process($input, $output, array('pdf'), $parameter, $database)->output());

            $jasper->process($input, $output, array('xlsx'), $parameter, $database)->execute();

            while (!file_exists($output . '.xlsx' )){
                sleep(1);
            }

            $file = File::get( $output . '.xlsx' );
            unlink($output . '.xlsx');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'filename="kiem-ke-hop-dong-cam.xlsx"'
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

	    }



	    //By the way, you can still create your own method in here... :) 


	}