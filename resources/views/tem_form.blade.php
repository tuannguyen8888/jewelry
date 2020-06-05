
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">           
    <title>:: {{CRUDBooster::getSetting('appname') }} ::</title>
    <!-- Bootstrap core CSS -->
    <link href="{{asset('vendor/crudbooster/assets/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('vendor/crudbooster/assets/select2/dist/css/select2.min.css')}}" />
</head>
<body>
    <div style="text-align:-webkit-center">
        <table id="table_received_header" class="table received_header_table">
            <tr>
                <th class="th_border">
                    <div class="row">
                        <label class="text-left" id="product_code" name="product_code"></label>
                        <label class="text-right" id="supplier_code" name="supplier_code"></label>
                    </div>
                    <div class="row">
                        <label class="control-label" id="total_weight" name="total_weight"></label>
                        <label class="control-label text-right" id="gem_weight" name="gem_weight"></label>
                    </div>
                    <div class="row">
                        <label class="control-label text-center" id="gold_weight" name="gold_weight"></label>
                    </div>
                    <div class="row">
                        <label class="control-label" id="retail_fee" name="retail_fee"></label>
                        <label class="control-label text-right" id="whole_fee" name="whole_fee"></label>
                    </div>
                </th>
                <th>
                    <div class="row">
                        <label class="control-label text-center" id="product_type_name" name="product_type_name"></label>
                    </div>
                    <div class="row">
                        <label class="control-label text-center">CTY TNHH VBĐQ KIM VẠN PHƯỚC</label>
                    </div>
                    <div class="row">
                        <label class="control-label text-center" id="bar_code_sign" name="bar_code_sign"></label>
                    </div>
                    <div class="row">
                        <label class="control-label text-center" id="bar_code" name="bar_code"></label>
                    </div>
                </th>
            </tr>
        </table>
    </div>
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="{{asset('vendor/crudbooster/assets/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('vendor/crudbooster/assets/select2/dist/js/select2.min.js')}}"></script>
    <script src="{{asset('vendor/crudbooster/jquery.price_format.2.0.min.js')}}"></script> 
    <script src="{{asset('js/notify.min.js')}}"></script> 

    <style>
        #table_received_header tbody tr {
            display:table;
            table-layout:fixed;
            border:none;
        }
        .received_header_table{
            margin-bottom:0px;
            border:none;
            width:500px;

        }
        #table_received_header .th_border {
            border-right: 1px solid #dddddd;
            width:50%;
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
        $(function(){
            // console.log('bar_code = ', '{{$bar_code}}');
            // console.log('product_code = ', '{{$product_code}}');
            $('#product_code').html('{{$product_code}}');
            $('#supplier_code').html('{{$supplier_code}}');
            $('#total_weigth').html('{{$total_weigth}}');
            $('#gem_weigth').html('{{$gem_weigth}}');
            $('#gold_weigth').html('{{$gold_weigth}}');
            $('#retail_fee').html('{{$retail_fee}}');
            $('#whole_fee').html('{{$whole_fee}}');
            $('#product_type_name').html('{{$product_type_name}}');
            $('#bar_code_sign').html('{{$bar_code}}');
            $('#bar_code').html('{{$bar_code}}');
        });
    </script>
</body>
</html>
