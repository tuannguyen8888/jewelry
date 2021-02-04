<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- test content here.-->
	<div>
		<p><a title="Return" href="{{CRUDBooster::mainpath()}}"><i class="fa fa-chevron-circle-left "></i> &nbsp; Quay lại danh sách</a></p>
		<div class='panel panel-default'>
			<div class="panel-body" id="parent-form-area">
				<form method='post' action='{{CRUDBooster::mainpath('add-save')}}' id="form">
					<input type="hidden" name="id" id="id">
					<div class="col-sm-12">
                        <div class="row">
							<label for="customer_code" class="control-label col-sm-2">Khách hàng <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-10">
								<div class="input-group">
									<span class="input-group-btn">
										<button id="btn_search_customer" type="button" class="btn btn-primary btn-flat" onclick="showModalcustomer_id()"><i class="fa fa-search"></i></button>
									</span>
									<input required type="text" name="customer_code" id="customer_code" onchange="searchCustomer();" class="form-control" placeholder="Mã KH" style="width: 10%">
									<input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Tên KH" style="width: 20%">
									<input type="text" name="customer_address" id="customer_address" class="form-control" placeholder="Địa chỉ" style="width: 55%">
                                    <input type="text" name="customer_phone" id="customer_phone" class="form-control" placeholder="Số ĐT" style="width: 15%">
									<input type="hidden" name="customer_id" id="customer_id">
								</div>
							</div>
						</div>
                        <div class="row">
                            <label class="control-label col-sm-2">Số tiền <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                            <div class="col-sm-2">
                                <input type="text" name="amount" id="amount" class="form-control money" onchange="amount_change();">
                            </div>
                            <label class="control-label col-sm-2 text-right">Thời gian chuyển <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                            <div class="col-sm-2">
                                <div class="input-group" >
                                    <input id="order_date" readonly type="text" class="form-control bg-white" placeholder="Ngày đơn hàng" required>
                                    <div class="input-group-addon bg-gray">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                            <label class="control-label col-sm-2 text-right">Số phiếu</label>
                            <div class="col-sm-2">
                                <input type="text" name="order_no" id="order_no" class="form-control" placeholder="Tự động tạo" readonly disabled>
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-sm-2">Mức phí <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                            <div class="col-sm-2">
                                <select id="fee_type" class="form-control" onchange="fee_type_change();"></select>
                            </div>

                            {{--<label class="control-label col-sm-2 text-right">% phí ngân hàng <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>--}}
                            {{--<div class="col-sm-2">--}}
                                {{--<input type="text" name="bank_fee" id="bank_fee" class="form-control money" onchange="amount_change();">--}}
                            {{--</div>--}}
                            <label class="control-label col-sm-2 text-right">Phí ngân hàng</label>
                            <div class="col-sm-2">
                                <input type="text" name="bank_amount" id="bank_amount" class="form-control money" readonly disabled>
                            </div>
                            {{--<label class="control-label col-sm-2">% phí dịch vụ <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>--}}
                            {{--<div class="col-sm-2">--}}
                                {{--<input type="text" name="fee" id="fee" class="form-control money" onchange="amount_change();">--}}
                            {{--</div>--}}
                            <label class="control-label col-sm-2 text-right">Phí dịch vụ</label>
                            <div class="col-sm-2">
                                <input type="text" name="fee_amount" id="fee_amount" class="form-control money" readonly disabled>
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-sm-2">Nội dung <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" name="notes" id="notes" class="form-control">
                            </div>
                            <label class="control-label col-sm-2 text-right">Tổng tiền thu của KH</label>
                            <div class="col-sm-2">
                                <input type="text" name="return_amount" id="return_amount" class="form-control money" readonly disabled>
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
		.row{
			margin-bottom: 5px;
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
	</style>

	<script type="application/javascript">
        order_id = null; // sẽ có khi lưu thành công
        fee_types = [];
        optionNumberInput = {
            allowDecimalPadding: false,
            decimalPlaces: 4,
            decimalPlacesRawValue: 4,
            leadingZero: "allow",
            modifyValueOnWheel: false,
            negativeSignCharacter: "−",
            outputFormat: "number"
        };
        
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
            if($('#order_date').val() == ''){
                $('#order_date').val(moment().format('DD/MM/YYYY HH:mm:ss'));
            }
            $('#order_date').datetimepicker({
                format:'d/m/Y H:i:s',
                autoclose:true,
                todayHighlight:true,
                showOnFocus:false,
                step: 5
            });
            AutoNumeric.multiple('.money', optionNumberInput);

            $.ajax({
                method: "GET",
                url: '{{Route("AdminTransferMoneyFeeTypeControllerGetListAll")}}',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: false,
                success: function (data) {
                    if (data && data.list && data.list.length > 0) {
                        fee_types = data.list;
                        let html = '';
						data.list.forEach(function (detail, i) {
                            html += `<option value=${detail.id}>${detail.name}</option>`;					
                            if(i == 0){
                                AutoNumeric.getAutoNumericElement(`#bank_amount`).set(detail.bank_fee_amount);
                                AutoNumeric.getAutoNumericElement(`#fee_amount`).set(detail.fee_amount);
                            }
                        });
                        $('#fee_type').append(html);
                        amount_change();
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
                    if (data){
                        if(data.customer){
                            $('#customer_id').val(data.customer.id);
                            $('#customer_code').val(data.customer.code);
                            $('#customer_name').val(data.customer.name);
                            $('#customer_address').val(data.customer.address);
                            $('#customer_phone').val(data.customer.phone);
                        }
						if(data.order){
						    order_id = data.order.id;
                            $('#id').val(order_id);
                            $('#order_date').val(moment(data.order.order_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            $('#order_no').val(data.order.order_no);
                            $('#notes').val(data.order.notes);
                            $('#fee_type').val(data.order.card_type_id);
                            AutoNumeric.getAutoNumericElement('#amount').set(data.order.amount);
                            // AutoNumeric.getAutoNumericElement('#bank_fee').set(data.order.bank_fee);
                            AutoNumeric.getAutoNumericElement('#bank_amount').set(Math.round(data.order.bank_fee));
                            // AutoNumeric.getAutoNumericElement('#fee').set(data.order.fee);
                            AutoNumeric.getAutoNumericElement('#fee_amount').set(Math.round(data.order.fee));
                            AutoNumeric.getAutoNumericElement('#return_amount').set(Math.round(data.order.amount + data.order.fee ));

                            disableOrder();
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

        function fee_type_change() {
            if(fee_types && fee_types.length > 0){
                fee_types.forEach(function (detail, i) {
                    if(detail.id == Number($(`#fee_type`).val())) {
                        AutoNumeric.getAutoNumericElement(`#bank_amount`).set(detail.bank_fee_amount);
                        AutoNumeric.getAutoNumericElement(`#fee_amount`).set(detail.fee_amount);
                        amount_change();
                    }
                });
            }
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
                $('#customer_type').val(null);
			}
        }

        function showModalcustomer_id() {
            var url_customer_id = "{{action('AdminTransferMoneyController@getModalData')}}/gold_purchase_orders/modal-data?table=gold_customers&columns=id,code,name,address,phone&name_column=customer_id&where=deleted_at+is+null&select_to=code:code&columns_name_alias=Mã khách hàng,Tên khách hàng,Địa chỉ,Số điện thoại";
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
		
        function amount_change() {
            let amount = $('#amount').val() ? AutoNumeric.getAutoNumericElement(`#amount`).getNumber() : 0;
            if(fee_types.length){
                fee_types.forEach(item=>{
                   if(item.value_from <= amount && amount <= item.value_to){
                       $('#fee_type').val(item.id);
                       // $('#bank_amount').val(item.bank_fee_amount);
                       AutoNumeric.getAutoNumericElement('#bank_amount').set(Math.round(item.bank_fee_amount));
                       // $('#fee_amount').val(item.fee_amount);
                       AutoNumeric.getAutoNumericElement('#fee_amount').set(Math.round(item.fee_amount));
                   }
                });
            }
            let bank_amount = $('#bank_amount').val() ? AutoNumeric.getAutoNumericElement(`#bank_amount`).getNumber() : 0;
            let fee_amount = $('#fee_amount').val() ? AutoNumeric.getAutoNumericElement(`#fee_amount`).getNumber() : 0;

            AutoNumeric.getAutoNumericElement(`#return_amount`).set(Math.round(amount + fee_amount));
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
            if(!$('#order_date').val()){
                valid = false;
                $('#order_date').addClass('invalid');
            }
            if(!$('#fee_type').val()){
                valid = false;
                $('#fee_type').addClass('invalid');
            }
            if(!$('#notes').val()){
                valid = false;
                $('#notes').addClass('invalid');
            }
            if(($('#amount').val() ? AutoNumeric.getAutoNumericElement('#amount').getNumber() : 0) == 0){
                valid = false;
                $('#amount').addClass('invalid');
            }
            if(($('#bank_fee').val() ? AutoNumeric.getAutoNumericElement('#bank_fee').getNumber() : 0) < 0){
                valid = false;
                $('#bank_fee').addClass('invalid');
            }
            if(($('#fee').val() ? AutoNumeric.getAutoNumericElement('#fee').getNumber() : 0) < 0){
                valid = false;
                $('#fee').addClass('invalid');
            }
            
            if(!valid) {
                swal("Thông báo", "Dữ liệu chưa được nhập đầy đủ, vui lòng kiểm tra lại.", "warning");
            }
			return valid;
        }

        function getOrderHeader(finish) {
            return {
                id: $('#id').val() ? Number($('#id').val()) : null,
                status: finish ? 1 : 0, //0 = Đang nhập, 1 = Hoàn tất
                object_id: $('#customer_id').val() ? Number($('#customer_id').val()) : null,
                card_type_id: $('#fee_type').val() ? Number($('#fee_type').val()) : null,
                order_date: moment($('#order_date').val(), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                order_no: $('#order_no').val() ? $('#order_no').val() : null,
                notes: $('#notes').val() ? $('#notes').val() : null,
                amount: $('#amount').val() ? AutoNumeric.getAutoNumericElement('#amount').getNumber() : 0,
                bank_fee: $('#bank_amount').val() ? AutoNumeric.getAutoNumericElement('#bank_amount').getNumber() : 0,
                fee: $('#fee_amount').val() ? AutoNumeric.getAutoNumericElement('#fee_amount').getNumber() : 0
            };
		}

        function submit(finish) {
			if(!finish || validate()){ // nếu finish == false thì không validate
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
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data) {
                            if(finish) {
                                disableOrder();
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

        function disableOrder() {
            $('#print_invoice').show();
            $('#save_button').hide();
            $('#btn_search_customer').attr('disabled', true);
            $('#check_actual_weight').attr('disabled', true);
            $('#form input').attr('disabled', true);
            $('#form textarea').attr('readonly', true);
            $('#form select').attr('disabled', true);
        }

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }

        function printInvoice() {
            if(order_id) {
                popupWindow("{{action('AdminTransferMoneyController@getPrintOrder')}}/" + order_id,"print");
            }else{
                alert("Bạn không thể in hóa đơn nếu chưa lưu phiếu!");
            }
        }
	</script>
@endpush