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
	use lepiaf\SerialPort\SerialPort;
	use lepiaf\SerialPort\Parser\SeparatorParser;
	use lepiaf\SerialPort\Configure\TTYConfigure;

	class AdminGoldCountersController extends CBExtendController {

	    public function cbInit() {
	    	# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->table 			   = "gold_counters";	        
			$this->title_field         = "counter_no";
			$this->limit               = 20;
			$this->orderby             = "id,desc";
			$this->global_privilege = false;
			$this->button_table_action = true;
			$this->button_bulk_action = false;
			$this->button_action_style = "button_icon_text";
			$rows = DB::table('gold_counters')
				->whereRaw('deleted_at is null AND closed_at is null')
				->where('saler_id', CRUDBooster::myId())
				->first();
			if($rows){
				$this->button_add = false;
			}else{
				$this->button_add = true;
			}
			$this->button_edit = true;
			$this->button_delete = true;				
			$this->button_detail = true;
			$this->button_show = false;
			$this->button_filter = false;
			$this->button_import = false;
			$this->button_export = true;
            $this->is_search_form = true;
			# END CONFIGURATION DO NOT REMOVE THIS LINE

			# START COLUMNS DO NOT REMOVE THIS LINE
			$this->col = [];
			$this->col[] = ["label"=>"Số phiếu","name"=>"counter_no"];
			$this->col[] = ["label"=>"T/g mở","name"=>"opened_at","callback_php"=>'date_time_format($row->opened_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"T/g đóng","name"=>"closed_at","callback_php"=>'date_time_format($row->closed_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Nội dung","name"=>"description"];
			$this->col[] = ["label"=>"Trạng thái","name"=>"status","callback_php"=>'get_counter_status($row->status);'];
			$this->col[] = ["label"=>"Nhân viên","name"=>"saler_id","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"Ứng trước","name"=>"advance_amount", "callback_php"=>'number_format($row->advance_amount)'];
			$this->col[] = ["label"=>"Bán ra (TM)","name"=>"sales_amount", "callback_php"=>'number_format($row->sales_amount)'];
			$this->col[] = ["label"=>"Bán ra (CK)","name"=>"bank_amount", "callback_php"=>'number_format($row->bank_amount)'];
			$this->col[] = ["label"=>"Mua vào","name"=>"purchase_amount", "callback_php"=>'number_format($row->purchase_amount)'];
			$this->col[] = ["label"=>"Cầm","name"=>"pawn_amount", "callback_php"=>'number_format($row->pawn_amount)'];
			$this->col[] = ["label"=>"Lãi","name"=>"interested_amount", "callback_php"=>'number_format($row->interested_amount)'];
			$this->col[] = ["label"=>"Tất toán","name"=>"liquidation_amount", "callback_php"=>'number_format($row->liquidation_amount)'];
			$this->col[] = ["label"=>"Tiền chuyển","name"=>"withdrawal_in", "callback_php"=>'number_format($row->withdrawal_in)'];
			$this->col[] = ["label"=>"Tiền nhận","name"=>"withdrawal_out", "callback_php"=>'number_format($row->withdrawal_out)'];
			$this->col[] = ["label"=>"Người khóa","name"=>"finalized_by","join"=>"cms_users,name"];
			$this->col[] = ["label"=>"T/g khóa","name"=>"finalized_at","callback_php"=>'date_time_format($row->finalized_at, \'Y-m-d H:i:s\', \'d/m/Y H:i:s\');'];
			$this->col[] = ["label"=>"Cửa hàng","name"=>"brand_id","join"=>"gold_brands,name"];
			$this->col[] = ["label"=>"Ghi chú","name"=>"notes"];
			// $this->col[] = ["label"=>"id","name"=>"id", "style='display: none'"];
			# END COLUMNS DO NOT REMOVE THIS LINE
            $this->search_form = [];
            $this->search_form[] = ["label"=>"Từ ngày", "name"=>"opened_at_from_date", "data_column"=>"opened_at", "search_type"=>"between_from","type"=>"date","width"=>"col-sm-2"];
            $this->search_form[] = ["label"=>"Đến ngày", "name"=>"opened_at_to_date", "data_column"=>"opened_at", "search_type"=>"between_to","type"=>"date","width"=>"col-sm-2"];

			# START FORM DO NOT REMOVE THIS LINE
			$this->form = [];
			$this->form[] = ["label"=>"Counter No","name"=>"counter_no","type"=>"text","required"=>TRUE,"validation"=>"required|min:1|max:255"];
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
			$this->addaction[] = ['label'=>'In sổ','url'=>CRUDBooster::mainpath('print-counter/[id]'),'icon'=>'fa fa-print','color'=>'info'];
            // $this->addaction[] = ['label'=>'Bảng kê','url'=>CRUDBooster::mainpath('print-counter-detail/[id]'),'icon'=>'glyphicon glyphicon-list-alt','color'=>'success'];

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
			$data = [];
			$data['page_title'] = 'Tạo mới sổ tính tiền';
			$data += ['mode' => 'new'];
			$this->cbView('counter_form', $data);
        }

        public function getEdit($id){
           	$data = [];
            $data['page_title'] = 'Sửa sổ tính tiền';
            $data += ['mode' => 'edit', 'resume_id' => $id];
            $this->cbView('counter_form', $data);
        }

        public function getDetail($id){
            $data = [];
            $data['page_title'] = 'Xem sổ tính tiền';
            $data += ['mode' => 'view', 'resume_id' => $id];
            $this->cbView('counter_form', $data);
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
                $query->where('saler_id', CRUDBooster::myId());
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
		
		public function getResumeCounter(){
            $this->cbLoader();
            if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
			$para = Request::all();
			$id = $para['id'];
			$counter = DB::table('gold_counters AS C')
				->leftJoin('cms_users as S', 'C.saler_id', '=', 'S.id')
				->leftJoin('cms_users as F', 'C.finalized_by', '=', 'F.id')
				->where('C.id', $id)
				->select('C.id',
                    'C.counter_no',
                    'C.opened_at',
                    'C.closed_at',
                    'C.description',
                    'C.status',
					'C.saler_id',
					'S.name AS saler',
                    'C.advance_amount',
					'C.sales_amount',
					'C.bank_amount',
                    'C.purchase_amount',
					'C.pawn_amount',
					'C.interested_amount',
					'C.liquidation_amount',
					'C.withdrawal_in',
					'C.withdrawal_out',
                    'C.amount',
					'C.finalized_by',
					'F.name AS finalized',
                    'C.finalized_at',
                    'C.notes')
				->first();
			$sales = DB::table('gold_sale_orders')
				->whereRaw('deleted_at is null')
				->where('counter_id', $id)
				->select('order_no AS trans_no',
					'order_date AS trans_date',
					'pay_gold_amount AS purchase_amount',
					DB::raw('1 AS trans_type, 0 as pawn_amount, 0 as interested_amount, 0 AS liquidation_amount, 0 AS withdrawal_in, 0 AS withdrawal_out,
						CASE WHEN payment_method = 0 THEN (gold_amount + fee - discount_amount - reduce - use_points + balance) ELSE 0 END AS sales_amount,
						CASE WHEN payment_method = 1 THEN (gold_amount + fee - discount_amount - reduce - use_points + balance) ELSE 0 END AS bank_amount'))
				->get();
			$purchase = DB::table('gold_purchase_orders')
				->whereRaw('deleted_at is null')
				->where('counter_id', $id)
				->select('order_no AS trans_no',
					'order_date AS trans_date',
					DB::raw('2 AS trans_type, 0 as sales_amount, 0 as bank_amount, 0 as pawn_amount, 0 as interested_amount, 0 AS liquidation_amount, 0 AS withdrawal_in, 0 AS withdrawal_out,
						(amount - fee) AS purchase_amount'))
				->get();
			$pawn = DB::table('gold_pawn_orders')
				->whereRaw('deleted_at is null')
				->where('counter_id', $id)
				->select('order_no AS trans_no',
					'order_date AS trans_date',
					'amount AS pawn_amount',
					DB::raw('3 AS trans_type, 0 as sales_amount, 0 as bank_amount, 0 as purchase_amount, 0 AS liquidation_amount, 0 as interested_amount, 0 AS withdrawal_in, 0 AS withdrawal_out'))
				->get();
			$interested = DB::table('gold_pawn_order_interested AS I')
				->join('gold_pawn_orders AS P', 'I.order_id', '=', 'P.id')
				->whereRaw('I.deleted_at is null AND P.deleted_at is null')
				->where('I.counter_id', $id)
				->select('I.interested_no AS trans_no',
					'I.interested_date AS trans_date',
					DB::raw('4 AS trans_type, 0 as sales_amount, 0 as bank_amount, 0 as purchase_amount, 0 AS pawn_amount, 0 AS withdrawal_in, 0 AS withdrawal_out,
						CASE WHEN P.liquidation_method = 0 THEN P.amount ELSE 0 END AS liquidation_amount,
						CASE WHEN P.liquidation_method = 0 THEN I.amount - P.amount ELSE I.amount END AS interested_amount'))
				->get();
			$withdrawal = DB::table('gold_vouchers AS I')
				->whereRaw('I.deleted_at is null AND I.order_type = 2')
				->where('I.counter_id', $id)
				->select('I.order_no AS trans_no',
					'I.order_date AS trans_date',
					DB::raw('5 AS trans_type, 0 as sales_amount, 0 as bank_amount, 0 as purchase_amount, 0 AS pawn_amount, 0 AS liquidation_amount, 0 AS interested_amount,
						round(I.amount * (1 - I.bank_fee / 100)) AS withdrawal_in, round(I.amount * (1 - (I.bank_fee + I.fee) / 100)) AS withdrawal_out'))
				->get();
			// Log::debug('$sales = ' . Json::encode($sales));
            return ['counter'=>$counter, 'sales'=>$sales, 'purchase'=>$purchase, 'pawn'=>$pawn, 'interested'=>$interested, 'withdrawal'=>$withdrawal];
		}

		public function getOpenCounter(){
            $this->cbLoader();
            if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }
			$para = Request::all();
			$counter = DB::table('gold_counters')
				->whereRaw('deleted_at is null AND closed_at is null')
				->where('saler_id', CRUDBooster::myId())
				->first();
            return ['counter'=>$counter];
		}
		
		private function updateCounterHeader($counter) {
            if( $counter['id'] && intval($counter['id']) > 0) // update order
            {
                $counter_id = intval($counter['id']);
                $counter['updated_at'] = date('Y-m-d H:i:s');
                $counter['updated_by'] = CRUDBooster::myId();
                unset($counter['id']);
                unset($counter['created_at']);
                unset($counter['created_by']);
                DB::table('gold_counters')->where('id', $counter_id)->update($counter);
                // Log::debug('$counter_id = ' . $counter_id);
            }
            return $counter_id;
		}
		
        public function postUpdateCounterHeader() {
            $this->cbLoader();
            if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
                CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
                CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
            }

            DB::beginTransaction();
            try {
                $para = Request::all();
                Log::debug('$para = ' . Json::encode($para));
				$counter = $para['counter'];

				Log::debug('Add counter = ' . Json::encode($counter));
				if( $counter['id'] && intval($counter['id']) > 0) // update order
                {
                    $counter_no = $counter['counter_no'];
                    $counter_id = $this->updateCounterHeader($counter);
                }else{
                    $date_str = $counter['opened_at'];
					$date = DateTime::createFromFormat('Y-m-d H:i:s', $date_str);
                    // get new counter no
                    $last_order = DB::table('gold_counters')
						->where('opened_at', '>=', $date->format('Y-m-d') . ' 00:00:00')
                        ->where('opened_at', '<=', $date->format('Y-m-d') . ' 23:59:59')
                        ->orderBy('counter_no', 'desc')
                        ->first();
                    if($last_order) {
                        $old_no = intval(explode('-', $last_order->counter_no)[1]);
                        $counter_no = '000'.($old_no + 1);
                        $counter_no = substr($counter_no, strlen($counter_no) - 3, 3);
                        $counter_no = 'STT'.$date->format('ymd').'-'.$counter_no;
                    }else{
                        $counter_no = 'STT'.$date->format('ymd').'-001';
                    }
                    $counter['counter_no'] = $counter_no;
					$counter['created_by'] = CRUDBooster::myId();
					$counter['brand_id'] = CRUDBooster::myBrand();
                    unset($counter['id']);
                    // Log::debug('Add counter = ' . Json::encode($counter));
                    $counter_id = DB::table('gold_counters')->insertGetId($counter);
                }
            }
            catch( \Exception $e){
                DB::rollback();
                Log::debug('PostAdd error $e = ' . Json::encode($e));
                throw $e;
            }
            DB::commit();
            return response()->json(['id'=>$counter_id, 'counter_no'=>$counter_no]);
		}
		
        public function getPrintCounter($id) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'STT'.time();
            $parameter = [
                'id'=>$id,
                'logo'=>storage_path().'/app/uploads/logo.png'
            ];
            $input = base_path().'/app/Reports/rpt_counter.jasper';
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
        
        public function getPrintCounterDetail($id) {
            $jasper = new JasperPHP();
            $database = \Config::get('database.connections.mysql');
            $filename = 'CTSTT-'.time();
            $parameter = [
                'id'=>$id,
                'logo'=>storage_path().'/app/uploads/logo.png'
            ];
            $input = base_path().'/app/Reports/rpt_counter_detail.jasper';
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