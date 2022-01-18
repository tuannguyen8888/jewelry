<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- Your custom  HTML goes here -->
	<!-- Your html goes here -->
	<div>
		<p><a title="Return" href="{{CRUDBooster::mainpath()}}"><i class="fa fa-chevron-circle-left "></i> &nbsp; Quay lại danh sách</a></p>
		<div class='panel panel-default'>
			<div class="panel-body" id="parent-form-area">
				<form method='post' action='{{CRUDBooster::mainpath('update-received-header')}}' id="form">
					<input type="hidden" name="id" id="id">
					<div class="col-sm-12">
                        <table id="table_received_header" class="table received_header_table">
                            <tr>
                                <th class="th_border">
                                    <div class="header-title form-divider">
                                        <i class="fa fa-list-alt"></i> THÔNG TIN CHUNG
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-3">Số phiếu</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="received_no" id="received_no" class="form-control" placeholder="Tự động tạo" readonly disabled>
                                        </div>
                                        <label class="control-label col-sm-1 text-right">Ngày</label>
                                        <div class="col-sm-4">
                                            <div class="input-group" >
                                                <input id="received_date" readonly type="text" class="form-control bg-white" required>
                                                <div class="input-group-addon bg-gray">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="supplier_code" class="control-label col-sm-3">NCC <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                                        <div class="col-sm-9">
                                            <select id="supplier_id" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="stock_name" class="control-label col-sm-3">Kho <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                                        <div class="col-sm-9">
                                            <select id="stock_id" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-3">Tổng cân Ttế <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <input type="text" name="actual_weight" id="actual_weight" class="form-control money" value="0" placeholder="Tcân thực tế" required>
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-warning btn-flat" id="btn_check_actual_weight" onclick="valid_actual_weight(true);"><i class="fa fa-question"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-3">Ghi chú</label>
                                        <div class="col-sm-9">
                                            <textarea name="notes" id="notes" class="form-control" rows="4"></textarea>
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="header-title form-divider">
                                        <i class="fa fa-list-alt"></i> THÔNG TIN TEM
                                    </div>
                                    <div class="row">
                                        <label for="product_code" class="control-label col-sm-2">Sản phẩm<span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <button id="btn_search_product" type="button" class="btn btn-primary btn-flat" onclick="showModalproduct_id()"><i class="fa fa-search"></i></button>
                                                </span>
                                                <input required type="text" name="product_code" id="product_code" onchange="searchProduct();" class="form-control" placeholder="Mã SP" style="width: 40%">
                                                <input type="text" name="product_name" id="product_name" class="form-control" placeholder="Tên sản phẩm" style="width: 60%" readonly disabled>
                                                <input type="hidden" name="product_id" id="product_id">
                                                <input type="hidden" name="product_category_id" id="product_category_id">
                                                <input type="hidden" name="product_group_id" id="product_group_id">
                                                <input type="hidden" name="product_unit_id" id="product_unit_id">
                                                <input type="hidden" name="producer_id" id="producer_id">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2">Mã vạch<span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <input type="text" name="bar_code" id="bar_code" class="form-control"
                                                    placeholder="Quét mã vạch" autocomplete="off" onkeyup="findItem(event)" readonly disabled>
                                                <span class="input-group-btn">
                                                    <button id="btn_deleted_bar_code" type="button" class="btn btn-danger btn-flat" onclick="$('#modal-datamodal-delete-barcode').modal('show')"><i class="fa fa-remove"></i></button>
                                                </span>
                                                <input type="hidden" name="item_id" id="item_id">
                                            </div>
                                        </div>
                                        <label class="control-label col-sm-2 text-right">Loại vàng</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="product_type_name" id="product_type_name" class="form-control" readonly disabled>
                                            <input type="hidden" name="product_type_id" id="product_type_id">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2">Công lẻ</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="retail_fee" id="retail_fee" class="form-control money" onchange="fee_change();">
                                        </div>
                                        <label class="control-label col-sm-2 text-right">CK VIP (%)</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="discount" id="discount" class="form-control money" onchange="fee_change();">
                                        </div>
                                        <label class="control-label col-sm-2 text-right">Công VIP</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="whole_fee" id="whole_fee" class="form-control money" disabled readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2">Tuổi vàng<span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                                        <div class="col-sm-2">
                                            <input type="text" name="age" id="age" class="form-control money" onchange="q10_change();" required>
                                        </div>
                                        <label class="control-label col-sm-2 text-right">Q10</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="q10" id="q10" class="form-control money" readonly disabled>
                                        </div>
                                        <label class="control-label col-sm-2 text-right">Công vốn<span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                                        <div class="col-sm-2">
                                            <input type="text" name="fund_fee" id="fund_fee" class="form-control money">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2">TL tổng<span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                                        <div class="col-sm-2">
                                            <input type="text" name="total_weight" id="total_weight" class="form-control money" style="background-color: rgba(251,240,83,0.52)" 
                                                onchange="weight_change();" required>
                                            <!-- <div class="input-group">
                                                <input type="text" name="total_weight" id="total_weight" class="form-control money" onchange="weight_change();" onkeyup="addNewItem(event)" required>
                                            </div> -->
                                        </div>
                                        <label class="control-label col-sm-2 text-right">TL đá</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="gem_weight" id="gem_weight" class="form-control money" onchange="weight_change();">
                                        </div>
                                        <label class="control-label col-sm-2 text-right">TL vàng</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="gold_weight" id="gold_weight" class="form-control money" readonly disabled>
                                        </div>
                                    </div>
                                    <div class="box-footer" style="background: #F5F5F5">
                                        <div class="form-group">
                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-info btn-flat" id="btn_add_item" onclick="addNewItem()"><i class="fa fa-plus"></i> Thêm</button>
                                            </div>
                                            <div class="col-sm-2">
                                                <button type="button" class="btn btn-info btn-flat" id="btn_get_data_device" onclick="getDataDevice()"><i class="fa fa-plus"></i> Đọc dữ liệu</button>
                                            </div>
                                            <!-- <label class="control-label col-sm-9 text-right">Xem trước khi in</label> -->
                                            <div class="col-sm-5 text-right">
                                                <label class="control-label text-blue">
                                                    <input type="checkbox" value="0" name="device_data" id="device_data" onchange="autoGetDataDevice(this)">
                                                    Lấy dữ liệu từ cân
                                                </label>
                                            </div>
                                            <div class="col-sm-5 text-right">
                                                <label class="control-label text-red">
                                                    <input type="checkbox" value="0" name="print_review" id="print_review">
                                                    Xem trước khi in
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </table>
					</div>

					<div class="col-sm-12">
						<div class="form-group header-group-1">
							<table id="table_received_items" class='table table-bordered' height=50>
								<thead>
								<tr class="bg-success">
                                    <th class="action">#</th>
                                    <th class="sort_no">Stt</th>
									<th class="bar_code">Mã vạch</th>
									<th class="product_code">Mã SP</th>
									<th class="product_name">Tên SP</th>
									<th class="product_type_name">Loại vàng</th>
									<th>TL Tổng</th>
									<th>TL Đá</th>
									<th>TL Vàng</th>
									<th>Tuổi vàng</th>
                                    <th>Q10</th>
                                    <th>Công vốn</th>
									<th>Công lẻ</th>
                                    <th>Công VIP</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
								<tr class="bg-gray-active">
									<th colspan="6" class="total_label">Tổng cộng</th>
									<th id="total_total_weight" class="text-right">0</th>
									<th id="total_gem_weight" class="text-right">0</th>
									<th id="total_gold_weight" class="text-right">0</th>
                                    <th></th>
									<th id="total_q10" class="text-right">0</th>
									<th id="total_fund_fee" class="text-right">0</th>
                                    <th id="total_retail_fee" class="text-right">0</th>
                                    <th id="total_whole_fee" class="text-right">0</th>
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
							<button id="save_button" class="btn btn-success" onclick="submit()"><i class="fa fa-save"></i> Lưu</button>
						@endif
						<a id="print_invoice" style="display: none;cursor: pointer;" onclick="printReceived()" class="btn btn-info"><i class="fa fa-print"></i> Phiếu nhập</a>
						<a id="print_report_detail" style="display: none;cursor: pointer;" onclick="printReportDetail()" class="btn btn-primary"><i class="fa fa-print"></i> Bảng kê chi tiết</a>
					</div>
				</div>
			</div>
		</div>
	</div>

    <div id="modal-datamodal-product_id" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">
		<div class="modal-dialog modal-lg " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><i class="fa fa-search"></i> Browse Data | Sản phẩm</h4>
				</div>
				<div class="modal-body">
					<iframe id="iframe-modal-product_id" style="border:0;height: 430px;width: 100%"></iframe>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>
    <div id="modal-datamodal-delete-barcode" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">
		<div class="modal-dialog modal-md " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><i class="fa fa-search"></i> Delete Barcode | Hàng đang nhập</h4>
				</div>
				<div class="modal-body">
					<form onsubmit="event.preventDefault();" id="form" style="min-height: 50px;">
						<input type="hidden" name="id" id="id">
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
        #table_received_header tbody tr {
			display:table;
			table-layout:fixed;
            border:none;
		}
        .received_header_table{
            margin-bottom:0px;
            border:none;
        }
        #table_received_header .th_border {
			border-right: 1px solid #dddddd;
            width:45%;
		}
        #table_received_header th {
			/* width:50%; */
            /* margin-right: 10px; */
		}

		#table_received_items tbody {
			display:block;
			max-height:200px;
			overflow:auto;
		}
		#table_received_items thead, #table_received_items tfoot, #table_received_items  tbody tr {
			display:table;
			width:100%;
			table-layout:fixed;
		}
		#table_received_items thead, #table_received_items tfoot {
			width: 100%
		}
		#table_received_items table {
			width:100%;
		}
		#table_received_items .action{
			width: 30px;
		}
		#table_received_items .sort_no{
			width: 30px;
		}
		#table_received_items .bar_code{
			width: 110px;
		}
		#table_received_items .product_type_name{
			width: 80px;
		}
		#table_received_items .product_name{
			width: 100px;
		}
		#table_received_items .product_code{
			width: 100px;
		}
		#table_received_items .total_label{
			width: 450px;
		}

		.form-divider {
			/*padding: 10px 0px 10px 0px;*/
			margin-bottom: 10px;
			border-bottom: 1px solid #dddddd;
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
		.hide_invalid_total_weight{
			display: none;
		}
	</style>

	<script type="application/javascript">
        local_server_active = true;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // table_received_items = null;
        stamp_weight = Number('{{CRUDBooster::getSetting('trong_luong_tem')}}');
		owner_stock_ids = '{{$stock_ids}}'.split(',');
        received_id = null; // sẽ có khi lưu thành công
		readOnlyAll = false;
        received_items = [];
        total_received = null;
        optionNumberInput = {
            allowDecimalPadding: false,
            decimalPlaces: 4,
            decimalPlacesRawValue: 4,
            leadingZero: "allow",
            modifyValueOnWheel: false,
            negativeSignCharacter: "−",
            outputFormat: "number"
        };
        lastTimeScanBarCode = moment();
        isProgess = false;

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
            isProgess = false;
            let todayStr = moment().format('DD/MM/YYYY');
            if($('#received_date').val() == ''){
                $('#received_date').val(moment().format('DD/MM/YYYY'));
            }
            $('#received_date').datetimepicker({
                format:'d/m/Y',
                autoclose:true,
                todayHighlight:true,
                showOnFocus:false,
                step: 5
            });
            AutoNumeric.multiple('.money', optionNumberInput);
            loadStock();
            loadSupplier();

            let resume_id = getUrlParameter('resume_id') ? getUrlParameter('resume_id') : '{{$resume_id}}';
            if(resume_id) {
                resume(resume_id);
			// }else{
            //     setInterval(function () {
            //         if($('#device_data').is(":checked")){
            //             AutoNumeric.getAutoNumericElement('#total_weight').set(getDataDevice());
            //             $('#total_weight').trigger('change');
            //         }
            //     }, 2000);
            }
		});

		function resume(id) {
            $.ajax({
                method: "GET",
                url: '{{CRUDBooster::mainpath('resume-received')}}',
                data: {
                    received_id: id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: true,
                success: function (data) {
                    if (data){
						if(data.received){
						    received_id = data.received.id;
                            $('#id').val(received_id);
                            $('#received_date').val(moment(data.received.received_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            $('#received_no').val(data.received.received_no);
                            $('#notes').val(data.received.notes);
                            $('#stock_id').val(data.received.stock_id);
                            $('#supplier_id').val(data.received.supplier_id);
                            AutoNumeric.getAutoNumericElement('#actual_weight').set(data.received.actual_weight);
						}
                        calcTotalOfDetails(data.total);
						if(data.items && data.items.length > 0){
                            data.items.forEach(function (detail, i) {
                                detail.sort_no = i + 1;
                                buildDetails(detail);
                                received_items.push(detail);
                            });
						}
                        if (data.received && data.received.status == 1){
                            disableReceived();
                        }
                        else{
                            $('#product_code').focus();
                        }
                    }
                },
                error: function (request, status, error) {
                    swal("Thông báo","Có lỗi xãy ra khi phục hồi đơn hàng, vui lòng thử lại.","warning");
                }
            });
        }

        function loadStock() {
            $.ajax({
                method: "GET",
                url: '{{Route("AdminGoldStocksControllerGetStocksByBrand")}}',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: false,
                success: function (data) {
                    if (data && data.stocks && data.stocks.length > 0) {
                        let html = '';
						data.stocks.forEach(function (detail, i) {
                            html += `<option value=${detail.id}>${detail.name}</option>`;					
                        });
                        $('#stock_id').append(html);
                    }
                },
                error: function (request, status, error) {
                    console.log('PostAdd status = ', status);
                    console.log('PostAdd error = ', error);
                }
            });
        }

        function loadSupplier() {
            $.ajax({
                method: "GET",
                url: '{{Route("AdminGoldSuppliersControllerGetSuppliers")}}',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: false,
                success: function (data) {
                    if (data && data.suppliers && data.suppliers.length > 0) {
                        let html = '';
						data.suppliers.forEach(function (detail, i) {
                            html += `<option value=${detail.id}>${detail.name}</option>`;					
                        });
                        if('{{CRUDBooster::myPrivilegeId()}}'=='4' || '{{CRUDBooster::myPrivilegeId()}}'=='1'){
                            html += `<option value=0></option>`;					
                        }
                        $('#supplier_id').append(html);
                    }
                },
                error: function (request, status, error) {
                    console.log('PostAdd status = ', status);
                    console.log('PostAdd error = ', error);
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

        function searchProduct() {
            let product_code = $('#product_code').val();
			if(product_code && product_code.trim() != ''){
                $.ajax({
                    method: "GET",
                    url: '{{Route("AdminGoldProductsControllerGetSearchProduct")}}',
                    data: {
                        product_code: product_code,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data){
                            if(data.product){
                                $('#product_id').val(data.product.id);
                                $('#product_name').val(data.product.product_name);
                                $('#product_type_id').val(data.product.product_type_id);
                                $('#product_type_name').val(data.product.product_type_name);
                                $('#product_category_id').val(data.product.product_category_id);
                                $('#product_group_id').val(data.product.product_group_id);
                                $('#product_unit_id').val(data.product.product_unit_id);
                                $('#producer_id').val(data.product.producer_id);
                                const retail_fee = AutoNumeric.getAutoNumericElement('#retail_fee');
                                retail_fee.set(data.product.retail_fee);
                                const whole_fee = AutoNumeric.getAutoNumericElement('#whole_fee');
                                whole_fee.set(data.product.whole_fee);
                                const fund_fee = AutoNumeric.getAutoNumericElement('#fund_fee');
                                fund_fee.set(data.product.fund_fee);
                                const gem_weight = AutoNumeric.getAutoNumericElement('#gem_weight');
                                gem_weight.set(data.product.gem_weight);

                                let total_weight = $('#total_weight').val() ? $('#total_weight').val() : 0;
                                const gold_weight = AutoNumeric.getAutoNumericElement('#gold_weight');
                                if(total_weight == 0){
                                    gold_weight.set(0);
                                }
                                else{
                                    gold_weight.set(total_weight - data.product.gem_weight);
                                }
                                
                                // setTimeout(function () {
                                $('#discount').focus();
                                // },100);
							}
						}
                    },
                    error: function (request, status, error) {
                        swal("Thông báo","Có lỗi xãy ra khi tải dữ liệu, vui lòng thử lại.","warning");
                    }
                });
			} else {
                $('#product_id').val(null);
                $('#product_name').val(null);
                $('#retail_fee').val(0);
                $('#whole_fee').val(0);
                $('#fund_fee').val(0);
                $('#gem_weight').val(0);
                $('#gold_weight').val($('#total_weight').val() ? $('#total_weight').val() : 0);
			}
        }

        function showModalproduct_id() {
            var url_product_id = "{{action('AdminGoldStocksReceivedController@getModalData')}}/gold_stocks_received/modal-data?table=gold_products&columns=id,product_code,product_name&name_column=product_id&where=deleted_at+is+null&select_to=product_code:product_code&columns_name_alias=Mã sản phẩm,Tên sản phẩm";
            $('#iframe-modal-product_id').attr('src',url_product_id);
            $('#modal-datamodal-product_id').modal('show');
        }
        function hideModalproduct_id() {
            $('#modal-datamodal-product_id').modal('hide');
        }

        function selectAdditionalDataproduct_id(select_to_json) {
            $('#product_code').val(select_to_json.product_code).trigger('change');
            hideModalproduct_id();
        }

        function weight_change() {
            let total_weight = $('#total_weight').val() ? Number($('#total_weight').val().replace(/,/g, '')) : 0;
            let gem_weight = $('#gem_weight').val() ? Number($('#gem_weight').val().replace(/,/g, '')) : 0;
            const element = AutoNumeric.getAutoNumericElement('#gold_weight');
            element.set(total_weight - gem_weight);
            q10_change();
        }

        function fee_change() {
            let retail_fee = $('#retail_fee').val() ? Number($('#retail_fee').val().replace(/,/g, '')) : 0;
            let discount = $('#discount').val() ? Number($('#discount').val().replace(/,/g, '')) : 0;
            const element = AutoNumeric.getAutoNumericElement('#whole_fee');
            element.set(Math.round(retail_fee * (1 - discount / 100)));
            // $(`#total_weight`).focus();
        }

        function q10_change() {
            let gold_weight = $('#gold_weight').val() ? Number($('#gold_weight').val().replace(/,/g, '')) : 0;
            let age = $('#age').val() ? Number($('#age').val().replace(/,/g, '')) : 0;
            const element = AutoNumeric.getAutoNumericElement('#q10');
            element.set(Math.round((gold_weight * age / 100) * 10000) / 10000);
            // $(`#total_weight`).focus();
        }

        function calcTotalOfDetails(data) {
            if(data){
                total_received = data;                
            }else{
                total_received = {
                    total_weight: 0,
                    gem_weight: 0,
                    gold_weight: 0,
                    q10: 0,
                    fund_fee: 0,
                    whole_fee: 0
                };
            }

            $('#total_total_weight').html(data.total_weight.toLocaleString('en-US'));
            $('#total_gem_weight').html(data.gem_weight.toLocaleString('en-US'));
            $('#total_gold_weight').html(data.gold_weight.toLocaleString('en-US'));
            $('#total_q10').html(data.q10.toLocaleString('en-US'));
            $('#total_fund_fee').html(data.fund_fee.toLocaleString('en-US'));
            $('#total_retail_fee').html(data.retail_fee.toLocaleString('en-US'));
            $('#total_whole_fee').html(data.whole_fee.toLocaleString('en-US'));    
        }

        function buildDetails(data) {
            if(data){
                let html = `<tr id="detail_${data.id}">
                        <th class="action text-center"><a style="cursor: pointer;" onclick="removeItem(${data.id})"><i class="fa fa-remove text-red"></i></a></th>
                        <th class="sort_no text-right" id="sort_no_${data.id}">${data.sort_no}</th>
                        <th class="bar_code">${data.bar_code}</th>
                        <th class="product_code">${data.product_code}</th>
                        <th class="product_name">${data.product_name}</th>
                        <th class="product_type_name">${data.product_type_name}</th>
                        <th class="text-right">${data.total_weight.toLocaleString('en-US')}</th>
                        <th class="text-right">${data.gem_weight.toLocaleString('en-US')}</th>
                        <th class="text-right">${data.gold_weight.toLocaleString('en-US')}</th>
                        <th class="text-right">${data.age.toLocaleString('en-US')}</th>
                        <th class="text-right">${data.q10.toLocaleString('en-US')}</th>
                        <th class="text-right">${data.fund_fee.toLocaleString('en-US')}</th>
                        <th class="text-right">${data.retail_fee.toLocaleString('en-US')}</th>
                        <th class="text-right">${data.whole_fee.toLocaleString('en-US')}</th>
                    </tr>`;
                $('#table_received_items tbody').append(html);
                // $('#table_received_items tbody').animate({scrollTop:9999999}, 'slow');
            }
        }

        function addNewItem(event) {
            if (event != null && event.keyCode != 13) {
                return;
            }
            if(readOnlyAll){
                swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu phiếu nhập, hãy tạo phiếu nhập mới.", "warning");
                return;
			}
            if(!validateItem()){
                return;
            }

            console.log('isProgess = ', isProgess)
            if (isProgess == true) {
                return;
            }else{
                isProgess = true;
            }
            let total_weight = $('#total_weight').val() ? Number($('#total_weight').val().replace(/,/g, '')) : 0;
            let gem_weight = $('#gem_weight').val() ? Number($('#gem_weight').val().replace(/,/g, '')) : 0;
            let gold_weight = $('#gold_weight').val() ? Number($('#gold_weight').val().replace(/,/g, '')) : 0;
            if(Math.round(total_weight*1000)/1000 != Math.round((Number(gem_weight) + Number(gold_weight))*1000)/1000){
                swal("Thông báo", "Dữ liệu trọng lượng có bất thường, vui lòng kiểm tra lại. TL tổng("+total_weight+") = TL đá("+gem_weight+") + TL vàng("+gold_weight+").", "warning");
                return;
            }
            // console.log('isProgess 1 = ', isProgess)
            item = {
                id: $('#item_id').val() ? Number($('#item_id').val()) : null,
                product_id: $('#product_id').val() ? Number($('#product_id').val()) : null,
                product_type_id: $('#product_type_id').val() ? Number($('#product_type_id').val()) : null,
                product_category_id: $('#product_category_id').val() ? Number($('#product_category_id').val()) : null,
                product_group_id: $('#product_group_id').val() ? Number($('#product_group_id').val()) : null,
                product_unit_id: $('#product_unit_id').val() ? Number($('#product_unit_id').val()) : null,
                producer_id: $('#producer_id').val() ? Number($('#producer_id').val()) : null,
                // bar_code: $('#bar_code').val(),
                total_weight: total_weight,
                gem_weight: gem_weight,
                gold_weight: gold_weight,
                retail_fee: $('#retail_fee').val() ? Number($('#retail_fee').val().replace(/,/g, '')) : 0,
                whole_fee: $('#whole_fee').val() ? Number($('#whole_fee').val().replace(/,/g, '')) : 0,
                fund_fee: $('#fund_fee').val() ? Number($('#fund_fee').val().replace(/,/g, '')) : 0,
                discount: $('#discount').val() ? Number($('#discount').val().replace(/,/g, '')) : 0,
                age: $('#age').val() ? Number($('#age').val().replace(/,/g, '')) : 0,
                q10: $('#q10').val() ? Number($('#q10').val().replace(/,/g, '')) : 0
            };

            $.ajax({
                method: "POST",
                url: '{{CRUDBooster::mainpath('add-new-item')}}',
                data: {
                    received: getReceivedHeader(),
                    item: item,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: true,
                success: function (data) {
                    if (data) {
                        // console.log('auto save add new success.')
                        if(!received_id){
                            received_id = data.id;
                            $('#id').val(received_id);
                            $('#received_no').val(data.received_no);
                        }
                        item.id = data.item_id
                        item.product_code = $('#product_code').val(),
                        item.product_name = $('#product_name').val(),
                        item.product_type_name = $('#product_type_name').val(),
                        item.bar_code = data.bar_code;
                        item.sort_no = received_items.length + 1;
                        buildDetails(item);
                        calcTotalOfDetails(data.total);
                        received_items.push(item);

                        // $('#bar_code').val(Number(moment()));
                        $('#item_id').val(null);            
                        const element = AutoNumeric.getAutoNumericElement('#total_weight');
                        element.set(0);
                        $('#total_weight').trigger('change');
                        $('#total_weight').focus();
                        isProgess = false;
                        printTem(item.id);
                    }
                },
                error: function (request, status, error) {
                    $('.loading').hide();
                    isProgess = false;
                    swal("Thông báo", "Có lỗi xãy ra khi lưu dữ liệu, vui lòng thử lại.", "error");
                }
            });
        }

        function removeBarcode(event){
            if(readOnlyAll){
                // swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
            }
			if (event == null || event.keyCode == 13) {
				let bar_code_delete = $('#bar_code_delete').val();
				if(received_items && received_items.length > 0) {
                    received_items.forEach(function (detail, index) {
						if(detail.bar_code == bar_code_delete) {
                            removeItem(detail.id);
						}
					});
				}else{
					console.log('Chưa có sản phẩm được quét barcode, không thể xóa');
				}
			}
		}

        function removeItem(id) {
            if(readOnlyAll){
                // swal("Thông báo", "Bạn không thể thêm sản phẩm sau khi đã lưu đơn hàng, hãy tạo đơn hàng mới.", "warning");
                return;
            }

            $.ajax({
                method: "POST",
                url: '{{CRUDBooster::mainpath('remove-item')}}',
                data: {
                    received_id: received_id,
                    item_id: id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: true,
                success: function (data) {
                    if (data) {
                        let removeIndex = -1;
                        // console.log('order_details = ', received_items);
                        received_items.forEach(function (detail, index) {
                            if(detail.id == id) {
                                removeIndex = index;
                            }
                            // giảm số thứ tự
                            if(removeIndex != -1 && index > removeIndex) {
                                detail.sort_no -= 1;
                                $('#sort_no_' + detail.id).html(detail.sort_no);
                            }
                        });
                        $('#detail_'+id).remove();
                        received_items.splice(removeIndex, 1);
                        calcTotalOfDetails(data.total);
                        $('#total_weight').focus();
                    }
                },
                error: function (request, status, error) {
                    $('.loading').hide();
                    swal("Thông báo", "Có lỗi xãy ra khi xóa dữ liệu, vui lòng thử lại.", "error");
                }
            });
        }

        function validateItem() {
            let valid = validate();
            if(valid){
                if(!$('#product_code').val()){
                    valid = false;
                    $('#product_code').addClass('invalid');
                    $(`#product_code`).focus();
                }
                if(!$('#age').val()){
                    valid = false;
                    $('#age').addClass('invalid');
                    $(`#age`).focus();
                }
                if(!$('#fund_fee').val()){
                    valid = false;
                    $('#fund_fee').addClass('invalid');
                    $(`#fund_fee`).focus();
                }
                let total_weight = $('#total_weight').val() ? Number($('#total_weight').val().replace(/,/g, '')) : 0;
                let gold_weight = $('#gold_weight').val() ? Number($('#gold_weight').val().replace(/,/g, '')) : 0;
                if(total_weight < 0 || gold_weight < 0){
                    valid = false;
                    $('#total_weight').addClass('invalid');
                    $(`#total_weight`).focus();
                }
                if(!valid) {
                    swal("Thông báo", "Dữ liệu chưa được nhập đầy đủ, vui lòng kiểm tra lại.", "warning");
                }
            }
			return valid;
        }

        function validate() {
			let valid = true;
            $('#form input').removeClass('invalid');
			if(!$('#supplier_id').val() && '{{CRUDBooster::myPrivilegeId()}}'!='4' && '{{CRUDBooster::myPrivilegeId()}}'!='1'){
                valid = false;
                $('#supplier_id').addClass('invalid');
                $(`#supplier_id`).focus();
            }
            if(!$('#stock_id').val()){
                valid = false;
                $('#stock_id').addClass('invalid');
                $(`#stock_id`).focus();
            }
            if(!$('#received_date').val()){
                valid = false;
                $('#received_date').addClass('invalid');
                $(`#received_date`).focus();
            }
            
            if(!valid) {
                swal("Thông báo", "Dữ liệu chưa được nhập đầy đủ, vui lòng kiểm tra lại.", "warning");
            }
			return valid;
        }

        function valid_actual_weight(show_alert) {
            let valid = true;
            let margin = 0;
            let actual_weight = $('#actual_weight').val() ? Number($('#actual_weight').val().replace(/,/g, '')) : 0;
            $(`#actual_weight`).removeClass('invalid');

            if (Math.round(actual_weight, 3) < Math.round(total_received.total_weight, 3)) {
                valid = false;
                if(show_alert) {
                    swal("Thông báo", "Tổng TL cao hơn Tổng cân thực tế", "warning");
                }
            } else if (Math.round(actual_weight, 3) > Math.round(total_received.total_weight, 3)) {
                valid = false;
                if(show_alert) {
                    swal("Thông báo", "Tổng TL thấp hơn Tổng cân thực tế", "warning");
                }
            }
            $('#btn_check_actual_weight').removeClass('btn-warning');
            $('#btn_check_actual_weight').removeClass('btn-success');
            if(valid){
                $('#btn_check_actual_weight').addClass('btn-success');
                $('#btn_check_actual_weight').html('<i class="fa fa-check"></i>');
                $('.hide_invalid_actual_weight').show();
			} else {
                $(`#actual_weight`).focus();
                $(`#actual_weight`).addClass('invalid');
                $('#btn_check_actual_weight').addClass('btn-warning');
                $('#btn_check_actual_weight').html('<i class="fa fa-question"></i>');
                $('.hide_invalid_actual_weight').hide();
			}
            return valid;
        }

        function getReceivedHeader() {
            return {
                id: $('#id').val() ? Number($('#id').val()) : null,
                supplier_id: $('#supplier_id').val() ? Number($('#supplier_id').val()) : null,
                stock_id: $('#stock_id').val() ? Number($('#stock_id').val()) : null,
                received_date: moment($('#received_date').val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                received_no: $('#received_no').val() ? $('#received_no').val() : null,
                actual_weight: Number($('#actual_weight').val() ? $('#actual_weight').val().replace(/,/g, '') : 0),
                notes: $('#notes').val()
            };
		}

		function submit() {
            if(!received_items || received_items.length <= 0){
                swal("Thông báo", "Chưa nhập tem, vui lòng kiểm tra lại.", "warning");
                $(`#total_weight`).focus();
                return;
			}

            if(!valid_actual_weight(true)){
                return;
            }

			if(validate()){
                $('#save_button').hide();
                $('.loading').show();

                $.ajax({
                    method: "POST",
                    url: '{{CRUDBooster::mainpath('update-received-header')}}',
                    data: {
                        received: getReceivedHeader(),
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data) {
                            disableReceived();
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
			// } else {
            //     $('#save_button').show();
			}
        }

        function disableReceived() {
            readOnlyAll = true;
            $('#print_invoice').show();
            $('#print_report_detail').show();
            $('#save_button').hide();
            $('#btn_search_product').attr('disabled', true);
            $('#btn_deleted_bar_code').attr('disabled', true);
            $('#btn_check_actual_weight').attr('disabled', true);
            $('#btn_add_item').attr('disabled', true);
            $('#form input').attr('disabled', true);
            $('#form textarea').attr('disabled', true);
            $('#form select').attr('disabled', true);
        }

        function popupWindow(url, windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }

        function printReceived() {
            if(received_id) {
                popupWindow("{{action('AdminGoldStocksReceivedController@getPrintReceived')}}/" + received_id,"print");
            }else{
                alert("Bạn không thể in hóa đơn nếu chưa lưu đơn hàng!");
            }
        }

        function printReportDetail() {
            if(received_id) {
                popupWindow("{{action('AdminGoldStocksReceivedController@getPrintReceivedDetail')}}/" + received_id,"print");
            }else{
                alert("Bạn không thể in bảng kê chi tiết nếu chưa lưu đơn hàng!");
            }
        }

        function printTem(id) {
            if(id) {
                // let print_review = $('#print_review').is(":checked");
                // console.log('print_review = ', print_review)
                if(local_server_active)
                {
                    ////1st Get JSON from cloud
                //     $.ajax({
                //         method: "GET",
                //         //url: 'http://localhost:8088/system/console/ShinkoDJ',
                //         url: "{{Route('AdminGoldItemsControllerGetPrintTemInfo')}}/" + id,
                //         // data: {
                //         //     _token: '{{ csrf_token() }}'
                //         // },
                //         dataType: "json",
                //         async: false,
                //         success: function (data0) {
                //             console.log('data = ', data0);
                // //             $.ajax({
                // //                 type: "POST",
				// // headers: {
                // //             		'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                // //         	},
                // //                 url: 'http://localhost:8000/device/print'+'?bar_code='+data0.bar_code+'&gem_weight='+data0.gem_weight+'&gold_weight='+data0.gold_weight+'&product_code='+data0.product_code+'&product_name='+data0.product_name+'&retail_fee='+data0.retail_fee+'&supplier='+data0.supplier+'&total_weight='+data0.total_weight+'&whole_fee='+data0.whole_fee+'&types='+data0.types+'',
                // //                 //data: {	
				// // //	_token: '{{ csrf_token() }}',
                // //                 //    	"bar_code": "003979"				},
                // //                 dataType: 'json',
                // //                 processData: false,
                // //                 contentType: 'application/json',
                // //                 CrossDomain:true,
                // //                 async: false,
                // //                 success: function (data) {
                // //                     console.log('data = ', data);
                // //                 },
                // //                 error: function (request, status, error) {
                // //                     local_server_active = false;
                // //                     console.log('PostAdd status = ', status);
                // //                     console.log('PostAdd error = ', error);
                // //                 } 
                // //             });
                //             var http_request;
                //             http_request = new XMLHttpRequest();
                //             http_request.onreadystatechange = function () { 
                //                 if (http_request.readyState>3 && http_request.status==200) 
                //                 { 
                //                     //success(http_request.responseText); 
                //                 } 
                //             };
                //             http_request.open("POST", "http://localhost:8000/device/print");
                //             http_request.withCredentials = true;
                //             http_request.setRequestHeader("Content-Type", "application/json");
                //             http_request.responseType = 'blob';
                //             http_request.send(JSON.stringify(data0));
                //             http_request.onload = () => {

                //                 //console.log(`Data Loaded: ${http_request.status} ${http_request.response}`);
                //                 // process response
                //                 if (http_request.status == 200) {
                //                     // parse JSON data
                //                     var blob = new Blob([http_request.response], { type: 'application/pdf' });
				//                     //fileURL = URL.createObjectURL(blob);
 
				//                     // open new URL
				//                     //window.open(fileURL, '_blank');
				//                     //popupWindow(fileURL, 'print');
				//                     var link = document.createElement('a');

            	// 		            link.href = window.URL.createObjectURL(blob);
                //                     link.download = "document.pdf";

                //                     link.click();
                //                 } else {
                //                     console.error('Error!');
                //                 }
                //             };

                //             // listen for `error` event
                //             http_request.onerror = () => {
                //                 console.error('Request failed.');
                //             };

                //             // listen for `progress` event
                //             http_request.onprogress = (event) => {
                //                 // event.loaded returns how many bytes are downloaded
                //                 // event.total returns the total number of bytes
                //                 // event.total is only available if server sends `Content-Length` header
                //                 console.log(`Downloaded ${event.loaded} of ${event.total}`);
                //             };
                //         },
                //         error: function (request, status, error) {
                //             console.log('PostAdd status = ', status);
                //             console.log('PostAdd error = ', error);
                //         }
                //     });

                    var http_request;
                    http_request = new XMLHttpRequest();
                    http_request.onreadystatechange = function () { 
                        if (http_request.readyState>3 && http_request.status==200) 
                        { 
                            //success(http_request.responseText); 
                        } 
                    };
                    http_request.open("GET", "{{Route('AdminGoldItemsControllerGetPrintTem')}}/" + id);
                    http_request.withCredentials = true;
                    http_request.setRequestHeader("Content-Type", "application/json");
                    http_request.responseType = 'blob';
                    http_request.send();
                    http_request.onload = () => {

                        //console.log(`Data Loaded: ${http_request.status} ${http_request.response}`);
                        // process response
                        if (http_request.status == 200) {
                            // parse JSON data
                            var blob = new Blob([http_request.response], { type: 'application/pdf' });
                            //fileURL = URL.createObjectURL(blob);

                            // open new URL
                            //window.open(fileURL, '_blank');
                            //popupWindow(fileURL, 'print');
                            // var link = document.createElement('a');

                            // link.href = window.URL.createObjectURL(blob);
                            // link.download = "document.pdf";

                            // link.click();
                            let date = new Date();
                            blob.lastModifiedDate = date;
                            blob.name = 'temp_'+date.getTime()+'.pdf';

                            var http_request1;
                            http_request1 = new XMLHttpRequest();
                            http_request1.onreadystatechange = function () { 
                                if (http_request1.readyState>3 && http_request1.status==200) 
                                { 
                                    //success(http_request1.responseText); 
                                } 
                            };
                            http_request1.open("POST", "http://localhost:8000/device/upload");
                            http_request1.withCredentials = true;
                            //http_request1.setRequestHeader("Content-Type", "multipart/form-data");
                            http_request1.responseType = 'json';
                            var formData = new FormData();
                            formData.append("file", blob);
                            http_request1.send(formData);
                            http_request1.onload = () => {

                                //console.log(`Data Loaded: ${http_request1.status} ${http_request1.response}`);
                                // process response
                                if (http_request1.status == 200) {
                                    // parse JSON data
                                    // var blob = new Blob([http_request1.response], { type: 'application/pdf' });
				                    console.log("result="+http_request1.response);
                                } else {
                                    console.error('Error!');
                                }
                            };

                            // listen for `error` event
                            http_request1.onerror = () => {
				local_server_active = false;
                                console.error('Request failed.');
                            };

                            // listen for `progress` event
                            http_request1.onprogress = (event) => {
                                // event.loaded returns how many bytes are downloaded
                                // event.total returns the total number of bytes
                                // event.total is only available if server sends `Content-Length` header
                                console.log(`Downloaded ${event.loaded} of ${event.total}`);
                            };
                        } else {
                            console.error('Error!');
                        }
                    };

                    // listen for `error` event
                    http_request.onerror = () => {
                        console.error('Request failed.');
                    };

                    // listen for `progress` event
                    http_request.onprogress = (event) => {
                        // event.loaded returns how many bytes are downloaded
                        // event.total returns the total number of bytes
                        // event.total is only available if server sends `Content-Length` header
                        console.log(`Downloaded ${event.loaded} of ${event.total}`);
                    };
                }else
                {
                    if($('#print_review').is(":checked")) {
                        popupWindow("{{action('AdminGoldItemsController@getPrintTem')}}/" + id,"print");
                    }else{
                        popupWindow("{{action('AdminGoldItemsController@getPrintTem')}}/" + id,"print");
                        // popupWindow("{{action('AdminGoldStocksReceivedController@getPrintTem')}}/" + id,"print");
                    }
                }
                
            }else{
                alert("Bạn không thể in tem nếu chưa thêm!");
            }
        }

        var isAutoGetData = false;
        function getDataDevice() {
        	console.log('Stock Received Form getDataDevice()');
            let result;// = Number("{{action('AdminGoldStocksReceivedController@getDataDevice')}}");
            if(local_server_active)
            {
                if(!isAutoGetData)
                {
                    $.ajax({
                        method: "GET",
                        url: 'http://localhost:8000/device/weight',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        processData: false,
                        contentType: 'application/json',
                        CrossDomain:true,
                        async: false,
                        success: function (data) {
                            console.log('data = ', data);
                            if (data) {
                                result = data.weight ? Number(data.weight) : 0;
                                result = parseFloat(result);
                                console.log('Gia trị từ dữ liệu = ', result);
                            }
                        },
                        error: function (request, status, error) {
                            local_server_active = false;
                            console.log('PostAdd status = ', status);
                            console.log('PostAdd error = ', error);
                        }
                    });
                    $('#total_weight').val(result);
                    weight_change();
                }  
            }else{
                // $.ajax({
                //     method: "GET",
                //     //url: 'http://localhost:8088/system/console/ShinkoDJ',
                //     url: '{{Route("AdminGoldStocksReceivedControllerGetDataDevice")}}',
                //     data: {
                //         _token: '{{ csrf_token() }}'
                //     },
                //     dataType: "json",
                //     async: false,
                //     success: function (data) {
                //         console.log('data = ', data);
                //         if (data) {
                //             result = data.weight ? Number(data.weight) : 0;
                //             result = parseInt(result);
                //             console.log('Gia trị từ dữ liệu = ', result);
                //         }
                //     },
                //     error: function (request, status, error) {
                //         console.log('PostAdd status = ', status);
                //         console.log('PostAdd error = ', error);
                //     }
                // });
            }			
            return result;
        }

        var intervalId;
        function autoGetDataDevice(cb)
        {
            console.log('autoGetDataDevice()');
            if(cb.checked == true) {
                console.log('checked event');
                $('#device_data').prop('readonly', true);
                $('#device_data').prop('disabled', true);
                intervalId = setInterval(function()
                {
                    if(local_server_active)
                    {
                        isAutoGetData = true;
                        $.ajax({
                            method: "GET",
                            url: 'http://localhost:8000/device/weight',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json',
                            processData: false,
                            contentType: 'application/json',
                            CrossDomain:true,
                            async: false,
                            success: function (data) {
                                console.log('data = ', data);
                                if (data) {
                                    result = data.weight ? Number(data.weight) : 0;
                                    //result = parseFloat(result).toPrecision(4);
				    result = parseFloat(result);
                                    console.log('Gia trị từ dữ liệu = ', result);
                                }
                            },
                            error: function (request, status, error) {
                                local_server_active = false;
				clearInterval(intervalId);//stop interval
                                console.log('PostAdd status = ', status);
                                console.log('PostAdd error = ', error);
                            }
                        });
                        $('#total_weight').val(result);
                        weight_change();
                    }
                }, 1000);
            }
            else {
                console.log('unchecked event');
                $('#device_data').prop('readonly', false);
                $('#device_data').prop('disabled', false);
                if(intervalId)
                {
                    clearInterval(intervalId);
                    if(isAutoGetData)
                    {
                        isAutoGetData = false;
                    }
                }
            }
        }
	</script>
@endpush