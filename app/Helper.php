<?php
class Enums{
    public static $PRODUCT_STATUS = "0|<label class='label label-warning'>Hết hàng</label>;1|<label class='label label-info'>Còn hàng</label>";
    public static $TRANFER_TYPES = "2|<label class='label label-warning'>Trả hàng</label>;1|<label class='label label-primary'>Chuyển kho</label>";
    public static $IMPORT = "0|Phát sinh khi saler bán cho khách hàng mới;1|Import từ bảng công nợ của kế toán";
    public static $INPUT_STATUS = "0|<label class='label label-success label-status' data-code='0'>Đang nhập</label>;1|<label class='label label-primary label-status' data-code='1'>Hoàn tất</label>;2|<label class='label label-warning label-status' data-code='2'>Đã bán</label>;3|<label class='label label-warning label-status' data-code='3'>Đã trả hàng</label>";
    public static $COUNTER_STATUS = "0|<label class='label label-info label-status' data-code='0'>Mở sổ</label>;1|<label class='label label-primary label-status' data-code='1'>Đóng sổ</label>;2|<label class='label label-success label-status' data-code='2'>Khóa sổ</label>";
    public static $PAWN_STATUS = "0|<label class='label label-info label-status' data-code='0'>Đang nhập</label>;1|<label class='label label-warning label-status' data-code='1'>Đang cầm</label>;2|<label class='label label-primary label-status' data-code='2'>Đã thanh lý</label>;3|<label class='label label-success label-status' data-code='3'>Đã tất toán</label>";
    public static $DUE_STATUS = "1|<label class='label label-primary label-status' data-code='1'>Đúng hạn</label>;2|<label class='label label-danger label-status' data-code='2'>Quá hạn</label>";
    public static $CUSTOMER_TYPE = "0|<label class='label label-info'>Thường</label>;1|<label class='label label-primary'>VIP</label>";
    public static $LIQUIDATION_METHOD = "0|<label class='label label-success'>Tất toán</label>;1|<label class='label label-primary'>Thanh lý</label>";
    public static $PAYMENT_METHOD = "0|<label class='label label-success'>Tiền mặt</label>;1|<label class='label label-primary'>Chuyển khoản</label>";
    public static $USER_STATUS = "0|<label class='label label-success'>Đang dùng</label>;1|<label class='label label-primary'>Tạm ngưng</label>";
    public static $OBJECT_TYPE = "0|Khách hàng;1|Nhà cung cấp;2|Nhà đầu tư;3|Nhân viên";
}

function employees() {
	if(Session::has('employees_id')) {
		$data = CRUDBooster::first('employees',Session::get('employees_id'));
		return $data;
	}else{
		return false;
	}
}

if (!function_exists('find_string_in_array')) {
    function find_string_in_array ($arr, $string) {
        return array_filter($arr, function($value) use ($string) {
            return strpos($value, $string) !== false;
        });
    }
}

if (!function_exists('get_string_in_array')) {
    function get_string_in_array ($key, $status) {
        $dataenum = (is_array($key))?$key:explode(";",$key);
        $results = find_string_in_array ($dataenum,$status."|");
        if( !empty($results) ) {
            foreach ($results as $value) {
                //$results = array_values($results)[0];
                $val = $lab = '';
                if(strpos($value,'|')!==FALSE) {
                    $draw = explode("|",$value);
                    $val = $draw[0];
                    $lab = $draw[1];
                }else{
                    $val = $lab = $value;
                }
                if ($status == $val){
                    return $lab;
                }
            }

        }
        return '';
    }
}

if (!function_exists('get_user_status')) {
    function get_user_status($status) {
        return get_string_in_array (Enums::$USER_STATUS, $status);
    }
}

if (!function_exists('get_product_status')) {
    function get_product_status($status) {
        return get_string_in_array (Enums::$PRODUCT_STATUS, $status);
    }
}

if (!function_exists('get_payment_method')) {
    function get_payment_method($status) {
        return get_string_in_array (Enums::$PAYMENT_METHOD, $status);
    }
}

if (!function_exists('get_input_status')) {
    function get_input_status($status) {
        return get_string_in_array (Enums::$INPUT_STATUS, $status);
    }
}

if (!function_exists('get_pawn_status')) {
    function get_pawn_status($status) {
        return get_string_in_array (Enums::$PAWN_STATUS, $status);
    }
}
if (!function_exists('get_due_status')) {
    function get_due_status($status) {
        return '<br>'.get_string_in_array (Enums::$DUE_STATUS, $status);
    }
}

if (!function_exists('get_liquidation_method')) {
    function get_liquidation_method($status) {
        return get_string_in_array (Enums::$LIQUIDATION_METHOD, $status);
    }
}

if (!function_exists('get_counter_status')) {
    function get_counter_status($status) {
        return get_string_in_array (Enums::$COUNTER_STATUS, $status);
    }
}

if (!function_exists('get_customer_type')) {
    function get_customer_type($type) {
        return get_string_in_array (Enums::$CUSTOMER_TYPE, $type);
    }
}

if (!function_exists('get_tranfer_type_name')) {
    function get_tranfer_type_name($tranfer_type) {
        return get_string_in_array (Enums::$TRANFER_TYPES, $tranfer_type);
    }
}
if (!function_exists('arrayCopy')) {
    function arrayCopy(array $array)
    {
        $result = array();
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $result[$key] = arrayCopy($val);
            } elseif (is_object($val)) {
                $result[$key] = clone $val;
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
    }
}
if (!function_exists('date_time_format')) {
    function date_time_format($dateTimeStr, $formatInString, $formatOutString) {
        if(!$dateTimeStr) return $dateTimeStr;
        $dateTime = DateTime::createFromFormat($formatInString, $dateTimeStr); // 'Y-m-d H:i:s'
        return $dateTime->format($formatOutString);
    }
}

if (!function_exists('convert_enums_to_array')) {
    function convert_enum_to_array ($enums_string) {
        $dataenums = explode(";",trim($enums_string));
        $results = [];
        if($dataenums && count($dataenums)){
            foreach ($dataenums as $item) {
                $id_name = explode("|",$item);
                if($id_name && count($id_name)==2){
                    $results[] = ['id'=>$id_name[0], 'name'=>$id_name[1]];
                }
            }
        }
        return $results;
    }
}