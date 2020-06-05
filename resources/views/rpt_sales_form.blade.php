<!-- First you need to extend the CB layout -->
@extends('crudbooster::admin_template')
@section('content')
	<!-- Your custom  HTML goes here -->
	<!-- Your html goes here -->
	<div>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<strong><i class="fa fa-bar-chart-o"></i> Báo cáo doanh số</strong>
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
											<option value=0>Tổng hợp</option>
											<option value=1>Chi tiết</option>
										</select>
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
								<div class="row">
									<label class="control-label col-sm-1"></label>
									<div class="col-sm-4">
										<a id="export_excel" style="cursor: pointer;" onclick="printDetail(true)" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Chi tiết bán</a>
									</div>
									<label class="control-label col-sm-1"></label>
									<div class="col-sm-4">
										<a id="export_excel" style="cursor: pointer;" onclick="printDetail(false)" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Chi tiết mua</a>
									</div>
								</div>
							</div>
                            <div class="col-sm-8">
								<div class="row">
									<table id="table_users" class='table table-bordered'>
										<thead>
										<tr class="bg-success">
											<th class="action no-padding text-center"><input id="check_all" type="checkbox" checked="1" onchange="checkAll();" required></th>
											<th class="sort_no">Stt</th>
											<th class="user_code">Mã nhân viên</th>
											<th>Tên nhân viên</th>
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
		#table_users tbody {
			display:block;
			max-height:600px;
			overflow:auto;
		}
		#table_users thead, #table_users tfoot, #table_users  tbody tr {
			display:table;
			width:100%;
			table-layout:fixed;
		}
		#table_users thead, #table_users tfoot {
			width: 100%
		}
		#table_users table {
			width:100%;
		}
		#table_users .action{
			width: 30px;
		}
		#table_users .sort_no{
			width: 30px;
		}
		#table_users .user_code{
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
		users = [];
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
                url: '{{Route("AdminCmsUsersControllerGetUsers")}}',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                dataType: "json",
                async: false,
                success: function (data) {
                    if (data && data.users) {
						users = data.users;
						loadUser();
                    }
                },
                error: function (request, status, error) {
                    console.log('PostAdd status = ', status);
                    console.log('PostAdd error = ', error);
                }
            });
		});

		function loadUser() {
			if (users && users.length > 0) {
				let html = '';
				users.forEach(function (detail, i) {
					html += `<tr id="user_index_${detail.id}">
						<th class="action no-padding text-center"><input id="user_${detail.id}_check" type="checkbox" checked="1" required></th>
						<th class="sort_no text-right">${i + 1}</th>
						<th class="user_code">${detail.employee_code}</th>
						<th>${detail.name}</th>
					</tr>`;					
				});
				// console.log('html = ', html)
				$('#table_users tbody').append(html);
			}
        }

		function checkAll() {
			console.log('checkAll');
			if (users && users.length > 0) {
				// $.ajax({
					users.forEach(function (detail, i) {
						$(`#user_${detail.id}_check`).prop('checked', $(`#check_all`).is(":checked"));
					});
				// });
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
				var user_ids = '';
				if(users && users.length > 0) {
					users.forEach(function (detail, i) {
						if($(`#user_${detail.id}_check`).is(":checked")) {
							if(user_ids){
								user_ids += ',';
							}
							user_ids += detail.id;
						}
					});
				}
				// console.log('user_ids = ', user_ids);
				if(user_ids) {
					var from_date = moment($('#from_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
					var to_date = moment($('#to_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
					// console.log('from_date = ', from_date);
					// console.log('to_date = ', to_date);
					if(print){
						if($('#rpt_type').val() == 0){
							popupWindow("{{action('AdminGoldSaleOrdersController@getPrintSales')}}/P@" + from_date + "@" + to_date + "@" + user_ids,"print");
						}else{
							popupWindow("{{action('AdminGoldSaleOrdersController@getPrintSalesDetail')}}/P@" + from_date + "@" + to_date + "@" + user_ids,"print");
						}
					}else{
						if($('#rpt_type').val() == 0){
							popupWindow("{{action('AdminGoldSaleOrdersController@getPrintSales')}}/X@" + from_date + "@" + to_date + "@" + user_ids,"print");
						}else{
							popupWindow("{{action('AdminGoldSaleOrdersController@getPrintSalesDetail')}}/X@" + from_date + "@" + to_date + "@" + user_ids,"print");
						}
					}
				}else{
					alert("Bạn phải chọn ít nhất 1 kho!");
				}
            }
        }

		function printDetail(isSales) {
            if(!$('#from_date').val()){
				alert("Bạn phải chọn từ ngày!");
				$('#from_date').focus();
			}else if(!$('#to_date').val()){
				alert("Bạn phải chọn đến ngày!");
				$('#to_date').focus();
            }else{
				var user_ids = '';
				if(users && users.length > 0) {
					users.forEach(function (detail, i) {
						if($(`#user_${detail.id}_check`).is(":checked")) {
							if(user_ids){
								user_ids += ',';
							}
							user_ids += detail.id;
						}
					});
				}
				if(user_ids) {
					var from_date = moment($('#from_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
					var to_date = moment($('#to_date').val(),'DD/MM/YYYY').format('YYYY-MM-DD');
					if(isSales){
						popupWindow("{{action('AdminGoldSaleOrdersController@getExportDetail')}}/S@" + from_date + "@" + to_date + "@" + user_ids,"print");
					}else{
						popupWindow("{{action('AdminGoldSaleOrdersController@getExportDetail')}}/P@" + from_date + "@" + to_date + "@" + user_ids,"print");
					}
				}else{
					alert("Bạn phải chọn ít nhất 1 kho!");
				}
            }
        }
	</script>
@endpush