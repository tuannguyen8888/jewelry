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
					{{--<div class="col-sm-12">--}}
                        {{--<div class="row">--}}
							{{--<label for="stock_code" class="control-label col-sm-2">Kho<span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>--}}
							{{--<div class="col-sm-10">--}}
								{{--<div class="input-group">--}}
									{{--<span class="input-group-btn">--}}
										{{--<button id="btn_search_stock" type="button" class="btn btn-primary btn-flat" onclick="showModalstock_id()"><i class="fa fa-search"></i></button>--}}
									{{--</span>--}}
									{{--<input type="text" name="stock_code" id="stock_code" onchange="searchStock();" class="form-control" required placeholder="Mã kho" style="width: 15%">--}}
									{{--<input type="text" name="stock_name" id="stock_name" class="form-control" placeholder="Tên kho" style="width: 85%"  disabled>--}}
									{{--<input type="hidden" name="stock_id" id="stock_id">--}}
								{{--</div>--}}
							{{--</div>--}}
						{{--</div>--}}
                        <div class="row">
                            <label class="control-label col-sm-2">Mã HĐ cầm</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" name="bar_code" id="bar_code" class="form-control"
                                            placeholder="Quét mã vạch" autocomplete="off" onkeyup="searchItem(event)"
                                            style="background-color: rgba(251,240,83,0.52)">
                                    <span class="input-group-btn">
                                        <button id="btn_bar_code" type="button" class="btn btn-danger btn-flat" onclick="$('#modal-datamodal-delete-barcode').modal('show')">
                                        <i class="fa fa-remove"></i></button>
                                    </span>
                                </div>
                                {{--$('#modal-datamodal-stock_id').modal('show');--}}
                            </div>
                            <label class="control-label col-sm-2 text-right">T/g kiểm kê <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
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
                            <label class="control-label col-sm-2">Lý do</label>
                            <div class="col-sm-10">
                                <input type="text" name="notes" id="notes" class="form-control" required>
                            </div>
                        </div>
                        <table id="table_order_details" class='table table-bordered' height=50>
                            <thead>
                            <tr class="bg-success">
                                <th class="sort_no">Stt</th>
                                <th>Mã HĐ cầm</th>
                                <th>T/g cầm</th>
                                <th>Thời hạn</th>
                                <th>T/h tối thiểu</th>
                                <th>Trạng thái</th>
                                <th>Số tiền</th>
                                {{--<th>TL Đá</th>--}}
                                {{--<th>TL Vàng</th>--}}
                                {{--<th class="qty">Số lượng</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            
                            </tbody>
                            <tfoot>
                            <tr class="bg-gray-active">
                                <th colspan="6" class="text-center">Tổng cộng</th>
                                <th id="total_order_total_amount" class="text-right">0</th>
                                {{--<th id="total_order_gem_weight" class="text-right">0</th>--}}
                                {{--<th id="total_order_gold_weight" class="text-right">0</th>--}}
                                <!-- <th id="total_order_balance_qty" class="qty text-right">0</th> -->
                                {{--<th id="total_order_actual_qty" class="qty text-right">0</th>--}}
                            </tr>
                            </tfoot>
                        </table>
					{{--</div>--}}
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
						<a id="print_order" style="display: none;cursor: pointer;" onclick="printOrder()" class="btn btn-info"><i class="fa fa-print"></i> In bảng kê</a>
                        <!-- <a id="print_order_xlsx" style="display: none;cursor: pointer;" onclick="printOrderXlsx()" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Xuất bảng kê</a> -->
					</div>
				</div>
			</div>
		</div>
	</div>

	{{--<div id="modal-datamodal-stock_id" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">--}}
		{{--<div class="modal-dialog modal-lg " role="document">--}}
			{{--<div class="modal-content">--}}
				{{--<div class="modal-header">--}}
					{{--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>--}}
					{{--<h4 class="modal-title"><i class="fa fa-search"></i> Browse Data | Kho</h4>--}}
				{{--</div>--}}
				{{--<div class="modal-body">--}}
					{{--<iframe id="iframe-modal-stock_id" style="border:0;height: 430px;width: 100%"></iframe>--}}
				{{--</div>--}}
			{{--</div><!-- /.modal-content -->--}}
		{{--</div><!-- /.modal-dialog -->--}}
	{{--</div>--}}
	<div id="modal-datamodal-delete-barcode" class="modal in" tabindex="-1" role="dialog" aria-hidden="false" style="display: none; padding-right: 7px;">
		<div class="modal-dialog modal-md " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title"><i class="fa fa-search"></i> Delete Barcode | HĐ đang kiểm kê</h4>
				</div>
				<div class="modal-body">
					<form onsubmit="event.preventDefault();" id="form" style="min-height: 50px;">
						<input type="hidden" name="id" id="id">
						<input type="hidden" name="saler_id" id="saler_id">
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
		#table_order_details thead, tfoot, tbody, tr{
			display:table;
			width:100%;
			table-layout:fixed;
		}
		/* #table_order_details thead, #table_order_details tfoot, #table_order_details tbody tr {
			width: 100%
		} */
		#table_order_details table {
			width:100%;
		}
        #table_order_details .sort_no{
			width: 45px;
		}
        #table_order_details .qty{
			width: 75px;
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
        order_id = null; // sẽ có khi lưu thành công
        order_details = [];
        total_order = {
			total_weight: 0,
			gem_weight: 0,
            gold_weight: 0,
            balance_qty: 0,
            actual_aty: 0
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
                showOnFocus:false,
                step: 5
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
						if(data.order){
						    order_id = data.order.id;
                            $('#id').val(order_id);
                            $('#order_date').val(moment(data.order.order_date, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            $('#order_no').val(data.order.order_no);
                            $('#notes').val(data.order.notes);
                            $('#print_order').show();
                            $('#print_order_xlsx').show();
                        }
						if(data.details && data.details.length > 0){
                            data.details.forEach(function (detail, i) {
                                detail.no = i + 1;
                                addNewOrderDetail(detail);
                            });
                            order_details = data.details.reverse();
                            calcTotalOfOrderDetails();
						}
                        if (data.order && data.order.status == 1){
                            disableOrder();
                        }
                        else{
                            $('#bar_code').focus();
                        }
                    }
                },
                error: function (request, status, error) {
                    swal("Thông báo","Có lỗi xãy ra khi tải kiểm kê, vui lòng thử lại.","warning");
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

        function showModalstock_id() {
            var url_stock_id = "{{action('AdminGoldPawnOdersTakingController@getModalData')}}/gold_stocks_taking/modal-data?table=gold_stocks&columns=id,code,name&name_column=stock_id&where=deleted_at+is+null&select_to=code:code&columns_name_alias=Mã kho,Tên kho";
            $('#iframe-modal-stock_id').attr('src',url_stock_id);
            $('#modal-datamodal-stock_id').modal('show');
        }
        function hideModalstock_id() {
            $('#modal-datamodal-stock_id').modal('hide');
        }
        function selectAdditionalDatastock_id(select_to_json) {
			if(select_to_json.code){
                $('#stock_code').val(select_to_json.code).trigger('change');
			}
            hideModalstock_id();
        }

        function searchItem(event) {
            if(event == null || event.keyCode == 13) {
                if(moment().diff(lastTimeScanBarCode, 's') >= 1) // productFinding &&
                {
                    lastTimeScanBarCode = moment();
                    let bar_code = $('#bar_code').val();
                    if(bar_code){
                        let added = false;
                        order_details.forEach(function (detail, index) {
                            if (detail.order_no == bar_code) {
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
                                url: '{{Route("AdminGoldPawnOdersTakingControllerGetSearchItem")}}',
                                data: {
                                    bar_code: bar_code,
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: "json",
                                async: true,
                                success: function (data) {
                                    if (data && data.item) {
                                        if(data.item.brand_id == Number('{{$brand_id}}')) {
                                            if (data.item.status == 1) {
                                                addNewItem(data.item);
                                            } else if (data.item.status == 0) {
                                                swal("Thông báo", "Hợp đồng cầm [" + bar_code + "] vẫn đang nhập, chưa hoàn tất", "warning");
                                            } else if (data.item.status == 2) {
                                                swal("Thông báo", "Hợp đồng cầm [" + bar_code + "] đã thanh lý", "warning");
                                            } else if (data.item.status == 3) {
                                                swal("Thông báo", "Hợp đồng cầm [" + bar_code + "] đã tất toán", "warning");
                                            }
                                        }else{
                                            swal("Thông báo", "Hợp đồng cầm [" + bar_code + "] không thuộc chi nhánh của bạn, vui lòng kiểm tra lại", "warning");
                                        }
                                    } else {
                                        swal("Thông báo", "Không tìm thấy hợp đồng cầm " + bar_code, "warning");
                                    }
                                },
                                error: function (request, status, error) {
                                    console.log('Lỗi khi tìm hợp đồng cầm ', [request, status, error]);
                                    swal("Thông báo", "Có lỗi xãy ra khi tải dữ liệu hợp đồng cầm, vui lòng thử lại.", "warning");
                                }
                            });
                        }
                    }
                }
            }
        }

        function removeBarcode(event){
            if (event == null || event.keyCode == 13) {
                let bar_code_delete = $('#bar_code_delete').val();
                let removeIndex = -1;
                if(order_details && order_details.length) {
                    order_details.forEach(function (detail, index) {
                        if(detail.order_no == bar_code_delete) {
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
                                        // giảm số thứ tự
                                        for (let i = 0; i < removeIndex; i++) {
                                            let detail = order_details[i];
                                            detail.no -= 1;
                                            $('#order_detail_'+detail.id+' .sort_no').html(detail.no);
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
                    });

                }else{
                    console.log('Chưa có sản phẩm được quét barcode, không thể xóa');
                }
            }
        }


        function calcTotalOfOrderDetails() {
            total_order = {
                amount: 0,
                // gem_weight: 0,
                // gold_weight: 0,
                // balance_qty: 0,
                // actual_qty: 0
            };
            order_details.forEach(function (detail, index) {
                total_order.amount += detail.amount ? detail.amount : 0;
                // total_order.gem_weight += detail.gem_weight ? detail.gem_weight : 0;
                // total_order.gold_weight += detail.gold_weight ? detail.gold_weight : 0;
                // total_order.balance_qty += detail.balance_qty ? detail.balance_qty : 0;
                // total_order.actual_qty += detail.actual_qty ? detail.actual_qty : 0;
            });
            $('#total_order_total_amount').html(total_order.amount.toLocaleString('en-US'));
            // $('#total_order_gem_weight').html(total_order.gem_weight.toLocaleString('en-US'));
            // $('#total_order_gold_weight').html(total_order.gold_weight.toLocaleString('en-US'));
            // $('#total_order_balance_qty').html(total_order.balance_qty.toLocaleString('en-US'));
            // $('#total_order_actual_qty').html(total_order.actual_qty.toLocaleString('en-US'));
        }

        function addNewOrderDetail(dataRow) {
            let html = `<tr id="order_detail_${dataRow.id}">
                    <th class="sort_no text-right">${dataRow.no}</th>
                    <th>${dataRow.pawn_order_no}</th>
                    <th>${dataRow.pawn_order_date}</th>
                    <th>${dataRow.due_date} ngày</th>
                    <th>${dataRow.min_days} ngày</th>
                    <th class="text-center">${dataRow.status==1?'Đang cầm':'lỗi'}</th>
                    <th class="text-right">${dataRow.amount.toLocaleString('en-US')}</th>
                </tr>`;
			$('#table_order_details tbody').prepend(html);
            // $('#table_order_details tbody').animate({scrollTop:9999999}, 'slow');
        }
        
        function notes_change(id) {
            $.ajax({
                method: "POST",
                url: '{{CRUDBooster::mainpath('update-item-notes')}}',
                data: {
                    detail_id: id,
                    notes: $('#notes_'+id).val(),
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: true,
                error: function (request, status, error) {
                    $('.loading').hide();
                    swal("Thông báo", "Có lỗi xãy ra khi lưu dữ liệu, vui lòng thử lại.", "error");
                    // continue;
                }
            });
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
            }else if(!order_details || order_details.length <= 0){
                valid = false;
                $(`#bar_code`).focus();
                swal("Thông báo", "Không có hợp đồng cầm được kiểm kê, vui lòng kiểm tra lại.", "warning");
            }
			return valid;
        }

        function getOrderHeader(finish) {
            return {
                id: $('#id').val() ? Number($('#id').val()) : null,
                status: finish ? 1 : 0,
                // stock_id: $('#stock_id').val() ? Number($('#stock_id').val()) : null,
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
                            $('#print_order').show();
                        }
                        item.id = data.detail_id
                        item.no = order_details ? order_details.length + 1 : 1;
                        order_details.unshift(item); // add first
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
                    //$('#form input').attr('readonly', true);
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
            $('#btn_search_stock').attr('disabled', true);
            $('#btn_bar_code').attr('disabled', true);
            $('#form input').attr('disabled', true);
            $('#form select').attr('disabled', true);
        }

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }

        function printOrder() {
            if(order_id) {
                popupWindow("{{action('AdminGoldPawnOdersTakingController@getPrintOrder')}}/" + order_id,"print");
            }else{
                alert("Bạn không thể in hóa đơn nếu chưa lưu!");
            }
        }

        function printOrderXlsx() {
            if(order_id) {
                popupWindow("{{action('AdminGoldPawnOdersTakingController@getPrintOrderXlsx')}}/" + order_id,"print");
            }else{
                alert("Bạn không thể in hóa đơn nếu chưa lưu!");
            }
        }
	</script>
@endpush