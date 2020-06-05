<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- Your custom  HTML goes here -->
	<!-- Your html goes here -->
	<div>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<strong><i class="fa fa-bar-chart-o"></i> Chi tiết Nhập - Xuất - Tồn</strong>
			</div>
			<div class="panel-body" id="parent-form-area">
				<form  id="form">
					<div class="col-sm-12">
						<div class="row">
							<div class="col-sm-4">
								<div class="row">
									<label class="control-label col-sm-4">Từ ngày <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
									<div class="col-sm-7">
										<div class="input-group" >
											<input id="from_date" readonly type="text" class="form-control bg-white" required>
											<div class="input-group-addon bg-gray">
												<i class="fa fa-calendar"></i>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<label class="control-label col-sm-4">Đến ngày <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
									<div class="col-sm-7">
										<div class="input-group" >
											<input id="to_date" readonly type="text" class="form-control" readonly>
											<div class="input-group-addon bg-gray">
												<i class="fa fa-calendar"></i>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<label class="control-label col-sm-1"></label>
									<div class="col-sm-4">
										<a id="print_report" style="cursor: pointer;" onclick="printReport(false)" class="btn btn-primary"><i class="fa fa-print"></i> Tổng kho</a>
									</div>
									<label class="control-label col-sm-1"></label>
									<div class="col-sm-4">
										<a id="print_report_detail" style="cursor: pointer;" onclick="printReport(true)" class="btn btn-primary"><i class="fa fa-print"></i> Theo kho</a>
									</div>
								</div>
								<div class="row">
									<label class="control-label col-sm-1"></label>
									<div class="col-sm-4">
										<a id="export_excel" style="cursor: pointer;" onclick="exportReport(false)" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Tổng kho</a>
									</div>
									<label class="control-label col-sm-1"></label>
									<div class="col-sm-4">
										<a id="export_excel_detail" style="cursor: pointer;" onclick="exportReport(true)" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Theo kho</a>
									</div>
								</div>
							</div>
                            <div class="col-sm-8">
								<div class="row">
									<table id="table_stocks" class='table table-bordered'>
										<thead>
										<tr class="bg-success">
											<th class="action no-padding text-center"><input id="check_all" type="checkbox" checked="1" onchange="checkAll();" required></th>
											<th class="sort_no">Stt</th>
											<th class="stock_code">Mã kho</th>
											<th>Tên kho</th>
										</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="loading"></div>
@endsection

@push('bottom')
	<style>
		#table_stocks tbody {
			display:block;
			max-height:600px;
			overflow:auto;
		}
		#table_stocks thead, #table_stocks tfoot, #table_stocks  tbody tr {
			display:table;
			width:100%;
			table-layout:fixed;
		}
		#table_stocks thead, #table_stocks tfoot {
			width: 100%
		}
		#table_stocks table {
			width:100%;
		}
		#table_stocks .action{
			width: 30px;
		}
		#table_stocks .sort_no{
			width: 30px;
		}
		#table_stocks .stock_code{
			width: 150px;
		}
		.row{
			margin-bottom: 5px;
		}
		.content-header{
			display: none;
		}
		.loading{
			display: none;
		}
	</style>

	<script type="application/javascript">
		stocks = [];
        $(function(){
            $('#from_date').datepicker({
                format:'dd/mm/yyyy',
                autoclose:true,
                todayHighlight:true,
                showOnFocus:false
            });
            $('#from_date').val(moment('01/' + moment().format('MM/YYYY'), 'DD/MM/YYYY').format('DD/MM/YYYY'))

			$('#to_date').datepicker({
                format:'dd/mm/yyyy',
                autoclose:true,
                todayHighlight:true,
                showOnFocus:false
            });
            $('#to_date').val(moment().format('DD/MM/YYYY'))

			$.ajax({
                method: "GET",
                url: '{{Route("AdminGoldStocksControllerGetStocks")}}',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: false,
                success: function (data) {
                    if (data && data.stocks) {
						stocks = data.stocks;
						loadStock();
                    }
                },
                error: function (request, status, error) {
                    console.log('PostAdd status = ', status);
                    console.log('PostAdd error = ', error);
                }
            });
		});

		function loadStock() {
			if (stocks && stocks.length > 0) {
				let html = '';
				stocks.forEach(function (detail, i) {
					html += `<tr id="stock_index_${detail.id}">
						<th class="action no-padding text-center"><input id="stock_${detail.id}_check" type="checkbox" checked="1" required></th>
						<th class="sort_no text-right">${i + 1}</th>
						<th class="stock_code">${detail.code}</th>
						<th>${detail.name}</th>
					</tr>`;					
				});
				// console.log('html = ', html)
				$('#table_stocks tbody').append(html);
			}
        }

		function checkAll() {
			console.log('checkAll');
			if (stocks && stocks.length > 0) {
				// $.ajax({
					stocks.forEach(function (detail, i) {
						$(`#stock_${detail.id}_check`).prop('checked', $(`#check_all`).is(":checked"));
					});
				// });
			}
        }

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }

        function printReport(detail) {
            if(!$('#from_date').val()){
				alert("Bạn phải chọn từ ngày!");
				$('#from_date').focus();
			}else if(!$('#to_date').val()){
				alert("Bạn phải chọn đến ngày!");
				$('#to_date').focus();
            }else{
				var stock_ids = '';
				if(stocks && stocks.length > 0) {
					stocks.forEach(function (detail, i) {
						if($(`#stock_${detail.id}_check`).is(":checked")) {
							if(stock_ids){
								stock_ids += ',';
							}
							stock_ids += detail.id;
						}
					});
				}
				// console.log('stock_ids = ', stock_ids);
				if(stock_ids) {
					var from_date = moment($('#from_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
					var to_date = moment($('#to_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
					// console.log('from_date = ', from_date);
					// console.log('to_date = ', to_date);
					if(detail){
						popupWindow("{{action('AdminGoldStocksController@getPrintStockMovement')}}/" + from_date + "@" + to_date + "@" + stock_ids,"print");
					}else{
						popupWindow("{{action('AdminGoldStocksController@getPrintStockMovementAll')}}/" + from_date + "@" + to_date + "@" + stock_ids,"print");
					}
				}else{
					alert("Bạn phải chọn ít nhất 1 kho!");
				}
            }
        }

		function exportReport(detail) {
            if(!$('#from_date').val()){
				alert("Bạn phải chọn từ ngày!");
				$('#from_date').focus();
			}else if(!$('#to_date').val()){
				alert("Bạn phải chọn đến ngày!");
				$('#to_date').focus();
            }else{
				var stock_ids = '';
				if(stocks && stocks.length > 0) {
					stocks.forEach(function (detail, i) {
						if($(`#stock_${detail.id}_check`).is(":checked")) {
							if(stock_ids){
								stock_ids += ',';
							}
							stock_ids += detail.id;
						}
					});
				}
				// console.log('stock_ids = ', stock_ids);
				if(stock_ids) {
					var from_date = moment($('#from_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
					var to_date = moment($('#to_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
					// console.log('from_date = ', from_date);
					// console.log('to_date = ', to_date);
					if(detail){
						popupWindow("{{action('AdminGoldStocksController@getPrintStockMovementXlsx')}}/" + from_date + "@" + to_date + "@" + stock_ids,"print");
					}else{
						popupWindow("{{action('AdminGoldStocksController@getPrintStockMovementAllXlsx')}}/" + from_date + "@" + to_date + "@" + stock_ids,"print");
					}
				}else{
					alert("Bạn phải chọn ít nhất 1 kho!");
				}
            }
        }
	</script>
@endpush