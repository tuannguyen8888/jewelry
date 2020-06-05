function control_primary_button(myPrivilegeId){
    let labels = $('.label-status').toArray();
    for(let i=0; i < labels.length; i++){
        let label = $(labels[i]);
        let row = label.parent().parent();
        let status = $(label).attr('data-code');
        if(status != 0){
            console.log('myPrivilegeId = ', myPrivilegeId);
            if(myPrivilegeId != 1 && myPrivilegeId != 4){ //Super Administrator; Quản trị hệ thống
                $($('.btn-delete', row)[0]).remove();
            }
            $($('.btn-edit', row)[0]).remove();
        }else{
            $($('.btn-detail', row)[0]).remove();
        }
    }
}

function getPrice(type_id, apply_time) {
    let result = null;
    $.ajax({
        method: "GET",
        url: '{{Route("AdminGoldPriceControllerGetPrice")}}',
        data: {
            apply_time: apply_time,
            product_type_id: type_id,
            _token: '{{ csrf_token() }}'
        },
        dataType: "json",
        async: false,
        success: function (data) {
            if (data && data.price) {
                result = data.price;
                // console.log('Gia trị từ dữ liệu = ', result);
            }
        },
        error: function (request, status, error) {
            console.log('PostAdd status = ', status);
            console.log('PostAdd error = ', error);
        }
    });
    // console.log('Gia trị trả về = ', result);
    return result;
}

function getOpenCounter() {
    let result = null;
    $.ajax({
        method: "GET",
        url: '{{Route("AdminGoldCountersControllerGetOpenCounter")}}',
        data: {
            _token: '{{ csrf_token() }}'
        },
        dataType: "json",
        async: false,
        success: function (data) {
            if (data && data.counter) {
                result = data.counter;
                // console.log('Gia trị từ dữ liệu = ', result);
            }
        },
        error: function (request, status, error) {
            console.log('PostAdd status = ', status);
            console.log('PostAdd error = ', error);
        }
    });
    // console.log('Gia trị trả về = ', result);
    return result;
}