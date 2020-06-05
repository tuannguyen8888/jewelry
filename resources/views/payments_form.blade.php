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
                            <label class="control-label col-sm-2">Đối tượng</label>
                            <div class="col-sm-2">
                                <select id="object_type" class="form-control" onchange="objectTypeChange();">
                                    <option value=0>Khách hàng</option>
                                    <option value=1>Nhà cung cấp</option>
                                    <option value=2>Nhà đầu tư</option>
                                    <option value=3>Nhân viên</option>
                                </select>
                            </div>
                            <label class="control-label col-sm-2 text-right">T/g chi <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                            <div class="col-sm-2">
                                <div class="input-group" >
                                    <input id="order_date" type="text" class="form-control bg-white" required readonly>
                                    <div class="input-group-addon bg-gray">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                            <label class="control-label col-sm-2 text-right">Số phiếu</label>
                            <div class="col-sm-2">
                                <input type="text" name="order_no" id="order_no" class="form-control" placeholder="Tự động tạo" disabled>
                            </div>
                        </div>
                        <div class="row">
							<label for="object_code" class="control-label col-sm-2">Đối tượng <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-10">
								<div class="input-group">
									<span class="input-group-btn">
										<button id="btn_search_object" type="button" class="btn btn-primary btn-flat" onclick="showModalobject_id()"><i class="fa fa-search"></i></button>
									</span>
									<input type="text" name="object_code" id="object_code" onchange="searchObject();" class="form-control" required style="width: 15%">
									<input type="text" name="object_name" id="object_name" class="form-control" style="width: 85%" disabled>
									<input type="hidden" name="object_id" id="object_id">
								</div>
							</div>
						</div>
                        <div class="row">
                            <label class="control-label col-sm-2">Hình thức chi</label>
                            <div class="col-sm-2">
                                <select id="method" class="form-control">
                                    <option value=0>Tiền mặt</option>
                                    <option value=1>Chuyển khoản</option>
                                </select>
                            </div>
                            <label class="control-label col-sm-2 text-right">Số tiền <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                            <div class="col-sm-2">
                                <input type="text" name="amount" id="amount" class="form-control money">
                            </div>
                            <label class="control-label col-sm-2 text-right">Công nợ</label>
                            <div class="col-sm-2">
                                <input type="text" name="balance" id="balance" class="form-control money" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-sm-2">Nội dung <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="notes" id="notes" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <label class="control-label col-sm-2">Ghi nhận công nợ</label>
                            <div class="col-sm-1">
                                <input id="is_balance" type="checkbox" checked="1" required>
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
						<a id="print_order" style="display: none;cursor: pointer;" onclick="printOrder()" class="btn btn-info"><i class="fa fa-print"></i> Phiếu chi</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal-datamodal-object_id" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">
		<div class="modal-dialog modal-lg " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><i class="fa fa-search"></i> Browse Data | Đối tượng</h4>
				</div>
				<div class="modal-body">
					<iframe id="iframe-modal-object_id" style="border:0;height: 430px;width: 100%"></iframe>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>
	<div class="loading"></div>
@endsection

@push('bottom')
	<style>
        .row{
			margin-bottom: 5px;
		}
		.form-divider {
			margin-bottom: 10px;
			border-bottom: 1px solid #dddddd;
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
                showOnFocus:false
            });

            AutoNumeric.multiple('.money', optionNumberInput);
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
                        if(data.object){
                            $('#object_code').val(data.object.code);
                            $('#object_name').val(data.object.name);
                        }
						if(data.order){
						    order_id = data.order.id;
                            $('#id').val(order_id);
                            $('#object_id').val(data.order.object_id);
                            $('#order_date').val(moment(data.order.order_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            $('#order_no').val(data.order.order_no);
                            $('#object_type').val(data.order.object_type);
                            $('#method').val(data.order.method);
                            $('#notes').val(data.order.notes);
                            AutoNumeric.getAutoNumericElement('#amount').set(data.order.out_amount);
                            AutoNumeric.getAutoNumericElement('#balance').set(data.order.balance);
                            $(`#is_balance`).prop('checked', (data.order.is_balance == 1));
                        }
                        if (data.order && data.order.status == 1){
                            disableOrder();
                        }
                    }
                },
                error: function (request, status, error) {
                    swal("Thông báo","Có lỗi xãy ra khi phục hồi đơn hàng, vui lòng thử lại.","warning");
                }
            });
        }

        function objectTypeChange() {
            $('#object_id').val(null);
            $('#object_code').val(null);
            $('#object_name').val(null);
            AutoNumeric.getAutoNumericElement('#balance').set(0);
            // if(Number($('#object_type').val()) == 3){
            //     $('#is_balance').attr('disabled', false);
            // }else{
            //     $(`#is_balance`).prop('checked', true);
            //     $('#is_balance').attr('disabled', true);
            // }
            $('#object_code').focus();
        }

        function getUrlParameter(sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        };

        function searchObject() {
            let object_code = $('#object_code').val();

			if(object_code && object_code.trim() != ''){
                $.ajax({
                    method: "GET",
                    url: '{{Route("AdminGoldPaymentsControllerGetSearchObject")}}',
                    data: {
                        object_type: $('#object_type').val() ? Number($('#object_type').val()) : 0,
                        object_code: object_code,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data){
                            if(data.object){
                                $('#object_id').val(data.object.id);
                                $('#object_name').val(data.object.name);
                                AutoNumeric.getAutoNumericElement('#balance').set(data.object.balance);
                            }
                        }
                    },
                    error: function (request, status, error) {
                        swal("Thông báo","Có lỗi xãy ra khi tải dữ liệu, vui lòng thử lại.","warning");
                    }
                });
			} else {
                $('#object_id').val(null);
                $('#object_name').val(null);
            }
        }

        function showModalobject_id() {
            var url_object_id = "{{action('AdminGoldPaymentsController@getModalData')}}/gold_payments/modal-data?table=gold_customers&columns=id,code,name&name_column=object_id&where=deleted_at+is+null&select_to=code:code&columns_name_alias=Mã khách hàng,Tên khách hàng";
            var object_type = $('#object_type').val() ? Number($('#object_type').val()) : 0;
            console.log('object_type = ', object_type);
            if(object_type == 1){
                url_object_id = "{{action('AdminGoldPaymentsController@getModalData')}}/gold_payments/modal-data?table=gold_suppliers&columns=id,code,name&name_column=object_id&where=deleted_at+is+null&select_to=code:code&columns_name_alias=Mã nhà cung cấp,Tên nhà cung cấp";    
            }else if(object_type == 2){
                url_object_id = "{{action('AdminGoldPaymentsController@getModalData')}}/gold_payments/modal-data?table=gold_investors&columns=id,code,name&name_column=object_id&where=deleted_at+is+null&select_to=code:code&columns_name_alias=Mã nhà đầu tư,Tên nhà đầu tư";    
            }else if(object_type == 3){
                if(Number('{{CRUDBooster::myPrivilegeId()}}') == 1){
                    url_object_id = "{{action('AdminGoldReceiptsController@getModalData')}}/gold_receipts/modal-data?table=cms_users&columns=id,employee_code,name&name_column=object_id&where=status+=+0&select_to=employee_code:code&columns_name_alias=Mã nhân viên,Tên nhân viên";
                }else{
                    url_object_id = "{{action('AdminGoldReceiptsController@getModalData')}}/gold_receipts/modal-data?table=cms_users&columns=id,employee_code,name&name_column=object_id&where=status+=+0+and+id+<>+1&select_to=employee_code:code&columns_name_alias=Mã nhân viên,Tên nhân viên";
                }
            }
            $('#iframe-modal-object_id').attr('src',url_object_id);
            $('#modal-datamodal-object_id').modal('show');
        }
        function hideModalobject_id() {
            $('#modal-datamodal-object_id').modal('hide');
        }
        function selectAdditionalDataobject_id(select_to_json) {
			if(select_to_json.code){
                $('#object_code').val(select_to_json.code).trigger('change');
			}
            hideModalobject_id();
        }
        
        function validate() {
			let valid = true;
            $('#form input').removeClass('invalid');
            if(!$('#object_code').val()){
                valid = false;
                $('#object_code').addClass('invalid');
            }
            if(!$('#order_date').val()){
                valid = false;
                $('#order_date').addClass('invalid');
            }
            if(!$('#notes').val()){
                valid = false;
                $('#notes').addClass('invalid');
            }
            if(($('#amount').val() ? Number($('#amount').val().replace(/,/g, '')) : 0) == 0){
                valid = false;
                $('#amount').addClass('invalid');
            }

            if(!valid) {
                swal("Thông báo", "Dữ liệu chưa được nhập đầy đủ, vui lòng kiểm tra lại.", "warning");
            }
			return valid;
        }

        function getOrderHeader(finish) {
            return {
                id: $('#id').val() ? Number($('#id').val()) : null,
                status: finish ? 1 : 0,
                order_date: moment($('#order_date').val(), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                order_no: $('#order_no').val(),
                object_type: $('#object_type').val() ? Number($('#object_type').val()) : 0,
                object_id: $('#object_id').val() ? Number($('#object_id').val()) : null,
                method: $('#method').val() ? Number($('#method').val()) : 0,
                notes: $('#notes').val(),
                out_amount: $('#amount').val() ? AutoNumeric.getAutoNumericElement(`#amount`).getNumber() : 0,
                balance: $('#balance').val() ? AutoNumeric.getAutoNumericElement(`#balance`).getNumber() : 0,
                is_balance: $(`#is_balance`).is(":checked") ? 1 : 0
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
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data) {
                            order_id = data.id;
                            $('#id').val(order_id)
                            $('#order_no').val(data.order_no);
                            if(finish) {
                                disableOrder();
                            }
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
            $('#save_button').hide();
            $('#print_order').show();
            $('#btn_search_object').attr('disabled', true);
            $('#form input').attr('disabled', true);
            $('#form select').attr('disabled', true);
            $('#method').attr('disabled', true);
            $('#object_type').attr('disabled', true);
        }

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }

        function printOrder() {
            if(order_id) {
                popupWindow("{{action('AdminGoldPaymentsController@getPrintOrder')}}/" + order_id,"print");
            }else{
                alert("Bạn không thể in phiếu chi nếu chưa lưu đơn hàng!");
            }
        }
	</script>
@endpush