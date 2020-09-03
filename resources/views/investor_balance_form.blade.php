<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- Your custom  HTML goes here -->
	<!-- Your html goes here -->
	<div>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<strong><i class="fa fa-bar-chart-o"></i>Công Nợ Nhà Đầu Tư</strong>
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
									<label class="control-label col-sm-4">Loại báo cáo <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
									<div class="col-sm-7">
										<select id="rpt_type" class="form-control">
											<option value=0>Công nợ</option>
											<option value=1>Bảng đối chiếu công nợ</option>
										</select>
									</div>
								</div>
								<div class="row">
									<label class="control-label col-sm-4">Cửa hàng <span class="text-danger" title="Không được bỏ trống trường này.">*</span></label>
									<div class="col-sm-7">
										<select id="brand_id" class="form-control"></select>
									</div>
								</div>
								<div class="row">
									<label class="control-label col-sm-1"></label>
									<div class="col-sm-4">
										<a id="print_report" style="cursor: pointer;" onclick="printReport(true)" class="btn btn-primary"><i class="fa fa-print"></i> Báo cáo</a>
									</div>
									<label class="control-label col-sm-1"></label>
									<div class="col-sm-4">
										<a id="export_excel" style="cursor: pointer;" onclick="printReport(false)" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Kết xuất</a>
									</div>
								</div>
							</div>
							<div class="col-sm-8">
								<div class="row">
									<table id="table_investors" class='table table-bordered'>
										<thead>
										<tr class="bg-success">
											<th class="action no-padding text-center"><input id="check_all" type="checkbox" checked="1" onchange="checkAll();" required></th>
											<th class="sort_no">Stt</th>
											<th class="investor_code">Mã nhà đầu tư</th>
											<th>Tên nhà đầu tư</th>
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
		#table_investors tbody {
			display:block;
			max-height:600px;
			overflow:auto;
		}
		#table_investors thead, #table_investors tfoot, #table_investors  tbody tr {
			display:table;
			width:100%;
			table-layout:fixed;
		}
		#table_investors thead, #table_investors tfoot {
			width: 100%
		}
		#table_investors table {
			width:100%;
		}
		#table_investors .action{
			width: 30px;
		}
		#table_investors .sort_no{
			width: 30px;
		}
		#table_investors .investor_code{
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
		investors = [];
        $(function (){
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
			loadBrands();

			$.ajax({
                method: "GET",
                url: '{{Route("AdminGoldInvestorsControllerGetInvestors")}}',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: false,
                success: function (data) {
                    if (data && data.investors) {
						investors = data.investors;
						loadInvestor();
                    }
                },
                error: function (request, status, error) {
                    console.log('PostAdd status = ', status);
                    console.log('PostAdd error = ', error);
                }
            });
		});

		function loadInvestor() {
			if (investors && investors.length > 0) {
				let html = '';
				investors.forEach(function (detail, i) {
					html += `<tr id="investor_index_${detail.id}">
						<th class="action no-padding text-center"><input id="investor_${detail.id}_check" type="checkbox" checked="1" required></th>
						<th class="sort_no text-right">${i + 1}</th>
						<th class="investor_code">${detail.code}</th>
						<th>${detail.name}</th>
					</tr>`;					
				});
				// console.log('html = ', html)
				$('#table_investors tbody').append(html);
			}
		}
		
		function loadBrands() {
            $.ajax({
                method: "GET",
                url: '{{Route("AdminGoldBrandsControllerGetBrands")}}',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: false,
                success: function (data) {
                    if (data && data.brands && data.brands.length > 0) {
                        let html = '';
						data.brands.forEach(function (detail, i) {
                            html += `<option value=${detail.id}>${detail.name}</option>`;					
                        });
                        $('#brand_id').append(html);
                    }
                },
                error: function (request, status, error) {
                    console.log('PostAdd status = ', status);
                    console.log('PostAdd error = ', error);
                }
            });
        }

		function checkAll() {
			console.log('checkAll');
			if (investors && investors.length > 0) {
				investors.forEach(function (detail, i) {
					$(`#investor_${detail.id}_check`).prop('checked', $(`#check_all`).is(":checked"));
				});
			}
        }

        function popupWindow(url,windowName) {
            window.open(url,windowName,'height=500,width=600');
            return false;
        }

        function printReport(print) {
            if(!$('#from_date').val()){
				alert("Bạn phải chọn từ ngày!");
				$('#from_date').focus();
			}else if(!$('#to_date').val()){
				alert("Bạn phải chọn đến ngày!");
				$('#to_date').focus();
            }else{
				var ids = '';
				if(investors && investors.length > 0) {
					investors.forEach(function (detail, i) {
						if($(`#investor_${detail.id}_check`).is(":checked")) {
							if(ids){
								ids += ',';
							}
							ids += detail.id;
						}
					});
				}
				// console.log('ids = ', ids);
				if(ids) {
					var from_date = moment($('#from_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
					var to_date = moment($('#to_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
					if(print){
						if($('#rpt_type').val() == 0){
							popupWindow("{{action('AdminGoldInvestorsController@getPrintBalance')}}/" + to_date + "@" + $('#brand_id').val() + "@" + ids,"print");
						}else{
							popupWindow("{{action('AdminGoldInvestorsController@getPrintBalanceDetail')}}/" + from_date + "@" + to_date + "@" + $('#brand_id').val() + "@" + ids,"print");
						}
					}else{
						if($('#rpt_type').val() == 0){
							popupWindow("{{action('AdminGoldInvestorsController@getPrintBalanceXlsx')}}/" + to_date + "@" + $('#brand_id').val() + "@" +ids,"export");
						}else{
							popupWindow("{{action('AdminGoldInvestorsController@getPrintBalanceDetailXlsx')}}/" + from_date + "@" + to_date + "@" + $('#brand_id').val() + "@" +ids,"export");
						}
					}
				}else{
					alert("Bạn phải chọn ít nhất 1 NCC!");
				}
            }
        }
	</script>
@endpush
