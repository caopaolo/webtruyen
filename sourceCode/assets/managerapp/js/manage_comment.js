function deleteComment(x, type=1, object=null) {
    var table = document.getElementById("example");
    var rowIndex = document.getElementById('row_'+x).rowIndex;
    swal({
        title: "Delete this comment !",
        text: "Bạn có muốn xóa bài comment này không ?",
        type: "info", showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        setTimeout(function () {
            $.ajax({
                method: "POST",
                url: "/manageapp/comment/delete",
                data: {comment_id: x}
            }).done(function (msg) {
                if (msg === 'success') {
                    // Xu ly du lieu hien thi
                    if (type == 1) {
                        table.deleteRow(rowIndex);    
                    } else {
                        $('#row_'+x).find('.status_comment').html('Deleted');
                        if (object != null) {
                            $(object).remove();
                        }
                    }
                    
                    // Hien alert thong bao ket qua
                    swal({
                        title: "Delete success!",
                        html: true});
                } else {
                    swal({
                        title: "Delete false!",
                        html: true});
                }
            });
        }, 1000);
    });

}

function approvalComment(x, type=1, object=null) {
    var table = document.getElementById("example");
    var rowIndex = document.getElementById('row_'+x).rowIndex;
    swal({
        title: "Approval this comment !",
        text: "Bạn muốn duyệt cho comment này không?",
        type: "info", showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        setTimeout(function () {
            $.ajax({
                method: "POST",
                url: "/manageapp/comment/approve",
                data: {comment_id: x}
            }).done(function (msg) {
                if (msg === 'success') {
                    // Xu ly du lieu hien thi
                    if (type == 1) {
                        table.deleteRow(rowIndex);    
                    } else {                        
                        $('#row_'+x).find('.status_comment').html('Approved');
                        if (object != null) {
                            $(object).remove();
                        }
                    }
                    
                    // Hien alert thong bao ket qua
                    swal({
                        title: "Approval success!",
                        html: true});
                } else {
                    swal({
                        title: "Approval false!",
                        html: true});
                }
            });
        }, 1000);
    });
}