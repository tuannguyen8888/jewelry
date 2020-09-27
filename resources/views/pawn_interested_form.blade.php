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
					<div class="col-sm-12">
						<div class="row">
							<label for="order_no" class="control-label col-sm-1">Số HĐ <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-2">
								<div class="input-group">
									<span class="input-group-btn">
										<button id="btn_search_order_no" type="button" class="btn btn-primary btn-flat" onclick="showModalorder_id()"><i class="fa fa-search"></i></button>
									</span>
									<input required type="text" name="order_no" id="order_no" onchange="searchOrder();" class="form-control" placeholder="Số hợp đồng">
								</div>
							</div>
                            <label class="control-label col-sm-1 text-right">T/g cầm</label>
                            <div class="col-sm-2">
                                <input type="text" name="order_date" id="order_date" class="form-control" disabled>
                            </div>
                            <label class="control-label col-sm-2 text-right">Thời hạn (ngày)</label>
                            <div class="col-sm-1">
                                <input type="text" name="due_date" id="due_date" class="form-control money" disabled>
                            </div>
                            <label class="control-label col-sm-2 text-right">T/h tối thiểu (ngày)</label>
                            <div class="col-sm-1">
                                <input type="text" name="min_days" id="min_days" class="form-control money" disabled>
                            </div>
						</div>
						<div class="row">
							<label for="customer_code" class="control-label col-sm-1">KH</label>
							<div class="col-sm-11">
								<div class="input-group">
									<span class="input-group-btn">
										<button id="btn_search_customer" type="button" class="btn btn-primary btn-flat" disabled><i class="fa fa-search"></i></button>
									</span>
									<input type="text" name="customer_code" id="customer_code" class="form-control" placeholder="Mã KH" style="width: 15%" disabled>
									<input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Tên khách hàng" style="width:25%" disabled>
									<input type="text" name="customer_address" id="customer_address" class="form-control" placeholder="Địa chỉ" style="width:45%" disabled>
                                    <input type="text" name="customer_phone" id="customer_phone" class="form-control" placeholder="Số ĐT" style="width:15%" disabled>
								</div>
							</div>
						</div>
                        <div class="row">
							<label for="investor_code" class="control-label col-sm-1">Nhà ĐT</label>
							<div class="col-sm-5">
								<div class="input-group">
									<span class="input-group-btn">
										<button id="btn_search_investor" type="button" class="btn btn-primary btn-flat" disabled><i class="fa fa-search"></i></button>
									</span>
									<input type="text" name="investor_code" id="investor_code" class="form-control" placeholder="Mã ĐT" style="width:35%" disabled>
									<input type="text" name="investor_name" id="investor_name" class="form-control" placeholder="Tên ĐT" style="width:65%" disabled>
								</div>
							</div>
                            <label class="control-label col-sm-1 text-right">Giảm lãi</label>
                            <div class="col-sm-2">
                                <input type="text" name="interest_reduced_amount" id="interest_reduced_amount" class="form-control money">
                            </div>
                            <label class="control-label col-sm-1 text-right">Lãi tạm tính</label>
                            <div class="col-sm-2">
                                <input type="text" name="estimate_amount" id="estimate_amount" class="form-control money" disabled>
                            </div>
						</div>
                        <div class="row">
                            <label class="control-label col-sm-1">T/g đóng</label>
                            <div class="col-sm-2">
                                <div class="input-group" >
                                    <input id="interested_date" readonly type="text" class="form-control bg-white" onchange="interested_date_change()" required>
                                    <div class="input-group-addon bg-gray">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                            <label class="control-label col-sm-1 text-right">Số phiếu</label>
                            <div class="col-sm-2">
                                <input type="text" name="interested_no" id="interested_no" class="form-control" placeholder="Tự động tạo" disabled>
                            </div>
                            <label class="control-label col-sm-1 text-right">Số ngày</label>
                            <div class="col-sm-1">
                                <input type="text" name="days" id="days" class="form-control money" disabled>
                            </div>
                            <label class="control-label col-sm-2 text-right">Tiền khách trả <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                            <div class="col-sm-2">
                                <input type="text" name="amount" id="amount" class="form-control money">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <strong><i class="fa fa-list-alt"></i> THÔNG TIN CẦM</strong>
                                <table id="table_pawns" class='table table-bordered'>
                                    <thead>
                                    <tr class="bg-success">
                                        <th style="width: 30px">Stt</th>
                                        <th>Nội dung</th>
                                        <th class="table_col_amount">Số tiền</th>
                                        <th class="table_col_rate">Lãi suất</th>
                                        <th class="table_col_interested">Tiền lãi/Ngày</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
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
                                <strong><i class="fa fa-list-alt"></i> LỊCH SỬ ĐÓNG LÃI</strong>
                                <table id="table_interested" class='table table-bordered'>
                                    <thead>
                                    <tr class="bg-success">
                                        <th style="width: 30px">Stt</th>
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
                            <label class="control-label col-sm-1">Hình thức <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                            <div class="col-sm-2">
                                <select id="liquidation_method" class="form-control" placeholder="Chọn hình thức thanh lý">
                                    <option value=-1></option>
                                    <option value=3>Đóng lãi</option>
                                    <option value=0>Tất toán</option>
                                    <option value=1>Thanh lý</option>
                                </select>
                            </div>
                            <label class="control-label col-sm-1 text-right">Nội dung</label>
                            <div class="col-sm-8">
                                <input type="text" name="liquidation_notes" id="liquidation_notes" class="form-control" placeholder="Nội dung thanh lý">
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
						<a id="print_invoice" style="display: none;cursor: pointer;" onclick="printInvoice()" class="btn btn-info"><i class="fa fa-print"></i> In phiếu</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal-datamodal-order_id" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">
		<div class="modal-dialog modal-lg " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><i class="fa fa-search"></i> Browse Data | Hợp đồng</h4>
				</div>
				<div class="modal-body">
					<iframe id="iframe-modal-order_id" style="border:0;height: 430px;width: 100%"></iframe>
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
        interested_id = null; // sẽ có khi lưu thành công
        interested = 0;
        pawn_detail = [];
        pawn_interested = [];
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
        order = null;

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
            if($('#interested_date').val() == ''){
                $('#interested_date').val(moment().format('DD/MM/YYYY HH:mm:ss')).trigger('change');
            }
            $('#interested_date').datetimepicker({
                format:'d/m/Y H:i:s',
                autoclose:true,
                todayHighlight:true,
                showOnFocus:false
            });
            $('#liquidation_method').change(calcAmount);
            $('#interest_reduced_amount').change(calcAmount);
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
                        // console.log('counter = ', counter);
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
            }
        });

        function calcAmount() {
            if ($('#liquidation_method').val() == 3) // đóng lãi
            {
                let estimate_amount = $('#estimate_amount').val() ? Number($('#estimate_amount').val().replace(/,/g, '')) : 0;
                let interest_reduced_amount = $('#interest_reduced_amount').val() ? Number($('#interest_reduced_amount').val().replace(/,/g, '')) : 0;
                const amountInput = AutoNumeric.getAutoNumericElement('#amount');
                amountInput.set(estimate_amount - interest_reduced_amount);
            } else if ($('#liquidation_method').val() == 0) // tất toán
            {
                let estimate_amount = $('#estimate_amount').val() ? Number($('#estimate_amount').val().replace(/,/g, '')) : 0;
                let amount = ((order && order.amount) ? order.amount : 0) + estimate_amount - interest_reduced_amount;
                const amountInput = AutoNumeric.getAutoNumericElement('#amount');
                amountInput.set(amount);
            }
        }
		function resume(id) {
            $.ajax({
                method: "GET",
                url: '{{CRUDBooster::mainpath('resume-order')}}',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: true,
                success: function (data) {
                    if (data && data.interested){
                        interested_id = data.interested.id;
                        $('#id').val(interested_id);
                        $('#interested_date').val(moment(data.interested.interested_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                        $('#interested_no').val(data.interested.interested_no);
                        const days = AutoNumeric.getAutoNumericElement('#days');
                        days.set(data.interested.due_date);
                        const estimate_amount = AutoNumeric.getAutoNumericElement('#estimate_amount');
                        estimate_amount.set(data.interested.estimate_amount);
                        const amount = AutoNumeric.getAutoNumericElement('#amount');
                        amount.set(data.interested.amount);
                            
                        if(data.order){
                            order = data.order;
                            $('#order_date').val(moment(order.order_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            $('#order_no').val(order.order_no);
                            order.last_interested_at = data.interested.last_interested_at;
                            const due_date = AutoNumeric.getAutoNumericElement('#due_date');
                            due_date.set(order.due_date);
                            const min_days = AutoNumeric.getAutoNumericElement('#min_days');
                            min_days.set(order.min_days);
                            $('#liquidation_method').val(order.liquidation_method);
                            $('#liquidation_notes').val(order.liquidation_notes);
                        }
                        if(data.customer){
                            $('#customer_code').val(data.customer.code);
                            $('#customer_name').val(data.customer.name);
                            $('#customer_address').val(data.customer.address);
                            $('#customer_phone').val(data.customer.phone);
                        }
                        if(data.investor){
                            $('#investor_code').val(data.investor.code);
                            $('#investor_name').val(data.investor.name);
                        }
                        loadPawnDetail(data.detail);
                        loadInterested(data.order_interested);
                        pawn_detail = data.detail;
                        pawn_interested = data.order_interested;
                        disableOrder();
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

        function searchOrder() {
            let order_no = $('#order_no').val();
			if(order_no && order_no.trim() != ''){
                $.ajax({
                    method: "GET",
                    url: '{{Route("AdminGoldPawnOrdersControllerGetSearchOrder")}}',
                    data: {
                        order_no: order_no,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data && data.order){
                            if(data.order.status == 2){
                                swal("Thông báo","Hợp đồng đã thanh lý, vui lòng chọn lại.","warning");        
                            }else{
                                order = data.order;
                                $('#order_date').val(moment(order.order_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                                $('#order_no').val(order.order_no);
                                const due_date = AutoNumeric.getAutoNumericElement('#due_date');
                                due_date.set(order.due_date);
                                const min_days = AutoNumeric.getAutoNumericElement('#min_days');
                                min_days.set(order.min_days);
                                if(data.customer){
                                    $('#customer_code').val(data.customer.code);
                                    $('#customer_name').val(data.customer.name);
                                    $('#customer_address').val(data.customer.address);
                                    $('#customer_phone').val(data.customer.phone);
                                }
                                if(data.investor){
                                    $('#investor_code').val(data.investor.code);
                                    $('#investor_name').val(data.investor.name);
                                }
                                loadPawnDetail(data.detail);
                                loadInterested(data.interested);
                                interested_date_change();
                                pawn_detail = data.detail;
                                pawn_interested = data.interested;
                            }
                        }else{
                            swal("Thông báo","Hợp đồng không hợp lệ, vui lòng chọn lại.","warning");        
                        }
                    },
                    error: function (request, status, error) {
                        swal("Thông báo","Có lỗi xãy ra khi tải dữ liệu, vui lòng thử lại.","warning");
                    }
                });
			} else {
                $('#order_no').val(null);
                $('#order_date').val(null);
                const due_date = AutoNumeric.getAutoNumericElement('#due_date');
                due_date.set(0);
                const estimate_amount = AutoNumeric.getAutoNumericElement('#estimate_amount');
                estimate_amount.set(0);
                $('#customer_code').val(null);
                $('#customer_name').val(null);
                $('#customer_address').val(null);
                $('#customer_phone').val(null);
			}
        }

        function showModalorder_id() {
            var url_order_no = "{{action('AdminGoldPawnOrderInterestedController@getModalData')}}/gold_pawn_order_interested/modal-data?table=gold_pawn_orders&columns=id,order_no,order_date,due_date&name_column=order_id&where=deleted_at+is+null+and+status+=+1&select_to=order_no:order_no&columns_name_alias=Số HĐ,T/g cầm,Thời hạn";
            $('#iframe-modal-order_id').attr('src',url_order_no);
            $('#modal-datamodal-order_id').modal('show');
        }
        function hideModalorder_id() {
            $('#modal-datamodal-order_id').modal('hide');
        }
        function selectAdditionalDataorder_id(select_to_json) {
			if(select_to_json.order_no){
                $('#order_no').val(select_to_json.order_no).trigger('change');
			}
            hideModalorder_id();
        }
		
        function loadPawnDetail(data) {
            interested = 0;
            if(pawn_detail && pawn_detail.length > 0){
                pawn_detail.forEach(function (detail, i) {
                    $(`#order_pawn_index_${detail.id}`).remove();
                });
            }

            if(data && data.length > 0){
                let html = ``;
                data.forEach(function (detail, i) {
                    html += `<tr id="order_pawn_index_${detail.id}">
                            <th class="text-right">${i + 1}</th>
                            <th>${detail.description}</th>
                            <th class="text-right">${detail.amount.toLocaleString('en-US')}</th>
                            <th class="text-right">${detail.rate.toLocaleString('en-US')}</th>
                            <th class="text-right">${detail.interested.toLocaleString('en-US')}</th>
                        </tr>`;
                        interested += detail.interested;
                });
                $('#table_pawns tbody').append(html);
            }
            $('#total_pawn_amount').html((order.amount ? order.amount : 0).toLocaleString('en-US'));
            $('#total_pawn_interested').html(interested.toLocaleString('en-US'));
        }

        function loadInterested(data) {
            let amount = 0;
            if(pawn_interested && pawn_interested.length > 0){
                pawn_interested.forEach(function (detail, i) {
                    $(`#order_interested_index_${detail.id}`).remove();
                });
            }
            
            if(data && data.length > 0){
                let html = ``;
                data.forEach(function (detail, i) {
                    html += `<tr id="order_interested_index_${detail.id}">
                            <th class="text-right">${i + 1}</th>
                            <th>${detail.interested_no}</th>
                            <th class="text-center">${moment(detail.interested_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss')}</th>
                            <th class="text-right">${detail.amount.toLocaleString('en-US')}</th>
                            <th>${detail.notes ? detail.notes : ""}</th>
                        </tr>`;
                    amount += detail.amount;
                });
                $('#table_interested tbody').append(html);
            }
            $('#total_interested').html(amount.toLocaleString('en-US'));
        }

        function interested_date_change(){
            if(!order){
                return;
            }

            let days = 0;
            interested_date_str = moment($('#interested_date').val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
            // console.log("interested_date_str = ", interested_date_str);
            if(order.last_interested_at){
                date_str = moment(order.last_interested_at, 'YYYY-MM-DD').format('YYYY-MM-DD');
                days = ((moment(interested_date_str, 'YYYY-MM-DD') - moment(date_str, 'YYYY-MM-DD')) / 86400000);
            }else if($('#order_date').val()){
                date_str = moment($('#order_date').val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
                days = ((moment(interested_date_str, 'YYYY-MM-DD') - moment(date_str, 'YYYY-MM-DD')) / 86400000) + 1;
            }
            // console.log("date_str = ", date_str);
            // console.log("days = ", days);
            if(days < order.min_days){
                days = order.min_days;
            }
            const element = AutoNumeric.getAutoNumericElement('#days');
            element.set(days);
            const estimate_amount = AutoNumeric.getAutoNumericElement('#estimate_amount');
            estimate_amount.set(Math.round(days * interested));
        }

        function validate() {
			let valid = true;
            $('#form input').removeClass('invalid');
            if(!$('#order_no').val()){
                valid = false;
                $('#order_no').addClass('invalid');
                $(`#order_no`).focus();
            }
            if(!$('#liquidation_method').val() || $('#liquidation_method').val()==-1){
                valid = false;
                $('#liquidation_method').addClass('invalid');
            }
            if(!$('#interested_date').val()){
                valid = false;
                $('#interested_date').addClass('invalid');
            }
            if(!$('#amount').val()){
                valid = false;
                $('#amount').addClass('invalid');
            }
            
            if(!valid) {
                swal("Thông báo", "Dữ liệu chưa được nhập đầy đủ, vui lòng kiểm tra lại.", "warning");
            }else{
                order.liquidation_method = $('#liquidation_method').val() ? $('#liquidation_method').val() : null;
                let amount = $('#amount').val() ? Number($('#amount').val().replace(/,/g, '')) : 0;
                let estimate_amount = $('#estimate_amount').val() ? Number($('#estimate_amount').val().replace(/,/g, '')) : 0;
                // console.log('order.liquidation_method = ', order.liquidation_method);
                if(order.liquidation_method) {
                    if(order.liquidation_method == 0 && amount < (estimate_amount + order.amount)) {
                        valid = false;
                        swal("Thông báo", "Phải thu đủ tiền gốc và lãi, vui lòng kiểm tra lại.", "warning");
                        $(`#amount`).addClass('invalid');
                        $(`#amount`).focus();
                    }else if(order.liquidation_method == 1 && !$('#liquidation_notes').val()){
                        valid = false;
                        swal("Thông báo", "Bạn phải nhập nội dung thanh lý, vui lòng kiểm tra lại.", "warning");
                        $(`#liquidation_notes`).addClass('invalid');
                        $(`#liquidation_notes`).focus();
                    }
                }else if(amount < estimate_amount){
                    valid = false;
                    swal("Thông báo", "Tiền khách trả phải >= tiền lãi tạm tính, vui lòng kiểm tra lại.", "warning");
                    $(`#amount`).addClass('invalid');
                    $(`#amount`).focus();
                }
            }
			return valid;
        }
        
        function getOrderHeader(finish) {
            return {
                id: $('#id').val() ? Number($('#id').val()) : null,
                interested_date: moment($('#interested_date').val(), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                interested_no: $('#interested_no').val() ? $('#interested_no').val() : null,
                due_date: $('#days').val() ? Number($('#days').val()) : 0,
                interest_reduced_amount: $('#interest_reduced_amount').val() ? Number($('#interest_reduced_amount').val().replace(/,/g, '')) : 0,
                estimate_amount: $('#estimate_amount').val() ? Number($('#estimate_amount').val().replace(/,/g, '')) : 0,
                amount: $('#amount').val() ? Number($('#amount').val().replace(/,/g, '')) : 0,
                last_interested_at: order.last_interested_at,
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
                order.liquidation_notes = $('#liquidation_notes').val() ? $('#liquidation_notes').val() : null;

                $.ajax({
                    method: "POST",
                    url: '{{CRUDBooster::mainpath('add-save')}}',
                    data: {
                        order: order,
                        counter: counter,
                        interested: getOrderHeader(finish),
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data) {
                            $('#interested_no').val(data.interested_no);
                            if(finish) {
                                disableOrder();
                            }
    						interested_id = data.id;
							$('#id').val(interested_id);
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

        function disableOrder() {
            readOnlyPawn = true;
            $('#print_invoice').show();
            $('#print_label').show();
            $('#btn_search_order_no').attr('disabled', true);
            $('#save_button').hide();
            $('#form input').attr('disabled', true);
            $('#liquidation_method').attr('disabled', true);
        }

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }

        function printInvoice() {
            if(interested_id) {
                popupWindow("{{action('AdminGoldPawnOrderInterestedController@getPrintInvoice')}}/" + interested_id,"print");
            }else{
                alert("Bạn không thể in hóa đơn nếu chưa lưu phiếu!");
            }
        }
	</script>
@endpush