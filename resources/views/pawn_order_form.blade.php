<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- test content here.-->
	<div>
		<p><a title="Return" href="{{CRUDBooster::mainpath()}}"><i class="fa fa-chevron-circle-left "></i> &nbsp; Quay lại danh sách</a></p>
		<div class='panel panel-default'>
			<div class='panel-heading'>
                <strong><i class="fa fa-list-alt"></i> THÔNG TIN CHUNG</strong>
			</div>

			<div class="panel-body" id="parent-form-area">
				<form method='post' action='{{CRUDBooster::mainpath('add-save')}}' id="form">
					<input type="hidden" name="id" id="id">
					<input type="hidden" name="purchase_id" id="purchase_id">
                    <input type="hidden" name="status" id="status">
					<div class="col-sm-12">
						<div class="row">
							<label for="customer_code" class="control-label col-sm-1">KH <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-11">
								<div class="input-group">
									<span class="input-group-btn">
										<button id="btn_search_customer" type="button" class="btn btn-primary btn-flat" onclick="showModalcustomer_id()"><i class="fa fa-search"></i></button>
									</span>
									<input required type="text" name="customer_code" id="customer_code" onchange="searchCustomer();" class="form-control" placeholder="Mã KH" style="width: 10%">
									<input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Tên khách hàng" style="width:25%">
									<input type="text" name="customer_address" id="customer_address" class="form-control" placeholder="Địa chỉ" style="width:50%">
                                    <input type="text" name="customer_phone" id="customer_phone" class="form-control" placeholder="Số ĐT" style="width:15%">
									<input type="hidden" name="customer_id" id="customer_id">
								</div>
							</div>
						</div>
                        <div class="row">
                            <label class="control-label col-sm-1">T/g cầm</label>
                            <div class="col-sm-2">
                                <div class="input-group" >
                                    <input id="order_date" readonly type="text" class="form-control bg-white" onchange="order_date_change()" required>
                                    <div class="input-group-addon bg-gray">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                            <label class="control-label col-sm-1 text-right">Số phiếu</label>
                            <div class="col-sm-2">
                                <input type="text" name="order_no" id="order_no" class="form-control" placeholder="Tự động tạo" readonly disabled>
                            </div>
                            <label class="control-label col-sm-2 text-right">Thời hạn (ngày) <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                            <div class="col-sm-1">
                                <input type="text" name="due_date" id="due_date" onchange="due_date_change()" class="form-control money">
                            </div>
                            <label class="control-label col-sm-2 text-right">T/h tối thiểu (ngày)</label>
                            <div class="col-sm-1">
                                <input type="text" name="min_days" id="min_days" class="form-control money">
                            </div>
                        </div>
                        <div class="row">
							<label for="investor_code" class="control-label col-sm-1">Nhà ĐT <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-8">
								<div class="input-group">
									<span class="input-group-btn">
										<button id="btn_search_investor" type="button" class="btn btn-primary btn-flat" onclick="showModalinvestor_id()"><i class="fa fa-search"></i></button>
									</span>
									<input required type="text" name="investor_code" id="investor_code" onchange="searchInvestor();" class="form-control" placeholder="Mã ĐT" style="width: 15%">
									<input type="text" name="investor_name" id="investor_name" class="form-control" placeholder="Tên nhà đầu tư" style="width:85%" disabled>
									<input type="hidden" name="investor_id" id="investor_id">
								</div>
							</div>
                            <label class="control-label col-sm-1 text-right">Tiền lãi</label>
                            <div class="col-sm-2">
                                <input type="text" name="interested" id="interested" class="form-control money" readonly disabled>
                            </div>
						</div>
                        <div class="row">
                            <div class="col-sm-6">
                                <strong><i class="fa fa-list-alt"></i> THÔNG TIN CẦM</strong>
                                <table id="table_pawns" class='table table-bordered'>
                                    <thead>
                                    <tr class="bg-success">
                                        <th class="table_col_action">#</th>
                                        <th>Nội dung <span class="text-danger" title="Không được bỏ trống trường này.">*</span></th>
                                        <th class="table_col_amount">Số tiền</th>
                                        <th class="table_col_rate">Lãi suất</th>
                                        <th class="table_col_interested">Tiền lãi/Ngày</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th class="text-center"><a onclick="addNewPawnDetail()" class="text-blue" style="cursor: pointer;"><i class="fa fa-plus"></i></a></th>
                                        <th colspan="3"></th>
                                    </tr>
                                    <tr class="bg-gray-active">
                                        <th colspan="2" class="text-center">Tổng cộng</th>
                                        <th id="total_pawn_amount" class="text-right">0</th>
                                        <th></th>
                                        <th id="total_pawn_interested" class="text-right">0</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-sm-6">
                                <strong><i class="fa fa-list-alt"></i> THÔNG TIN ĐÓNG LÃI</strong>
                                <table id="table_interested" class='table table-bordered'>
                                    <thead>
                                    <tr class="bg-success">
                                        <th style="width: 30px;">Stt</th>
                                        <th style="width: 110px;">Số phiếu</th>
                                        <th style="width: 150px;">Thời gian</th>
                                        <th>Số tiền</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                    <tr class="bg-gray-active">
                                        <th colspan="3" class="text-center">Tổng cộng</th>
                                        <th id="total_interested" class="text-right">0</th>
                                        <th></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <strong><i class="fa fa-list-alt"></i> THÔNG TIN THANH LÝ</strong>
						<div class="row">
                            <label class="control-label col-sm-1">Hình thức</label>
                            <div class="col-sm-2">
                                <input id="liquidation_method" type="text" class="form-control" disabled>
                            </div>
                            <label class="control-label col-sm-1 text-right">Thời gian</label>
                            <div class="col-sm-3">
                                <input id="liquidation_at" type="text" class="form-control" disabled>
                            </div>
                            <label class="control-label col-sm-1 text-right">Nhân viên</label>
                            <div class="col-sm-4">
                                <input id="liquidation_by" type="text" class="form-control" disabled>
                            </div>
						</div>
                        <div class="row">
                            <label class="control-label col-sm-1">Nội dung</label>
                            <div class="col-sm-11">
                                <input type="text" name="liquidation_notes" id="liquidation_notes" class="form-control" disabled>
                            </div>
                        </div>
					</div>
				</form>
			</div>
			<div class="box-footer" style="background: #F5F5F5">
				<div class="form-group">
					<label class="control-label col-sm-2"></label>
					<div class="col-sm-10">
						<a href="{{CRUDBooster::mainpath()}}" class="btn btn-default"><i class="fa fa-chevron-circle-left"></i> Quay về</a>
						@if($mode=='new' || $mode=='edit')
							<button id="save_button" class="btn btn-success" onclick="submit(true)"><i class="fa fa-save"></i> Lưu</button>
						@endif
						<a id="print_invoice" style="display: none;cursor: pointer;" onclick="printInvoice()" class="btn btn-info"><i class="fa fa-print"></i> In hợp đồng</a>
						<a id="print_label" style="display: none;cursor: pointer;" onclick="printLabel()" class="btn btn-info"><i class="fa fa-print"></i> In nhãn</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal-datamodal-customer_id" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">
		<div class="modal-dialog modal-lg " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><i class="fa fa-search"></i> Browse Data | Khách hàng</h4>
				</div>
				<div class="modal-body">
					<iframe id="iframe-modal-customer_id" style="border:0;height: 430px;width: 100%"></iframe>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>
    <div id="modal-datamodal-investor_id" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">
		<div class="modal-dialog modal-lg " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><i class="fa fa-search"></i> Browse Data | Nhà đầu tư</h4>
				</div>
				<div class="modal-body">
					<iframe id="iframe-modal-investor_id" style="border:0;height: 430px;width: 100%"></iframe>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>
	<div class="loading"></div>
@endsection

@push('bottom')
	<style>
		.form-divider {
			/*padding: 10px 0px 10px 0px;*/
			margin-bottom: 10px;
			border-bottom: 1px solid #dddddd;
            /* background-color: #3c8dbc; */
		}
        .table_col_action{
			width: 20px;
		}
        .table_col_rate{
			width: 70px;
		}
        .table_col_interested{
			width: 100px;
		}
        .table_col_amount{
			width: 110px;
		}
		.row{
			margin-bottom: 5px;
		}
		.table thead tr th{
			text-align: center;
			vertical-align: middle;
		}
		select.form-control{
			border-radius: 0 !important;
		}
		.invalid{
			border-color: red !important;
		}
		.loading{
			display: none;
		}
		.money{
			text-align: right;
		}
		.hide_invalid_actual_weight{
			display: none;
		}
	</style>

	<script type="application/javascript">
        order_id = null; // sẽ có khi lưu thành công
        readOnlyAll = false;
        order_pawns = [];
        total_pawn = {
			amount: 0,
            interested_amount: 0,
            interested: 0,
            interested_days: 0,
            due_date: 30,
            rate: 0
        };
        optionNumberInput = {
            allowDecimalPadding: false,
            decimalPlaces: 4,
            decimalPlacesRawValue: 4,
            leadingZero: "allow",
            modifyValueOnWheel: false,
            negativeSignCharacter: "−",
            outputFormat: "number"
        };
        counter = null;
        
        $(function(){
            sessionTimeout = Number('{{Config::get('session.lifetime') * 60}}');
			setTimeout(function () {
                swal(
                    	{
							title: "Thông báo",
							text: "Phiên đăng nhập của bạn đã hết hạn, vui lòng đăng nhập lại.",
							type: "danger"
						},
						function() {
                    	    window.location = '{{CRUDBooster::adminPath()}}/logout';
                    	}
					);
            }, sessionTimeout * 1000);
            AutoNumeric.multiple('.money', optionNumberInput);
            if($('#order_date').val() == ''){
                $('#order_date').val(moment().format('DD/MM/YYYY HH:mm:ss')).trigger('change');
            }
            $('#order_date').datetimepicker({
                format:'d/m/Y H:i:s',
                autoclose:true,
                todayHighlight:true,
                showOnFocus:false,
                step: 5
            });

            $.ajax({
                method: "GET",
                url: '{{Route("AdminGoldCountersControllerGetOpenCounter")}}',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: false,
                success: function (data) {
                    if (data && data.counter) {
                        counter = data.counter;
                    }
                },
                error: function (request, status, error) {
                    console.log('PostAdd status = ', status);
                    console.log('PostAdd error = ', error);
                }
            });

            let resume_id = getUrlParameter('resume_id') ? getUrlParameter('resume_id') : '{{$resume_id}}';
            if(resume_id) {
                resume(resume_id);
            }else{
                $('#investor_code').val('NDT01').trigger('change');
                console.log('tự động chọn NDT01');
            }
        });
        
		function resume(id) {
            $.ajax({
                method: "GET",
                url: '{{CRUDBooster::mainpath('resume-order')}}',
                data: {
                    order_id: id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: true,
                success: function (data) {
                    if (data){
                        if(data.customer){
                            $('#customer_id').val(data.customer.id);
                            $('#customer_code').val(data.customer.code);
                            $('#customer_name').val(data.customer.name);
                            $('#customer_address').val(data.customer.address);
                            $('#customer_phone').val(data.customer.phone);
                        }
                        if(data.investor){
                            $('#investor_id').val(data.investor.id);
                            $('#investor_code').val(data.investor.code);
                            $('#investor_name').val(data.investor.name);
                        }
                        if(data.liquidation){
                            $('#liquidation_by').val(data.liquidation.name);
                        }
						if(data.order){
						    order_id = data.order.id;
                            $('#id').val(order_id);
                            $('#order_date').val(moment(data.order.order_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            $('#order_no').val(data.order.order_no);
                            $('#liquidation_notes').val(data.order.liquidation_notes);
                            if(data.order.liquidation_method == 0){
                                $('#liquidation_method').val('Tất toán');
                            }else if(data.order.liquidation_method == 1){
                                $('#liquidation_method').val('Thanh lý');
                            }
                            const min_days = AutoNumeric.getAutoNumericElement('#min_days');
                            min_days.set(data.order.min_days);
                            total_pawn.due_date = data.order.due_date;
                            const due_date = AutoNumeric.getAutoNumericElement('#due_date');
                            due_date.set(total_pawn.due_date);
                            total_pawn.amount = data.order.amount;
                            total_pawn.interested_amount = data.order.interested_amount;

                            if(data.order.liquidation_at){
                                $('#liquidation_at').val(moment(data.order.liquidation_at, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            }else{
                                curr_date_str = moment().format('YYYY-MM-DD');
                                // if(data.order.last_interested_at){
                                //     curr_date_str = moment(data.order.last_interested_at, 'YYYY-MM-DD').format('YYYY-MM-DD');    
                                // }
                                date_str = moment(data.order.order_date, 'YYYY-MM-DD').format('YYYY-MM-DD');
                                total_pawn.interested_days = ((moment(curr_date_str, 'YYYY-MM-DD') - moment(date_str, 'YYYY-MM-DD')) / 86400000) + 1;
                                // console.log('total_pawn.interested_days = ', total_pawn.interested_days);
                            }

                            if(data.order_pawns && data.order_pawns.length > 0){
                                data.order_pawns.forEach(function (detail, i) {
                                    total_pawn.interested += detail.interested;
                                    loadPawnDetail(detail);
                                });
                                order_pawns = data.order_pawns;
                                // console.log('total_pawn.interested = ', total_pawn.interested);
                                calcTotalOfPawns();
                            }
                            loadInterested(data.order_interested);
                            disableOrder(data.order.status);
                        }
                    }else{
                        $('#customer_name').attr('readonly', false);
                        $('#customer_address').attr('readonly', false);
                        $('#customer_phone').attr('readonly', false);
                        $('#customer_type').val(0);
                    }
                },
                error: function (request, status, error) {
                    swal("Thông báo","Có lỗi xãy ra khi phục hồi đơn hàng, vui lòng thử lại.","warning");
                }
            });
        }

        function getUrlParameter(sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)), sURLVariables = sPageURL.split('&'), sParameterName, i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        };

        function searchCustomer() {
            let customer_code = $('#customer_code').val();
			if(customer_code && customer_code.trim() != ''){
                $.ajax({
                    method: "GET",
                    url: '{{Route("AdminGoldCustomersControllerGetSearchCustomer")}}',
                    data: {
                        customer_code: customer_code,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data){
                            if(data.customer){
                                $('#customer_id').val(data.customer.id);
                                $('#customer_name').val(data.customer.name);
                                $('#customer_address').val(data.customer.address);
                                $('#customer_phone').val(data.customer.phone);
                                $('#customer_name').attr('readonly', true);
                                $('#customer_address').attr('readonly', true);
                                $('#customer_phone').attr('readonly', true);
                            }else{
                                $('#customer_name').attr('readonly', false);
                                $('#customer_address').attr('readonly', false);
                                $('#customer_phone').attr('readonly', false);
                            }
                        }else{
                            $('#customer_name').attr('readonly', false);
                            $('#customer_address').attr('readonly', false);
                            $('#customer_phone').attr('readonly', false);
                        }
                    },
                    error: function (request, status, error) {
                        swal("Thông báo","Có lỗi xãy ra khi tải dữ liệu, vui lòng thử lại.","warning");
                    }
                });
			} else {
                $('#customer_id').val(null);
                $('#customer_name').val(null);
                $('#customer_address').val(null);
                $('#customer_phone').val(null);
			}
        }

        function showModalcustomer_id() {
            var url_customer_id = "{{action('AdminGoldPawnOrdersController@getModalData')}}/gold_pawn_orders/modal-data?table=gold_customers&columns=id,code,name,address,phone&name_column=customer_id&where=deleted_at+is+null&select_to=code:code&columns_name_alias=Mã khách hàng,Tên khách hàng,Địa chỉ,Số điện thoại";
            $('#iframe-modal-customer_id').attr('src',url_customer_id);
            $('#modal-datamodal-customer_id').modal('show');
        }
        function hideModalcustomer_id() {
            $('#modal-datamodal-customer_id').modal('hide');
        }
        function selectAdditionalDatacustomer_id(select_to_json) {
			if(select_to_json.code){
                $('#customer_code').val(select_to_json.code).trigger('change');
			}
            hideModalcustomer_id();
        }

        function searchInvestor() {
            let investor_code = $('#investor_code').val();
			if(investor_code && investor_code.trim() != ''){
                $.ajax({
                    method: "GET",
                    url: '{{Route("AdminGoldInvestorsControllerGetSearchInvestor")}}',
                    data: {
                        code: investor_code,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data){
                            if(data.investor){
                                $('#investor_id').val(data.investor.id);
                                $('#investor_name').val(data.investor.name);
                            }
                        }
                    },
                    error: function (request, status, error) {
                        swal("Thông báo","Có lỗi xãy ra khi tải dữ liệu, vui lòng thử lại.","warning");
                    }
                });
			} else {
                $('#investor_id').val(null);
                $('#investor_name').val(null);
			}
        }

        function showModalinvestor_id() {
            var url_investor_id = "{{action('AdminGoldPawnOrdersController@getModalData')}}/gold_pawn_orders/modal-data?table=gold_investors&columns=id,code,name,address,phone&name_column=investor_id&where=deleted_at+is+null&select_to=code:code&columns_name_alias=Mã nhà đầu tư,Tên đầu tư,Địa chỉ,Điện thoại";
            $('#iframe-modal-investor_id').attr('src',url_investor_id);
            $('#modal-datamodal-investor_id').modal('show');
        }
        function hideModalinvestor_id() {
            $('#modal-datamodal-investor_id').modal('hide');
        }
        function selectAdditionalDatainvestor_id(select_to_json) {
			if(select_to_json.code){
                $('#investor_code').val(select_to_json.code).trigger('change');
			}
            hideModalinvestor_id();
        }
		
        function loadPawnDetail(data) {
            let html = `<tr id="order_pawn_index_${data.id}">
                    <th class="table_col_action text-center"><a disabled onclick="removePawnDetail(${data.id})" class="text-red" style="cursor: pointer;"><i class="fa fa-remove"></i></a></th>
                    <th class="no-padding"><input disabled id="pawn${data.id}_description" onchange="pawn_description_change(${data.id})" type="text" class="form-control" value="${data.description}" required></th>
                    <th class="no-padding"><input disabled id="pawn${data.id}_amount" onchange="pawn_amount_change(${data.id})" type="text" class="form-control money" value="${data.amount}"></th>
                    <th class="table_col_rate no-padding"><input disabled id="pawn${data.id}_rate" onchange="pawn_rate_change(${data.id})" type="text" class="form-control money" value="${data.rate}"></th>
                    <th class="text-right" id="pawn${data.id}_interested">${total_pawn.interested.toLocaleString('en-US')}</th>
                </tr>`;
            $('#table_pawns tbody').append(html);

            setTimeout(function () {
                AutoNumeric.multiple(`#order_pawn_index_${data.id} .money`, optionNumberInput);
            },100);
        }

        function addNewPawnDetail() {
            if(readOnlyAll){
                swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu phiếu nhập, hãy tạo phiếu nhập mới.", "warning");
                return;
			}

            let tmp_id = - (order_pawns.length + 1);
            let html = `<tr id="order_pawn_index_${tmp_id}">
                    <th class="table_col_action text-center"><a onclick="removePawnDetail(${tmp_id})" class="text-red" style="cursor: pointer;"><i class="fa fa-remove"></i></a></th>
                    <th class="no-padding"><input id="pawn${tmp_id}_description" onchange="pawn_description_change(${tmp_id})" type="text" class="form-control" required></th>
                    <th class="no-padding"><input id="pawn${tmp_id}_amount" onchange="pawn_amount_change(${tmp_id})" type="text" class="form-control money" value="0"></th>
                    <th class="table_col_rate no-padding"><input id="pawn${tmp_id}_rate" onchange="pawn_rate_change(${tmp_id})" type="text" class="form-control money" value="${total_pawn.rate}"></th>
                    <th class="text-right" id="pawn${tmp_id}_interested">0</th>
                </tr>`;
            $('#table_pawns tbody').append(html);

            order_pawns.push({
                id: tmp_id, 
                amount: 0,
                interested: 0,
                rate: total_pawn.rate
            });

            setTimeout(function () {
                AutoNumeric.multiple(`#order_pawn_index_${tmp_id} .money`, optionNumberInput);
                $(`#pawn${tmp_id}_description`).focus();
            },100);
        }

        function removePawnDetail(removeId) {
            if(readOnlyAll){
                return;
            }

            let removeIndex = -1;
            let removeAmount = 0;
            let removeInterested = 0;
            order_pawns.forEach(function (detail, index) {
                if(detail.id == removeId) {
                    removeIndex = index;
                    removeAmount = detail.amount;
                    removeInterested = detail.interested;
                }
            });

			$('#order_pawn_index_'+removeId).remove();
			order_pawns.splice(removeIndex, 1);

            total_pawn.amount -= removeAmount;
            total_pawn.interested -= removeAmount;
            calcTotalOfPawns();
        }

        function loadInterested(data) {
            if(data && data.length > 0){
                let html = ``;
                data.forEach(function (detail, i) {
                    html = `<tr>
                            <th class="text-right">${i + 1}</th>
                            <th>${detail.interested_no}</th>
                            <th class="text-center">${moment(detail.interested_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss')}</th>
                            <th class="text-right">${detail.amount.toLocaleString('en-US')}</th>
                            <th>${detail.notes ? detail.notes : ''}</th>
                        </tr>`;
                });
                $('#table_interested tbody').append(html);
            }
            $('#total_interested').html((total_pawn.interested_amount ? total_pawn.interested_amount : 0).toLocaleString('en-US'));
        }

        function order_date_change(){
            curr_date_str = moment().format('YYYY-MM-DD');
            // console.log("curr_date_str = ", curr_date_str);
            date_str = moment($('#order_date').val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
            // console.log("date_str = ", date_str);
            total_pawn.interested_days = ((moment(curr_date_str, 'YYYY-MM-DD') - moment(date_str, 'YYYY-MM-DD')) / 86400000) + 1;
            // console.log("total_pawn.interested_days = ", total_pawn.interested_days);

            $.ajax({
                method: "GET",
                url: '{{Route("AdminGoldInterestRateControllerGetRate")}}',
                data: {
                    apply_time: moment($('#order_date').val(), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: true,
                success: function (data) {
                    if (data && data.rate) {
                        total_pawn.due_date = data.rate.due_date ? data.rate.due_date : 30;
                        total_pawn.rate = data.rate.rate ? data.rate.rate : 0;
                        // console.log('due_date', total_pawn.due_date);
                        if(($(`#due_date`).val() ? Number($(`#due_date`).val()) : 0) == 0){
                            const element = AutoNumeric.getAutoNumericElement('#due_date');
                            element.set(total_pawn.due_date);
                        }

                        if(($(`#min_days`).val() ? Number($(`#min_days`).val()) : 0) == 0){
                            const min_days = AutoNumeric.getAutoNumericElement('#min_days');
                            min_days.set(data.rate.min_days);
                        }
                    }
                },
            });
            calcTotalOfPawns();
        }

        function due_date_change() {
            total_pawn.due_date = $(`#due_date`).val()?Number($(`#due_date`).val().replace(/,/g, '')):0;            
            total_pawn.interested = 0;

            order_pawns.forEach(function (detail, i) {
                detail.interested = Math.round(detail.amount * (detail.rate / total_pawn.due_date / 100));
                total_pawn.interested += detail.interested;
                $(`#pawn${detail.id}_interested`).html(detail.interested.toLocaleString('en-US'));
            });
            calcTotalOfPawns();
        }

        function pawn_description_change(changeId) {
            order_pawns.forEach(function (detail, i) {
                if(detail.id == changeId) {
                    detail.description = $(`#pawn${changeId}_description`).val();
                }
            });
        }

        function pawn_rate_change(changeId) {
            total_pawn.interested = 0;

            order_pawns.forEach(function (detail, i) {
                if(detail.id == changeId) {
                    detail.rate = $(`#pawn${detail.id}_rate`).val()?Number($(`#pawn${detail.id}_rate`).val().replace(/,/g, '')):0;
                    detail.interested = Math.round(detail.amount * (detail.rate / total_pawn.due_date / 100));
                    $(`#pawn${detail.id}_interested`).html(detail.interested.toLocaleString('en-US'));
                }
                total_pawn.interested += detail.interested;
            });
            calcTotalOfPawns();
        }

        function pawn_amount_change(changeId) {
            total_pawn.amount = 0;
            total_pawn.interested = 0;
            
            order_pawns.forEach(function (detail, i) {
                if(detail.id == changeId) {
                    detail.amount = $(`#pawn${detail.id}_amount`).val()?Number($(`#pawn${detail.id}_amount`).val().replace(/,/g, '')):0;
                    detail.interested = Math.round(detail.amount * (detail.rate / total_pawn.due_date / 100));
                    $(`#pawn${detail.id}_interested`).html(detail.interested.toLocaleString('en-US'));
                }
                total_pawn.amount += detail.amount;
                total_pawn.interested += detail.interested;
            });
            calcTotalOfPawns();
        }

        function calcTotalOfPawns() {
            const element = AutoNumeric.getAutoNumericElement('#interested');
            if($('#liquidation_at').val()){
                element.set(0);
            }else{
                element.set(Math.round((total_pawn.interested ? total_pawn.interested : 0) * total_pawn.interested_days) - total_pawn.interested_amount);                
            }

            $('#total_pawn_amount').html((total_pawn.amount ? total_pawn.amount : 0).toLocaleString('en-US'));
            $('#total_pawn_interested').html((total_pawn.interested ? total_pawn.interested : 0).toLocaleString('en-US'));
        }

        function validate() {
			let valid = true;
            $('#form input').removeClass('invalid');
            if(!$('#customer_id').val()) {
                if(!$('#customer_name').val()){
                    valid = false;
                    $('#customer_name').addClass('invalid');
                }
			}
            if(!$('#investor_code').val()){
                valid = false;
                $('#investor_code').addClass('invalid');
                $(`#investor_code`).focus();
            }
            if(!$('#order_date').val()){
                valid = false;
                $('#order_date').addClass('invalid');
                $(`#order_date`).focus();
            }
            if(!$('#min_days').val()){
                valid = false;
                $('#min_days').addClass('invalid');
                $(`#min_days`).focus();
            }
            if(total_pawn.due_date <= 0){
                valid = false;
                $('#due_date').addClass('invalid');
                $(`#due_date`).focus();
            }
            
            order_pawns.forEach(function (detail, i) {
                if(!detail.description) {
                    valid = false;
                    $(`#pawn${detail.id}_description`).addClass('invalid');
                    $(`#pawn${detail.id}_description`).focus();
                }else if(detail.amount == 0) {
                    valid = false;
                    $(`#pawn${detail.id}_amount`).addClass('invalid');
                    $(`#pawn${detail.id}_amount`).focus();
                }else if(detail.rate == 0) {
                    valid = false;
                    $(`#pawn${detail.id}_rate`).addClass('invalid');
                    $(`#pawn${detail.id}_rate`).focus();
                }
            });

            if(!valid) {
                swal("Thông báo", "Dữ liệu chưa được nhập đầy đủ, vui lòng kiểm tra lại.", "warning");
            }else if(!order_pawns || order_pawns.length <= 0){
                valid = false;
                swal("Thông báo", "Chưa nhập hàng cầm, vui lòng kiểm tra lại.", "warning");
            }
			return valid;
        }

        function getOrderHeader(finish) {
            return {
                id: $('#id').val() ? Number($('#id').val()) : null,
                status: finish ? 1 : 0, //0 = Đang nhập, 1 = Hoàn tất
                customer_id: $('#customer_id').val() ? Number($('#customer_id').val()) : null,
                investor_id: $('#investor_id').val() ? Number($('#investor_id').val()) : null,
                saler_id: Number('{{CRUDBooster::myId()}}'),
                order_date: moment($('#order_date').val(), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                order_no: $('#order_no').val() ? $('#order_no').val() : null,
                min_days: $('#min_days').val() ? Number($('#min_days').val()) : 0,
                due_date: total_pawn.due_date,
                amount: total_pawn.amount,
                interested_amount: total_pawn.interested_amount,
            };
		}

        function submit(finish) {
			if(!finish || validate()){ // nếu finish == false thì không validate
                if(!counter){
                    swal("Thông báo", "Bạn phải mở sổ tính tiền trước, vui lòng kiểm tra lại.", "warning");
                    return;
                }

                if(finish) {
                    $('#save_button').hide();
                    $('.loading').show();
                }

                $.ajax({
                    method: "POST",
                    url: '{{CRUDBooster::mainpath('add-save')}}',
                    data: {
                        order: getOrderHeader(finish),
                        customer: {
                            code: $('#customer_code').val(),
                            name: $('#customer_name').val(),
                            phone: $('#customer_phone').val(),
                            address: $('#customer_address').val()
						},
                        order_pawns: order_pawns,
                        counter: counter,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data) {
                            if(finish) {
                                disableOrder(1);
                            }
    						order_id = data.id;
							$('#id').val(order_id);
                            $('#order_no').val(data.order_no);
                            $('#customer_id').val(data.customer_id);
                        }
                        $('.loading').hide();
                    },
                    error: function (request, status, error) {
                        $('.loading').hide();
                        console.log('PostAdd status = ', status);
                        console.log('PostAdd error = ', error);
                        swal("Thông báo", "Có lỗi xãy ra khi lưu dữ liệu, vui lòng thử lại.", "error");
                        $('#save_button').show();
                    }
                });
			} else {
                $('#save_button').show();
			}
        }

        function disableOrder(status) {
            readOnlyAll = true;
            $('#print_invoice').show();
            $('#print_label').show();
            $('#btn_search_customer').attr('disabled', true);
            $('#btn_search_investor').attr('disabled', true);
            $('#save_button').hide();
            $('#form input').attr('disabled', true);
        }

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }

        function printInvoice() {
            if(order_id) {
                popupWindow("{{action('AdminGoldPawnOrdersController@getPrintInvoice')}}/" + order_id,"print");
            }else{
                alert("Bạn không thể in hóa đơn nếu chưa lưu phiếu!");
            }
        }

        function printLabel() {
            if(order_id) {
                popupWindow("{{action('AdminGoldPawnOrdersController@getPrintLabel')}}/" + order_id,"print");
            }else{
                alert("Bạn không thể in nhãn nếu chưa lưu phiếu!");
            }
        }
	</script>
@endpush