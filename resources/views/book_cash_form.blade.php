<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- Your custom  HTML goes here -->
	<!-- Your html goes here -->
	<div>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<strong><i class="fa fa-book"></i> Sổ quỹ</strong>
			</div>
			<div class="panel-body" id="parent-form-area">
				<form  id="form">
					<div class="col-sm-12">
						<div class="row">
							<label class="control-label col-sm-1">Từ ngày <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-2">
								<div class="input-group" >
									<input id="from_date" readonly type="text" class="form-control bg-white" required>
									<div class="input-group-addon bg-gray">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
							</div>
							<label class="control-label col-sm-1 text-right">Đến ngày <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
							<div class="col-sm-2">
								<div class="input-group" >
									<input id="to_date" readonly type="text" class="form-control" readonly>
									<div class="input-group-addon bg-gray">
										<i class="fa fa-calendar"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-1">Loại sổ</label>
							<div class="col-sm-2">
								<select id="book_type" class="form-control">
                                    <option value=-1>Tất cả</option>
                                    <option value=0>Tiền mặt</option>
                                    <option value=1>Ngân hàng</option>
                                </select>
							</div>
						</div>
						<div class="row">
							<label class="control-label col-sm-1"></label>
							<div class="col-sm-1">
								<a id="print_report" style="cursor: pointer;" onclick="printReport(false)" class="btn btn-primary"><i class="fa fa-print"></i> In sổ</a>
							</div>
							<label class="control-label col-sm-1"></label>
							<div class="col-sm-1">
								<a id="export_excel" style="cursor: pointer;" onclick="printReport(true)" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Xuất dữ liệu</a>
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
		});

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }

        function printReport(isExport) {
            if(!$('#from_date').val()){
				alert("Bạn phải chọn từ ngày!");
				$('#from_date').focus();
			}else if(!$('#to_date').val()){
				alert("Bạn phải chọn đến ngày!");
				$('#to_date').focus();
            }else{
				var from_date = moment($('#from_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
				var to_date = moment($('#to_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
				var book_type = $('#book_type').val() ? $('#book_type').val() : 0;
				// console.log('from_date = ', from_date);
				// console.log('to_date = ', to_date);
				if(isExport){
					popupWindow("{{action('AdminGoldReceiptsController@getPrintBookCashXlsx')}}/" + from_date + "@" + to_date + "@" + book_type,"Export");
				}else{
					popupWindow("{{action('AdminGoldReceiptsController@getPrintBookCash')}}/" + from_date + "@" + to_date + "@" + book_type,"Print");
				}
            }
        }
	</script>
@endpush