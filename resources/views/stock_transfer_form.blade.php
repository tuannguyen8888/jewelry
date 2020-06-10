<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- test content here.-->
	<div>
		<p><a title="Return" href="{{CRUDBooster::mainpath()}}"><i class="fa fa-chevron-circle-left "></i> &nbsp; Quay lại danh sách</a></p>
		<div class='panel panel-default'>
			<div class="panel-body" id="parent-form-area">
				<form method='post' action='{{CRUDBooster::mainpath('update-order')}}' id="form">
					<input type="hidden" name="id" id="id">
					<div class="col-sm-12">
                        <div class="col-sm-8">
                            <div class="row">
                                <label class="control-label col-sm-2">T/g chuyển <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                                <div class="col-sm-4">
                                    <div class="input-group" >
                                        <input id="order_date" type="text" class="form-control bg-white" required readonly>
                                        <div class="input-group-addon bg-gray">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>
                                <label class="control-label col-sm-2 text-right">Số phiếu</label>
                                <div class="col-sm-4">
                                    <input type="text" name="order_no" id="order_no" class="form-control" placeholder="Tự động tạo" disabled>
                                </div>
                            </div>
                            <div class="row">
                                <label class="control-label col-sm-2">Xuất tại kho <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                                <div class="col-sm-10">
                                    <select id="from_stock" class="form-control"></select>
                                </div>
                            </div>
                            <div class="row">
                                <label class="control-label col-sm-2">Nhâp tại kho <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
                                <div class="col-sm-10">
                                    <select id="to_stock" class="form-control"></select>
                                </div>
                            </div>
                            <div class="row">
                                <label class="control-label col-sm-2">Mã vạch </label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="text" name="bar_code" id="bar_code" class="form-control"
                                                placeholder="Quét mã vạch" autocomplete="off" onkeyup="findProduct(event)"
                                                style="background-color: rgba(251,240,83,0.52)">
                                        <span class="input-group-btn">
                                            <button id="btn_bar_code" type="button" class="btn btn-danger btn-flat" onclick="$('#modal-datamodal-delete-barcode').modal('show')">
                                            <i class="fa fa-remove"></i></button>
                                        </span>
                                    </div>
                                    {{--$('#modal-datamodal-stock_id').modal('show');--}}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="row">
                                <label class="control-label col-sm-12">Lý do</label>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <textarea name="notes" id="notes" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                        <table id="table_order_details" class='table table-bordered' height=50>
                            <thead>
                            <tr class="bg-success">
                                <th class="sort_no">Stt</th>
                                <th>Mã vạch</th>
                                <th>Mã sản phẩm</th>
                                <th>Tên sản phẩm</th>
                                <th>Loại vàng</th>
                                <th>TL Tổng</th>
                                <th>TL Đá</th>
                                <th>TL Vàng</th>
                                <th class="age">Tuổi vàng</th>
                                <th>Q10</th>
                                <th class="fee">Công vốn</th>
                            </tr>
                            </thead>
                            <tbody>
                            
                            </tbody>
                            <tfoot>
                            <tr class="bg-gray-active">
                                <th colspan="5" class="text-center">Tổng cộng</th>
                                <th id="total_order_total_weight" class="text-right">0</th>
                                <th id="total_order_gem_weight" class="text-right">0</th>
                                <th id="total_order_gold_weight" class="text-right">0</th>
                                <th class="age"></th>
                                <th id="total_order_q10" class="text-right">0</th>
                                <th id="total_order_fee" class="fee text-right">0</th>
                            </tr>
                            </tfoot>
                        </table>
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
						<a id="print_order" style="display: none;cursor: pointer;" onclick="printOrder()" class="btn btn-info"><i class="fa fa-print"></i> In phiếu</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal-datamodal-delete-barcode" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">
		<div class="modal-dialog modal-md " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><i class="fa fa-search"></i> Delete Barcode | Hàng đang chuyển</h4>
				</div>
				<div class="modal-body">
					<form onsubmit="event.preventDefault();" id="form" style="min-height: 50px;">
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
		#table_order_details tbody {
			display:block;
			max-height:500px;
			overflow:auto;
		}
		#table_order_details thead, tfoot, tbody tr {
			display:table;
			width:100%;
			table-layout:fixed;
		}
		#table_order_details table {
			width:100%;
		}
        #table_order_details .sort_no{
			width: 45px;
		}
        #table_order_details .age{
			width: 80px;
		}
        #table_order_details .fee{
			width: 120px;
		}

		.form-divider {
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
		.hide_invalid_actual_weight{
			display: none;
		}
	</style>

	<script type="application/javascript">
        order_id = null; // sẽ có khi lưu thành công
        order_details = [];
        total_order = {
			total_weight: 0,
			gem_weight: 0,
            gold_weight: 0,
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
        lastTimeScanBarCode = moment();
        
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

            loadStock();

            // AutoNumeric.multiple('.money', optionNumberInput);
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
						if(data.order){
						    order_id = data.order.id;
                            $('#id').val(order_id);
                            $('#order_date').val(moment(data.order.order_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            $('#order_no').val(data.order.order_no);
                            $('#from_stock').val(data.order.from_stock_id);
                            $('#to_stock').val(data.order.to_stock_id);
                            $('#notes').val(data.order.notes);
                        }
						if(data.details && data.details.length > 0){
                            $('#from_stock').attr('disabled', true);
                            data.details.forEach(function (detail, i) {
                                detail.no = i + 1;
                                addNewOrderDetail(detail);
                            });
                            order_details = data.details;
                            calcTotalOfOrderDetails();
						}else{
                            $('#from_stock').attr('disabled', false);
                        }
                        if (data.order && data.order.status == 1){
                            disableOrder();
                        }else{
                            $('#bar_code').focus();
                        }
                    }else{
                        $('#bar_code').focus();
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
                url: '{{Route("AdminGoldStocksControllerGetStocks")}}',
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
                        $('#from_stock').append(html);
                        $('#to_stock').append(html);
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

        function findProduct(event) {
            if(event == null || event.keyCode == 13) {
                if(moment().diff(lastTimeScanBarCode, 's') >= 1) // productFinding &&
                {
                    lastTimeScanBarCode = moment();
                    let bar_code = $('#bar_code').val();
                    if(bar_code){
                        let added = false;
                        order_details.forEach(function (detail, index) {
                            if (detail.bar_code == bar_code) {
                                added = true;
                                $('#bar_code').val(null);
                            }
                        });
                        if(!added) {
                            setTimeout(function () {
                                $('#bar_code').val(null);
                            },200);
                            $.ajax({
                                method: "GET",
                                url: '{{Route("AdminGoldStocksTransferControllerGetSearchItem")}}',
                                data: {
                                    bar_code: bar_code,
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: "json",
                                async: true,
                                success: function (data) {
                                    if (data && data.item) {
                                        if(data.item.qty <= 0){
                                            swal("Thông báo", "Hàng hóa [" + data.item.bar_code + "] đã hết tồn kho.", "warning");    
                                        }else if(data.item.stock_id != Number($('#from_stock').val())){
                                            swal("Thông báo", "Hàng hóa [" + data.item.bar_code + "] thuộc kho [" + data.item.stock_name + "].", "warning");    
                                        }else{
                                            addNewItem(data.item);
                                        }
                                    } else {
                                        swal("Thông báo", "Không tìm thấy mã [" + bar_code + "]", "warning");
                                    }
                                },
                                error: function (request, status, error) {
                                    console.log('Lỗi khi tìm sản phẩm ', [request, status, error]);
                                    swal("Thông báo", "Có lỗi xãy ra khi tải dữ liệu, vui lòng thử lại.", "warning");
                                }
                            });
                        }
                    }
                }
            }
        }

        function calcTotalOfOrderDetails() {
            total_order = {
                total_weight: 0,
                gem_weight: 0,
                gold_weight: 0,
                q10: 0,
                fee: 0
            };
            order_details.forEach(function (detail, index) {
                total_order.total_weight += detail.total_weight ? detail.total_weight : 0;
                total_order.gem_weight += detail.gem_weight ? detail.gem_weight : 0;
                total_order.gold_weight += detail.gold_weight ? detail.gold_weight : 0;
                total_order.q10 += detail.q10 ? detail.q10 : 0;
                total_order.fee += detail.fee ? detail.fee : 0;
            });
            $('#total_order_total_weight').html(total_order.total_weight.toLocaleString('en-US'));
            $('#total_order_gem_weight').html(total_order.gem_weight.toLocaleString('en-US'));
            $('#total_order_gold_weight').html(total_order.gold_weight.toLocaleString('en-US'));
            $('#total_order_q10').html(total_order.q10.toLocaleString('en-US'));
            $('#total_order_fee').html(total_order.fee.toLocaleString('en-US'));
        }

        function addNewOrderDetail(dataRow) {
            let html = `<tr id="order_detail_${dataRow.id}">
                    <th class="sort_no text-right">${dataRow.no}</th>
                    <th>${dataRow.bar_code}</th>
                    <th>${dataRow.product_code}</th>
                    <th>${dataRow.product_name}</th>
                    <th class="text-center">${dataRow.product_type_name}</th>
                    <th class="text-right">${dataRow.total_weight.toLocaleString('en-US')}</th>
                    <th class="text-right">${dataRow.gem_weight.toLocaleString('en-US')}</th>
                    <th class="text-right">${dataRow.gold_weight.toLocaleString('en-US')}</th>
                    <th class="text-center">${dataRow.age.toLocaleString('en-US')}</th>
                    <th class="text-right">${dataRow.q10.toLocaleString('en-US')}</th>
                    <th class="text-right">${dataRow.fee.toLocaleString('en-US')}</th>
                </tr>`;
			$('#table_order_details tbody').append(html);
            // $('#table_order_details tbody').animate({scrollTop:9999999}, 'slow');
            setTimeout(function () {
                AutoNumeric.multiple(`#order_detail_${dataRow.id} .money`, optionNumberInput);
            },100);
        }

        function removeBarcode(event){
			if (event == null || event.keyCode == 13) {
                let bar_code_delete = $('#bar_code_delete').val();
                let removeIndex = -1;
				if(order_details && order_details.length) {
					order_details.forEach(function (detail, index) {
						if(detail.bar_code == bar_code_delete) {
                            $.ajax({
                                method: "POST",
                                url: '{{CRUDBooster::mainpath('remove-item')}}',
                                data: {
                                    detail_id: detail.id,
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: "json",
                                async: true,
                                success: function (data) {
                                    if (data && data.result) {
                                        removeIndex = index;
                                        $('#order_detail_'+detail.id).remove();
                                        order_details.splice(removeIndex, 1);
                                        if(order_details.length == 0){
                                            $('#from_stock').attr('disabled', false);
                                        }
                                        calcTotalOfOrderDetails();
                                    }
                                },
                                error: function (request, status, error) {
                                    $('.loading').hide();
                                    swal("Thông báo", "Có lỗi xãy ra khi lưu dữ liệu, vui lòng thử lại.", "error");
                                    // continue;
                                }
                            });
                        }
                        // giảm số thứ tự
                        if(removeIndex != -1 && index > removeIndex) {
                            detail.no -= 1;
                            $('#no_'+detail.id).html(detail.no);
                        }
                    });
				}else{
					console.log('Chưa có sản phẩm được quét barcode, không thể xóa');
				}
			}
        }
        
        function validate() {
			let valid = true;
            $('#form input').removeClass('invalid');
            // if(!$('#stock_code').val()){
            //     valid = false;
            //     $('#stock_code').addClass('invalid');
            // }
            if(!$('#order_date').val()){
                valid = false;
                $('#order_date').addClass('invalid');
            }

            if(!valid) {
                swal("Thông báo", "Dữ liệu chưa được nhập đầy đủ, vui lòng kiểm tra lại.", "warning");
            }else if($('#from_stock').val() == $('#to_stock').val()){
                valid = false;
                $(`#bar_code`).focus();
                swal("Thông báo", "Kho nhập phải khác kho chuyển, vui lòng kiểm tra lại.", "warning");
            }else if(!order_details || order_details.length <= 0){
                valid = false;
                $(`#bar_code`).focus();
                swal("Thông báo", "Không có hàng hóa chuyển kho, vui lòng kiểm tra lại.", "warning");
            }
			return valid;
        }

        function getOrderHeader(finish) {
            return {
                id: $('#id').val() ? Number($('#id').val()) : null,
                status: finish ? 1 : 0,
                from_stock_id: $('#from_stock').val() ? Number($('#from_stock').val()) : null,
                to_stock_id: $('#to_stock').val() ? Number($('#to_stock').val()) : null,
                order_date: moment($('#order_date').val(), 'DD/MM/YYYY HH:mm:ss').format('YYYY-MM-DD HH:mm:ss'),
                order_no: $('#order_no').val(),
                notes: $('#notes').val()
            };
		}

        function addNewItem(item) {
            $.ajax({
                method: "POST",
                url: '{{CRUDBooster::mainpath('add-new-item')}}',
                data: {
                    order: getOrderHeader(false),
                    item: item,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: true,
                success: function (data) {
                    if (data) {
                        console.log('auto save add new success.')
                        if(!order_id){
                            order_id = data.id;
                            $('#id').val(order_id);
                            $('#order_no').val(data.order_no);
                            $('#from_stock').attr('disabled', true);
                        }
                        item.id = data.detail_id
                        item.no = order_details ? order_details.length + 1 : 1;
                        order_details.push(item);
                        addNewOrderDetail(item);
                        calcTotalOfOrderDetails();
                    }
                },
                error: function (request, status, error) {
                    $('.loading').hide();
                    swal("Thông báo", "Có lỗi xãy ra khi lưu dữ liệu, vui lòng thử lại.", "error");
                }
            });
        }
        
        function submit(finish) {
			if(!finish || validate()){ // nếu finish == false thì không validate
			    if(finish) {
                    $('#save_button').hide();
                    $('.loading').show();
                }
                $.ajax({
                    method: "POST",
                    url: '{{CRUDBooster::mainpath('update-order')}}',
                    data: {
                        order: getOrderHeader(finish),
						details: order_details,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data) {
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
            $('#btn_bar_code').attr('disabled', true);
            $('#form input').attr('disabled', true);
            $('#form select').attr('disabled', true);
            $('#form textarea').attr('disabled', true);
        }

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }

        function printOrder() {
            if(order_id) {
                popupWindow("{{action('AdminGoldStocksTransferController@getPrintOrder')}}/" + order_id,"print");
            }else{
                alert("Bạn không thể in phiếu chuyển kho nếu chưa lưu phiếu!");
            }
        }
	</script>
@endpush