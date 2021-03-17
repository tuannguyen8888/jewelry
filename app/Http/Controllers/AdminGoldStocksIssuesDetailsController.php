<?php namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;

	class AdminGoldStocksIssuesDetailsController extends CBExtendController {

	    public function cbInit() {

			# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->title_field = "id";
			$this->limit = "20";
			$this->orderby = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = false;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon_text";
			$this->button_add = false;
			$this->button_edit = false;
			$this->button_delete = false;
			$this->button_detail = false;
			$this->button_show = false;
			$this->button_filter = false;
			$this->button_import = false;
			$this->button_export = true;
			$this->table = "gold_stocks_issues";
            $this->is_search_form = true;
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
            $this->col[] = ["label"=>"Barcode","name"=>"id","callback_php"=>'$row->bar_code'];
            $this->col[] = ["label"=>"Số phiếu","name"=>"order_no"];
            $this->col[] = ["label"=>"T/g xuất","name"=>"order_date","callback_php"=>'date_time_format($row->order_date, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
            $this->col[] = ["label"=>"Đối tượng","name"=>"object_id","callback_php"=>'$row->object_name'];
            $this->col[] = ["label"=>"Trạng thái","name"=>"status","callback_php"=>'get_input_status($row->status);'];
            $this->col[] = ["label"=>"Lý do","name"=>"notes"];
            $this->col[] = ["label"=>"Cửa hàng","name"=>"brand_id","join"=>"gold_brands,name"];
//            $this->col[] = ["label"=>"Người tạo","name"=>"created_by","join"=>"cms_users,name"];
//            $this->col[] = ["label"=>"T/g tạo","name"=>"created_at","callback_php"=>'date_time_format($row->created_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
//            $this->col[] = ["label"=>"Người sửa","name"=>"updated_by","join"=>"cms_users,name"];
//            $this->col[] = ["label"=>"T/g sửa","name"=>"updated_at","callback_php"=>'date_time_format($row->updated_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
            # END COLUMNS DO NOT REMOVE THIS LINE
            $this->search_form = [];
            $this->search_form[] = ["label"=>"Từ ngày", "name"=>"order_date_from_date", "data_column"=>"order_date", "search_type"=>"between_from","type"=>"date","width"=>"col-sm-2"];
            $this->search_form[] = ["label"=>"Đến ngày", "name"=>"order_date_to_date", "data_column"=>"order_date", "search_type"=>"between_to","type"=>"date","width"=>"col-sm-2"];
            if(CRUDBooster::myPrivilegeId() == 1 || CRUDBooster::myPrivilegeId() == 4){
                $this->search_form[] = ["label" => "Cửa hàng", "name" => "brand_id", "data_column"=>$this->table.".brand_id", "search_type"=>"equals_raw", "type" => "select2", "width" => "col-sm-2", 'datatable' => 'gold_brands,name', 'datatable_where' => 'deleted_at is null'];
            }

            # START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
//			$this->form[] = ['label'=>'Order No','name'=>'order_no','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
//			$this->form[] = ['label'=>'Order Date','name'=>'order_date','type'=>'datetime','validation'=>'required|date_format:Y-m-d H:i:s','width'=>'col-sm-10'];
//			$this->form[] = ['label'=>'Object Type','name'=>'object_type','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
//			$this->form[] = ['label'=>'Object Id','name'=>'object_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'object,id'];
//			$this->form[] = ['label'=>'Brand Id','name'=>'brand_id','type'=>'select2','validation'=>'required|integer|min:0','width'=>'col-sm-10','datatable'=>'brand,id'];
//			$this->form[] = ['label'=>'Status','name'=>'status','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
//			$this->form[] = ['label'=>'Notes','name'=>'notes','type'=>'text','validation'=>'required|min:1|max:255','width'=>'col-sm-10'];
//			$this->form[] = ['label'=>'Total Wage','name'=>'total_wage','type'=>'money','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
//			$this->form[] = ['label'=>'Created By','name'=>'created_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
//			$this->form[] = ['label'=>'Updated By','name'=>'updated_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
//			$this->form[] = ['label'=>'Deleted By','name'=>'deleted_by','type'=>'number','validation'=>'required|integer|min:0','width'=>'col-sm-10'];
			# END FORM DO NOT REMOVE THIS LINE

			# OLD START FORM
			//$this->form = [];
			//$this->form[] = ["label"=>"Order No","name"=>"order_no","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Order Date","name"=>"order_date","type"=>"datetime","required"=>TRUE,"validation"=>"required|date_format:Y-m-d H:i:s"];
			//$this->form[] = ["label"=>"Object Type","name"=>"object_type","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Object Id","name"=>"object_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"object,id"];
			//$this->form[] = ["label"=>"Brand Id","name"=>"brand_id","type"=>"select2","required"=>TRUE,"validation"=>"required|integer|min:0","datatable"=>"brand,id"];
			//$this->form[] = ["label"=>"Status","name"=>"status","type"=>"number","required"=>TRUE,"validation"=>"required|integer|min:0"];
			//$this->form[] = ["label"=>"Notes","name"=>"notes","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
			//$this->form[] = ["label"=>"Total Wage","name"=>"total_wage","type"=>"money","required"=>TRUE,"validation"=>"required|integer|min:0"];
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
            $query->leftJoin('gold_customers', function($join)
            {
                $join->on('gold_customers.id', '=', $this->table.'.object_id')
                    ->where($this->table.'.object_type', '=', '0');
            })->leftJoin('gold_suppliers', function($join)
            {
                $join->on('gold_suppliers.id', '=', $this->table.'.object_id')
                    ->where($this->table.'.object_type', '=', '1');
            })->leftJoin('gold_investors', function($join)
            {
                $join->on('gold_investors.id', '=', $this->table.'.object_id')
                    ->where($this->table.'.object_type', '=', '2');
            })->leftJoin('cms_users as U', function($join)
            {
                $join->on('U.id', '=', $this->table.'.object_id')
                    ->where($this->table.'.object_type', '=', '3');
            });
            $current_brand = CRUDBooster::myBrand();
            $privilegeId = CRUDBooster::myPrivilegeId();
            if($current_brand && $privilegeId != 1 && $privilegeId != 4){
                $query->where($this->table.'.brand_id', '=', $current_brand);
            }
            $query->addSelect(DB::raw('CASE WHEN object_type = 0 THEN gold_customers.name WHEN object_type = 1 THEN gold_suppliers.name WHEN object_type = 2 THEN gold_investors.name WHEN object_type = 3 THEN U.name ELSE \'Lỗi\' END as object_name'));

            $query->join('gold_stocks_issue_detail as SID', $this->table.'.id','=', 'SID.order_id')
                ->leftJoin('gold_items as I', 'SID.item_id', '=', 'I.id')
                ->addSelect('I.bar_code');
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


	}