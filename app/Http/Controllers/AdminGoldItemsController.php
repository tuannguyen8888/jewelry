<?php namespace App\Http\Controllers;

	use Psy\Util\Json;
	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use Enums;
	use Illuminate\Support\Facades\Cache;
	use Illuminate\Support\Facades\PDF;
	use Maatwebsite\Excel\Facades\Excel;
	use Illuminate\Support\Facades\Route;
	use JasperPHP\JasperPHP;
	use Schema;
	use CB;
	use DateTime;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\File;
    use Response;

	class AdminGoldItemsController extends CBExtendController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "bar_code";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon_text";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = true;
			$this->button_show = false;
			$this->button_filter = false;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "gold_items";
            $this->is_search_form = true;
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Số phiếu nhập","name"=>"received_id","join"=>"gold_stocks_received,received_no"];
			$this->col[] = ["label"=>"Ngày nhập","name"=>"received_id","join"=>"gold_stocks_received,received_date"];
            $this->col[] = ["label"=>"Mã vạch","name"=>"bar_code"];
            $this->col[] = ["label"=>"Trạng thái","name"=>"status","callback_php"=>'get_input_status($row->status);'];
            $this->col[] = ["label"=>"Mã SP","name"=>"product_id","join"=>"gold_products,product_code"];
			$this->col[] = ["label"=>"Sản phẩm","name"=>"product_id","join"=>"gold_products,product_name"];
			$this->col[] = ["label"=>"ĐVT","name"=>"product_unit_id","join"=>"gold_product_units,name"];
			$this->col[] = ["label"=>"KL tổng","name"=>"total_weight", "callback_php"=>'number_format($row->total_weight, 4)'];
			$this->col[] = ["label"=>"KL đá","name"=>"gem_weight", "callback_php"=>'number_format($row->gem_weight, 4)'];
			$this->col[] = ["label"=>"KL vàng","name"=>"gold_weight", "callback_php"=>'number_format($row->gold_weight, 4)'];
			$this->col[] = ["label"=>"Công bán","name"=>"retail_fee", "callback_php"=>'number_format($row->retail_fee)'];
			$this->col[] = ["label"=>"Công VIP","name"=>"whole_fee", "callback_php"=>'number_format($row->whole_fee)'];
			if(CRUDBooster::myPrivilegeId() != 2)// Nhân viên bán hàng
            {
                $this->col[] = ["label"=>"Công vốn","name"=>"fund_fee", "callback_php"=>'number_format($row->fund_fee)'];
			}
			$this->col[] = ["label"=>"Loại vàng","name"=>"product_type_id","join"=>"gold_product_types,name"];
            $this->col[] = ["label"=>"Loại SP","name"=>"product_category_id","join"=>"gold_product_categories,name"];
            $this->col[] = ["label"=>"Nhóm SP","name"=>"product_group_id","join"=>"gold_product_groups,name"];
            $this->col[] = ["label"=>"NCC","name"=>"supplier_id","join"=>"gold_suppliers,name"];
            $this->col[] = ["label"=>"Kho","name"=>"stock_id","join"=>"gold_stocks,name"];
            $this->col[] = ["label"=>"Ghi chú","name"=>"notes"];
			$this->col[] = ["label"=>"Người tạo","name"=>"created_by","join"=>"cms_users,name"];
            $this->col[] = ["label"=>"T/g tạo","name"=>"created_at","callback_php"=>'date_time_format($row->created_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
            $this->col[] = ["label"=>"Người sửa","name"=>"updated_by","join"=>"cms_users,name"];
            $this->col[] = ["label"=>"T/g sửa","name"=>"updated_at","callback_php"=>'date_time_format($row->updated_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			# END COLUMNS DO NOT REMOVE THIS LINE
            $this->search_form = [];
            $this->search_form[] = ["label"=>"Từ ngày", "name"=>"received_date_from_date", "data_column"=>"gold_stocks_received.received_date", "search_type"=>"between_from","type"=>"date","width"=>"col-sm-2"];
            $this->search_form[] = ["label"=>"Đến ngày", "name"=>"received_date_to_date", "data_column"=>"gold_stocks_received.received_date", "search_type"=>"between_to","type"=>"date","width"=>"col-sm-2"];

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Số phiếu nhập','name'=>'received_id','type'=>'select2','validation'=>'required|min:0','width'=>'col-sm-4','datatable'=>'gold_stocks_received,received_no'];
            $this->form[] = ['label'=>'Ngày nhập','name'=>'received_id','type'=>'select2','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-4','datatable'=>'gold_stocks_received,received_date'];
            $this->form[] = ['label'=>'Sản phẩm','name'=>'product_id','type'=>'select2','validation'=>'required|min:0','width'=>'col-sm-10','datatable'=>'gold_products,product_name'];
			$this->form[] = ['label'=>'ĐVT','name'=>'product_unit_id','type'=>'select2','validation'=>'required|min:0','width'=>'col-sm-4','datatable'=>'gold_product_units,name'];
			$this->form[] = ['label'=>'KL tổng','name'=>'total_weight','type'=>'text','validation'=>'required|numeric|min:0','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'KL đá','name'=>'gem_weight','type'=>'text','validation'=>'required|numeric|min:0','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'KL vàng','name'=>'gold_weight','type'=>'text','validation'=>'required|numeric|min:0','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Công bán','name'=>'retail_fee','type'=>'money','validation'=>'required|min:0','width'=>'col-sm-4'];
            $this->form[] = ['label'=>'Công VIP','name'=>'whole_fee','type'=>'money','validation'=>'required|min:0','width'=>'col-sm-4'];
            if(CRUDBooster::myPrivilegeId() != 2)// Nhân viên bán hàng
            {
                $this->form[] = ['label'=>'Công vốn','name'=>'fund_fee','type'=>'money','validation'=>'required|min:0','width'=>'col-sm-4'];
            }
            $this->form[] = ['label'=>'Hàm lượng vàng','name'=>'product_type_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-4','datatable'=>'gold_product_types,name'];
            $this->form[] = ['label'=>'Loại sản phẩm','name'=>'product_category_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-4','datatable'=>'gold_product_categories,name'];
            $this->form[] = ['label'=>'Nhóm sản phẩm','name'=>'product_group_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-4','datatable'=>'gold_product_groups,name'];
            $this->form[] = ['label'=>'Nhà sản xuất','name'=>'producer_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-4','datatable'=>'gold_producer,name'];
            $this->form[] = ['label'=>'Kho','name'=>'stock_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-4','datatable'=>'gold_stocks,name'];
			$this->form[] = ['label'=>'Ghi chú','name'=>'notes','type'=>'textarea','validation'=>'max:255','width'=>'col-sm-10'];
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
			$this->addaction[] = ['label'=>'In tem','url'=>CRUDBooster::mainpath('print-tem/[id]'),'icon'=>'fa fa-print','color'=>'info'];

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
	        $this->script_js = NULL;


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
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = "tr th, .button_action {white-space: nowrap;}
				tr td:last-child {vertical-align: middle !important;}";
	        
	        
	        
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
	        // if(CRUDBooster::myPrivilegeId() == 2)// Nhân viên bán hàng
            // {
            //     $user = DB::table('cms_users')->where('id', CRUDBooster::myId())->first();
            //     if($user && $user->stock_id) {
            //         $stock_ids = array_map('intval', explode(',', $user->stock_id));
            //         $query->whereIn('gold_products.stock_id', $stock_ids);
            //     } else {
            //         $query->where('gold_products.stock_id', -1);
            //     }
            // }
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
		
		public function getImportData() {
            set_time_limit(0);
            ini_set('memory_limit', '4294967296');
            $this->cbLoader();
            $data['page_menu']       = Route::getCurrentRoute()->getActionName();
            $module     = CRUDBooster::getCurrentModule();
            $data['page_title']      = 'Import dữ liệu: '.$module->name;

            if(Request::get('file') && !Request::get('import')) {
                $file = base64_decode(Request::get('file'));
                $file = storage_path('app/'.$file);
                $rows = [];

                Log::debug('chỉ load 2 dòng đầu để check column ');
                Excel::filter('chunk')->load($file)->chunk(2, function($results) use (&$rows) // chỉ load 2 dòng đầu để check column
                {
                    $rows = array_merge($rows,  $results->toArray());
                    return true; // return true để không load tiếp
                }, false);

                Log::debug('count($rows) = ');
                Log::debug(count($rows));
                Session::put('total_data_import',15000); // set đại, load file xong sẽ set lại
                $data['table_rows'] = []; /// $data['table_rows'] = $rows; chuyển qua load bằng ajax
                //Session::put('table_rows',$rows);
                Session::put('path_import_product',$file);
                //unlink($file);
                $data_import_column = array();
                foreach($rows as $value) {
                    $a = array();
//                    Log::debug('$rows = ',[Json::encode($rows)]);
                    foreach($value as $k=>$v) {
                        $a[] = $k;
                    }
                    if(count($a)) {
                        $data_import_column = $a;
                    }
                    break;
                }
                Log::debug('$data_import_column = ',$data_import_column);
                if(in_array('ma_hang', $data_import_column, true)
                    && in_array('ten_hang', $data_import_column, true)
                    && in_array('dvt', $data_import_column, true)
                    && in_array('kl_tong', $data_import_column, true)
                    && in_array('kl_da', $data_import_column, true)
                    && in_array('kl_vang', $data_import_column, true)
                    && in_array('cong_ban', $data_import_column, true)
                    && in_array('cong_vip', $data_import_column, true)
                    && in_array('cong_von', $data_import_column, true)
                    && in_array('loai_hang', $data_import_column, true)
                    && in_array('nhom_hang', $data_import_column, true)
                    && in_array('loai_vang', $data_import_column, true)
                    && in_array('nha_san_xuat', $data_import_column, true))
                {
//                    Log::debug('File dung dinh dang');
                    $table_columns = $data_import_column; 
                    $data['table_columns'] = $table_columns;
                    $data_import_column = ['Mã hàng', 'Tên hàng', 'ĐVT', 'LK Tổng', 'KL Đá', 'KL vàng', 'Công bán', 'Công VIP', 'Công vốn', 'Loại hàng', 'Nhóm hàng', 'Loại vàng', 'Nhà sản xuất'];
                    $data['data_import_column'] = $data_import_column;
                    Session::put('select_column',$table_columns);
                } else {
                    //File không đúng định dạng
//                    Log::debug('File KHONG dung dinh dang');
                    $message_all = [sprintf('File không đúng định dạng, vui lòng kiểm tra lại.','File')];
                    $res = redirect()->back()->with(['message'=>trans('crudbooster.alert_validation_error',['error'=>implode(', ',$message_all)]),'message_type'=>'warning'])->withInput();
                    Session::driver()->save();
                    $res->send();
                    exit();
                }
            } else {
                if (!(strpos( Request::server('HTTP_REFERER'), 'done-import') !== false || strpos(Request::server('HTTP_REFERER'), 'import-data') !== false)) {
                    Session::put('return_url',Request::server('HTTP_REFERER'));
                }
            }
            return view('import_products',$data);
        }
//        public function postLoadDataImport(){
//            set_time_limit(0);
//            ini_set('memory_limit', '4294967296');
//            $para = Request::json()->all();
////            $data = Session::get('table_rows');
//            $file = Session::get('path_import_product');
//            $total_data_import = Session::get('total_data_import');
//            $start = intval($para['dataTableParameters']['start']);
//            $length = intval($para['dataTableParameters']['length']);
//            Log::debug('$start = '.$start.'; $length = '.$length);
//            $dataTableData = array();
//            $dataTableData += ['draw' => $para['dataTableParameters']['draw']];
//            $dataTableData += ['recordsTotal' => $total_data_import];
//            $dataTableData += ['recordsFiltered' => $dataTableData['recordsTotal']];
//            $select_data = array();
//            $i = $start;
//            if($length == -1){
////                $select_data = $data;
//                Excel::filter('chunk')->load($file)->chunk(1000, function($results) use (&$select_data) {
//                    $select_data = array_merge($select_data,  $results->toArray());
//                }, false);
//            }else{
////                while ($i < $start + $length && $i < $dataTableData['recordsTotal']) {
////                    //Log::debug('$data['.$i.'] = '.Json::encode($data[$i]));
////                    array_push($select_data, $data[$i]);
////                    $i += 1;
////                }
//                $select_data = Excel::load($file, function($results) use (&$select_data) {
//                    $results->take($start - 1)->limit($length);
//                })->get();
//
//                Log::debug('$select_data = '.Json::encode($select_data));
//            }
//            $dataTableData += ['data'=> $select_data];
//            return response()->json($dataTableData);
//        }

        public function postDoneImport() {
            set_time_limit(0);
            ini_set('memory_limit', '4294967296');
            $this->cbLoader();
            $data['page_menu']       = Route::getCurrentRoute()->getActionName();
            $module     = CRUDBooster::getCurrentModule();
            $data['page_title']      = trans('crudbooster.import_page_title',['module'=>$module->name]);
            //Session::put('select_column',Request::get('select_column'));
            return view('crudbooster::import',$data);
        }

        public function postDoImportChunk() {
            set_time_limit(0);
            ini_set('memory_limit', '4294967296');
            $this->cbLoader();
            $file_md5 = md5(Request::get('file'));

            if(Request::get('file') && Request::get('resume')==1) {
                $total = Session::get('total_data_import');
                $prog = intval(Cache::get('success_'.$file_md5)) / $total * 100;
                $prog = round($prog,2);
                if($prog >= 100) {
                    Cache::forget('success_'.$file_md5);
                }
                return response()->json(['progress'=> $prog, 'last_error'=>Cache::get('error_'.$file_md5) ]);
            }
            $select_column = Session::get('select_column');
//            Log::debug('$select_column = '.Json::encode($select_column));
            $table_columns = [
                'product_code',
                'product_name',
                'product_unit_id',
                'total_weight',
                'gem_weight',
                'gold_weight',
                'retail_fee',
                'whole_fee',
                'fund_fee',
                'product_category_id',
                'product_group_id',
                'product_type_id',
                'producer_id'
            ];
//            $table_columns  =  DB::getSchemaBuilder()->getColumnListing($this->table);

//            $file = base64_decode(Request::get('file'));

            $file = Session::get('path_import_product');
            $rows = [];
            Excel::filter('chunk')->load($file)->chunk(1000, function($results) use (&$rows){
                $rows = array_merge($rows,  $results->toArray());
            }, false);
            Session::put('total_data_import',count($rows));
//            $rows = Excel::load($file,function($reader) {
//            })->get();
//            $rows = Session::get('table_rows');
            unlink($file);
            $has_created_at = false;
            if(CRUDBooster::isColumnExists($this->table,'created_at')) {
                $has_created_at = true;
            }
            $has_created_by = false;
            if(CRUDBooster::isColumnExists($this->table,'created_by')) {
                $has_created_by = true;
            }
            $has_updated_at = false;
            if(CRUDBooster::isColumnExists($this->table,'updated_at')) {
                $has_updated_at = true;
            }
            $has_updated_by = false;
            if(CRUDBooster::isColumnExists($this->table,'updated_by')) {
                $has_updated_by = true;
            }
            Cache::put('success_'.$file_md5, 0, 60*30); // cache trong 30 phút
//            Log::debug('$rows = '.Json::encode($rows));
            $data_import_column = array();
            foreach($rows as $value) {
//                Log::debug('$value = '.$value);
                $import_row = array();
                foreach($select_column as $sk => $s) {
                    $colname = $table_columns[$sk];
//                    Log::debug('$sk = '.$sk);
//                    Log::debug('$colname = '.$colname);
//                    Log::debug('$this->isForeignKey($colname) = '.$this->isForeignKey($colname));
//                    Log::debug('$s = '.$s);
//                    Log::debug('$value[$s] = '.$value[$s]);
//                    Log::debug('$value[$sk] = '.$value[$sk]);
                    if($colname  == 'status') {
                        if($value[$s] == '') continue;
                        elseif ($value[$s] == 'Còn hàng')
                            $import_row[$colname] = 1;
                        else
                            $import_row[$colname] = 0; // Hết hàng
                    } elseif($colname  == 'input_date') {
                        if($value[$s] == ''){
                            $import_row[$colname] = null;
                        }else{
                            $tmp_date = DateTime::createFromFormat('d/m/y h:i:s A', $value[$s]);
                            if($tmp_date) {
                                $import_row[$colname] = $tmp_date->format('Y-m-d H:i:s');
                            } else {
                                $import_row[$colname] = null;
                            }
                        }
                    }  elseif($colname  == 'make_stemp_date') {
                        if($value[$s] == ''){
                            $import_row[$colname] = null;
                        }else{
                            $tmp_date = DateTime::createFromFormat('d/m/y h:i:s A', $value[$s]);
                            if($tmp_date) {
                                $import_row[$colname] = $tmp_date->format('Y-m-d H:i:s');
                            }else{
                                $import_row[$colname] = null;
                            }
                        }
                    } elseif($this->isForeignKey($colname)) {

                        //Skip if value is empty
                        if($value[$s] == '') continue;

                        if(intval($value[$s]) && $colname != 'product_type_id') {
                            $import_row[$colname] = $value[$s];
                        }else{
                            $relation_table = $this->getTableForeignKey($colname);
                            $relation_moduls = DB::table('cms_moduls')->where('table_name',$relation_table)->first();

                            $relation_class = __NAMESPACE__ . '\\' . $relation_moduls->controller;
                            if(!class_exists($relation_class)) {
                                $relation_class = '\App\Http\Controllers\\'.$relation_moduls->controller;
                            }
                            $relation_class = new $relation_class;
                            $relation_class->cbLoader();

                            $title_field = $relation_class->title_field;

                            $relation_insert_data = array();
                            $relation_insert_data[$title_field] = $value[$s];

                            if(CRUDBooster::isColumnExists($relation_table,'created_at')) {
                                $relation_insert_data['created_at'] = date('Y-m-d H:i:s');
                            }
                            if(CRUDBooster::isColumnExists($relation_table,'created_by')) {
                                $relation_insert_data['created_by'] = CRUDBooster::myId();
                            }

                            try{
                                $relation_exists = DB::table($relation_table)->where($title_field,strval($value[$s]))->first();
                                if($relation_exists) {
                                    $relation_primary_key = $relation_class->primary_key;
                                    $relation_id = $relation_exists->$relation_primary_key;
                                }else{
                                    $relation_id = DB::table($relation_table)->insertGetId($relation_insert_data);
                                }

                                $import_row[$colname] = $relation_id;
                            }catch(\Exception $e) {

                                Log::debug('$e = '.$e);
                                exit($e);
                            }
                        } //END IS INT

                    }else{
                        $import_row[$colname] = $value[$s];
                    }
                }

                if(!$import_row['bar_code']){
                    Cache::increment('success_'.$file_md5);
                    continue;
                }
                try{
                    $row_exists = DB::table($this->table)->where('bar_code', $import_row['bar_code'])->first();
                    if(!$row_exists) {
                        if($has_created_at) {
                            $import_row['created_at'] = date('Y-m-d H:i:s');
                        }
                        if($has_created_by){
                            $import_row['created_by'] = CRUDBooster::myId();
                        }

                        DB::table($this->table)->insert($import_row);
                    } else {
                        if($has_updated_at) {
                            $import_row['updated_at'] = date('Y-m-d H:i:s');
                        }
                        if($has_updated_by){
                            $import_row['updated_by'] = CRUDBooster::myId();
                        }
                        DB::table($this->table)->where('id', $row_exists->id)->update($import_row);
                    }
                    Cache::increment('success_'.$file_md5);
                }catch(\Exception $e) {
                    Log::debug('error $import_row = ' . Json::encode($import_row));
                    Log::debug('error $value = ' . Json::encode($value));
                    $e = (string) $e;
                    Log::debug('(string) $e = '.$e);
                    Cache::put('error_'.$file_md5,$e,500);
                }
            }

            Log::debug('đã import '.count($rows). ' dòng xong');
            $rows = null;
            Session::put('table_rows',null);//đặt lại cho đỡ nặng memory
            return response()->json(['status'=>true]);
        }
        public function getTableForeignKey($fieldName)
        {
            $table = null;
            switch ($fieldName) {
                case 'product_unit_id':
                    $table  = 'gold_product_units';
                    break;
                case 'stock_id':
                    $table  = 'gold_stocks';
                    break;
                case 'product_category_id':
                    $table  = 'gold_product_categories';
                    break;
                case 'product_group_id':
                    $table  = 'gold_product_groups';
                    break;
                case 'product_type_id':
                    $table  = 'gold_product_types';
                    break;
                default:
                    if (substr($fieldName, 0, 3) == 'id_') {
                        $table = substr($fieldName, 3);
                    } elseif
                    (substr($fieldName, -3) == '_id') {
                        $table = substr($fieldName, 0, (strlen($fieldName) - 3));
                    }
            }
            return $table;
        }
        public function isForeignKey($fieldName) {
//            if(substr($fieldName, 0,3) == 'id_') {
//                $table = substr($fieldName, 3);
//            }elseif(substr($fieldName, -3) == '_id') {
//                $table = substr($fieldName, 0, (strlen($fieldName)-3) );
//            }
            if(Cache::has('isForeignKey_'.$fieldName)) {
                return Cache::get('isForeignKey_'.$fieldName);
            }else{
                $table  = $this->getTableForeignKey($fieldName);
                if($table) {
                    $hasTable = Schema::hasTable($table);
                    if($hasTable) {
                        Cache::forever('isForeignKey_'.$fieldName,true);
                        return true;
                    }else{
                        Cache::forever('isForeignKey_'.$fieldName,false);
                        return false;
                    }
                }else{
                    return false;
                }
            }
        }

	    //By the way, you can still create your own method in here... :) 
		public function getSearchItem(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $para = Request::all();
            $bar_code = $para['bar_code'];
            // $order_date = date('DD/MM/YYYY', $para['order_date']);
            $order_date = DateTime::createFromFormat('Y-m-d H:i:s', $para['order_date']);
            Log::debug('$order_date = ' . $order_date->format('Y-m-d H:i:s'));
            $item = DB::table('gold_items as I')
                ->leftJoin('gold_products as P', 'I.product_id', '=', 'P.id')
                ->leftJoin('gold_product_types as PT', 'P.product_type_id', '=', 'PT.id')
                // ->leftJoin('gold_product_groups as PG', 'P.product_group_id', '=', 'PG.id')
                ->leftJoin('gold_stocks as S', 'I.stock_id', '=', 'S.id')
                ->whereRaw('I.deleted_at is null AND I.status <> 0')
                ->where('I.bar_code', ''.$bar_code)
                ->select('I.id',
                    'I.bar_code',
                    'P.product_code',
                    'P.product_name',
                    //'P.product_unit_id',
                    'I.total_weight',
                    'I.gem_weight',
                    'I.gold_weight',
                    'I.qty',
                    'I.retail_fee',
                    'I.whole_fee',
                    'I.fund_fee',
                    'I.stock_id',
                    'S.name as stock_name',
                    // //'P.product_category_id',
                    // 'P.product_group_id',
                    // 'PG.name as product_group_name',
                    'P.product_type_id',
                    'PT.name as product_type_name'
                    // 'P.status'
                    )
                ->first();
            //Calculate price 
            Log::debug('$item = ' . Json::encode($item));
            if($item){
                $price = DB::table('gold_price')
                    ->whereRaw('deleted_at is null')
                    ->where('product_type_id', $item->product_type_id)
                    ->where('apply_time', '<=', $order_date->format('Y-m-d H:i:s'))
                    ->orderBy('apply_time', 'desc')
                    ->select('id',
                            'product_type_id',
                            'apply_time',
                            'sales_price'
                            )
                    ->first();
                Log::debug('$price = ' . Json::encode($price));
                if($price){
                    $item->price = $price->sales_price ? $price->sales_price : 0;
                }else{
                    $item->price = 0;
                }
            }
            return ['item'=>$item];
        }
        
        public function getCheckItem(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $para = Request::all();
            $bar_code = $para['bar_code'];
            $item = DB::table('gold_items')
                ->whereRaw('deleted_at is null')
                ->where('bar_code', ''.$bar_code)
                ->first();
            return ['item'=>$item];
		}
		
        public function getPrintTem($id) {
            Log::debug('getPrintTem('.$id.')');
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'Tem-'.time();
            $parameter = ['id'=>$id];
            $input = base_path().'/app/Reports/rpt_tem.jasper';
            $output = public_path() . '/output_reports/'.$filename;
            $jasper->process($input, $output, array('pdf'), $parameter, $database)->execute();

            while (!file_exists($output . '.pdf' )){
                sleep(1);
            }

            // Log::debug('$output = ' . $output);
            $file = File::get( $output . '.pdf' );

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/pdf',
                    'Content-Disposition' => 'filename="'.$filename.'.pdf"'
                )
            );
        }

        public function getPrintDirectTem($id) {
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

            $file = File::get( $output . '.html' );

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/pdf',
                    'Content-Disposition' => 'filename="'.$filename.'.html"'
                )
            );
        }

        public function getPrintTemInfo($id) {
            Log::debug('getPrintTemInfo(('.$id.')');
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'Tem-'.time();
            $parameter = ['id'=>$id];
            $input = base_path().'/app/Reports/rpt_tem.jasper';
            $output = public_path() . '/output_reports/'.$filename;
            $jasper->process($input, $output, array('pdf'), $parameter, $database)->execute();

            while (!file_exists($output . '.pdf' )){
                sleep(1);
            }

            // Log::debug('$output = ' . $output);
            $file = File::get( $output . '.pdf' );

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/pdf',
                    'Content-Disposition' => 'filename="'.$filename.'.pdf"'
                )
            );
            /* 
            SELECT T.bar_code, P.product_code, P.product_name, T.total_weight, T.gem_weight, T.gold_weight,
	T.retail_fee AS retail_fee, T.whole_fee / 1000 AS whole_fee, PT.name AS types, IFNULL(S.code, '') as supplier
FROM gold_items T 
	INNER JOIN gold_products P ON T.product_id = P.id
	LEFT JOIN gold_suppliers S ON T.supplier_id = S.id
	LEFT JOIN gold_product_types PT ON T.product_type_id = PT.id
WHERE T.id = $P{id}
            */
            /*
            $stamp_detail = DB::table('gold_items as T')
            ->join('gold_products as P', 'T.product_id', '=', 'P.id')
            ->leftJoin('gold_suppliers as S', 'T.supplier_id', '=', 'S.id')
            ->leftJoin('gold_product_types as PT', 'T.product_type_id', '=', 'PT.id')
            ->where('T.id', '=', $id)
            ->select(
                'T.bar_code as bar_code', 
                'P.product_code as product_code', 
                'P.product_name as product_name', 
                'T.total_weight as total_weight', 
                'T.gem_weight as gem_weight', 
                'T.gold_weight as gold_weight',
                'T.retail_fee as retail_fee', 
                // 'T.whole_fee / 1000 as whole_fee', 
                DB::raw('T.whole_fee / 1000 as whole_fee'),
                'PT.name as types',
                DB::raw('IFNULL(S.code, "") as supplier')
                )
            ->first();
            return response()->json([
                'bar_code' => $stamp_detail->bar_code,
                'product_code' => $stamp_detail->product_code,
                'product_name' => $stamp_detail->product_name,
                'total_weight' => $stamp_detail->total_weight,
                'gem_weight' => $stamp_detail->gem_weight,
                'gold_weight' => $stamp_detail->gold_weight,
                'retail_fee' => $stamp_detail->retail_fee,
                'whole_fee' => $stamp_detail->whole_fee,
                'types' => $stamp_detail->types,
                'supplier' => $stamp_detail->supplier
            ]);
            */
        }
	}