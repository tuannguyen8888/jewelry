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
    // use lepiaf\SerialPort\SerialPort;
    // use lepiaf\SerialPort\Parser\SeparatorParser;
    // use lepiaf\SerialPort\Configure\TTYConfigure;

    use Noikos\LaravelWeigh\Facades\LaravelWeigh as LaravelWeigh;
    use Noikos\LaravelPrinter\Facades\LaravelPrinter as LaravelPrinter;
    use Noikos\LaravelPrinter\Facades\LaravelLabelPrinter as LaravelLabelPrinter;

	class AdminGoldStocksReceivedController extends CBExtendController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "received_no";
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
			$this->table = "gold_stocks_received";
            $this->is_search_form = true;
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Số phiếu","name"=>"received_no"];
			$this->col[] = ["label"=>"Ngày nhập","name"=>"received_date","callback_php"=>'date_time_format($row->received_date, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Mã NCC","name"=>"supplier_id","join"=>"gold_suppliers,code"];
			$this->col[] = ["label"=>"Nhà cung cấp","name"=>"supplier_id","join"=>"gold_suppliers,name"];
			$this->col[] = ["label"=>"Kho","name"=>"stock_id","join"=>"gold_stocks,name"];
            $this->col[] = ["label"=>"Ghi chú","name"=>"notes"];
            $this->col[] = ["label"=>"Cửa hàng","name"=>"brand_id","join"=>"gold_brands,name"];
			$this->col[] = ["label"=>"Trạng thái","name"=>"status","callback_php"=>'get_input_status($row->status);'];
			$this->col[] = ["label"=>"Người tạo","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g tạo","name"=>"created_at","callback_php"=>'date_time_format($row->created_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Người sửa","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g sửa","name"=>"updated_at","callback_php"=>'date_time_format($row->updated_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			# END COLUMNS DO NOT REMOVE THIS LINE
            $this->search_form = [];
            $this->search_form[] = ["label"=>"Từ ngày", "name"=>"received_date_from_date", "data_column"=>"received_date", "search_type"=>"between_from","type"=>"date","width"=>"col-sm-2"];
            $this->search_form[] = ["label"=>"Đến ngày", "name"=>"received_date_to_date", "data_column"=>"received_date", "search_type"=>"between_to","type"=>"date","width"=>"col-sm-2"];
            if(CRUDBooster::myPrivilegeId() == 1 || CRUDBooster::myPrivilegeId() == 4){
                $this->search_form[] = ["label" => "Cửa hàng", "name" => "brand_id", "data_column"=>$this->table.".brand_id", "search_type"=>"equals_raw", "type" => "select2", "width" => "col-sm-2", 'datatable' => 'gold_brands,name', 'datatable_where' => 'deleted_at is null'];
            }

            # START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Số phiếu','name'=>'received_no','type'=>'text','validation'=>'required|min:1|max:20','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Ngày nhập','name'=>'received_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Nhà chung cấp','name'=>'supplier_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'gold_suppliers,name'];
			$this->form[] = ['label'=>'Kho','name'=>'stock_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'gold_stocks,name'];
			$this->form[] = ['label'=>'Ghi chú','name'=>'notes','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
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
            $this->addaction[] = ['label'=>'Phiếu NK','url'=>CRUDBooster::mainpath('print-received/[id]'),'icon'=>'fa fa-file-text-o','color'=>'primary', 'showIf'=>"[status] != 0"];
            $this->addaction[] = ['label'=>'Bảng kê','url'=>CRUDBooster::mainpath('print-received-detail/[id]'),'icon'=>'glyphicon glyphicon-list-alt','color'=>'success', 'showIf'=>"[status] != 0"];

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
            $para = Request::all();
			$user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
			// Log::debug('Tạo mới phiếu nhập');
            // Log::debug('$user = ' . Json::encode($user));
            // Log::debug('stock_ids = ' . $user->stock_id);
			$data = [];
			$data['page_title'] = 'Tạo mới phiếu nhập';
			$data += ['mode' => 'new', 'stock_ids' => $user->stock_id];
			$this->cbView('stock_received_form', $data);
        }

        public function getEdit($id)
        {
            $para = Request::all();
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $data = [];
            $data['page_title'] = 'Sửa phiếu nhập';
            $data += ['mode' => 'edit', 'stock_ids' => $user->stock_id, 'resume_id' => $id];
            $this->cbView('stock_received_form', $data);
        }

        public function getDetail($id)
        {
            $para = Request::all();
            $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            $data = [];
            $data['page_title'] = 'Xem phiếu nhập';
            $data += ['mode' => 'view', 'stock_ids' => $user->stock_id, 'resume_id' => $id];
            $this->cbView('stock_received_form', $data);
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
	        $query->where('gold_stocks_received.order_type', 0);
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
		
        public function getResumeReceived(){
            $this->cbLoader();
            if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
            $para = Request::all();
            $received_id = $para['received_id'];

            $received = DB::table('gold_stocks_received')->where('id', $received_id)->first();
            $total = $this->totalReceived($received_id);
            $items = DB::table('gold_items as I')
                ->leftJoin('gold_products as P', 'I.product_id', '=', 'P.id')
                ->leftJoin('gold_product_types as PT', 'I.product_type_id', '=', 'PT.id')
                ->whereRaw('I.deleted_at is null')
                ->where('I.received_id', $received_id)
                ->select('I.id',
                    'I.bar_code',
                    'P.product_code',
                    'P.product_name',
                    'I.total_weight',
                    'I.gem_weight',
                    'I.gold_weight',
                    'I.retail_fee',
                    'I.whole_fee',
                    'I.fund_fee',
                    'I.discount',
                    'I.age',
                    'I.q10',
                    'I.product_type_id',
                    'PT.name as product_type_name')
                ->get();
            return ['received'=>$received, 'supplier'=>$supplier, 'stock'=>$stock, 'total'=>$total, 'items'=>$items];
        }

        private function totalReceived($id){
            $this->cbLoader();

            $total = DB::table('gold_items')
                ->whereRaw('deleted_at is null')
                ->where('received_id', $id)
                ->select(DB::raw('ROUND(IFNULL(SUM(total_weight), 0), 4) AS total_weight,
                    ROUND(IFNULL(SUM(gem_weight), 0), 4) AS gem_weight,
                    ROUND(IFNULL(SUM(gold_weight), 0), 4) AS gold_weight,
                    IFNULL(SUM(retail_fee), 0) AS retail_fee,
                    IFNULL(SUM(whole_fee), 0) AS whole_fee,
                    IFNULL(SUM(fund_fee), 0) AS fund_fee,
                    ROUND(IFNULL(SUM(q10), 0), 4) AS q10')
                )
                // ->groupBy('supplier_id','status')
                // ->havingRaw('balance > 0')
                ->first();
            // Log::debug('$total = ' . Json::encode($total));
            return $total;
        }

        private function updateReceivedHeader($update_received) {
            if( $update_received['id'] && intval($update_received['id']) > 0) // update order
            {
                $received_id = intval($update_received['id']);
                $update_received['updated_at'] = date('Y-m-d H:i:s');
                $update_received['updated_by'] = CRUDBooster::myId();
                unset($update_received['id']);
                unset($update_received['created_at']);
                unset($update_received['created_by']);
                DB::table('gold_stocks_received')->where('id', $received_id)->update($update_received);
                // Log::debug('$received_id = ' . $received_id);
            }
            return $received_id;
        }

        public function postUpdateReceivedHeader() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
                $update_received = $para['received'];
                $update_received['status'] = 1;
                $this->updateReceivedHeader($update_received);

                $supplier = DB::table('gold_suppliers')->where('id', $update_received['supplier_id'])->first();
                if($supplier){
                    $total = $this->totalReceived($update_received['id']);
                    DB::table('gold_suppliers')->where('id', $supplier->id)->update([
                        'q10' => $supplier->q10 + ($total->q10 ? $total->q10 : 0), 
                        'balance' => $supplier->balance + ($total->fund_fee ? $total->fund_fee : 0)
                    ]);
                }

                DB::table('gold_items')->where('received_id', $update_received['id'])->update([
                    'qty' => 1, 
                    'status' => 1, 
                    'stock_id' => $update_received['stock_id'], 
                    'updated_at' => date('Y-m-d H:i:s'), 
                    'updated_by' => CRUDBooster::myId()
                ]);
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
            
            $item_id = null;
            $received_id = null;
            $received_no = '';
            $bar_code = '';

            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
                $received = $para['received'];
                $item = $para['item'];

                if( $received['id'] && intval($received['id']) > 0) // update order
                {
                    $received_id = intval($received['id']);
                    $received_no = $received['order_no'];
                    $this->updateReceivedHeader($received);
                }else{
                    $received_date_str = $received['received_date'];
                    $received_date = DateTime::createFromFormat('Y-m-d', $received_date_str);
                    // get new order no
                    $last_order = DB::table('gold_stocks_received')
                        ->where('received_date', '>=', $received_date->format('Y-m-d') . ' 00:00:00')
                        ->where('received_date', '<=', $received_date->format('Y-m-d') . ' 23:59:59')
                        ->orderBy('received_no', 'desc')
                        ->first();
                    if($last_order) {
                        $old_no = intval(explode('-', $last_order->received_no)[1]);
                        $received_no = '000'.($old_no + 1);
                        $received_no = substr($received_no, strlen($received_no) - 3, 3);
                        $received_no = 'PN'.$received_date->format('ymd').'-'.$received_no;
                    }else{
                        $received_no = 'PN'.$received_date->format('ymd').'-001';
                    }
                    $received['received_no'] = $received_no;
                    $received['created_by'] = CRUDBooster::myId();
                    $received['brand_id'] = CRUDBooster::myBrand();
                    unset($received['id']);
                    // Log::debug('Add received = ' . Json::encode($received));
                    $received_id = DB::table('gold_stocks_received')->insertGetId($received);
                }

                $item['received_id'] = $received_id;
                $item['stock_id'] = $received['stock_id'];
                $item['supplier_id'] = $received['supplier_id'];
                if( $item['id'] && intval($item['id']) > 0) // update order
                {
                    $item_id = intval($item['id']);
                    $bar_code = $item['bar_code'];
                    // Log::debug('Update gold_items = ' . Json::encode($item));
                    DB::table('gold_items')->where('id', $item_id)->update($item);
                }else{
                    $item['created_by'] = CRUDBooster::myId();
                    unset($item['id']);
                    // Log::debug('Add gold_items = ' . Json::encode($item));
                    $item_id = DB::table('gold_items')->insertGetId($item);
                    $bar_code = ''.$item_id;
                    if(strlen($bar_code) < 6){
                        $bar_code = '000000'.$item_id;
                        $bar_code = substr($bar_code, strlen($bar_code) - 6, 6);
                    }
                    DB::table('gold_items')->where('id', $item_id)->update(['bar_code'=>$bar_code]);
                }
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            $total = $this->totalReceived($received_id);
            // Log::debug('$total = ' . Json::encode($total));
            return response()->json(['id'=>$received_id, 'received_no'=>$received_no, 'item_id'=>$item_id, 'total'=>$total, 'bar_code'=>$bar_code]);
            // return response()->json(['id'=>$received_id, 'received_no'=>$received_no, 'item_id'=>$item_id, 'total'=>$total]);
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
                $received_id = intval($para['received_id']);
                $item_id = intval($para['item_id']);

                if($received_id && $received_id > 0 && $item_id && $item_id > 0) {
                    DB::table('gold_items')->where('id', $item_id)->where('received_id', $received_id)->delete();
                }
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            $total = $this->totalReceived($received_id);
            return response()->json(['total'=>$total]);
        }

        public function getPrintReceived($id) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'PN_'.time();
            $parameter = [
                'id'=>$id,
                'logo'=>storage_path().'/app/uploads/logo.png'
            ];
            $input = base_path().'/app/Reports/rpt_received.jasper';
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
        
        public function getPrintReceivedDetail($id) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'CTPN-'.time();
            $parameter = [
                'id'=>$id,
                'logo'=>storage_path().'/app/uploads/logo.png'
            ];
            $input = base_path().'/app/Reports/rpt_received_detail.jasper';
            $output = public_path().'/output_reports/'.$filename;
            $jasper->process($input, $output, array('pdf'), $parameter, $database)->execute();

            while (!file_exists($output.'.pdf' )){
                sleep(1);
            }

            $file = File::get( $output.'.pdf' );
            unlink($output.'.pdf');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/pdf',
                    'Content-Disposition' => 'filename="'.$filename.'.pdf"'
                )
            );
        }

        public function getPrintTem($id)
        {
            Log::debug('getPrintTem');
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'Tem-'.time();
            $parameter = ['id'=>$id];
            $input = base_path().'/app/Reports/rpt_tem.jasper';
            $output = public_path() . '/output_reports/'.$filename;
            $jasper->process($input, $output, array('html'), $parameter, $database)->execute();

            while (!file_exists($output . '.html' )){
                sleep(1);
            }

            // Log::debug('$output = ' . $output);
//            $file = File::get( $output . '.html' );
            $html = file_get_contents($output . '.html');
            $html = str_replace('<style type="text/css">','<style type="text/css">@page{ margin-left: 0cm; margin-right: 0cm; margin-top: 0cm; margin-bottom: 0cm;}',$html);
            $html = str_replace('<tr><td width="50%">&nbsp;</td><td align="center">','<tr><td align="left">',$html);
            $html = str_replace('Tem-','/output_reports/Tem-',$html);
            $html = $html.'
                    <script>
                        function ready() {
                            window.print();
//                            window.open("", "_self", "").close();
                            window.onfocus = function () { 
                                setTimeout(function () { window.close(); }, 500); }
                        }  
                        window.onload = ready;
                    </script>';
            return Response::make($html, 200,
                array(
                    'Content-type' => 'text/html'
                )
            );
        }

        public function getDataDevice() {
            $data = 0;
            // $serialPort = new SerialPort(new SeparatorParser(), new TTYConfigure());
            // $serialPort->open("/dev/ttyACM0");
            // Log::debug('Open serial port = ' . Json::encode($serialPort));
            // while ($data = $serialPort->read()) {
            //     // echo $data."\n";
            //     Log::debug('$serialPort data = ' . $data);

            //     if ($data == "OK") {
            //         $serialPort->write("1\n");
            //         $serialPort->close();
            //     }
            // }
            return ['data'=>$data];
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
            $received = DB::table('gold_stocks_received')->where('id', $id)->first();    
            // Log::debug('$received = ' . Json::encode($received));
            if($received && $received->status == 1){
                $supplier = DB::table('gold_suppliers')->where('id', $received->supplier_id)->first();    
                // Log::debug('$supplier = ' . Json::encode($supplier));
                if($supplier){
                    $total = $this->totalReceived($id);
                    DB::table('gold_suppliers')->where('id', $supplier->id)->update([
                        'q10' => $supplier->q10 - $total->q10, 
                        'balance' => $supplier->balance - $total->fund_fee
                    ]);
                }
            }

            DB::table('gold_items')->where('received_id', $id)->update([
                'deleted_at'=>date('Y-m-d H:i:s'), 'deleted_by'=>CRUDBooster::myId()
            ]);
	    }

	    //By the way, you can still create your own method in here... :) 

	}