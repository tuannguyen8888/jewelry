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

	class AdminGoldStocksController extends CBExtendController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "name";
			$this->limit = "20";
			$this->orderby = "name,asc";
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
			$this->table = "gold_stocks";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Mã kho","name"=>"code"];
			$this->col[] = ["label"=>"Tên kho","name"=>"name"];
			$this->col[] = ["label"=>"Ghi chú","name"=>"notes"];
            $this->col[] = ["label"=>"Người tạo","name"=>"created_by","join"=>"cms_users,name"];
            $this->col[] = ["label"=>"Ngày tạo","name"=>"created_at","callback_php"=>'date_time_format($row->created_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
            $this->col[] = ["label"=>"Người sửa","name"=>"updated_by","join"=>"cms_users,name"];
            $this->col[] = ["label"=>"Ngày sửa","name"=>"updated_at","callback_php"=>'date_time_format($row->updated_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Mã kho','name'=>'code','type'=>'text','validation'=>'required|string|min:1|max:10','width'=>'col-sm-4','placeholder'=>'Nhập mã kho'];
			$this->form[] = ['label'=>'Tên kho','name'=>'name','type'=>'text','validation'=>'required|string|min:1|max:100','width'=>'col-sm-10','placeholder'=>'Nhập tên kho'];
			$this->form[] = ['label'=>'Ghi chú','name'=>'notes','type'=>'text','validation'=>'min:1|max:255','width'=>'col-sm-10'];
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
			if(CRUDBooster::myPrivilegeId() != 2){
				$this->addaction[] = ['label'=>'Tồn kho','url'=>CRUDBooster::mainpath('print-balance-detail/[id]'),'icon'=>'fa fa-print','color'=>'info'];
				$this->addaction[] = ['label'=>'Tồn kho','url'=>CRUDBooster::mainpath('print-balance-xlsx/[id]'),'icon'=>'fa fa-file-excel-o','color'=>'success'];
			}

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
		public function getSearchStock(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $para = Request::all();
            $stock_code = $para['stock_code'];
            $stock = DB::table('gold_stocks')
                ->whereRaw('deleted_at is null')
                ->where('code', $stock_code)
                ->select('id',
                    'code',
                    'name')
                ->first();
            return ['stock'=>$stock];
		}

		public function getStocks(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $stocks = DB::table('gold_stocks')
                ->whereRaw('deleted_at is null')
				->orderBy('code')
				->select('id',
                    'code',
					'name')
                ->get();
            return ['stocks'=>$stocks];
		}
		
		public function getPrintBalanceDetail($id) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'TK_'.time();
            $parameter = [
                'id'=>$id,
                'logo'=>storage_path().'/app/uploads/logo.png'
			];
            $input = base_path().'/app/Reports/rpt_stock_balance_detail.jasper';
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
		
		public function getPrintBalanceXlsx($id){
            $jasper = new JasperPHP();
			$database = \Config::get('database.connections.mysql');

			$stock = DB::table('gold_stocks')->where('id', $id)->first();
			if($stock){
				$filename = 'TK-'.$stock->code.'-'.time();
			}else{
				$filename = 'TK-'.time();
			}
			$parameter = ['id'=>$id];
			$input = base_path().'/app/Reports/rpt_stock_balance_xlsx.jasper';
            $output = public_path() . '/output_reports/' . $filename;
            $jasper->process($input, $output, array('xlsx'), $parameter, $database)->execute();

            while (!file_exists($output . '.xlsx' )){
                sleep(1);
            }

            $file = File::get( $output . '.xlsx' );
            unlink($output . '.xlsx');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'filename="'.$filename.'.xlsx"'
                )
            );
		}
		
		public function getStockMovement() {
			//            $para = Request::all();
			$data = [];
			$data['page_title'] = 'Chi tiết Nhập - Xuất - Tồn';
			$this->cbView('stock_movement_form', $data);
		}

		public function getStockBalance() {
			//            $para = Request::all();
			$data = [];
			$data['page_title'] = 'Báo cáo tồn kho';
			$this->cbView('stock_balance_form', $data);
		}

		public function getPrintStockMovement($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SM_'.time();
			$para_values = explode("@", $para);
			// Log::debug('$para = '.$para_values[0]);
			// Log::debug('$para = '.$para_values[1]);
			// Log::debug('$para = '.$para_values[2]);
            $parameter = [
				'from_date'=>$para_values[0],
				'to_date'=>$para_values[1],
				'stock_ids'=>$para_values[2],
				// 'brand_id'=>$para_values[3],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];

            $input = base_path().'/app/Reports/rpt_stock_movement.jasper';
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

		public function getPrintStockMovementAll($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SMA_'.time();
            $para_values = explode("@", $para);
			// Log::debug('$para = '.$para_values[0]);
			// Log::debug('$para = '.$para_values[1]);
			// Log::debug('$para = '.$para_values[2]);
            $parameter = [
				'from_date'=>$para_values[0],
				'to_date'=>$para_values[1],
				'stock_ids'=>$para_values[2],
				// 'brand_id'=>$para_values[3],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];

            $input = base_path().'/app/Reports/rpt_stock_movement_all.jasper';
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

		public function getPrintStockMovementXlsx($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SM_'.time();
			$para_values = explode("@", $para);
            $parameter = [
				'from_date'=>$para_values[0],
				'to_date'=>$para_values[1],
				'stock_ids'=>$para_values[2],
				// 'brand_id'=>$para_values[3],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];

            $input = base_path().'/app/Reports/rpt_stock_movement.jasper';
            $output = public_path().'/output_reports/'.$filename;
            $jasper->process($input, $output, array('xlsx'), $parameter, $database)->execute();

            while (!file_exists($output . '.xlsx' )){
                sleep(1);
            }

            $file = File::get( $output . '.xlsx' );
            unlink($output . '.xlsx');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'filename="'.$filename.'.xlsx"'
                )
            );
		}

		public function getPrintStockMovementAllXlsx($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SMA_'.time();
            $para_values = explode("@", $para);
            $parameter = [
				'from_date'=>$para_values[0],
				'to_date'=>$para_values[1],
				'stock_ids'=>$para_values[2],
				// 'brand_id'=>$para_values[3],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];

            $input = base_path().'/app/Reports/rpt_stock_movement_all.jasper';
            $output = public_path().'/output_reports/'.$filename;
            $jasper->process($input, $output, array('xlsx'), $parameter, $database)->execute();

            while (!file_exists($output . '.xlsx' )){
                sleep(1);
            }

            $file = File::get( $output . '.xlsx' );
            unlink($output . '.xlsx');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'filename="'.$filename.'.xlsx"'
                )
            );
		}

		public function getStockMovementSupplier() {
			//            $para = Request::all();
			$data = [];
			$data['page_title'] = 'Nhập - Xuất - Tồn theo nhà cung cấp';
			$this->cbView('stock_movement_supplier_form', $data);
		}

		public function getPrintStockMovementSupplier($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SMS_'.time();
			$para_values = explode("@", $para);
            $parameter = [
				'from_date'=>$para_values[0],
				'to_date'=>$para_values[1],
				'supplier_ids'=>$para_values[2],
				// 'brand_id'=>$para_values[3],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];

            $input = base_path().'/app/Reports/rpt_stock_movement_supplier.jasper';
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

		public function getPrintStockMovementSupplierXlsx($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SMS_'.time();
			$para_values = explode("@", $para);
            $parameter = [
				'from_date'=>$para_values[0],
				'to_date'=>$para_values[1],
				'supplier_ids'=>$para_values[2],
				// 'brand_id'=>$para_values[3],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];

            $input = base_path().'/app/Reports/rpt_stock_movement_supplier.jasper';
            $output = public_path().'/output_reports/'.$filename;
            $jasper->process($input, $output, array('xlsx'), $parameter, $database)->execute();

            while (!file_exists($output . '.xlsx' )){
                sleep(1);
            }

            $file = File::get( $output . '.xlsx' );
            unlink($output . '.xlsx');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'filename="'.$filename.'.xlsx"'
                )
            );
		}

		public function getPrintStockBalance($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SB_'.time();
			$para_values = explode("@", $para);
            $parameter = [
				'to_date'=>$para_values[0],
				'brand_id'=>$para_values[1],
				'stock_ids'=>$para_values[2],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];
            $input = base_path().'/app/Reports/rpt_stock_balance.jasper';
			$output = public_path().'/output_reports/'.$filename;
			Log::debug('$para = '.$input);
			Log::debug('$para = '.$output);
			Log::debug('$para = '.storage_path().'/app/uploads/logo.png');
            $jasper->process($input, $output, array('pdf'), $parameter, $database)->execute();
			
            while (!file_exists($output.'.pdf')){
                sleep(1);
            }

            $file = File::get($output.'.pdf');
			Log::debug('$para = '.$file);
            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/pdf',
                    'Content-Disposition' => 'filename="'.$filename.'.pdf"'
                )
            );
		}

		public function getPrintStockBalanceAll($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SBA_'.time();
            $para_values = explode("@", $para);
            $parameter = [
				'to_date'=>$para_values[0],
				'brand_id'=>$para_values[1],
				'stock_ids'=>$para_values[2],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];

            $input = base_path().'/app/Reports/rpt_stock_balance_all.jasper';
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

		public function getPrintStockBalanceXlsx($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SB_'.time();
            $para_values = explode("@", $para);
            $parameter = [
				'to_date'=>$para_values[0],
				'brand_id'=>$para_values[1],
				'stock_ids'=>$para_values[2],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];
            $input = base_path().'/app/Reports/rpt_stock_balance.jasper';
            $output = public_path().'/output_reports/'.$filename;
            $jasper->process($input, $output, array('xlsx'), $parameter, $database)->execute();

            while (!file_exists($output . '.xlsx' )){
                sleep(1);
            }

            $file = File::get( $output . '.xlsx' );
            unlink($output . '.xlsx');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'filename="'.$filename.'.xlsx"'
                )
            );
		}

		public function getPrintStockBalanceAllXlsx($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SBA_'.time();
            $para_values = explode("@", $para);
            $parameter = [
				'to_date'=>$para_values[0],
				'brand_id'=>$para_values[1],
				'stock_ids'=>$para_values[2],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];

            $input = base_path().'/app/Reports/rpt_stock_balance_all.jasper';
            $output = public_path().'/output_reports/'.$filename;
            $jasper->process($input, $output, array('xlsx'), $parameter, $database)->execute();

            while (!file_exists($output . '.xlsx' )){
                sleep(1);
            }

            $file = File::get( $output . '.xlsx' );
            unlink($output . '.xlsx');

            return Response::make($file, 200,
                array(
                    'Content-type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'filename="'.$filename.'.xlsx"'
                )
            );
		}
	}