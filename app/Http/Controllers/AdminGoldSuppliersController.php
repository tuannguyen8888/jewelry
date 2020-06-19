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

	class AdminGoldSuppliersController extends CBExtendController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "code";
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
			$this->button_filter = true;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "gold_suppliers";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Mã số","name"=>"code"];
			$this->col[] = ["label"=>"Tên","name"=>"name"];
			$this->col[] = ["label"=>"Địa chỉ","name"=>"address"];
			$this->col[] = ["label"=>"Điện thoại","name"=>"phone"];
			$this->col[] = ["label"=>"MST","name"=>"tax_no"];
			$this->col[] = ["label"=>"Người liên hệ","name"=>"contact"];
			$this->col[] = ["label"=>"Công nợ Q10","name"=>"q10", "callback_php"=>'number_format($row->q10, 4)'];
			$this->col[] = ["label"=>"Công nợ","name"=>"balance", "callback_php"=>'number_format($row->balance)'];
			// $this->col[] = ["label"=>"Import","name"=>"import"];
			$this->col[] = ["label"=>"Người tạo","name"=>"created_by","join"=>"cms_users,name"];
            $this->col[] = ["label"=>"T/g tạo","name"=>"created_at","callback_php"=>'date_time_format($row->created_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
            $this->col[] = ["label"=>"Người sửa","name"=>"updated_by","join"=>"cms_users,name"];
            $this->col[] = ["label"=>"T/g sửa","name"=>"updated_at","callback_php"=>'date_time_format($row->updated_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'Mã số','name'=>'code','type'=>'text','validation'=>'required|min:1|max:10','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Tên','name'=>'name','type'=>'text','validation'=>'required|string|min:3|max:200','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Địa chỉ','name'=>'address','type'=>'text','validation'=>'min:1|max:200','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Điện thoại','name'=>'phone','text'=>'number','validation'=>'max:100','width'=>'col-sm-6'];
			$this->form[] = ['label'=>'Mã số thuế','name'=>'tax_no','type'=>'text','validation'=>'max:20','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Người liên hệ','name'=>'contact','type'=>'text','validation'=>'max:100','width'=>'col-sm-6'];
			$this->form[] = ['label'=>'Notes','name'=>'notes','type'=>'text','validation'=>'min:1|max:200','width'=>'col-sm-10'];
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
				$this->addaction[] = ['label'=>'Bảng kê','url'=>CRUDBooster::mainpath('print-list/[id]'),'icon'=>'fa fa-print','color'=>'info'];
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
		public function getSearchSupplier(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $para = Request::all();
            $supplier_code = $para['supplier_code'];

           	// Log::debug('$para = '.json_encode($para));
            $supplier = DB::table('gold_suppliers')->whereRaw('deleted_at is null')->where('code', $supplier_code)->first();
			return ['supplier'=>$supplier];
        }

		public function getSuppliers(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $suppliers = DB::table('gold_suppliers')->whereRaw('deleted_at is null')->orderBy('name')->get();
			return ['suppliers'=>$suppliers];
		}
		
		public function getBalance() {
			//            $para = Request::all();
			$data = [];
			$data['page_title'] = 'Công nợ nhà cung cấp';
			$this->cbView('supplier_balance_form', $data);
		}
		
		public function getPrintList($id) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'SL_'.time();
            $parameter = [
                'id'=>$id,
                'logo'=>storage_path().'/app/uploads/logo.png'
			];
            $input = base_path().'/app/Reports/rpt_supplier_list.jasper';
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

		public function getPrintBalance($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SB_'.time();
			$para_values = explode("@", $para);
            $parameter = [
				'to_date'=>$para_values[0],
				'brand_id'=>$para_values[1],
				'ids'=>$para_values[2],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];

            $input = base_path().'/app/Reports/rpt_supplier_balance.jasper';
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

		public function getPrintBalanceXlsx($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SB_'.time();
            $para_values = explode("@", $para);
            $parameter = [
				// 'from_date'=>$para_values[0],
				'to_date'=>$para_values[0],
				'brand_id'=>$para_values[1],
				'ids'=>$para_values[2],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];
            $input = base_path().'/app/Reports/rpt_supplier_balance.jasper';
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

		public function getPrintBalanceDetail($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SB_'.time();
			$para_values = explode("@", $para);
            $parameter = [
				'from_date'=>$para_values[0],
				'to_date'=>$para_values[1],
				'brand_id'=>$para_values[2],
				'ids'=>$para_values[3],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];

            $input = base_path().'/app/Reports/rpt_supplier_balance_detail.jasper';
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

		public function getPrintBalanceDetailXlsx($para) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'SB_'.time();
            $para_values = explode("@", $para);
            $parameter = [
				'from_date'=>$para_values[0],
				'to_date'=>$para_values[1],
				'brand_id'=>$para_values[2],
				'ids'=>$para_values[3],
                'logo'=>storage_path().'/app/uploads/logo.png'
			];
            $input = base_path().'/app/Reports/rpt_supplier_balance_detail.jasper';
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