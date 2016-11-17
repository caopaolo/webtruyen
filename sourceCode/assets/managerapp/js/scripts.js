function initializeJS() {

    //tool tips
    jQuery('.tooltips').tooltip();

    //popovers
    jQuery('.popovers').popover();

    //custom scrollbar
        //for html
    jQuery("html").niceScroll({styler:"fb",cursorcolor:"#007AFF", cursorwidth: '6', cursorborderradius: '10px', background: '#F7F7F7', cursorborder: '', zindex: '1000'});
        //for sidebar
    jQuery("#sidebar").niceScroll({styler:"fb",cursorcolor:"#007AFF", cursorwidth: '10', cursorborderradius: '10px', background: '#F7F7F7', cursorborder: '', autohidemode: false});
        // for scroll panel
    jQuery(".scroll-panel").niceScroll({styler:"fb",cursorcolor:"#007AFF", cursorwidth: '3', cursorborderradius: '10px', background: '#F7F7F7', cursorborder: ''});
    
    //sidebar dropdown menu
    jQuery('#sidebar .sub-menu > a').click(function () {
        var last = jQuery('.sub-menu.open', jQuery('#sidebar'));        
        jQuery('.menu-arrow').removeClass('arrow_carrot-right');
        jQuery('.sub', last).slideUp(200);
        var sub = jQuery(this).next();
        if (sub.is(":visible")) {
            jQuery('.menu-arrow').addClass('arrow_carrot-right');            
            sub.slideUp(200, function() {
                jQuery("#sidebar").getNiceScroll().resize();
            });
        } else {
            jQuery('.menu-arrow').addClass('arrow_carrot-down');            
            sub.slideDown(200, function() {
                jQuery("#sidebar").getNiceScroll().resize();
            });
        }
        var o = (jQuery(this).offset());
        diff = 200 - o.top;
        if(diff>0)
            jQuery("#sidebar").scrollTo("-="+Math.abs(diff),500);
        else
            jQuery("#sidebar").scrollTo("+="+Math.abs(diff),500);
    });

    // sidebar menu toggle
    jQuery(function() {
        function responsiveView() {
            var wSize = jQuery(window).width();
            if (wSize <= 768) {
                jQuery('#container').addClass('sidebar-close');
                jQuery('#sidebar > ul').hide();
            }

            if (wSize > 768) {
                jQuery('#container').removeClass('sidebar-close');
                jQuery('#sidebar > ul').show();
            }
        }
        jQuery(window).on('load', responsiveView);
        jQuery(window).on('resize', responsiveView);
    });

    jQuery('.toggle-nav').click(function () {
        if (jQuery('#sidebar > ul').is(":visible") === true) {
            jQuery('#main-content').css({
                'margin-left': '0px'
            });
            jQuery('#sidebar').css({
                'margin-left': '-180px'
            });
            jQuery('#sidebar > ul').hide();
            jQuery("#container").addClass("sidebar-closed");
        } else {
            jQuery('#main-content').css({
                'margin-left': '180px'
            });
            jQuery('#sidebar > ul').show();
            jQuery('#sidebar').css({
                'margin-left': '0'
            });
            jQuery("#container").removeClass("sidebar-closed");
        }
    });

    //bar chart
    if (jQuery(".custom-custom-bar-chart")) {
        jQuery(".bar").each(function () {
            var i = jQuery(this).find(".value").html();
            jQuery(this).find(".value").html("");
            jQuery(this).find(".value").animate({
                height: i
            }, 2000)
        })
    }
    
    $(".img_for_choose").click(function(){
    	$(".fileinput-container input[name="+$(this).attr("id")+"]").trigger("click");
    });
    
    $(".fileinput-container > input").change(function(){
        imgPreview(this);
    });
}

jQuery(document).ready(function(){
    initializeJS();
});

// Ham xem truoc anh up len
function imgPreview(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $("#"+input.name).attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

// Ham xu ly loi ajax
function handleAjaxError(xhr, textStatus, error){
    if(xhr.status == '403') {
        window.location.reload();
    } else {
        alert(error);
    }
}

// Ham xoa doi tuong
function deleteObject(x, type) {
    var url = '';
    var text = '';
    var title = '';
    switch (type) {
        case 1: 
            url = '/manageapp/story/delete';
            text = 'Bạn có muốn xóa nội dung này không?';
            title = 'Nếu xóa bạn sẽ không thể khôi phục lại được';
            break;
        case 2: 
            url = '/manageapp/category/delete';
            text = 'Bạn có muốn xóa category này không?';
            title = 'Nếu xóa category, đồng nghĩa với việc bạn sẽ xóa tất cả các bài viết thuộc category này!';
            break;
        case 3: 
            url = '/manageapp/collection/delete';
            text = 'Bạn có muốn xóa collection này không?';
            title = 'Nếu xóa bạn sẽ không thể khôi phục lại được';
            break;
        case 4: 
            url = '/manageapp/push/delete-schedule';
            text = 'Bạn có chắc muốn xóa lịch push này ?';
            title = 'Nếu xóa, bạn không thể sử dụng lại lịch push này nữa';
            break;
        case 5: 
            url = '/manageapp/tag/delete';
            text = 'Bạn có chắc muốn xóa tag này không?';
            title = 'Nếu xóa bạn sẽ không thể khôi phục lại được';
            break;
        case 6: 
        url = '/manageapp/story-content/delete';
        text = 'Bạn có chắc muốn xóa tag này không?';
        title = 'Nếu xóa bạn sẽ không thể khôi phục lại được';
            break;
        default: break;
    }
    
    if (url) {
        var table = document.getElementById("example");
        var rowIndex = document.getElementById('row_'+x).rowIndex;
        swal({
            title: title,
            text: text,
            type: "info", showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function () {
            setTimeout(function () {
                $.ajax({
                    method: "POST",
                    url: url,
                    data: {id: x}
                }).done(function (response) {
                    if (response.status == 1) {
                        table.deleteRow(rowIndex);
                        swal({
                            title: "Delete success!",
                            html: true});
                    } else {
                        swal({
                            title: "Delete false!",
                            html: true});
                    }
                });
            }, 500);
        });   
    } else {
        alert('Đối tượng không được hỗ trợ');
    }
}

// Ham thay doi loai hien thi noi dung
function changeTypeDisplay(ob, id) {
    var prev_type = $(ob).attr('_type');
    var type_display = $(ob).val();
    swal({
        title: "Xác nhận đổi loại hiển thị",
        text: "Bạn có muốn đổi loại hiện thị cho nội dung này không?",
        type: "info", showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function (isConfirm) {
        if (isConfirm) {
            setTimeout(function () {
                $.ajax({
                    method: "POST",
                    url: "/manageapp/content/change-type-display",
                    data: {id: id, type_display: type_display}
                }).done(function (response) {
                    if (response.status == 1) {
                        $(ob).attr('_type', type_display);
                        swal({
                            title: "Change success!",
                            html: true});
                    } else {
                        $(ob).val(prev_type);
                        swal({
                            title: "Change false!",
                            html: true});
                    }
                });
            }, 500);    
        } else {
            $(ob).val(prev_type);
        }
    });
}

// Ham thay doi gia tri cac truong cua noi dung
function changeValue(ob, status, type) {
    var id = $(ob).attr('_id');
    var title = '', text = '';
    switch (type) {
        case 1: 
            title = 'Xác nhận đổi trạng thái';
            text = 'Bạn có muốn đổi trạng thái nội dung này không?';
            break;
        case 2: 
            title = 'Xác nhận đổi giá trị hiển thị quảng cáo';
            text = 'Bạn có muốn đổi giá trị hiển thị quảng cáo của nội dung này không?';
            break;
        case 3: 
            title = 'Xác nhận đổi giá trị chỉ hiển thị chi tiết';
            text = 'Bạn có muốn đổi giá trị chỉ hiển thị chi tiết của nội dung này không?';
            break;
         case 4: 
            title = 'Xác nhận đổi trạng thái truyện';
            text = 'Bạn có muốn đổi trạng thái truyện này không?';
            break;
            
        default: break;
    }
    
    if (title && text) {
        swal({
            title: title,
            text: text,
            type: "info", showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        }, function () {
            setTimeout(function () {
                $.ajax({
                    method: "POST",
                    url: "/manageapp/story/change-value",
                    data: {id: id, status: status, type: type}
                }).done(function (response) {
                    if (response.status == 1) {                        
                        switch (type) {
                            case 1: 
                                var status_html = 'Status: ';
                                if (status == STATUS_ON) {
                                    status_html += "<span class='btn btn-success' _id='" + id + "' onclick='changeValue(this, " + STATUS_OFF + ", 1)'>ON</span>";
                                } else if (status == STATUS_OFF) {
                                    status_html += "<span class='btn btn-warning' _id='" + id + "' onclick='changeValue(this, " + STATUS_ON + ", 1)'>OFF</span>";
                                } else if (status == STATUS_VIDEO_ERROR) {
                                    status_html += "<span class='btn btn-danger' _id='" + id + "' onclick='changeValue(this, " + STATUS_ON + ", 1)'>ERROR</span>";
                                }
                                break;
                            case 2: 
                                var status_html = 'Ads: ';
                                if (status == 1) {
                                    status_html += "<span class='btn btn-success' _id='" + id + "' onclick='changeValue(this, 0, 2)'>Có</span>";
                                } else if (status == 0) {
                                    status_html += "<span class='btn btn-warning' _id='" + id + "' onclick='changeValue(this, 1, 2)'>Không</span>";
                                }
                                break;
                            case 3: 
                                var status_html = 'Only detail: ';
                                if (status == 1) {
                                    status_html += "<span class='btn btn-success' _id='" + id + "' onclick='changeValue(this, 0, 3)'>Có</span>";
                                } else if (status == 0) {
                                    status_html += "<span class='btn btn-warning' _id='" + id + "' onclick='changeValue(this, 1, 3)'>Không</span>";
                                }
                            case 4: 
                                var status_html = 'Completed: ';
                                if (status == 1) {
                                    status_html += "<span class='btn btn-success' _id='" + id + "' onclick='changeValue(this, 0, 4)'>TRUE</span>";
                                } else {
                                    status_html += "<span class='btn btn-warning' _id='" + id + "' onclick='changeValue(this, 1, 4)'>FALSE</span>";
                                }
                                break;
                            default: break;
                        }
                        
                        $(ob).parent().html(status_html);
                        
                        swal({
                            title: "Change success!",
                            html: true});
                    } else {
                        swal({
                            title: "Change false!",
                            html: true});
                    }
                });
            }, 500);
        });
    } else {
        alert('Thiếu thông tin');
    }
}

// Ham thay doi trang thai cua tag
function changeTagStatus(ob, status) {
    var id = $(ob).attr('_id');
    title = 'Xác nhận đổi trạng thái';
    text = 'Bạn có muốn đổi trạng thái tag này không?';    
    
    swal({
        title: title,
        text: text,
        type: "info", showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        setTimeout(function () {
            $.ajax({
                method: "POST",
                url: "/manageapp/tag/change-status",
                data: {id: id, status: status}
            }).done(function (response) {
                if (response.status == 1) {
                    if (status == STATUS_ON) {
                        status_html = "<span class='btn btn-success' _id='" + id + "' onclick='changeTagStatus(this, " + STATUS_OFF + ")'>ON</span>";
                    } else if (status == STATUS_OFF) {
                        status_html = "<span class='btn btn-warning' _id='" + id + "' onclick='changeTagStatus(this, " + STATUS_ON + ")'>OFF</span>";
                    }
                    
                    $(ob).parent().html(status_html);
                    
                    swal({
                        title: "Change success!",
                        html: true});
                } else {
                    swal({
                        title: "Change false!",
                        html: true});
                }
            });
        }, 500);
    });
}
// Ham thay doi trang thai cua tag
function changeStoryContentStatus(ob, status) {
    var id = $(ob).attr('_id');
    title = 'Xác nhận đổi trạng thái';
    text = 'Bạn có muốn đổi trạng thái chương này không?';    
    
    swal({
        title: title,
        text: text,
        type: "info", showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        setTimeout(function () {
            $.ajax({
                method: "POST",
                url: "/manageapp/story-content/change-status",
                data: {id: id, status: status}
            }).done(function (response) {
                if (response.status == 1) {
                    if (status == STATUS_ON) {
                        status_html = "<span class='btn btn-success' _id='" + id + "' onclick='changeStoryContentStatus(this, " + STATUS_OFF + ")'>ON</span>";
                    } else if (status == STATUS_OFF) {
                        status_html = "<span class='btn btn-warning' _id='" + id + "' onclick='changeStoryContentStatus(this, " + STATUS_ON + ")'>OFF</span>";
                    }
                    
                    $(ob).parent().html(status_html);
                    
                    swal({
                        title: "Change success!",
                        html: true});
                } else {
                    swal({
                        title: "Change false!",
                        html: true});
                }
            });
        }, 500);
    });
}
// Ham lay thong tin noi dung bai post
function getPostInfo(x) {
    $.ajax({
        method: "POST",
        url: "/manageapp/content/get-post-info",
        beforeSend: function () {
            swal({   
                    title: "Please wait!",   
                    type: "info",
                    imageUrl: "/assets/managerapp/img/icons/loading.gif",   
                    showCancelButton: false,    
                    showConfirmButton: false, 
            });
        },
        data: {id: x}
    }).done(function (response) {
        if (response.status == 1) {
            var text = "<label>Title:</label><textarea cols='100' rows='1' class='post_field' id='post_title' autofocus>" + response.data.title + "</textarea>";
            text += "<label>Des:</label><textarea cols='100' rows='2' class='post_field' id='post_des' autofocus>" + response.data.des + "</textarea>";
            text += "<label>Content:</label><textarea cols='100' rows='15' class='post_field' id='post_content' autofocus>" + response.data.content + "</textarea>";
            swal({
                title: "Thông tin tạo audio !",
                text: text,   
                html: true,
                showCancelButton: true,
                customClass: 'form_swal',
                closeOnConfirm: false
            }, function () {
                // Kiem tra bien
                var title = $('#post_title').val();
                var des = $('#post_des').val();
                var content = $('#post_content').val();
                if (title == '') {     
                    swal.showInputError("Không được để tiêu đề bài trống");     
                    return false   
                }
                if (des == '') {     
                    swal.showInputError("Không được để mô tả bài trống");     
                    return false   
                }
                if (content == '') {     
                    swal.showInputError("Không được để nội dung trống");     
                    return false   
                }
                data = {
                    'id'        : x,
                    'title'     : title,
                    'des'       : des,
                    'content'   : content
                }
                createAudio(data);
            });
        } else {
            swal("Error!", "Can't get info of post", "error");
        }
    });
}

// Ham tao voice
function createAudio(data) {
    swal({
        title: "Tạo audio !",
        text: "Bạn có chắc muốn tạo audio cho bài post này",
        type: "info", 
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        setTimeout(function () {
            $.ajax({
                method: "POST",
                url: "/manageapp/content/create-audio",
                data: data
            }).done(function (response) {
                if (response.status == 1) { // Da tong hop xong
                    action_html = "Đang chờ tổng hợp";
                    $('#row_' + data.id).find('.audio-wrapper').html(action_html);
                    swal({
                        title: response.message,
                        html: true});
                } else {
                    swal({
                        title: "Tổng hợp thất bại!",
                        html: true});
                }
            });
        }, 500);
    });
}

// Ham tao voice cho quizgame
function createQgVoice(ob, id) {
    swal({
        title: "Tạo audio !",
        text: "Bạn có chắc muốn tạo audio cho nội dung này",
        type: "info", 
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        setTimeout(function () {
            $.ajax({
                method: "POST",
                url: "/manageapp/content/create-qg-voice",
                data: {id: id}
            }).done(function (response) {
                if (response.status == 1) { // Da tong hop xong
                    $(ob).remove();
                    $('#row_' + id).find('.span_status').parent().html('<span status_span>PENDDING</span>');
                    $('#row_' + id).find('.link_audio').remove();
                    swal({
                        title: response.message,
                        html: true});
                } else {
                    swal({
                        title: "Tổng hợp thất bại!",
                        html: true});
                }
            });
        }, 500);
    });
}
 
// Ham show loi
function showError(ob) {
    var error_info = JSON.parse($(ob).attr('_error_info'));
    swal("Error!", error_info, "error");
}

// Ham push notification
function push(post_id, type) {
    swal({
        title: "Do you want push !",
        text: "Bạn có muốn push!",
        type: "info", showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        setTimeout(function () {
            $.ajax({
                method: "POST",
                url: "/manageapp/push/set-push",
                data: {post_id: post_id}
            }).done(function (response) {
                var result = JSON.parse(response);
                // Xoa nut push
                $('#' + type + '_' + post_id).find('.btn-group').html('');
                if (result.status == 1) {
                    swal({
                        title: "Push successfully!",
                        html: true});
                } else {
                    swal({
                        title: result.message,
                        html: true});
                }
            });
        }, 1000);
    });
}
var requests = new Array();
var flag;
function crawler(ob){
            var id = $(ob).attr('_id');
            for(i=0;i<10;i++){
            flag = i;
            requests.push(
                    $.ajax({
                        method: "POST",
                        url: '/manageapp/crawler/get-story-ajax', 
                        data: { site_id: 'id' }, 
                    }).done(function (response) {
                        var result = JSON.parse(response);

                        if (result.status == 1) {
                            console.log(result.data.current + '/' + result.total);
                        } else {
                            console.log(result.message);
                        }
                    })
            );
                
            
        }
}


function stopCrawler(){
    requests[flag].abort();
    requests = null;
}
// Ham push tung bai post
function pushArtical(post_id) {
    swal({
        title: "Do you want push !",
        text: "Bạn có muốn push!",
        type: "info", showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        setTimeout(function () {
            $.ajax({
                method: "POST",
                url: "/manageapp/push/set-push",
                data: {post_id: post_id}
            }).done(function (response) {
                var result = JSON.parse(response);
                // Xoa nut push
                $('#row_'+post_id).find('.push-wrappper').html('');
                if (result.status == 1) {
                    swal({
                        title: "Push successfully!",
                        html: true});
                } else {
                    swal({
                        title: result.message,
                        html: true});
                }
            });
        }, 1000);
    });
}

// Ham kiem tra form
function validateForm()
{
    // Kiem tra thoi gian bat dau
    var k = document.forms["myForm"]["start_on"].value;
    if (k == "on") {
        var k1 = document.forms["myForm"]["stime_m"].value;
        var k2 = document.forms["myForm"]["stime_h"].value;
        var k3 = document.forms["myForm"]["stime_d"].value;
        if (k1 == null || k1 == "" || k2 == null || k2 == "" || k3 == null || k3 == "" || k3 == 0)
        {
            alert("Thiếu tham số START TIME!");
            return false;
        }
        
        if (k1 > 59)
        {
            alert("Phút phải nhỏ hơn 60");
            return false;
        }
        
        if (k2 > 23)
        {
            alert("Giờ phải nhỏ hơn 24");
            return false;
        }

    }
    // Kiem tra thoi gian chay thuong xuyen
    var k = document.forms["myForm"]["repeat"].value;
    if (k == 2) {
        var k1 = document.forms["myForm"]["recur_time"].value;
        if (k1 == null || k1 == "" || k1 == 0)
        {
            alert("Thiếu tham số RECUR EVERY!");
            return false;
        }

    }
    // Kiem tra thoi gian ket thuc
    var k = document.forms["myForm"]["end_on"].value;
    if (k == "on") {
        var k1 = document.forms["myForm"]["etime_m"].value;
        var k2 = document.forms["myForm"]["etime_h"].value;
        var k3 = document.forms["myForm"]["etime_d"].value;
        if (k1 == null || k1 == "" || k1 == 0 || k2 == null || k2 == "" || k2 == 0 || k3 == null || k3 == "" || k3 == 0)
        {
            alert("Thiếu tham số END TIME!");
            return false;
        }

    }
}

// Ham hien loading
function showLoading(){
	jQuery('#loading').removeClass('hidden');  
}

// Ham an loading
function hideLoading(){
	jQuery('#loading').addClass('hidden');  
}

function submitForm(object) {
    $(object).submit();
}