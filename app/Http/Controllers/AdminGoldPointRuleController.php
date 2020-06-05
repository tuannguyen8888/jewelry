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

	class AdminGoldPointRuleController extends CBExtendController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "apply_time";
			$this->limit = "20";
			$this->orderby = "apply_time,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon_text";
			if(CRUDBooster::myPrivilegeId() == 2){
				$this->button_add = false;
				$this->button_edit = false;
				$this->button_delete = false;
				$this->button_detail = false;
			}else{
				$this->button_add = true;
				$this->button_edit = true;
				$this->button_delete = true;
				$this->button_detail = true;
			}
			$this->button_show = false;
			$this->button_filter = false;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "gold_point_rule";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"T/g áp dụng","name"=>"apply_time","callback_php"=>'date_time_format($row->apply_time, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\')'];
			$this->col[] = ["label"=>"Loại vàng","name"=>"product_type_id","join"=>"gold_product_types,name"];
			$this->col[] = ["label"=>"Doanh số","name"=>"sales_amount","callback_php"=>'number_format($row->sales_amount)'];
			$this->col[] = ["label"=>"Điểm","name"=>"point","callback_php"=>'number_format($row->point)'];
			$this->col[] = ["label"=>"Ghi chú","name"=>"notes"];
			$this->col[] = ["label"=>"Người tạo","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g tạo","name"=>"created_at","callback_php"=>'date_time_format($row->created_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\')'];
			$this->col[] = ["label"=>"Người sửa","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g sửa","name"=>"updated_at","callback_php"=>'date_time_format($row->update_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\')'];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ['label'=>'T/g áp dụng','name'=>'apply_time','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Loại vàng','name'=>'product_type_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-4','datatable'=>'gold_product_types,name'];
			$this->form[] = ['label'=>'Doanh số','name'=>'sales_amount','type'=>'money','validation'=>'required|min:0','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Điểm','name'=>'point','type'=>'money','validation'=>'required|min:0','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Ghi chú','name'=>'notes','type'=>'text','validation'=>'min:0|max:200','width'=>'col-sm-10'];
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
		public function getPoint(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $para = Request::all();
			// $apply_time = date('DD/MM/YYYY', $para['apply_time']);
			$apply_time = DateTime::createFromFormat('Y-m-d H:i:s', $para['apply_time']);
			$product_type_id = $para['product_type_id'];

			Log::debug('$apply_time = ' . $apply_time->format('Y-m-d H:i:s'));
			Log::debug('$product_type_id = ' . $product_type_id);

            $point = DB::table('gold_point_rule')
                ->whereRaw('deleted_at is null')
				->where('apply_time', '<=', $apply_time->format('Y-m-d H:i:s'))
				->where('product_type_id', $product_type_id)
				->orderBy('apply_time', 'desc')
				->select('id',
                    'product_type_id',
                    'apply_time',
					'sales_amount',
					'point')
                ->first();
            return ['point'=>$point];
		}
	}