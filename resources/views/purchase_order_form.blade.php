<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- test content here.-->
	<div>
		<p><a title="Return" href="{{CRUDBooster::mainpath()}}"><i class="fa fa-chevron-circle-left "></i> &nbsp; Quay lại danh sách</a></p>
		<div class='panel panel-default'>
			<!-- <div class='panel-heading'>
                <strong><i class="fa fa-list-alt"></i> THÔNG TIN ĐƠN HÀNG</strong>
			</div> -->

			<div class="panel-body" id="parent-form-area">
				<form method='post' action='{{CRUDBooster::mainpath('add-save')}}' id="form">
					<input type="hidden" name="id" id="id">
					<input type="hidden" name="purchase_id" id="purchase_id">
                    <input type="hidden" name="status" id="status">
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
					</div>
					<div class="col-sm-12">
						<div class="form-group header-group-1">
							<div class="row">
								<label class="control-label col-sm-2">Ngày đơn hàng</label>
								<div class="col-sm-2">
									<div class="input-group" >
										<input id="order_date" readonly type="text" class="form-control bg-white" placeholder="Ngày đơn hàng" required>
										<div class="input-group-addon bg-gray">
											<i class="fa fa-calendar"></i>
										</div>
									</div>
								</div>
                                <label class="control-label col-sm-2 text-right">Số đơn hàng</label>
								<div class="col-sm-2">
									<input type="text" name="order_no" id="order_no" class="form-control" placeholder="Tự động tạo" readonly disabled>
								</div>
                                <label class="control-label col-sm-2 text-right">Tổng cân <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
								<div class="col-sm-2">
									<div class="input-group">
										<input type="text" name="actual_weight" id="actual_weight" class="form-control money" value="0" required>
										<span class="input-group-btn">
											<button type="button" class="btn btn-warning btn-flat" id="check_actual_weight" onclick="valid_actual_weight(true);"><i class="fa fa-question"></i></button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <div class="col-sm-12">
						<div class="form-group header-group-2">
                            <table id="table_pays" class='table table-bordered'>
                                <thead>
                                <tr class="bg-success">
                                    <th style="width: 20px;">#</th>
                                    <th style="width: 250px;">Nội dung <span class="text-danger" title="Không được bỏ trống trường này.">*</span></th>
                                    <th style="width: 80px;">Loại vàng</th>
                                    <th>TL tổng</th>
                                    <th>TL đá</th>
                                    <th>TL trừ</th>
                                    <th>TL vàng</th>
                                    <th>Tuổi vàng</th>
                                    <th>Q10</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                    <th>Ghi chú</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th class="text-center"><a onclick="addNewPayDetail()" class="text-blue" style="cursor: pointer;"><i class="fa fa-plus"></i></a></th>
                                    <th colspan="8"></th>
                                </tr>
                                <tr class="bg-gray-active">
                                    <th colspan="3" class="text-center">Tổng cộng</th>
                                    <th id="total_pay_total_weight" class="text-right">0</th>
                                    <th id="total_pay_gem_weight" class="text-right">0</th>
                                    <th id="total_pay_abate_weight" class="text-right">0</th>
                                    <th id="total_pay_gold_weight" class="text-right">0</th>
                                    <th></th>
                                    <th id="total_pay_q10" class="text-right">0</th>
                                    <th></th>
                                    <th id="total_pay_gold_amount" class="text-right">0</th>
                                    <th></th>
                                </tr>
                                <tr class="bg-success">
                                    <th colspan="10" class="text-right">Phí dịch vụ</th>
                                    <th class="no-padding"><input type="text" name="fee" id="fee" onchange="calcTotalOfPays()" class="form-control money"></th>
                                    <th></th>
                                </tr>
                                <tr class="bg-danger">
                                    <th colspan="10" class="text-right">Tổng tiền trả cho khách</th>
                                    <th id="total_pay_amount" class="text-right" style="color:#f30707">0</th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
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
						<a id="print_invoice" style="display: none;cursor: pointer;" onclick="printInvoice()" class="btn btn-info"><i class="fa fa-print"></i> In hóa đơn</a>
						<!-- <a id="print_report_detail" style="display: none;cursor: pointer;" onclick="printReportDetail()" class="btn btn-primary"><i class="fa fa-print"></i> In Bảng kê chi tiết</a> -->
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
	<div id="modal-datamodal-delete-barcode" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">
		<div class="modal-dialog modal-md " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><i class="fa fa-search"></i> Delete Barcode | Hàng đang bán</h4>
				</div>
				<div class="modal-body">
					<form onsubmit="event.preventDefault();" id="form" style="min-height: 50px;">
						<input type="hidden" name="id" id="id">
						<input type="hidden" name="purchase_id" id="purchase_id">
						<div class="col-sm-12">
							<div class="row">
								<label class="control-label col-sm-2">Mã vạch </label>
								<div class="col-sm-10">
									<input type="text" name="bar_code_delete" id="bar_code_delete" class="form-control"
										   placeholder="Quét mã vạch để xóa" autocomplete="off" onkeyup="removeBarcode(event)"
										   style="background-color: rgba(251,17,24,0.23)">
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer text-center">
					<button type="button" class="btn btn-info" data-dismiss="modal" aria-label="Close">Xong</button>
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
        stamp_weight = Number('{{CRUDBooster::getSetting('trong_luong_tem')}}');
        order_id = null; // sẽ có khi lưu thành công
		readOnlyAll = false;
        order_pays = [];
        product_types = [];
        total_pay = {
			total_weight: 0,
			gem_weight: 0,
			abate_weight: 0,
            gold_weight: 0,
            gold_amount: 0,
            q10: 0,
            fee: 0
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
        pur_price = 0;
        
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

            $.ajax({
                method: "GET",
                url: '{{Route("AdminGoldProductTypesControllerGetListAll")}}',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: false,
                success: function (data) {
                    if (data){
                        product_types = data.list;
                    }
                },
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
						if(data.order){
						    order_id = data.order.id;
                            $('#id').val(order_id);
                            $('#order_date').val(moment(data.order.order_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            $('#order_no').val(data.order.order_no);
                            const actual_weight = AutoNumeric.getAutoNumericElement('#actual_weight');
                            actual_weight.set(data.order.actual_weight);
                            const fee = AutoNumeric.getAutoNumericElement('#fee');
                            fee.set(data.order.fee);
                        }
                        if(data.order_pays && data.order_pays.length > 0){
                            data.order_pays.forEach(function (detail, i) {
                                total_pay.total_weight += detail.total_weight;
                                total_pay.gem_weight += detail.gem_weight;
                                total_pay.abate_weight += detail.abate_weight;
                                total_pay.gold_weight += detail.gold_weight;
                                total_pay.gold_amount += detail.amount;
                                total_pay.q10 += detail.q10;
                                loadPayDetail(detail);
                            });
                            order_pays = data.order_pays;
                        }
                        calcTotalOfPays();
                        if (data.order && data.order.status == 1){
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
            var url_customer_id = "{{action('AdminGoldPurchaseOrdersController@getModalData')}}/gold_purchase_orders/modal-data?table=gold_customers&columns=id,code,name,address,phone&name_column=customer_id&where=deleted_at+is+null&select_to=code:code&columns_name_alias=Mã khách hàng,Tên khách hàng,Địa chỉ,Số điện thoại";
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
		
        function loadPayDetail(data) {
            let html = `<tr id="order_pay_index_${data.id}">
                    <th class="text-center"><a onclick="removePayDetail(${data.id})" class="text-red" style="cursor: pointer;"><i class="fa fa-remove"></i></a></th>
                    <th class="no-padding"><input id="pay${data.id}_description" onchange="pay_description_change(${data.id})" type="text" class="form-control" value="${data.description}" required></th>
                    <th class="text-center">${data.product_type_name}</th>
                    <th class="no-padding"><input id="pay${data.id}_total_weight" onchange="pay_total_weight_change(${data.id})" type="text" class="form-control money" value="${data.total_weight}"></th>
                    <th class="no-padding"><input id="pay${data.id}_gem_weight" onchange="pay_gem_weight_change(${data.id})" type="text" class="form-control money" value="${data.gem_weight}"></th>
                    <th class="no-padding"><input id="pay${data.id}_abate_weight" onchange="pay_abate_weight_change(${data.id})" type="text" class="form-control money" value="${data.abate_weight}"></th>
                    <th class="text-right" id="pay${data.id}_gold_weight">${data.gold_weight}</th>
                    <th class="no-padding"><input id="pay${data.id}_age" onchange="pay_age_change(${data.id})" type="text" class="form-control money" value="${data.age}"></th>
                    <th class="text-right" id="pay${data.id}_q10">${data.q10}</th>
                    <th class="no-padding"><input id="pay${data.id}_price" onchange="pay_price_change(${data.id})" type="text" class="form-control money" value="${data.price}"></th>
                    <th class="text-right" id="pay${data.id}_amount">${data.amount.toLocaleString('en-US')}</th>
                    <th class="no-padding"><input id="pay${data.id}_notes" onchange="pay_notes_change(${data.id})" type="text" class="form-control value="${data.notes}"></th>
                </tr>`;
            $('#table_pays tbody').append(html);

            setTimeout(function () {
                AutoNumeric.multiple(`#order_pay_index_${data.id} .money`, optionNumberInput);
            },100);
        }

        function addNewPayDetail() {
            if(readOnlyAll){
                // swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
            }
            let tmp_id = - (order_pays.length + 1);
            let tmp_price = 0;
            let tmp_age = 0;
            let type_id = 0;
            let html = `<tr id="order_pay_index_${tmp_id}">
                <th class="text-center"><a onclick="removePayDetail(${tmp_id})" class="text-red" style="cursor: pointer;"><i class="fa fa-remove"></i></a></th>
                <th class="no-padding"><input id="pay${tmp_id}_description" onchange="pay_description_change(${tmp_id})" type="text" class="form-control" required></th>
                <th class="no-padding">
                    <select id="pay${tmp_id}_product_type" class="form-control" onchange="pay_product_type_change(${tmp_id})">`;
            if(product_types && product_types.length > 0){
                product_types.forEach(function (detail, i) {
                    html = html + ` <option value=${detail.id}>${detail.name}</option>`;
                    if(i == 0){
                        type_id = detail.id;
                        tmp_age = detail.age;
                        tmp_price = getPurchasePrice(detail.id);
                    }
                });
            }
            html = html + ` </select>
                    </th>
                    <th class="no-padding"><input id="pay${tmp_id}_total_weight" onchange="pay_total_weight_change(${tmp_id})" type="text" class="form-control money" value="0"></th>
                    <th class="no-padding"><input id="pay${tmp_id}_gem_weight" onchange="pay_gem_weight_change(${tmp_id})" type="text" class="form-control money" value="0"></th>
                    <th class="no-padding"><input id="pay${tmp_id}_abate_weight" onchange="pay_abate_weight_change(${tmp_id})" type="text" class="form-control money" value="0"></th>
                    <th class="text-right" id="pay${tmp_id}_gold_weight">0</th>
                    <th class="no-padding"><input id="pay${tmp_id}_age" onchange="pay_age_change(${tmp_id})" type="text" class="form-control money" value="${tmp_age.toLocaleString('en-US')}"></th>
                    <th class="text-right" id="pay${tmp_id}_q10">0</th>
                    <th class="no-padding"><input id="pay${tmp_id}_price" onchange="pay_price_change(${tmp_id})" type="text" class="form-control money" value="${tmp_price.toLocaleString('en-US')}"></th>
                    <th class="text-right" id="pay${tmp_id}_amount">0</th>
                    <th class="no-padding"><input id="pay${tmp_id}_notes" onchange="pay_notes_change(${tmp_id})" type="text" class="form-control"></th>
                </tr>`;
            $('#table_pays tbody').append(html);

            order_pays.push({
                id: tmp_id, 
                total_weight: 0,
                product_type_id: type_id,
                gem_weight: 0,
                abate_weight: 0,
                gold_weight: 0,
                age: tmp_age,
                q10: 0,
                price: tmp_price,
                amount: 0,
            });

            setTimeout(function () {
                AutoNumeric.multiple(`#order_pay_index_${tmp_id} .money`, optionNumberInput);
                $(`#pay${tmp_id}_description`).focus();
            },100);
        }

        function removePayDetail(removeId) {
            if(readOnlyAll){
                // swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
            }

            let removePay = null;
            let removeIndex = -1;
            order_pays.forEach(function (pay, index) {
                if(pay.id == removeId) {
                    removePay = pay;
                    removeIndex = index;
                }
            });

			$('#order_pay_index_'+removeId).remove();
			order_pays.splice(removeIndex, 1);

            total_pay = {
                total_weight: 0,
                gem_weight: 0,
                abate_weight: 0,
                gold_weight: 0,
                q10: 0,
                gold_amount: 0,
            };
            order_pays.forEach(function (pay, i) {
                total_pay.total_weight += pay.total_weight ? pay.total_weight : 0;
                total_pay.gem_weight += pay.gem_weight ? pay.gem_weight : 0;
                total_pay.abate_weight += pay.abate_weight ? pay.abate_weight : 0;
                total_pay.gold_weight += pay.gold_weight ? pay.gold_weight : 0;
                total_pay.q10 += pay.q10 ? pay.q10 : 0;
                total_pay.gold_amount += pay.amount ? pay.amount : 0;
            });
            calcTotalOfPays();
        }

        function pay_description_change(changeId) {
            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.description = $(`#pay${changeId}_description`).val();
                }
            });
        }

        function pay_notes_change(changeId) {
            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.notes = $(`#pay${changeId}_notes`).val();
                }
            });
        }

        function pay_product_type_change(changeId) {
            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.product_type_id = $(`#pay${pay.id}_product_type`).val();
                    if(product_types && product_types.length > 0){
                        product_types.forEach(function (detail, i) {
                            if(pay.product_type_id == detail.id){
                                pay.age = detail.age;
                                AutoNumeric.getAutoNumericElement(`#pay${pay.id}_age`).set(pay.age);
                                $(`#pay${pay.id}_age`).trigger("change");
                            }
                        });
                    }
                    AutoNumeric.getAutoNumericElement(`#pay${pay.id}_price`).set(getPurchasePrice(pay.product_type_id));
                    $(`#pay${pay.id}_price`).trigger("change");
                }
            });
        }

        function pay_total_weight_change(changeId) {
            total_pay.total_weight = 0;
            total_pay.gold_weight = 0;
            total_pay.q10 = 0;
            total_pay.gold_amount = 0;
            
            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.total_weight = $(`#pay${pay.id}_total_weight`).val()?Number($(`#pay${pay.id}_total_weight`).val().replace(/,/g, '')):0;
                    pay.gold_weight = Math.round((pay.total_weight - pay.gem_weight - pay.abate_weight) * 10000) / 10000;
                    pay.q10 = Math.round((pay.gold_weight * pay.age / 100) * 10000) / 10000;
                    pay.amount = Math.round(pay.gold_weight * pay.price);

                    $(`#pay${pay.id}_gold_weight`).html(pay.gold_weight.toLocaleString('en-US'));
                    $(`#pay${pay.id}_q10`).html(pay.q10.toLocaleString('en-US'));
                    $(`#pay${pay.id}_amount`).html(pay.amount.toLocaleString('en-US'));
                }
                total_pay.total_weight += pay.total_weight ? pay.total_weight : 0;
                total_pay.gold_weight += pay.gold_weight ? pay.gold_weight : 0;
                total_pay.q10 += pay.q10 ? pay.q10 : 0;
                total_pay.gold_amount += pay.amount ? pay.amount : 0;
            });
            calcTotalOfPays();
        }

        function pay_gem_weight_change(changeId) {
            total_pay.gem_weight = 0;
            total_pay.gold_weight = 0;
            total_pay.q10 = 0;
            total_pay.gold_amount = 0;

            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.gem_weight = $(`#pay${pay.id}_gem_weight`).val()?Number($(`#pay${pay.id}_gem_weight`).val().replace(/,/g, '')):0;
                    pay.gold_weight = Math.round((pay.total_weight - pay.gem_weight - pay.abate_weight) * 10000) / 10000;
                    pay.q10 = Math.round((pay.gold_weight * pay.age / 100) * 10000) / 10000;
                    pay.amount = Math.round(pay.gold_weight * pay.price);

                    $(`#pay${pay.id}_gold_weight`).html(pay.gold_weight.toLocaleString('en-US'));
                    $(`#pay${pay.id}_q10`).html(pay.q10.toLocaleString('en-US'));
                    $(`#pay${pay.id}_amount`).html(pay.amount.toLocaleString('en-US'));
                }
                total_pay.gem_weight += pay.gem_weight ? pay.gem_weight : 0;
                total_pay.gold_weight += pay.gold_weight ? pay.gold_weight : 0;
                total_pay.q10 += pay.q10 ? pay.q10 : 0;
                total_pay.gold_amount += pay.amount ? pay.amount : 0;
            });
            calcTotalOfPays();
        }

        function pay_abate_weight_change(changeId) {
            total_pay.abate_weight = 0;
            total_pay.gold_weight = 0;
            total_pay.q10 = 0;
            total_pay.gold_amount = 0;

            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.abate_weight = $(`#pay${pay.id}_abate_weight`).val()?Number($(`#pay${pay.id}_abate_weight`).val().replace(/,/g, '')):0;
                    pay.gold_weight = Math.round((pay.total_weight - pay.gem_weight - pay.abate_weight) * 10000) / 10000;
                    pay.q10 = Math.round((pay.gold_weight * pay.age / 100) * 10000) / 10000;
                    pay.amount = Math.round(pay.gold_weight * pay.price);

                    $(`#pay${pay.id}_gold_weight`).html(pay.gold_weight.toLocaleString('en-US'));
                    $(`#pay${pay.id}_q10`).html(pay.q10.toLocaleString('en-US'));
                    $(`#pay${pay.id}_amount`).html(pay.amount.toLocaleString('en-US'));
                }
                total_pay.abate_weight += pay.abate_weight ? pay.abate_weight : 0;
                total_pay.gold_weight += pay.gold_weight ? pay.gold_weight : 0;
                total_pay.q10 += pay.q10 ? pay.q10 : 0;
                total_pay.gold_amount += pay.amount ? pay.amount : 0;
            });
            calcTotalOfPays();
        }

        function pay_age_change(changeId) {
            total_pay.q10 = 0;

            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.age = $(`#pay${pay.id}_age`).val() ? Number($(`#pay${pay.id}_age`).val().replace(/,/g, '')) : 0;
                    pay.q10 = Math.round((pay.gold_weight * pay.age / 100) * 10000) / 10000;

                    $(`#pay${pay.id}_q10`).html(pay.q10.toLocaleString('en-US'));
                }
                total_pay.q10 += pay.q10 ? pay.q10 : 0;
            });
            calcTotalOfPays();
        }

        function pay_price_change(changeId) {
            total_pay.gold_amount = 0;

            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.price = $(`#pay${pay.id}_price`).val()?Number($(`#pay${pay.id}_price`).val().replace(/,/g, '')):0;
                    pay.amount = Math.round(pay.gold_weight * pay.price);

                    $(`#pay${pay.id}_amount`).html(pay.amount.toLocaleString('en-US'));
                }
                total_pay.gold_amount += pay.amount ? pay.amount : 0;
            });
            calcTotalOfPays();
        }

        function calcTotalOfPays() {
            $('#total_pay_total_weight').html(total_pay.total_weight ? total_pay.total_weight.toLocaleString('en-US') : 0);
            $('#total_pay_gem_weight').html(total_pay.gem_weight ? total_pay.gem_weight.toLocaleString('en-US') : 0);
            $('#total_pay_abate_weight').html(total_pay.abate_weight ? total_pay.abate_weight.toLocaleString('en-US') : 0);
            $('#total_pay_gold_weight').html(total_pay.gold_weight ? total_pay.gold_weight.toLocaleString('en-US') : 0);
            $('#total_pay_q10').html(total_pay.q10 ? total_pay.q10.toLocaleString('en-US') : 0);
            $('#total_pay_gold_amount').html(total_pay.gold_amount ? total_pay.gold_amount.toLocaleString('en-US') : 0);

            total_pay.fee = $('#fee').val() ? Number($('#fee').val().replace(/,/g, '')) : 0;
            $('#total_pay_amount').html(((total_pay.gold_amount ? total_pay.gold_amount : 0) - total_pay.fee).toLocaleString('en-US'));
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
            
            order_pays.forEach(function (pay, i) {
                if(!pay.description) {
                    valid = false;
                    $(`#pay${pay.id}_description`).addClass('invalid');
                    $(`#pay${pay.id}_description`).focus();
                }else if(pay.total_weight == 0) {
                    valid = false;
                    $(`#pay${pay.id}_total_weight`).addClass('invalid');
                    $(`#pay${pay.id}_total_weight`).focus();
                }else if(pay.q10 > pay.gold_weight || pay.age <= 0) {
                    valid = false;
                    $(`#pay${pay.id}_age`).addClass('invalid');
                    $(`#pay${pay.id}_age`).focus();
                }
            });

            if(!valid) {
                swal("Thông báo", "Dữ liệu chưa được nhập đầy đủ, vui lòng kiểm tra lại.", "warning");
            }else if(!order_pays || order_pays.length <= 0){
                valid = false;
                swal("Thông báo", "Chưa nhập hàng, vui lòng kiểm tra lại.", "warning");
            }else{
                valid = valid && valid_actual_weight(true);
            }
			return valid;
        }
        function valid_actual_weight(show_alert) {
            let valid = true;
            let margin = 0;
            let actual_weight = $('#actual_weight').val() ? Math.round(Number($('#actual_weight').val().replace(/,/g, '')), 4) : 0;
            $(`#actual_weight`).removeClass('invalid');
            if (Math.round(actual_weight, 3) < Math.round(total_pay.total_weight, 3)) {
                valid = false;
                if(show_alert) {
                    swal("Thông báo", "Tổng TL cao hơn Tổng cân thực tế", "warning");
                }
            } else if (Math.round(actual_weight, 3) > Math.round(total_pay.total_weight, 3)) {
                valid = false;
                if(show_alert) {
                    swal("Thông báo", "Tổng TL thấp hơn Tổng cân thực tế", "warning");
                }
            }
            $('#check_actual_weight').removeClass('btn-warning');
            $('#check_actual_weight').removeClass('btn-success');
            if(valid){
                $('#check_actual_weight').addClass('btn-success');
                $('#check_actual_weight').html('<i class="fa fa-check"></i>');
                $('.hide_invalid_actual_weight').show();
			} else {
                $(`#actual_weight`).focus();
                $(`#actual_weight`).addClass('invalid');
                $('#check_actual_weight').addClass('btn-warning');
                $('#check_actual_weight').html('<i class="fa fa-question"></i>');
                $('.hide_invalid_actual_weight').hide();
			}
            return valid;
        }
        function getOrderHeader(finish) {
            return {
                id: $('#id').val() ? Number($('#id').val()) : null,
                status: finish ? 1 : 0, //0 = Đang nhập, 1 = Hoàn tất
                customer_id: $('#customer_id').val() ? Number($('#customer_id').val()) : null,
                purchase_id: Number('{{CRUDBooster::myId()}}'),
                order_date: moment($('#order_date').val(), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                order_no: $('#order_no').val() ? $('#order_no').val() : null,
                actual_weight: $('#actual_weight').val() ? Number($('#actual_weight').val().replace(/,/g, '')) : 0,
                amount: total_pay.gold_amount,
                fee: total_pay.fee,
            };
		}

        function submit(finish) {
			if(!finish || validate()){ // nếu finish == false thì không validate
			    if(finish) {
                    $('#save_button').hide();
                    $('.loading').show();
                }
                let counter = null;
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
                        order_pays: order_pays,
                        counter: counter,
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
            readOnlyAll = true;
            $('#print_invoice').show();
            $('#save_button').hide();
            $('#btn_search_customer').attr('disabled', true);
            $('#check_actual_weight').attr('disabled', true);
            $('#form input').attr('disabled', true);
            // $('#form textarea').attr('readonly', true);
            // $('#form select').attr('disabled', true);
        }

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }

        function printInvoice() {
            if(order_id) {
                popupWindow("{{action('AdminGoldPurchaseOrdersController@getPrintInvoice')}}/" + order_id,"print");
            }else{
                alert("Bạn không thể in hóa đơn nếu chưa lưu đơn hàng!");
            }
        }

        function getPurchasePrice(type_id) {
            let result = 0;
            $.ajax({
                method: "GET",
                url: '{{Route("AdminGoldPriceControllerGetPrice")}}',
                data: {
                    apply_time: moment($('#order_date').val(), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                    product_type_id: type_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: false,
                success: function (data) {
                    if (data && data.price) {
                        result = data.price.purchase_price ? data.price.purchase_price : 0;
                        // console.log('Gia trị từ dữ liệu = ', result);
                    }
                },
                error: function (request, status, error) {
                    console.log('PostAdd status = ', status);
                    console.log('PostAdd error = ', error);
                }
            });
            // console.log('Gia trị trả về = ', result);
            return result;
        }
	</script>
@endpush