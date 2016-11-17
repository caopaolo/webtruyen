// Ham tu dong danh tag cho content
function autoTagContent() {
    swal({
        title: "Do you want auto tag content !",
        text: "Bạn có muốn tự động đánh tag cho content!",
        type: "info", showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        var content = CKEDITOR.instances.content.getData();
        setTimeout(function () {
            $.ajax({
                method: "POST",
                url: "/manageapp/content/auto-tag-content",
                data: {content: content}
            }).done(function (response) {
                if (response.status == 1) {
                    CKEDITOR.instances.content.setData(response.data.content);
                    swal({
                        title: "Tag successfully!",
                        html: true});
                } else {
                    swal({
                        title: response.message,
                        html: true});
                }
            });
        }, 1000);
    });
}

// Ham tu dong tim tag trong content va fill
function autoFillTag(tag_id) {
    swal({
        title: "Do you want auto fill tag !",
        text: "Bạn có muốn tự động đánh tag cho content!",
        type: "info", showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        var content = CKEDITOR.instances.content.getData();
        setTimeout(function () {
            $.ajax({
                method: "POST",
                url: "/manageapp/content/auto-fill-tag",
                data: {content: content}
            }).done(function (response) {
                if (response.status == 1) {
                    $('#' + tag_id).tokenfield('setTokens', response.data.tag);
                    swal({
                        title: "Tag successfully!",
                        html: true});
                } else {
                    swal({
                        title: response.message,
                        html: true});
                }
            });
        }, 1000);
    });
}