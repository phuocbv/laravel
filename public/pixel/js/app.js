function checkbox(id, arid, the_form)
{
    var n = $('#'+id+':checked').val();
    if(n){
        setCheckboxes(the_form, arid, true);
        // Set Style Uifrom Checked
        $("#"+the_form+" .checker span").addClass('checked');

    }else{
        setCheckboxes(the_form, arid, false);
        // Set Style Uifrom un Checked
        $("#"+the_form+" .checker span").removeClass('checked');

    }
}


function setCheckboxes(the_form, id, do_check)
{
    var elts      = (typeof(document.forms[the_form].elements[id]) != 'undefined')
        ? document.forms[the_form].elements[id]
        : 0;
    var elts_cnt  = (typeof(elts.length) != 'undefined')
        ? elts.length
        : 0;

    if (elts_cnt) {
        for (var i = 0; i < elts_cnt; i++) {
            elts[i].checked = do_check;
            //$(elts[i].checked).parent('<span>').addClass().
        }
    } else {
        elts.checked        = do_check;
    }
    return true;
}

function submitAdminForm(){
    var checked = $('input[type=checkbox]').is(':checked');
    if(!checked){
        bootbox.alert({
            message: "Vui lòng chọn một mục để xóa",
            className: "bootbox-sm"
        });
    } else {
        bootbox.confirm({
            message: "Bạn có chắc là muốn xóa mục  không?",
            callback: function(result) {
                if(result == true){
                    $("#adminForm").submit();
                }
            },
            className: "bootbox-sm"
        });
    }

}

function submitAssignForm(){
    var checked = $('input[type=checkbox]').is(':checked');
    if(!checked){
        bootbox.alert({
            message: "Vui lòng chọn ít nhất 1 order để assign",
            className: "bootbox-sm"
        });
    } else {
        bootbox.confirm({
            message: "Bạn có chắc là muốn Assign  không?",
            callback: function(result) {
                if(result == true){
                    $("#adminForm").submit();
                }
            },
            className: "bootbox-sm"
        });
    }

}

function submitPaidForm(){
    var checked = $('input[type=checkbox]').is(':checked');
    if(!checked){
        bootbox.alert({
            message: "Vui lòng chọn ít nhất 1 order để thanh toán",
            className: "bootbox-sm"
        });
    } else {
        bootbox.confirm({
            message: "Bạn có chắc là muốn Pay  không?",
            callback: function(result) {
                if(result == true){

                    //var hix = $('[name=ar_id]').val();

                    //console.dir(hix);
                    $('#adminForm').attr('action', 'order/payall');
                    $('#adminForm').attr('method', 'POST');
                    $("#adminForm").submit();
                }
            },
            className: "bootbox-sm"
        });
    }

}

function submitDeleteForm(){
    var checked = $('input[type=checkbox]').is(':checked');
    if(!checked){
        bootbox.alert({
            message: "Vui lòng chọn ít nhất 1 order để xoá",
            className: "bootbox-sm"
        });
    } else {
        bootbox.confirm({
            message: "Bạn có chắc là muốn Xoá không?",
            callback: function(result) {
                if(result == true){

                    //var hix = $('[name=ar_id]').val();

                    //console.dir(hix);
                    $('#adminForm').attr('action', 'order/deleteall');
                    $('#adminForm').attr('method', 'POST');
                    $("#adminForm").submit();
                }
            },
            className: "bootbox-sm"
        });
    }

}

function exportExcel(){
    bootbox.confirm({
        message: "Export ra Excel hả?",
        callback: function(result) {
            if(result == true){
                $("#export").submit();
            }
        },
        className: "bootbox-sm"
    });
}




// Common Action


