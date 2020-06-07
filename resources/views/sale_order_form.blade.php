<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- test content here.-->
	<div>
		<p><a title="Return" href="{{CRUDBooster::mainpath()}}"><i class="fa fa-chevron-circle-left "></i> &nbsp; Quay lại danh sách</a></p>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<!-- <strong><i class="fa fa-diamond"></i> {{$mode=='new'?'Tạo mới Đơn Hàng':(mode=='edit'?'Sửa Đơn Hàng':'Chi tiết Đơn Hàng')}}</strong> -->
                <strong><i class="fa fa-list-alt"></i> THÔNG TIN ĐƠN HÀNG</strong>
			</div>

			<div class="panel-body" id="parent-form-area">
				<form method='post' action='{{CRUDBooster::mainpath('add-save')}}' id="form">
					<input type="hidden" name="id" id="id">
					<input type="hidden" name="saler_id" id="saler_id">
                    <input type="hidden" name="order_type" id="order_type">
					<div class="col-sm-12">
						<div class="row">
							<label for="customer_code" class="control-label col-sm-1">KH<span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-11">
								<div class="input-group">
									<span class="input-group-btn">
										<button id="btn_search_customer" type="button" class="btn btn-primary btn-flat" onclick="showModalcustomer_id()"><i class="fa fa-search"></i></button>
									</span>
									<input type="text" name="customer_code" id="customer_code" onchange="searchCustomer();" class="form-control" required placeholder="Mã KH" style="width: 10%">
									<input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Tên khách hàng" style="width: 30%">
                                    <input type="text" name="customer_address" id="customer_address" class="form-control" placeholder="Địa chỉ" style="width: 45%">
									<input type="text" name="customer_phone" id="customer_phone" class="form-control" placeholder="Số ĐT" style="width: 15%">
									<input type="hidden" name="customer_id" id="customer_id">
                                    <input type="hidden" name="customer_type" id="customer_type">
								</div>
							</div>
						</div>
						<div class="form-group header-group-2">
							<div class="row">
								<label class="control-label col-sm-1">Ngày ĐH</label>
								<div class="col-sm-2">
									<div class="input-group" >
										<input id="order_date" readonly type="text" class="form-control bg-white" placeholder="Ngày đơn hàng" required>
										<div class="input-group-addon bg-gray">
											<i class="fa fa-calendar"></i>
										</div>
									</div>
								</div>
                                <label class="control-label col-sm-1 text-right">Số ĐH</label>
								<div class="col-sm-2">
									<input type="text" name="order_no" id="order_no" class="form-control" readonly disabled>
								</div>
								<label class="control-label col-sm-1 text-right">Công nợ</label>
								<div class="col-sm-2">
									<input type="text" name="customer_balance" id="customer_balance" class="form-control money"  disabled readonly>
								</div>
                                <label class="control-label col-sm-1 text-right">Điểm t.lũy</label>
                                <div class="col-sm-2">
                                    <input type="text" name="points" id="points" class="form-control money" disabled readonly>
                                </div>
							</div>
							<div class="row">
								<label class="control-label col-sm-1">Mã vạch </label>
								<div class="col-sm-2">
									<div class="input-group">
										<input type="text" name="bar_code" id="bar_code" class="form-control"
											   placeholder="Quét mã vạch" autocomplete="off" onkeyup="findProduct(event)"
											   style="background-color: rgba(251,240,83,0.52)">
										<span class="input-group-btn">
											<button id="btn_bar_code" type="button" class="btn btn-danger btn-flat" onclick="$('#modal-datamodal-delete-barcode').modal('show')">
                                            <i class="fa fa-remove"></i></button>
										</span>
									</div>
									{{--$('#modal-datamodal-customer_id').modal('show');--}}
								</div>
								<label class="control-label col-sm-1 text-right">TC thực tế </label>
								<div class="col-sm-2">
									<div class="input-group">
										<input type="text" name="actual_weight" id="actual_weight" class="form-control money" value="0" required>
										<span class="input-group-btn">
											<button type="button" class="btn btn-warning btn-flat" id="check_actual_weight" onclick="valid_actual_weight(true);"><i class="fa fa-question"></i></button>
										</span>
									</div>
								</div>
                                <label class="control-label col-sm-1 text-right">% CK</label>
								<div class="col-sm-2">
									<input type="text" name="sampling_discount" id="sampling_discount" onchange="saleOrderHeadChange();" class="form-control money" placeholder="% chiết khấu">
								</div>
								<label class="control-label col-sm-1 text-right">Giảm trừ</label>
								<div class="col-sm-2">
									<input type="text" name="reduce" id="reduce" class="form-control money" onchange="calcTotalSaleOrder()" required>
								</div>
							</div>
						</div>
						<div class="form-group header-group-1">
							<table id="table_order_details" class='table table-bordered' height=50>
								<thead>
								<tr class="bg-success">
									<th class="action">#</th>
									<th class="sort_no">Stt</th>
									<th class="bar_code">Mã vạch</th>
									<th class="product_code">Mã SP</th>
									<th class="product_name">Tên SP</th>
									<th class="product_type_name">Loại vàng</th>
									<th class="weight">TLT</th>
									<th class="weight">TLĐ</th>
									<th class="weight">TLV</th>
                                    <th class="edit_weight">TLV sau sửa</th>
                                    <th class="weight">TLV CL</th>
									<th class="fee">Đơn giá</th>
                                    <th class="amount">Tiền vàng</th>
                                    <th class="fee">Tiền công</th>
									<th class="fee">Chiết khấu</th>
                                    <th>Thành tiền</th>
								</tr>
								</thead>
								<tbody>
                                
								</tbody>
								<tfoot>
								<tr class="bg-gray-active">
									<th colspan="6" class="total_label">Tổng cộng</th>
									<th id="total_order_total_weight" class="weight_value">0</th>
									<th id="total_order_gem_weight" class="weight_value">0</th>
									<th id="total_order_gold_weight" class="weight_value">0</th>
									<th id="total_order_edit_weight" class="edit_weight_value">0</th>
									<th id="total_order_diff_weight" class="weight_value">0</th>
									<th class="fee_value"></th>
                                    <th id="total_order_gold_amount" class="amount_value">0</th>
                                    <th id="total_order_fee" class="fee_value">0</th>
                                    <th id="total_order_discount_amount" class="fee_value">0</th>
									<th id="total_order_amount" class="text-right">0</th>
								</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<!-- <div class="col-sm-12 hide_invalid_actual_weight"> -->
                    <div class="col-sm-12">
						<div id="header3" data-collapsed="false" class="header-title form-divider">
                            <strong><i class="fa fa-dollar"></i> THÔNG TIN THANH TOÁN</strong>
						</div>
					</div>
					<!-- <div class="col-sm-12 hide_invalid_actual_weight"> -->
                    <div class="col-sm-12">
						<div class="form-group header-group-2">
                            <table class='table table-bordered'>
                                <tr>
                                    <th>
                                        <table id="table_pays" class='table table-bordered'>
                                            <thead>
                                            <tr class="bg-success">
                                                <th style="width: 20px;">#</th>
                                                <th>Nội dung <span class="text-danger" title="Không được bỏ trống trường này.">*</span></th>
                                                <th>Loại vàng</th>
                                                <th>TL tổng</th>
                                                <th>TL đá</th>
                                                <th>TL trừ</th>
                                                <th>TL vàng</th>
                                                <th>Đơn giá</th>
                                                <th>Tiền công</th>
                                                <th>Thành tiền</th>
                                                <th>Tuổi vàng</th>
                                                <th>Q10</th>
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
                                                <th id="total_pay_fee" class="text-right">0</th>
                                                <th id="total_pay_gold_amount" class="text-right">0</th>
                                                <th></th>
                                                <th id="total_pay_q10" class="text-right">0</th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </th>
                                    <th class="bg-danger" style="width: 300px;">
                                        <div class="form-group header-group-2">
                                            <div class="row">
                                                <label class="control-label col-sm-6 text-right">Tổng tiền bán</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="total_sales_amount" id="total_sales_amount" class="form-control money" readonly disabled>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="control-label col-sm-6 text-right">Điểm tích lũy</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="use_points" id="use_points" class="form-control money" onchange="calcTotalOfPays()" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="control-label col-sm-6 text-right">Công nợ</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="balance" id="balance" class="form-control money" onchange="calcTotalOfPays()" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="control-label col-sm-6 text-right">T.toán bằng hàng</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="pay_item" id="pay_item" class="form-control money"  readonly disabled>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="control-label col-sm-6 text-right">Hình thức thanh toán</label>
                                                <div class="col-sm-6">
                                                    <select id="payment_method" class="form-control" onchange="payment_method_change()">
                                                        <option value=0>Tiền mặt</option>
                                                        <option value=1>Chuyển khoản</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="control-label col-sm-6 text-right">Loại thẻ</label>
                                                <div class="col-sm-6">
                                                    <select id="card_type" class="form-control" onchange="card_type_change()" disabled></select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="control-label col-sm-6 text-right">% phí ngân hàng</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="bank_fee" id="bank_fee" class="form-control money" onchange="calcTotalOfPays()" disabled>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="control-label col-sm-6 text-right">Tiền còn lại phải thu</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="remain_amount" id="remain_amount" class="form-control money" style="color:#0000ff" readonly disabled>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="control-label col-sm-6 text-right">T.toán bằng tiền</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="pay_amount" id="pay_amount" class="form-control money" onchange="calcTotalOfPays()" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="control-label col-sm-6 text-right">Tiền thừa</label>
                                                <div class="col-sm-6">
                                                    <input type="text" name="return_amount" id="return_amount" class="form-control money" style="color:#f30707" readonly disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
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
						<a id="reset_points" style="display: none;cursor: pointer;" onclick="resetPoints()" class="btn btn-warning"><i class="fa fa-warning"></i> Reset points</a>
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
						<input type="hidden" name="saler_id" id="saler_id">
						<div class="col-sm-12">
							<div class="row">
								<label class="control-label col-sm-2">Mã vạch</label>
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
		#table_order_details tbody {
			display:block;
			max-height:200px;
			overflow:auto;
		}
		#table_order_details thead, #table_order_details tfoot, #table_order_details  tbody tr {
			display:table;
			width:100%;
			table-layout:fixed;
		}
		#table_order_details thead, #table_order_details tfoot {
			width: 100%
		}
		#table_order_details table {
			width:100%;
		}
		#table_order_details .action{
			width: 20px;
		}
		#table_order_details .sort_no{
			width: 45px;
		}
		#table_order_details .bar_code{
			width: 100px;
		}
		#table_order_details .product_type_name{
			width: 50px;
		}
		#table_order_details .product_name{
			width: 120px;
		}
		#table_order_details .product_code{
			width: 120px;
		}
		#table_order_details .total_label{
			width: 440px;
		}
        #table_order_details .weight{
			/* width: 50px; */
		}
        #table_order_details .weight_value{
			text-align: right;
            /* width: 50px; */
		}
        #table_order_details .amount{
			/* width: 90px; */
		}
        #table_order_details .amount_value{
            text-align: right;
			/* width: 90px; */
		}
        #table_order_details .fee{
			/* width: 80px; */
		}
        #table_order_details .fee_value{
            text-align: right;
			/* width: 80px; */
		}
        #table_order_details .edit_weight{
			/* width: 70px; */
		}
        #table_order_details .edit_weight_value{
            text-align: right;
			/* width: 70px; */
		}

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
        // table_order_details = null;
        stamp_weight = Number('{{CRUDBooster::getSetting('trong_luong_tem')}}');
		owner_stock_ids = '{{$stock_ids}}'.split(',');
        order_id = null; // sẽ có khi lưu thành công
		readOnlyAll = false;
        order_details = [];
        card_types = [];
        total_order = {
			total_weight: 0,
			gem_weight: 0,
			gold_weight: 0,
			edit_weight: 0,
            gold_amount: 0,
            fee: 0,
            discount_amount: 0
        };
        order_pays = [];
        product_types = [];
        total_pay = {
			total_weight: 0,
			gem_weight: 0,
			abate_weight: 0,
            gold_weight: 0,
            fee: 0,
            gold_amount: 0,
            pay_amount: 0,
            q10: 0
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
        // total_sale = 0;
        pur_price = 0;
        invalid_order = false;// đánh dấu đơn hàng vi phạm
        lastTimeScanBarCode = moment();
        autosave_add_new_detail = null;
        autosave_remove_detail = null;
        
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

            if(Number('{{CRUDBooster::myPrivilegeId()}}') == 1){
                $('#reset_points').show();
            }
            AutoNumeric.multiple('.money', optionNumberInput);
            
            $.ajax({
                method: "GET",
                url: '{{Route("AdminGoldBankCardControllerGetListAll")}}',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: false,
                success: function (data) {
                    if (data && data.list && data.list.length > 0) {
                        card_types = data.list;
                        let html = ``;
						data.list.forEach(function (detail, i) {
                            html += `<option value=${detail.id}>${detail.name}</option>`;
                        });
                        $('#card_type').append(html);
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
                            $('#customer_name').attr('readonly', true);
                            $('#customer_address').attr('readonly', true);
                            $('#customer_phone').attr('readonly', true);
                            $('#customer_type').val(data.customer.type);
                            AutoNumeric.getAutoNumericElement('#points').set(data.customer.points);
                            AutoNumeric.getAutoNumericElement('#customer_balance').set(data.customer.balance);
                        }else{
                            $('#customer_name').attr('readonly', false);
                            $('#customer_address').attr('readonly', false);
                            $('#customer_phone').attr('readonly', false);
                        }
						if(data.order){
						    order_id = data.order.id;
                            $('#id').val(order_id);
                            $('#order_date').val(moment(data.order.order_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            $('#order_no').val(data.order.order_no);
                            $('#order_type').val(data.order.order_type);
                            $('#payment_method').val(data.order.payment_method);
                            $('#card_type').val(data.order.card_type_id);
                            AutoNumeric.getAutoNumericElement('#sampling_discount').set(data.order.discount);
                            AutoNumeric.getAutoNumericElement('#reduce').set(data.order.reduce);
                            AutoNumeric.getAutoNumericElement('#use_points').set(data.order.use_points);
                            AutoNumeric.getAutoNumericElement('#balance').set(data.order.balance);
                            AutoNumeric.getAutoNumericElement('#actual_weight').set(data.order.actual_weight);
                            AutoNumeric.getAutoNumericElement('#bank_fee').set(data.order.bank_fee);
                            AutoNumeric.getAutoNumericElement('#pay_amount').set(data.order.pay_amount);
                        }
                        if(data.order_pays && data.order_pays.length > 0){
                            data.order_pays.forEach(function (detail, i) {
                                total_pay.total_weight += detail.total_weight;
                                total_pay.gem_weight += detail.gem_weight;
                                total_pay.abate_weight += detail.abate_weight;
                                total_pay.gold_weight += detail.gold_weight;
                                total_pay.fee += detail.fee;
                                total_pay.gold_amount += detail.amount;
                                total_pay.q10 += detail.q10;
                                loadPayDetail(detail);
                            });
                            order_pays = data.order_pays;
						}
						if(data.order_details && data.order_details.length > 0){
                            data.order_details.forEach(function (detail, i) {
                                addNewSaleOrderDetail(detail);
                            });
                            order_details = data.order_details;
                            calcTotalOfSaleOrderDetails();
                            calcTotalSaleOrder();
						}
                        // if ((data.order && data.order.order_type == 1) || ('{{$mode}}' == 'view')){
                        if (data.order && data.order.order_type == 1){
                            disableOrder();
                        }
                        else{
                            $('#bar_code').focus();
                        }
                    }else{
                        $('#customer_name').attr('readonly', false);
                        $('#customer_address').attr('readonly', false);
                        $('#customer_phone').attr('readonly', false);
                        $('#customer_type').val(0);
                        // calcTotalSaleOrder();
                    }
                    //$('#loading').hide();
                },
                error: function (request, status, error) {
                    //$('.loading').hide();
                    swal("Thông báo","Có lỗi xãy ra khi phục hồi đơn hàng, vui lòng thử lại.","warning");
                }
            });
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

        function searchCustomer() {
            let customer_code = $('#customer_code').val();
			if(customer_code && customer_code.trim() != ''){
                //$('.loading').show();
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
                                AutoNumeric.getAutoNumericElement('#sampling_discount').set(data.customer.discount_rate);
                                $('#sampling_discount').trigger("change");
                                $('#customer_type').val(data.customer.type);
                                AutoNumeric.getAutoNumericElement('#points').set(data.customer.points);
                                AutoNumeric.getAutoNumericElement('#customer_balance').set(data.customer.balance);
                                // calcTotalSaleOrder();
                                setTimeout(function () {
                                    $('#bar_code').focus();
                                },100);
                            }else{
                                $('#customer_name').attr('readonly', false);
                                $('#customer_address').attr('readonly', false);
                                $('#customer_phone').attr('readonly', false);
                                // calcTotalSaleOrder();
							}
                        }else{
                            $('#customer_name').attr('readonly', false);
                            $('#customer_address').attr('readonly', false);
                            $('#customer_phone').attr('readonly', false);
                            // calcTotalSaleOrder();
						}
                        //$('#loading').hide();
                    },
                    error: function (request, status, error) {
                        //$('.loading').hide();
                        swal("Thông báo","Có lỗi xãy ra khi tải dữ liệu, vui lòng thử lại.","warning");
                    }
                });
			} else {
                $('#customer_id').val(null);
                $('#customer_name').val(null);
                $('#customer_address').val(null);
                $('#customer_phone').val(null);
                $('#customer_type').val(null);
                AutoNumeric.getAutoNumericElement('#sampling_discount').set(0);
                // calcTotalSaleOrder();
			}
        }

        function showModalcustomer_id() {
            var url_customer_id = "{{action('AdminGoldSaleOrdersController@getModalData')}}/gold_sale_orders/modal-data?table=gold_customers&columns=id,code,name,address,phone&name_column=customer_id&where=deleted_at+is+null&select_to=code:code&columns_name_alias=Mã khách hàng,Tên khách hàng,Địa chỉ,Số điện thoại";
            // console.log(url_customer_id);
            //alert(url_customer_id);
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

        function findProduct(event) {
            if(event == null || event.keyCode == 13) {
                if(moment().diff(lastTimeScanBarCode, 's') >= 1) // productFinding &&
                {
                    lastTimeScanBarCode = moment();
                    // productFinding = true;
                    let bar_code = $('#bar_code').val();
                    let added = false;
                    order_details.forEach(function (detail, index) {
                        if (detail.bar_code == bar_code) {
                            added = true;
                            $('#bar_code').val(null);
                            // swal("Thông báo", "Sản phẩm [" + bar_code + "] đã được thêm.", "info");
                        }
                    });
                    if (bar_code && !added) {
                        // $('.loading').show();
						setTimeout(function () {
                            $('#bar_code').val(null);
                        },200);
                        $.ajax({
                            method: "GET",
                            url: '{{Route("AdminGoldItemsControllerGetSearchItem")}}',
                            data: {
                                order_date:moment($('#order_date').val(), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                                bar_code: bar_code,
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: "json",
                            async: true,
                            success: function (data) {
                                if (data && data.item) {
                                    if (data.item.qty == 0) {
                                        $('#bar_code').val(null);
                                        swal("Thông báo", "Sản phẩm [" + bar_code + "] đã bán.", "warning");
                                    // } else if (owner_stock_ids.indexOf(data.item.stock_id + '') < 0) {
                                    //     swal("Thông báo", "Sản phẩm [" + bar_code + "] nằm trong " + data.item.stock_name + ", không thuộc quyền quản lý của bạn, hãy kiểm tra lại.", "error");
									} else {
                                        let tmp_added = false;
                                        order_details.forEach(function (detail, index) {
                                            if (detail.bar_code == data.item.bar_code) {
                                                tmp_added = true;
                                            }
                                        });
                                        if(!tmp_added) {
                                            data.item.no = order_details ? order_details.length + 1 : 1;
                                            data.item.edit_weight = data.item.gold_weight;
                                            data.item.diff_weight = 0;
                                            if ($('#customer_type').val() == 1) {
                                                data.item.fee = data.item.whole_fee;
                                            }
                                            else{
                                                data.item.fee = data.item.retail_fee;
                                            }
                                            data.item.gold_amount = Math.round(data.item.edit_weight * data.item.price);
                                            data.item.discount_amount = Math.round(data.item.fee * ($('#sampling_discount').val() ? Number($('#sampling_discount').val().replace(/,/g, '')) : 0) / 100);
                                            data.item.amount = data.item.gold_amount + data.item.fee - data.item.discount_amount;
                                            order_details.push(data.item);
                                            autosave_add_new_detail = data.item;
                                            addNewSaleOrderDetail(data.item);
                                            $('#bar_code').val(null);

                                            calcTotalOfSaleOrderDetails();
                                            calcTotalSaleOrder();
                                            autoSave();
                                            $('#bar_code').focus();
                                        }
                                    }
                                } else {
                                    $('#bar_code').val(null);
                                    swal("Thông báo", "Không tìm thấy mã " + bar_code, "warning");
                                }
                                // productFinding = false;
                                // $('.loading').hide();
                            },
                            error: function (request, status, error) {
                                // $('.loading').hide();
								console.log('Lỗi khi tìm sản phẩm ', [request, status, error]);
                                swal("Thông báo", "Có lỗi xãy ra khi tải dữ liệu, vui lòng thử lại.", "warning");
                                // productFinding = false;
                            }
                        });
                    } else {
                        // productFinding = false;
					}
                }
            }
        }

        function saleOrderHeadChange() {
            let sampling_discount = $('#sampling_discount').val()?Number($('#sampling_discount').val().replace(/,/g, '')):0;
            order_details.forEach(function (detail, index) {
				detail.discount_amount = Math.round(detail.fee * sampling_discount / 100);
                detail.amount = detail.gold_amount + detail.fee - detail.discount_amount;
				$(`#discount_amount_${detail.id}`).html(detail.discount_amount.toLocaleString('en-US'));
                $(`#amount_${detail.id}`).html(detail.amount.toLocaleString('en-US'));
            })
            valid_actual_weight();
            calcTotalOfSaleOrderDetails();
            calcTotalSaleOrder();
        }

        function payment_method_change(){
            if(Number($(`#payment_method`).val()) == 0){
                $('#card_type').attr('disabled', true);
                $('#bank_fee').attr('disabled', true);
                AutoNumeric.getAutoNumericElement(`#bank_fee`).set(0);
                calcTotalOfPays();
            }else{
                $('#card_type').attr('disabled', false);
                $('#bank_fee').attr('disabled', false);
                card_type_change();
            }
        }

        function card_type_change(){
            if(card_types && card_types.length > 0){
                card_types.forEach(function (detail, i) {
                    if(detail.id == Number($(`#card_type`).val())){
                        AutoNumeric.getAutoNumericElement(`#bank_fee`).set(detail.bank_fee);
                        calcTotalOfPays();
                    }
                });
            }
        }

        function calcTotalOfSaleOrderDetails() {
            total_order = {
                total_weight: 0,
                gem_weight: 0,
                gold_weight: 0,
                edit_weight: 0,
                gold_amount: 0,
                fee: 0,
                discount_amount: 0,
            };
            order_details.forEach(function (detail, index) {
                total_order.total_weight += detail.total_weight ? detail.total_weight : 0;
                total_order.gem_weight += detail.gem_weight ? detail.gem_weight : 0;
                total_order.gold_weight += detail.gold_weight ? detail.gold_weight : 0;
                total_order.edit_weight += detail.edit_weight ? detail.edit_weight : 0;
                total_order.gold_amount += detail.gold_amount ? detail.gold_amount : 0;
                total_order.fee += detail.fee ? detail.fee : 0;
                total_order.discount_amount += detail.discount_amount ? detail.discount_amount : 0;
            });
            $('#total_order_total_weight').html(total_order.total_weight.toLocaleString('en-US'));
            $('#total_order_gem_weight').html(total_order.gem_weight.toLocaleString('en-US'));
            $('#total_order_gold_weight').html(total_order.gold_weight.toLocaleString('en-US'));
            $('#total_order_edit_weight').html(total_order.edit_weight.toLocaleString('en-US'));
            $('#total_order_diff_weight').html((total_order.edit_weight - total_order.gold_weight).toLocaleString('en-US'));
            $('#total_order_gold_amount').html(total_order.gold_amount.toLocaleString('en-US'));
            $('#total_order_fee').html(total_order.fee.toLocaleString('en-US'));
            $('#total_order_discount_amount').html(total_order.discount_amount.toLocaleString('en-US'));
            $('#total_order_amount').html((total_order.gold_amount + total_order.fee - total_order.discount_amount).toLocaleString('en-US'));
        }

        function addNewSaleOrderDetail(dataRow) {
            if(readOnlyAll){
                swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
			}
            let html = `<tr id="order_detail_` + dataRow.id + `">` +
                `<th class="action text-center"><a style="cursor: pointer;" onclick="removeSaleOrderDetail(` + dataRow.id + `)"><i class="fa fa-remove text-red"></i></a></th>` +
                `<th class="sort_no text-right" id="no_` + dataRow.id + `">${dataRow.no}</th>` +
                `<th class="bar_code">${dataRow.bar_code}</th>` +
                `<th class="product_code">${dataRow.product_code}</th>` +
                `<th class="product_name">${dataRow.product_name}</th>` +
                `<th class="product_type_name text-center">${dataRow.product_type_name}</th>` +
                `<th class="weight_value">${dataRow.total_weight.toLocaleString('en-US')}</th>` +
                `<th class="weight_value">${dataRow.gem_weight.toLocaleString('en-US')}</th>` +
                `<th class="weight_value">${dataRow.gold_weight.toLocaleString('en-US')}</th>` +
                `<th class="no-padding edit_weight"><input id="edit_weight_` + dataRow.id + `" type="text" class="form-control money" value="${dataRow.edit_weight}" onchange="edit_weight_change(` + dataRow.id + `)"></th>` +
                `<th class="weight_value" id="diff_weight_${dataRow.id}">${(dataRow.edit_weight - dataRow.gold_weight).toLocaleString('en-US')}</th>` + //TL chênh lệch
                `<th class="fee_value" id="price_${dataRow.id}">${dataRow.price.toLocaleString('en-US')}</th>` +
                `<th class="amount_value" id="gold_amount_${dataRow.id}">${dataRow.gold_amount.toLocaleString('en-US')}</th>` +
                `<th class="fee_value" id="fee_${dataRow.id}">${dataRow.fee.toLocaleString('en-US')}</th>` +
                `<th class="fee_value" id="discount_amount_${dataRow.id}">${dataRow.discount_amount.toLocaleString('en-US')}</th>` +
                `<th class="text-right" id="amount_${dataRow.id}">${dataRow.amount.toLocaleString('en-US')}</th>` +
                `</tr>`;
			$('#table_order_details tbody').append(html);
            valid_actual_weight();
            // $('#table_order_details tbody').animate({scrollTop:9999999}, 'slow');
        }

        function removeBarcode(event){
            if(readOnlyAll){
                // swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
            }
			if (event == null || event.keyCode == 13) {
				let bar_code_delete = $('#bar_code_delete').val();
				if(order_details && order_details.length) {
					for (let i = 0; i < order_details.length; i++) {
						if(order_details[i].bar_code == bar_code_delete) {
							removeSaleOrderDetail(order_details[i].id);
						}
					}
				}else{
					console.log('Chưa có sản phẩm được quét barcode, không thể xóa');
				}
			}
		}
        function removeSaleOrderDetail(id) {
            if(readOnlyAll){
                // swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
            }
            autosave_remove_detail = null;
            let removeIndex = -1;
            order_details.forEach(function (detail, index) {
                if(detail.id == id) {
                    autosave_remove_detail = detail;
                    removeIndex = index;
                }
                // giảm số thứ tự
                if(removeIndex != -1 && index > removeIndex) {
                    detail.no -= 1;
                    $('#no_'+id).html(detail.no);
				}
            });
            if(autosave_remove_detail){
                $('#order_detail_'+id).remove();
                order_details.splice(removeIndex, 1);
                calcTotalOfSaleOrderDetails();
                calcTotalSaleOrder();
                valid_actual_weight();
                // showStatisticGroupProduct();
                autoSave();
			}
        }
        function discount_amount_change(id) {
            order_details.forEach(function (detail, index) {
                if(detail.id == id) {
                    detail.discount_amount = Number($('#discount_amount_'+id).val()?$('#discount_amount_'+id).val().replace(/,/g, ''):0);
                    // console.log('change detail = ', detail);
                }
            });

            calcTotalSaleOrder();
        }
        function edit_weight_change(id) {
            order_details.forEach(function (detail, index) {
                if(detail.id == id) {
                    detail.edit_weight = $('#edit_weight_'+id).val() ? Math.round(Number($('#edit_weight_'+id).val().replace(/,/g, '')) * 10000) / 10000 : 0;
                    detail.diff_weight = Math.round((detail.edit_weight - detail.gold_weight) * 10000) / 10000;
                    detail.gold_amount = Math.round(detail.edit_weight * detail.price);
                    // detail.discount_amount = Math.round(detail.gold_amount * Number($('#sampling_discount').val()?$('#sampling_discount').val().replace(/,/g, ''):0) / 100);
                    detail.amount = detail.gold_amount + detail.fee - detail.discount_amount;
                    // console.log('change detail = ', detail);

                    $(`#diff_weight_${detail.id}`).html(detail.diff_weight.toLocaleString('en-US'));
                    $(`#gold_amount_${detail.id}`).html(detail.gold_amount.toLocaleString('en-US'));
                    // $(`#discount_amount_${detail.id}`).html(detail.discount_amount.toLocaleString('en-US'));
                    $(`#amount_${detail.id}`).html(detail.amount.toLocaleString('en-US'));
                }
            });

            calcTotalOfSaleOrderDetails();
            calcTotalSaleOrder();
            valid_actual_weight();
            autoSave();
        }

        function loadPayDetail(data) {
            let html = `<tr id="order_pay_index_${data.id}">
                <th class="text-center"><a onclick="removePayDetail(${data.id})" class="text-red" style="cursor: pointer;"><i class="fa fa-remove"></i></a></th>
                <th class="no-padding"><input id="pay${data.id}_description" onchange="pay_description_change(${data.id})" type="text" class="form-control" value="${data.description}" required></th>
                <th class="no-padding">
                    <select id="pay${data.id}_product_type" class="form-control" onchange="pay_product_type_change(${data.id}) value="` + Number(data.product_type_id) + `">`;
            if(product_types && product_types.length > 0){
                product_types.forEach(function (detail, i) {
                    html = html + ` <option value=${detail.id}>${detail.name}</option>`;
                });
            }
            html = html + ` </select>
                    </th>
                    <th class="no-padding"><input id="pay${data.id}_total_weight" onchange="pay_total_weight_change(${data.id})" type="text" class="form-control money" value="${data.total_weight}"></th>
                    <th class="no-padding"><input id="pay${data.id}_gem_weight" onchange="pay_gem_weight_change(${data.id})" type="text" class="form-control money" value="${data.gem_weight}"></th>
                    <th class="no-padding"><input id="pay${data.id}_abate_weight" onchange="pay_abate_weight_change(${data.id})" type="text" class="form-control money" value="${data.abate_weight}"></th>
                    <th class="text-right" id="pay${data.id}_gold_weight">${data.gold_weight}</th>
                    <th class="no-padding"><input id="pay${data.id}_price" onchange="pay_price_change(${data.id})" type="text" class="form-control money" value="${data.price}"></th>
                    <th class="no-padding"><input id="pay${data.id}_fee" onchange="pay_fee_change(${data.id})" type="text" class="form-control money" value="${data.fee}"></th>
                    <th class="text-right" id="pay${data.id}_amount">${data.amount.toLocaleString('en-US')}</th>
                    <th class="no-padding"><input id="pay${data.id}_age" onchange="pay_age_change(${data.id})" type="text" class="form-control money" value="${data.age}"></th>
                    <th class="text-right" id="pay${data.id}_q10">${data.q10.toLocaleString('en-US')}</th>
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
                    <th class="no-padding"><input id="pay${tmp_id}_price" onchange="pay_price_change(${tmp_id})" type="text" class="form-control money" value="${tmp_price.toLocaleString('en-US')}"></th>
                    <th class="no-padding"><input id="pay${tmp_id}_fee" onchange="pay_fee_change(${tmp_id})" type="text" class="form-control money" value="0"></th>
                    <th class="text-right" id="pay${tmp_id}_amount">0</th>
                    <th class="no-padding"><input id="pay${tmp_id}_age" onchange="pay_age_change(${tmp_id})" type="text" class="form-control money" value="${tmp_age.toLocaleString('en-US')}"></th>
                    <th class="text-right" id="pay${tmp_id}_q10">0</th>
                </tr>`;
            $('#table_pays tbody').append(html);

            order_pays.push({
                id: tmp_id, 
                total_weight: 0,
                product_type_id: type_id,
                gem_weight: 0,
                abate_weight: 0,
                gold_weight: 0,
                price: tmp_price,
                fee: 0,
                amount: 0,
                age: tmp_age,
                q10: 0
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
                fee: 0,
                gold_amount: 0,
                q10: 0
            };
            order_pays.forEach(function (pay, i) {
                total_pay.total_weight += pay.total_weight ? pay.total_weight : 0;
                total_pay.gem_weight += pay.gem_weight ? pay.gem_weight : 0;
                total_pay.abate_weight += pay.abate_weight ? pay.abate_weight : 0;
                total_pay.gold_weight += pay.gold_weight ? pay.gold_weight : 0;
                total_pay.fee += pay.fee ? pay.fee : 0;
                total_pay.gold_amount += pay.amount ? pay.amount : 0;
                total_pay.q10 += pay.q10 ? pay.q10 : 0;
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
            total_pay.gold_amount = 0;
            total_pay.q10 = 0;
            
            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.total_weight = $(`#pay${pay.id}_total_weight`).val()?Number($(`#pay${pay.id}_total_weight`).val().replace(/,/g, '')):0;
                    pay.gold_weight = Math.round((pay.total_weight - pay.gem_weight - pay.abate_weight) * 10000) / 10000;
                    pay.q10 = Math.round((pay.gold_weight * pay.age / 100) * 10000) / 10000;
                    pay.amount = Math.round(pay.gold_weight * pay.price) + pay.fee;

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
            total_pay.gold_amount = 0;
            total_pay.q10 = 0;

            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.gem_weight = $(`#pay${pay.id}_gem_weight`).val()?Number($(`#pay${pay.id}_gem_weight`).val().replace(/,/g, '')):0;
                    pay.gold_weight = Math.round((pay.total_weight - pay.gem_weight - pay.abate_weight) * 10000) / 10000;
                    pay.q10 = Math.round((pay.gold_weight * pay.age / 100) * 10000) / 10000;
                    pay.amount = Math.round(pay.gold_weight * pay.price) + pay.fee;

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
            total_pay.gold_amount = 0;
            total_pay.q10 = 0;

            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.abate_weight = $(`#pay${pay.id}_abate_weight`).val()?Number($(`#pay${pay.id}_abate_weight`).val().replace(/,/g, '')):0;
                    pay.gold_weight = Math.round((pay.total_weight - pay.gem_weight - pay.abate_weight) * 10000) / 10000;
                    pay.q10 = Math.round((pay.gold_weight * pay.age / 100) * 10000) / 10000;
                    pay.amount = Math.round(pay.gold_weight * pay.price) + pay.fee;

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

        function pay_price_change(changeId) {
            total_pay.gold_amount = 0;

            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.price = $(`#pay${pay.id}_price`).val()?Number($(`#pay${pay.id}_price`).val().replace(/,/g, '')):0;
                    pay.amount = Math.round(pay.gold_weight * pay.price) + pay.fee;

                    $(`#pay${pay.id}_amount`).html(pay.amount.toLocaleString('en-US'));
                }
                total_pay.gold_amount += pay.amount ? pay.amount : 0;
            });
            calcTotalOfPays();
        }

        function pay_fee_change(changeId) {
            total_pay.fee = 0;
            total_pay.gold_amount = 0;

            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.fee = $(`#pay${pay.id}_fee`).val()?Number($(`#pay${pay.id}_fee`).val().replace(/,/g, '')):0;
                    pay.amount = Math.round(pay.gold_weight * pay.price) + pay.fee;

                    $(`#pay${pay.id}_amount`).html(pay.amount.toLocaleString('en-US'));
                }
                total_pay.fee += pay.fee ? pay.fee : 0;
                total_pay.gold_amount += pay.amount ? pay.amount : 0;
            });
            calcTotalOfPays();
        }

        function pay_age_change(changeId) {
            total_pay.q10 = 0;

            order_pays.forEach(function (pay, i) {
                if(pay.id == changeId) {
                    pay.age = $(`#pay${pay.id}_age`).val()?Number($(`#pay${pay.id}_age`).val().replace(/,/g, '')):0;
                    pay.q10 = Math.round((pay.gold_weight * pay.age / 100) * 10000) / 10000

                    $(`#pay${pay.id}_q10`).html(pay.q10.toLocaleString('en-US'));
                }
                total_pay.q10 += pay.q10 ? pay.q10 : 0;
            });
            calcTotalOfPays();
        }

        function calcTotalOfPays() {
            $('#total_pay_total_weight').html(total_pay.total_weight ? total_pay.total_weight.toLocaleString('en-US') : 0);
            $('#total_pay_gem_weight').html(total_pay.gem_weight ? total_pay.gem_weight.toLocaleString('en-US') : 0);
            $('#total_pay_abate_weight').html(total_pay.abate_weight ? total_pay.abate_weight.toLocaleString('en-US') : 0);
            $('#total_pay_gold_weight').html(total_pay.gold_weight ? total_pay.gold_weight.toLocaleString('en-US') : 0);
            $('#total_pay_fee').html(total_pay.fee ? total_pay.fee.toLocaleString('en-US') : 0);
            $('#total_pay_gold_amount').html(total_pay.gold_amount ? total_pay.gold_amount.toLocaleString('en-US') : 0);
            $('#total_pay_q10').html(total_pay.q10 ? total_pay.q10.toLocaleString('en-US') : 0);

            total_pay.pay_amount = $(`#pay_amount`).val() ? AutoNumeric.getAutoNumericElement(`#pay_amount`).getNumber() : 0;
            // console.log('total_pay.pay_amount = ', total_pay.pay_amount);
            let use_points = $(`#use_points`).val() ? AutoNumeric.getAutoNumericElement(`#use_points`).getNumber() : 0;
            let balance = $(`#balance`).val() ? AutoNumeric.getAutoNumericElement('#balance').getNumber() : 0;
            let total_sales = $(`#total_sales_amount`).val() ? AutoNumeric.getAutoNumericElement('#total_sales_amount').getNumber() : 0;
            let bank_fee = $(`#bank_fee`).val() ? AutoNumeric.getAutoNumericElement('#bank_fee').getNumber() : 0;
            // console.log('total_sales = ', total_sales);
            let remain = total_sales + balance - total_pay.gold_amount - use_points;
            remain = Math.round(remain * (1 + bank_fee / 100));
            // console.log('remain = ', remain);
            
            AutoNumeric.getAutoNumericElement(`#pay_item`).set(total_pay.gold_amount);
            AutoNumeric.getAutoNumericElement(`#remain_amount`).set(remain);
            AutoNumeric.getAutoNumericElement(`#return_amount`).set(total_pay.pay_amount - remain);
        }

        function calcTotalSaleOrder() {
            let reduce = $(`#reduce`).val() ? AutoNumeric.getAutoNumericElement('#reduce').getNumber() : 0;
            let total = total_order.gold_amount + total_order.fee - total_order.discount_amount - reduce;
            AutoNumeric.getAutoNumericElement(`#total_sales_amount`).set(total);
            calcTotalOfPays();
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
            if(Number($('#payment_method').val()) != 0 && !$('#card_type').val()){
                valid = false;
                $('#card_type').addClass('invalid');
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
            }else if(!order_details || order_details.length <= 0){
                valid = false;
                $(`#bar_code`).focus();
                swal("Thông báo", "Chưa nhập hàng bán, vui lòng kiểm tra lại.", "warning");
            }else{
                let points = $('#points').val() ? AutoNumeric.getAutoNumericElement('#points').getNumber() : 0;
                let use_points = $('#use_points').val() ? AutoNumeric.getAutoNumericElement('#use_points').getNumber() : 0;
                let customer_balance = $('#customer_balance').val() ? AutoNumeric.getAutoNumericElement('#customer_balance').getNumber() : 0;
                let balance = $('#balance').val() ? AutoNumeric.getAutoNumericElement('#balance').getNumber() : 0;
                if(use_points > points){
                    valid = false;
                    swal("Thông báo", "Sử dụng tiền tích lũy phải <= tiền tích lũy, vui lòng kiểm tra lại.", "warning");
                    $(`#use_points`).addClass('invalid');
                    $(`#use_points`).focus();
                }else if((customer_balance < balance && customer_balance >= 0) || (customer_balance > balance && customer_balance < 0)){
                    valid = false;
                    swal("Thông báo", "Công nợ thanh toán phải <= công nợ của khách.", "warning");
                    $(`#balance`).addClass('invalid');
                    $(`#balance`).focus();
                }else{
                    let remain = $('#remain_amount').val() ? AutoNumeric.getAutoNumericElement('#remain_amount').getNumber() : 0;
                    // console.log('remain_amount = ', remain);
                    // console.log('pay_amount = ', total_pay_amount);
                    if(remain > 0 && remain > total_pay.pay_amount){
                        valid = false;
                        $('#pay_amount').addClass('invalid');
                        $(`#pay_amount`).focus();
                        swal("Thông báo", "Tổng tiền thanh toán chưa đúng.\nBạn cần thu đủ " + remain.toLocaleString('en-US'), "warning");
                    }else{
                        valid = valid && valid_actual_weight(true);
                    }
                }
			}
            if(valid) {
                order_details.forEach(function (detail, i) {
                    $.ajax({
                        method: "GET",
                        url: '{{Route("AdminGoldItemsControllerGetCheckItem")}}',
                        data: {
                            bar_code: detail.bar_code,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: "json",
                        async: false,
                        success: function (data) {
                            if (data && data.item) {
                                if (data.item.qty == 0) {
                                    valid = false;
                                    swal("Thông báo", "Sản phẩm [" + data.item.bar_code + "] đã bán.", "warning");
                                } 
                                // else if (owner_stock_ids.indexOf(data.item.stock_id + '') < 0) {
                                //     valid = false;
                                //     swal("Thông báo", "Sản phẩm [" + data.item.bar_code + "] nằm trong " + data.item.stock_name + ", không thuộc quyền quản lý của bạn, hãy kiểm tra lại.", "error");
                                // }
                            } else {
                                valid = false;
                                swal("Thông báo", "Không tìm thấy mã " + detail.bar_code, "warning");
                            }
                        },
                        error: function (request, status, error) {
                            console.log('Lỗi khi tìm sản phẩm ', [request, status, error]);
                            swal("Thông báo", "Có lỗi xãy ra khi tải dữ liệu, vui lòng thử lại.", "warning");
                        }
                    });
                    if(!valid) {
                        $(`#edit_weight_${detail.id}`).addClass('invalid');
                        $(`#edit_weight_${detail.id}`).focus();
                        return valid;
                    }
                });
            }
			return valid;
        }
        function valid_actual_weight(show_alert) {
            let valid = true;
            let margin = 0;
            // order_details.forEach(function (detail, index) {
            //     total_weight_calc += detail.total_weight + stamp_weight;
            // });
            let actual_weight = $('#actual_weight').val() ? Number($('#actual_weight').val()) : 0;
            total_order.edit_weight = Math.round(total_order.edit_weight * 10000) / 10000;
            // console.log('total_weight_calc = ', total_order.total_weight);
            // console.log('edit_weight_calc = ', total_order.edit_weight);
            // console.log('gold_weight_calc = ', total_order.gold_weight);
            // console.log('actual_weight = ', actual_weight);
            // if(order_details.length <= 50){
            //     margin = 0.02;
            // } else if(order_details.length <= 100)
            // {
            //     margin = 0.04;
            // } else // if(order_details.length > 100)
            // {
            //     margin = 0.05;
            // }
            $(`#actual_weight`).removeClass('invalid');
            if (Math.round(actual_weight, 3) < Math.round(total_order.total_weight + (total_order.edit_weight - total_order.gold_weight), 3)) {
                valid = false;
                if(show_alert) {
                    swal("Thông báo", "Tổng TL cao hơn Tổng cân thực tế", "warning");
                }
            } else if (Math.round(actual_weight, 3) > Math.round(total_order.total_weight + (total_order.edit_weight - total_order.gold_weight), 3)) {
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
                order_type: finish ? 1 : 0, //0 = Đang nhập, 1 = Hoàn tất
                customer_id: $('#customer_id').val() ? Number($('#customer_id').val()) : null,
                payment_method: $('#payment_method').val() ? Number($('#payment_method').val()) : 0,
                saler_id: Number('{{CRUDBooster::myId()}}'),
                order_date: moment($('#order_date').val(), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                order_no: $('#order_no').val() ? $('#order_no').val() : null,
                discount: $('#sampling_discount').val() ? Number($('#sampling_discount').val().replace(/,/g, '')) : 0,
                actual_weight: $('#actual_weight').val() ? Number($('#actual_weight').val().replace(/,/g, '')) : 0,
                reduce: $('#reduce').val() ? AutoNumeric.getAutoNumericElement('#reduce').getNumber() : 0,
                gold_amount: total_order.gold_amount,
                discount_amount: total_order.discount_amount,
                fee: total_order.fee,
                pay_gold_amount: total_pay.gold_amount,
                pay_amount: total_pay.pay_amount,
                use_points: $('#use_points').val() ? AutoNumeric.getAutoNumericElement('#use_points').getNumber() : 0,
                balance: $('#balance').val() ? AutoNumeric.getAutoNumericElement('#balance').getNumber() : 0,
                card_type_id: $('#card_type').val() ? Number($('#card_type').val()) : 0,
                bank_fee: $('#bank_fee').val() ? AutoNumeric.getAutoNumericElement('#bank_fee').getNumber() : 0,
                return_amount: $('#return_amount').val() ? AutoNumeric.getAutoNumericElement('#return_amount').getNumber() : 0
            };
		}
		function autoSave() {
            if($('#id').val()) {
                if(autosave_add_new_detail) {
                    let current_autosave_detail = autosave_add_new_detail;
                    $.ajax({
                        method: "POST",
                        url: '{{CRUDBooster::mainpath('add-new-order-detail')}}',
                        data: {
                            order: getOrderHeader(false),
                            order_details: [autosave_add_new_detail],
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: "json",
                        async: true,
                        success: function (data) {
                            if (data) {
                                console.log('auto save add new success.')
                                let order_detail_ids = data.order_detail_ids;
                                if(order_detail_ids && order_detail_ids.length > 0){
                                    current_autosave_detail.order_detail_id = order_detail_ids[0];
                                }
                            }
                        },
                        error: function (request, status, error) {
                            $('.loading').hide();
                            swal("Thông báo", "Có lỗi xãy ra khi lưu dữ liệu, vui lòng thử lại.", "error");
                        }
                    });
                    autosave_add_new_detail = null;
                }
                if(autosave_remove_detail) {
                    console.log('autosave_remove_detail.id', autosave_remove_detail.order_detail_id)
                    $.ajax({
                        method: "POST",
                        url: '{{CRUDBooster::mainpath('remove-order-detail')}}',
                        data: {
                            order: getOrderHeader(false),
                            remove_order_detail_id: autosave_remove_detail.order_detail_id,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: "json",
                        async: true,
                        success: function (data) {
                            if (data) {
                                console.log('auto save remove success.')
                            }
                        },
                        error: function (request, status, error) {
                            $('.loading').hide();
                            swal("Thông báo", "Có lỗi xãy ra khi lưu dữ liệu, vui lòng thử lại.", "error");
                        }
                    });
                    autosave_remove_detail = null;
                }
			} else {
                submit(false);
			}
		}
        function submit(finish) {
			if(!finish || validate()){ // nếu finish == false thì không validate
			    if(finish) {
                    $('#save_button').hide();
                    $('.loading').show();
                    //$('#form input').attr('readonly', true);
                    calcTotalSaleOrder();
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
                            address: $('#customer_address').val(),
                            points: $('#points').val() ? AutoNumeric.getAutoNumericElement('#points').getNumber() : 0,
                            balance: $('#customer_balance').val() ? AutoNumeric.getAutoNumericElement('#customer_balance').getNumber() : 0
						},
						order_details: order_details,
						order_pays: order_pays,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data) {
                            if(finish) {
                                disableOrder();
                            }
							let order_detail_ids = data.order_detail_ids;
							if(order_detail_ids && order_detail_ids.length > 0){
								order_detail_ids.forEach(function (detail_id, i) {
									order_details[i].order_detail_id = detail_id;
								});
							}
    						order_id = data.id;
							$('#id').val(order_id);
                            $('#order_no').val(data.order_no);
                            $('#customer_id').val(data.customer_id);
                        } else {
                            $('#bar_code').val(null);
                            swal("Thông báo", "Không tìm thấy mã " + bar_code, "warning");
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
            $('#btn_bar_code').attr('disabled', true);
            $('#check_actual_weight').attr('disabled', true);
            $('#form input').attr('disabled', true);
            $('#form textarea').attr('disabled', true);
            $('#form select').attr('disabled', true);
        }

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }
        
        function printInvoice() {
            if(order_id) {
                // popupWindow("{{action('AdminGoldSaleOrdersController@getPrintInvoice')}}?id=" + order_id,"print");
                popupWindow("{{action('AdminGoldSaleOrdersController@getPrintInvoice')}}/" + order_id,"print");
            }else{
                alert("Bạn không thể in hóa đơn nếu chưa lưu đơn hàng!");
            }
        }

        function resetPoints() {
            $.ajax({
                method: "POST",
                url: '{{CRUDBooster::mainpath('reset-point')}}',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: true,
                success: function (data) {
                    if (data) {
                        swal("Thông báo", "Cập nhật thành công.", "info");
                    }
                },
                error: function (request, status, error) {
                    $('.loading').hide();
                    swal("Thông báo", "Có lỗi xãy ra khi lưu dữ liệu, vui lòng thử lại.", "error");
                }
            });
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