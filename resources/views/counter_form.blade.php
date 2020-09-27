<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- Your custom  HTML goes here -->
	<!-- Your html goes here -->
	<div>
		<p><a title="Return" href="{{CRUDBooster::mainpath()}}"><i class="fa fa-chevron-circle-left "></i> &nbsp; Quay lại danh sách</a></p>
		<div class='panel panel-default'>
			<div class="panel-body" id="parent-form-area">
				<form method='post' action='{{CRUDBooster::mainpath('update-counter-header')}}' id="form">
					<input type="hidden" name="id" id="id">
					<div class="col-sm-12">
                        <table id="table_counter_header" class="table counter_header_table">
                            <tr>
                                <th class="th_border">
                                    <div class="header-title form-divider">
                                        <i class="fa fa-list-alt"></i> THÔNG TIN CHUNG
                                    </div>
                                    <div class="row">
                                    <label class="control-label col-sm-2">Nhân viên</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="saler" id="saler" class="form-control" readonly disabled>
                                        </div>
                                        <label class="control-label col-sm-2 text-right">Số phiếu</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="counter_no" id="counter_no" class="form-control" placeholder="Tự động tạo" readonly disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2">T/g mở</label>
                                        <div class="col-sm-4">
                                            <input id="opened_at" type="text" class="form-control bg-white" disabled readonly>
                                        </div>
                                        <label class="control-label col-sm-2 text-right">T/g đóng</label>
                                        <div class="col-sm-4">
                                            <input id="closed_at" type="text" class="form-control bg-white" disabled readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2">Tiền ứng</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="advance_amount" id="advance_amount" class="form-control money" onchange="advance_amount_change();" readonly disabled>
                                        </div>
                                        <label class="control-label col-sm-2 text-right">Phải nộp</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="total_amount" id="total_amount" class="form-control money" readonly disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2">Nội dung</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="description" id="description" class="form-control">
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="header-title form-divider">
                                        <i class="fa fa-list-alt"></i> THÔNG TIN KHÓA SỔ
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2">Khóa bởi</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="finalized" id="finalized" class="form-control" readonly disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2">T/g khóa</label>
                                        <div class="col-sm-4">
                                            <input id="finalized_at" type="text" class="form-control bg-white" disabled readonly>
                                        </div>
                                        <label class="control-label col-sm-2 text-right">Tiền thu</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="amount" id="amount" class="form-control money" onchange="amount_change();" disabled readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="control-label col-sm-2">Ghi chú</label>
                                        <div class="col-sm-10">
                                            <textarea name="notes" id="notes" class="form-control" rows="3" disabled readonly></textarea>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </table>
					</div>

					<div class="col-sm-12">
						<div class="form-group header-group-1">
							<table id="table_counter_details" class='table table-bordered' height=50>
								<thead>
								<tr class="bg-success">
                                    <th class="sort_no">Stt</th>
									<th class="trans_no">Số chứng từ</th>
                                    <th class="trans_date">Ngày chứng từ</th>
									<th class="trans_type">Loại chứng từ</th>
									<th>Bán ra (TM)</th>
                                    <th>Bán ra (CK)</th>
									<th>Mua vào</th>
									<th>Cầm</th>
                                    <th>Lãi</th>
                                    <th>Giảm lãi</th>
                                    <th>Tất toán</th>
                                    <th>Tiền chuyển</th>
                                    <th>Tiền nhân</th>
									<th>Ghi chú</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
								<tr class="bg-gray-active">
									<th colspan="4" class="total_label text-center">Tổng cộng</th>
									<th id="total_sales_amount" class="text-right">0</th>
                                    <th id="total_bank_amount" class="text-right">0</th>
									<th id="total_purchase_amount" class="text-right">0</th>
									<th id="total_pawn_amount" class="text-right">0</th>
                                    <th id="total_interested_amount" class="text-right">0</th>
                                    <th id="total_interest_reduced_amount" class="text-right">0</th>
                                    <th id="total_liquidation_amount" class="text-right">0</th>
                                    <th id="total_withdrawal_in" class="text-right">0</th>
                                    <th id="total_withdrawal_out" class="text-right">0</th>
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
					<label class="control-label col-sm-1"></label>
					<div class="col-sm-11">
						<a href="{{CRUDBooster::mainpath()}}" class="btn btn-default"><i class="fa fa-chevron-circle-left"></i> Quay về</a>
						@if($mode=='new' || $mode=='edit')
							<button id="save_button" class="btn btn-success" onclick="submit()"><i class="fa fa-save"></i> Lưu</button>
						@endif
                        <a id="close_button" style="display: none;cursor: pointer;" onclick="closed()" class="btn btn-primary"><i class="fa fa-close"></i> Đóng phiếu</a>
                        <a id="finalized_button" style="display: none;cursor: pointer;" onclick="finalized()" class="btn btn-warning"><i class="fa fa-lock"></i> Khóa phiếu</a>
						<a id="print_invoice" style="display: none;cursor: pointer;" onclick="printCounter()" class="btn btn-info"><i class="fa fa-print"></i> In phiếu</a>
						<!-- <a id="print_report_detail" style="display: none;cursor: pointer;" onclick="printCounterDetail()" class="btn btn-info"><i class="fa fa-print"></i> In Bảng kê</a> -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="loading"></div>
@endsection

@push('bottom')
	<style>
        #table_counter_header tbody tr {
			display:table;
			table-layout:fixed;
            border:none;
		}
        .counter_header_table{
            margin-bottom:0px;
            border:none;
        }
        #table_counter_header .th_border {
			border-right: 1px solid #dddddd;
            width:50%;
		}

		#table_counter_details tbody {
			display:block;
			max-height:500px;
			overflow:auto;
		}
		#table_counter_details thead, tfoot, tbody, tr{
			display:table;
			width:100%;
			table-layout:fixed;
		}
		#table_counter_details table {
			width:100%;
		}
		#table_counter_details .sort_no{
			width: 30px;
		}
		#table_counter_details .trans_no{
			width: 120px;
		}
		#table_counter_details .trans_date{
			width: 150px;
		}
		#table_counter_details .trans_type{
			width: 100px;
		}
		#table_counter_details .product_code{
			width: 100px;
		}
		#table_counter_details .total_label{
			width: 400px;
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
		.hide_invalid_total_weight{
			display: none;
		}
	</style>

	<script type="application/javascript">
        readOnlyAll = false;
        counter_count = 0;
        counter = {
            id: null, // sẽ có khi lưu thành công
			advance_amount: 0,
			sales_amount: 0,
            bank_amount: 0,
			purchase_amount: 0,
			pawn_amount: 0,
            interested_amount: 0,
            liquidation_amount: 0,
            withdrawal_in: 0,
            withdrawal_out: 0,
            amount: 0
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

            counter.saler_id = Number('{{CRUDBooster::myId()}}');
            $.ajax({
                method: "GET",
                url: '{{Route("AdminCmsUsersControllerGetUser")}}',
                data: {
                    id: counter.saler_id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: false,
                success: function (data) {
                    if (data && data.user){
                        $('#saler').val(data.user.name);
                        counter.advance_amount = data.user.balance;
                        $('#advance_amount').val(counter.advance_amount);
                        $('#total_amount').val(counter.advance_amount);
                    }
                },
            });

            counter.opened_at = moment().format('YYYY-MM-DD HH:mm:ss');
            $('#opened_at').val(moment().format('DD/MM/YYYY HH:mm:ss'));
            AutoNumeric.multiple('.money', optionNumberInput);
            let resume_id = getUrlParameter('resume_id') ? getUrlParameter('resume_id') : '{{$resume_id}}';
            if(resume_id) {
                resume(resume_id);
			}
		});

		function resume(id) {
            $.ajax({
                method: "GET",
                url: '{{CRUDBooster::mainpath('resume-counter')}}',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: true,
                success: function (data) {
                    if (data){
						if(data.counter){
                            counter = {
                                id: data.counter.id,
                                counter_no: data.counter.counter_no,
                                opened_at: data.counter.opened_at,
                                closed_at: data.counter.closed_at,
                                description: data.counter.description,
                                saler_id: data.counter.saler_id,
                                status: data.counter.status,
                                advance_amount: data.counter.advance_amount,
                                sales_amount: data.counter.sales_amount,
                                bank_amount: data.counter.bank_amount,
                                purchase_amount: data.counter.purchase_amount,
                                pawn_amount: data.counter.pawn_amount,
                                interested_amount: data.counter.interested_amount,
                                liquidation_amount: data.counter.liquidation_amount,
                                withdrawal_in: data.counter.withdrawal_in,
                                withdrawal_out: data.counter.withdrawal_out,
                                amount: data.counter.amount
                            };
                            
                            $('#id').val(counter.id);
                            $('#counter_no').val(counter.counter_no);
                            $('#saler').val(data.counter.saler);
                            $('#opened_at').val(moment(counter.opened_at, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            if(counter.closed_at){
                                $('#closed_at').val(moment(counter.closed_at, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            }
                            $('#description').val(counter.description);
                            $('#finalized').val(data.counter.finalized);
                            if(data.counter.finalized_at){
                                $('#finalized_at').val(moment(data.counter.finalized_at, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm:ss'));
                            }
                            $('#notes').val(data.counter.notes);
                            calcTotalOfDetails();
                            AutoNumeric.getAutoNumericElement('#advance_amount').set(counter.advance_amount);
                            AutoNumeric.getAutoNumericElement('#total_amount').set(counter.advance_amount + counter.sales_amount + counter.interested_amount - counter.purchase_amount - counter.pawn_amount + counter.liquidation_amount - counter.withdrawal_out);
                            AutoNumeric.getAutoNumericElement('#amount').set(counter.amount);

                            disableCounter(counter.status);
                        }
                        // console.log(data.sales);
                        counter_count = 0;
                        if(data.sales && data.sales.length > 0){
                            data.sales.forEach(function (detail, index) {
                                counter_count += 1;
                                detail.no = counter_count;
                                buildDetails(detail);
                            });
                        }
                        if(data.purchase && data.purchase.length > 0){
                            data.purchase.forEach(function (detail, index) {
                                counter_count += 1;
                                detail.no = counter_count;
                                buildDetails(detail);
                            });
                        }
                        if(data.pawn && data.pawn.length > 0){
                            data.pawn.forEach(function (detail, index) {
                                counter_count += 1;
                                detail.no = counter_count;
                                buildDetails(detail);
                            });
                        }
                        if(data.interested && data.interested.length > 0){
                            data.interested.forEach(function (detail, index) {
                                counter_count += 1;
                                detail.no = counter_count;
                                buildDetails(detail);
                            });
                        }
                        if(data.withdrawal && data.withdrawal.length > 0){
                            data.withdrawal.forEach(function (detail, index) {
                                counter_count += 1;
                                detail.no = counter_count;
                                buildDetails(detail);
                            });
                        }
                    }
                },
                error: function (request, status, error) {
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

        function advance_amount_change() {
            counter.advance_amount = $('#advance_amount').val() ? Number($('#advance_amount').val().replace(/,/g, '')) : 0;
            AutoNumeric.getAutoNumericElement('#total_amount').set(counter.advance_amount + counter.sales_amount + counter.interested_amount - counter.purchase_amount - counter.pawn_amount + counter.liquidation_amount - counter.withdrawal_out);
        }

        function amount_change() {
            counter.amount = $('#amount').val() ? Number($('#amount').val().replace(/,/g, '')) : 0;
        }

        function calcTotalOfDetails() {
            $('#total_sales_amount').html(counter.sales_amount.toLocaleString('en-US'));
            $('#total_bank_amount').html(counter.bank_amount.toLocaleString('en-US'));
            $('#total_purchase_amount').html(counter.purchase_amount.toLocaleString('en-US'));
            $('#total_pawn_amount').html(counter.pawn_amount.toLocaleString('en-US'));
            $('#total_interested_amount').html(counter.interested_amount.toLocaleString('en-US'));
            $('#total_liquidation_amount').html(counter.liquidation_amount.toLocaleString('en-US'));
            $('#total_withdrawal_in').html(counter.withdrawal_in.toLocaleString('en-US'));
            $('#total_withdrawal_out').html(counter.withdrawal_out.toLocaleString('en-US'));
        }

        function buildDetails(detail) {
            let html = `<tr>
                    <th class="sort_no text-right">${detail.no}</th>
                    <th class="trans_no">${detail.trans_no}</th>
                    <th class="trans_date">${moment(detail.trans_date).format('DD/MM/YYYY HH:mm:ss')}</th>
                    <th class="trans_type text-center">`;
                    if(detail.trans_type == 1){
                        html = html + `<label class='label label-success'>Bán hàng</label>`;
                    }else if(detail.trans_type == 2){
                        html = html + `<label class='label label-primary'>Mua hàng</label>`;
                    }else if(detail.trans_type == 3){
                        html += `<label class='label label-danger'>Cầm đồ</label>`;
                    }else if(detail.trans_type == 4){
                        html += `<label class='label label-warning'>Đóng lãi</label>`;
                    }else{
                        html = html + `<label class='label label-info'>Rút tiền</label>`;
                    }
            html = html + `</th>
                    <th class="text-right">${(detail.sales_amount ? detail.sales_amount : 0).toLocaleString('en-US')}</th>
                    <th class="text-right">${(detail.bank_amount ? detail.bank_amount : 0).toLocaleString('en-US')}</th>
                    <th class="text-right">${(detail.purchase_amount ? detail.purchase_amount : 0).toLocaleString('en-US')}</th>
                    <th class="text-right">${(detail.pawn_amount ? detail.pawn_amount : 0).toLocaleString('en-US')}</th>
                    <th class="text-right">${(detail.interested_amount ? detail.interested_amount : 0).toLocaleString('en-US')}</th>
                    <th class="text-right">${(detail.interest_reduced_amount ? detail.interest_reduced_amount : 0).toLocaleString('en-US')}</th>
                    <th class="text-right">${(detail.liquidation_amount ? detail.liquidation_amount : 0).toLocaleString('en-US')}</th>
                    <th class="text-right">${(detail.withdrawal_in ? detail.withdrawal_in : 0).toLocaleString('en-US')}</th>
                    <th class="text-right">${(detail.withdrawal_out ? detail.withdrawal_out : 0).toLocaleString('en-US')}</th>
                    <th></th>
                </tr>`;
            console.log(html);
            $('#table_counter_details tbody').append(html);
            // $('#table_counter_details tbody').animate({scrollTop:9999999}, 'slow');
        }

        function validate() {
			let valid = true;
            $('#form input').removeClass('invalid');
            
            // if (actual_weight < total_counter.total_weight) {
            //     valid = false;
            //     if(show_alert) {
            //         swal("Thông báo", "Tổng TL cao hơn Tổng cân thực tế", "warning");
            //     }
            // } else if (actual_weight > total_counter.total_weight) {
            //     valid = false;
            //     if(show_alert) {
            //         swal("Thông báo", "Tổng TL thấp hơn Tổng cân thực tế", "warning");
            //     }
            // }
			return valid;
        }

		function submit() {
            $('.loading').show();

            counter.description = $('#description').val() ? $('#description').val() : null;

            $.ajax({
                method: "POST",
                url: '{{CRUDBooster::mainpath('update-counter-header')}}',
                data: {
                    counter: counter,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: true,
                success: function (data) {
                    if (data) {
                        counter.id = data.id;
                        counter.counter_no = data.counter_no;
                        $('#id').val(data.id);
                        $('#counter_no').val(data.counter_no);
                    }
                    $('.loading').hide();
                    $('#print_report_detail').show();
                    $('#close_button').show();
                },
                error: function (request, status, error) {
                    $('.loading').hide();
                    console.log('PostAdd status = ', status);
                    console.log('PostAdd error = ', error);
                    swal("Thông báo", "Có lỗi xãy ra khi lưu dữ liệu, vui lòng thử lại.", "error");
                }
            });
        }

        function disableCounter(status) {
            $('#print_invoice').show();
            $('#print_report_detail').show();
            $('#close_button').hide();
            $('#finalized_button').hide();
            $('#save_button').hide();
            if(status == 2){
                // $('#print_invoice').show();
                $('#form input').attr('disabled', true);
                $('#form textarea').attr('disabled', true);
                $('#form select').attr('disabled', true);
            }else if(status == 1){
                // $('#print_invoice').show();
                if(Number('{{CRUDBooster::myPrivilegeId()}}') == 3 || Number('{{CRUDBooster::myPrivilegeId()}}') == 4){
                    $('#finalized_button').hide();
                }else{
                    $('#finalized_button').show();
                    $('#amount').attr('disabled', false);
                    $('#notes').attr('disabled', false);
                }
            }else{
                $('#close_button').show();
                $('#save_button').show();
                $('#amount').attr('disabled', true);
                $('#notes').attr('disabled', true);
            }
        }

        function closed() {
            $('.loading').show();

            counter.closed_at = moment().format('YYYY-MM-DD HH:mm:ss');
            counter.status = 1;

            $.ajax({
                method: "POST",
                url: '{{CRUDBooster::mainpath('update-counter-header')}}',
                data: {
                    counter: counter,
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: true,
                success: function (data) {
                    if (data) {
                        $('#closed_at').val(moment(counter.closed_at).format('DD/MM/YYYY HH:mm:ss'));
                        disableCounter(counter.status);
                    }
                    $('.loading').hide();
                },
                error: function (request, status, error) {
                    $('.loading').hide();
                    console.log('PostAdd status = ', status);
                    console.log('PostAdd error = ', error);
                    swal("Thông báo", "Có lỗi xãy ra khi lưu dữ liệu, vui lòng thử lại.", "error");
                }
            });
        }

        function finalized() {
            if(validate()){
                $('.loading').show();

                counter.finalized_at = moment().format('YYYY-MM-DD HH:mm:ss');
                counter.finalized_by = Number('{{CRUDBooster::myId()}}');
                counter.notes = $('#notes').val() ? $('#notes').val() : null;
                counter.status = 2;

                $.ajax({
                    method: "POST",
                    url: '{{CRUDBooster::mainpath('update-counter-header')}}',
                    data: {
                        counter: counter,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    async: true,
                    success: function (data) {
                        if (data) {
                            $('#finalized_at').val(moment(counter.finalized_at).format('DD/MM/YYYY HH:mm:ss'));
                            $('#finalized').val('{{CRUDBooster::myName()}}');
                            disableCounter(counter.status);
                        }
                        $('.loading').hide();
                    },
                    error: function (request, status, error) {
                        $('.loading').hide();
                        console.log('PostAdd status = ', status);
                        console.log('PostAdd error = ', error);
                        swal("Thông báo", "Có lỗi xãy ra khi lưu dữ liệu, vui lòng thử lại.", "error");
                    }
                });
            }
        }

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }

        function printCounter() {
            if(counter.id) {
                popupWindow("{{action('AdminGoldCountersController@getPrintCounter')}}/" + counter.id,"print");
            }else{
                alert("Bạn không thể in hóa đơn nếu chưa lưu đơn hàng!");
            }
        }

        function printCounterDetail() {
            if(counter.id) {
                popupWindow("{{action('AdminGoldCountersController@getPrintCounterDetail')}}/" + counter.id,"print");
            }else{
                alert("Bạn không thể in bảng kê chi tiết nếu chưa lưu đơn hàng!");
            }
        }
	</script>
@endpush