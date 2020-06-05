<?php namespace App\Http\Controllers;

    use Enums;
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

	class AdminGoldCustomersController extends CBExtendController {

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
			$this->table = "gold_customers";
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Mã số","name"=>"code"];
			$this->col[] = ["label"=>"Tên","name"=>"name"];
			$this->col[] = ["label"=>"Ngày sinh","name"=>"dob","callback_php"=>'date_time_format($row->dob, \'Y-m-d H:i:s\', \'d/m/Y\');'];
			$this->col[] = ["label"=>"Giới tính","name"=>"gender"];
			$this->col[] = ["label"=>"Địa chỉ","name"=>"address"];
			$this->col[] = ["label"=>"ĐT bàn","name"=>"phone"];
			$this->col[] = ["label"=>"Người liên hệ","name"=>"contact"];
			$this->col[] = ["label"=>"ĐTDĐ","name"=>"zalo_phone"];
			$this->col[] = ["label"=>"Mã số thuế","name"=>"tax_no"];
			$this->col[] = ["label"=>"Công nợ","name"=>"balance","callback_php"=>'number_format($row->balance, 0)'];
			$this->col[] = ["label"=>"Loại","name"=>"type","callback_php"=>'get_customer_type($row->type);'];
			$this->col[] = ["label"=>"Chiết khấu (%)","name"=>"discount_rate"];
			$this->col[] = ["label"=>"Số thẻ","name"=>"card_no"];
			$this->col[] = ["label"=>"Điểm tích lũy","name"=>"points","callback_php"=>'number_format($row->points, 0)'];
			$this->col[] = ["label"=>"Người tạo","name"=>"created_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g tạo","name"=>"created_at","callback_php"=>'date_time_format($row->created_at, \'Y-m-d H:i:s\', \'d/m/Y\');'];
			$this->col[] = ["label"=>"Người sửa","name"=>"updated_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g sửa","name"=>"updated_at","callback_php"=>'date_time_format($row->updated_at, \'Y-m-d H:i:s\', \'d/m/Y\');'];
			# END COLUMNS DO NOT REMOVE THIS LINE

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
            $this->form[] = ['label' => 'Mã số', 'name' => 'code', 'type' => 'text', 'validation' => 'min:1|max:10', 'width' => 'col-sm-2', 'help' => 'Mã này phát sinh tự động', 'readonly' => true];
            $this->form[] = ['label'=>'Tên','name'=>'name','type'=>'text','validation'=>'required|string|min:3|max:200','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Địa chỉ','name'=>'address','type'=>'text','validation'=>'required|min:1|max:200','width'=>'col-sm-10'];
			$this->form[] = ['label'=>'Ngày sinh','name'=>'dob','type'=>'date','validation'=>'date_format:Y-m-d','width'=>'col-sm-2'];
			$this->form[] = ['label'=>'Giới tính','name'=>'gender','type'=>'select','width'=>'col-sm-2', 'dataenum'=>"Nữ|<lable class='label label-warning'>Nữ</lable>;Nam|<lable class='label label-danger'>Nam</lable>"];
			$this->form[] = ['label'=>'Mã số thuế','name'=>'tax_no','type'=>'text','validation'=>'max:20','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'ĐT bàn','name'=>'phone','text'=>'number','validation'=>'max:100','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Người liên hệ','name'=>'contact','type'=>'text','validation'=>'max:100','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'ĐTDĐ','name'=>'zalo_phone','type'=>'text','validation'=>'max:100','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Loại','name'=>'type','type'=>'select','width'=>'col-sm-2', 'dataenum'=>"0|<lable class='label label-warning'>Thường</lable>;1|<lable class='label label-danger'>VIP</lable>"];
			$this->form[] = ['label'=>'Chiết khấu (%)','name'=>'discount_rate','type'=>'number','validation'=>'numeric|min:0|max:100','width'=>'col-sm-2'];
			$this->form[] = ['label'=>'Số thẻ','name'=>'card_no','type'=>'text','validation'=>'max:20','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'CMND','name'=>'id_card','type'=>'text','validation'=>'max:20','width'=>'col-sm-4'];
			$this->form[] = ['label'=>'Ngày cấp','name'=>'issue_date','type'=>'date','validation'=>'date_format:Y-m-d','width'=>'col-sm-2'];
			$this->form[] = ['label'=>'Nơi cấp','name'=>'issue_by','type'=>'text','validation'=>'max:100','width'=>'col-sm-4'];
            $this->form[] = ['label'=>'Ghi chú','name'=>'notes','type'=>'textarea','validation'=>'min:1|max:255','width'=>'col-sm-10'];

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
			/*
			if(CRUDBooster::myPrivilegeId() == 2)// Nhân viên bán hàng
            {
                $query->where('gold_customers.saler_id', CRUDBooster::myId());
			}
			*/
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
			// $postdata['address'] = $postdata['address_home_number'].', '.$postdata['address_street'].', '.$postdata['address_ward'].', '.$postdata['address_district'].', '.$postdata['address_province'];
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
			$postdata['code'] = $new_code;
			$postdata['discount_rate'] = $postdata['discount_rate'] ? $postdata['discount_rate'] : 0;
			$postdata['type'] = $postdata['type'] ? $postdata['type'] : 0;
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
            // $postdata['address'] = $postdata['address_home_number'].', '.$postdata['address_street'].', '.$postdata['address_ward'].', '.$postdata['address_district'].', '.$postdata['address_province'];
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

        public function getSearchCustomer(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $para = Request::all();
            $customer_code = $para['customer_code'];

           	Log::debug('$para = '.json_encode($para));
            $customer = DB::table('gold_customers as C')->whereRaw('C.deleted_at is null')
                ->where('C.code', $customer_code)
                ->first();
			return ['customer'=>$customer];
		}

		public function getCustomers(){
            //First, Add an auth
            if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
            $customers = DB::table('gold_customers')->whereRaw('deleted_at is null')->orderBy('code')->get();
            return ['customers'=>$customers];
		}

		public function getBalance() {
			//            $para = Request::all();
			$data = [];
// 			$data['page_title'] = 'Công nợ khách hàng';
// 			$data['table'] = "gold_customers_balance";
// 			$data['col'] = [];
// 			$data['col'] += ["label"=>"Mã số","name"=>"code"];
// 			$data['col'] += ["label"=>"Tên","name"=>"name"];
// 			$data['col'] += ["label"=>"Ngày sinh","name"=>"dob","callback_php"=>'date_time_format($row->dob, \'Y-m-d H:i:s\', \'d/m/Y\');'];
// 			$this->cbView('index', $data);
// 			$this->view('customer_balance_form',$data);
			$this->cbView('customer_balance_form', $data);
		}
		
		public function getBalanceDetail($startDate,$endDate = 0) {
		    //            $para = Request::all();
		    //First, Add an auth
		    file_put_contents('php://stderr', print_r("getBalanceDetail(".$startDate.",".$endDate.")\n", TRUE));
		    if(!CRUDBooster::isView()) CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
// 		    if(CRUDBooster::myPrivilegeId() == 1) {
// 		        $users = DB::table('gold_customers')->orderBy('employee_code')->get();
// 		    }else{
// 		        $users = DB::table('cms_users')->whereRaw('id <> 1')->orderBy('employee_code')->get();
// 		    }
		    file_put_contents('php://stderr', print_r("Query Database\n", TRUE));
		    $balance = DB::table('gold_customers')
		    ->join('gold_sale_orders', 'gold_customers.id', '=', 'gold_sale_orders.customer_id')
		    // ->select('gold_customers.code', 'gold_customers.name' , DB::raw('MAX(gold_sale_orders.order_date) AS most_recent_date'),
 		    //     DB::raw('(gold_sale_orders.gold_amount + gold_sale_orders.fee) AS deposit'), 
			//     'gold_customers.balance')
			->selectRaw('gold_customers.code, gold_customers.name ,MAX(gold_sale_orders.order_date) AS most_recent_date,
			(gold_sale_orders.gold_amount + gold_sale_orders.fee) AS deposit, 
		   gold_customers.balance')
// 		    ->where(  ['most_recent_date', '>=', $startDate], ['most_recent_date', '<=', $endDate])
     ->whereBetween('gold_sale_orders.order_date', [$startDate, $endDate])//Very stupid???????most_recent_date?
->groupBy('gold_customers.id','deposit')
		    ->get();
// $balance = DB::raw(" SELECT `gold_suppliers`.`code` , `gold_suppliers`.`name` , MAX(`gold_sale_orders`.`order_date`) AS 'ngay mua gan nhat`gold_sale_orders`',
// `gold_sale_orders`.`gold_amount` + `gold_sale_orders`.`fee` AS 'tra truoc nha cung cap', `gold_suppliers`.`balance` , `gold_suppliers`.`q10`
// FROM `gold_suppliers` INNER JOIN `gold_sale_orders` ON `gold_suppliers`.`id` = gold_sale_orders.`customer_id`
// GROUP BY `gold_suppliers`.`id`
// ;");
		    file_put_contents('php://stderr', print_r("Query Done\n", TRUE));
		    return ['balance'=>$balance];
		}
		
		public function getPrintList($id) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'CL_'.time();
            $parameter = [
                'id'=>$id,
                'logo'=>storage_path().'/app/uploads/logo.png'
			];
            $input = base_path().'/app/Reports/rpt_customer_list.jasper';
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

		public function getPrintBalance($startDate,$endDate = 0) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
			$filename = 'CB_'.time();
            $parameter = [
				'from_date'=>$startDate,
				'to_date'=>$endDate,
				// 'ids'=>'123456789',
                 'logo'=>storage_path().'/app/uploads/logo.png'
			];

            $input = base_path().'/app/Reports/rpt_customers_balance_all.jasper';
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
	}