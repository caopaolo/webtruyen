var arrayInfo;
var home_url = 'http://webtruyen.vn/';
$(document).ready(function () {
    // sort table
    $("#sortable").sortable({
        create: function (event, ui) {
            arrayInfo = $(this).sortable('toArray');
            console.log(arrayInfo);
        },
        update: function (event, ui) {
            arrayInfo = $(this).sortable('toArray');
            console.log(arrayInfo);
        },
    });
    $("#sortable").disableSelection();
    
    $( "#extra_content" ).autoComplete({
        minChars: 3,
        source: function(term, response){
            try { xhr.abort(); } catch(e){}
            xhr = $.getJSON(
                '/manageapp/story/search', 
                { q: term }, 
                function(data){
                    response(data);
                }
            );
        },
        renderItem: function (item, search){
            // escape special characters
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
            var result = '<div class="autocomplete-suggestion" data-id="' + item.id + '" data-sort_focus="';
                result += item.sort_focus + '" data-avatar_small="' + item.avatar_small + '" data-story_name="' + item.story_name; 
                result += '">' + item.story_name.replace(re, "<b>$1</b>") + '</div>';
            return result;
        },
        onSelect: function(e, term, item){
            $( "#extra_content" ).val( item.data('story_name') );
            $( "#extra_content_data" ).attr( '_id', item.data('id') );
            $( "#extra_content_data" ).attr( '_sort_focus', item.data('sort_focus') );
            $( "#extra_content_data" ).attr( '_avatar_small', item.data('avatar_small') );
            $( "#extra_content_data" ).attr( '_story_name', item.data('story_name') );
        }
    });
});
// Ham thuc hien viec sap xep
function saveSort() {
    swal({
        title: "Xác nhận sắp xếp",
        text: "Bạn có chắc muốn sắp xếp theo thứ tự này không?",
        type: "info", showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        setTimeout(function () {
            $.ajax({                        
                url : '/manageapp/story/sort-content',
      	    	type: 'POST',
      	    	data: {'data': arrayInfo},
      	    	success:function(result){
      	    	    var response = JSON.parse(result);
                    if (response['status'] == 1) {
                        render(response['data']);
                        swal({
                            title: "Sắp xếp thành công!",
                            html: true
                        });
                    } else {
                        swal({
                            title: response['message'],
                            html: true
                        });
                    }
      	    	},
      	    	error : function (x, status, error){
                    if (x.status == 403) {
                        alert('Your session appears to have expired.  You will asked to log in again and returned here.');
                        window.location.reload();
                        return true;
                    } else {
                        alert(error);
                    }
      	    	}
            });
        }, 100);
    });
}

// Ham thuc hien viec reset thu tu
function resetSort() {
    swal({
        title: "Xác nhận reset",
        text: "Bạn có chắc muốn reset thứ tự về 0 hết?",
        type: "info", showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        setTimeout(function () {
            $.ajax({                        
                url : '/manageapp/story/reset-sort-story',
      	    	type: 'POST',
      	    	data: {},
      	    	success:function(result){
      	    	    var response = JSON.parse(result);
                    if (response['status'] == 1) {
                        render(response['data']);
                        swal({
                            title: "Reset thành công!",
                            html: true
                        });
                    } else {
                        swal({
                            title: response['message'],
                            html: true
                        });
                    }
      	    	},
      	    	error : function (x, status, error){
                    if (x.status == 403) {
                        alert('Your session appears to have expired.  You will asked to log in again and returned here.');
                        window.location.reload();
                        return true;
                    } else {
                        alert(error);
                    }
      	    	}
            });    
        }, 100);
    });
}

// Ham thuc hien viec them noi dung cho sap xep
function addContent(object_id, wrapper_id) {
    var id = $('#' + object_id).attr('_id');
    var sort_focus = $('#' + object_id).attr('_sort_focus');
    var avatar_small = $('#' + object_id).attr('_avatar_small');
    var story_name = $('#' + object_id).attr('_story_name');
    
    var check = 0; // Bien danh dau su ton tai cua noi dung
    $('#' + wrapper_id).children().each(function(){
        var el_id = $(this).attr('id');
        if (el_id.replace('item-', '') == id) {
            $('html, body').animate({
                scrollTop: $(this).offset().top - 60
            }, 500);
            $(this).attr('style', 'opacity: 1; background-color: #ffa; -webkit-transition: all 0.5s linear; transition: all 0.5s linear;');
            setTimeout(function(){
                $('#'+ el_id).attr('style', '');        
            }, 2000);    
            check = 1;
        }
    });
    
    if (check == 0) {
        swal({
            title: "Xác nhận thêm",
            text: "Bạn có chắc muốn thêm nội dung này không?",
            type: "info", showCancelButton: true,
            closeOnConfirm: false,
        }, function () {
            var html = '<li class="sort-wrapper" id="item-' + id + '">\
                            <span class="close" onclick="removeContent(this, \'sortable\')"><i class="icon_close"></i></span>\
                            <div class="sort-focus">' + sort_focus + '</div>\
                            <div class="sort-avatar">\
                                <img src="'+home_url + avatar_small + '" />\
                            </div>\
                            <div class="sort-story_name">' + story_name + '</div>\
                        </li>';  

            // Them noi dung                     
            $("#" + wrapper_id).prepend(html);
            
            // sort table
            arrayInfo = $('#' + wrapper_id).sortable('toArray');
            
            swal({
                title: "Thêm thành công!",
                html: true
            });
        });    
    }
}

// Ham thuc hien viec xoa noi dung cho sap xep
function removeContent(object, wrapper_id) {    
    swal({
        title: "Xác nhận xóa",
        text: "Bạn có chắc muốn xóa nội dung này không?",
        type: "info", showCancelButton: true,
        closeOnConfirm: false,
    }, function () {
        // Xoa noi dung
        $(object).parent().remove();
        
        // sort table
        arrayInfo = $('#' + wrapper_id).sortable('toArray');
        
        swal({
            title: "Xóa thành công!",
            html: true
        });
    }); 
}

// Ham thuc hien viec render lai
function render(data) {
    if (typeof data == 'object' && data.length > 0) {
        $('#sortable').html('');
        for (var i=0; i<data.length; i++) {
            if (data[i].avatar_small != null) {
                var avatar_small = data[i].avatar_small; 
            } else {
                var avatar_small = '';
            }
            var html = '<li class="sort-wrapper" id="item-' + data[i].id + '">\
                            <span class="close" onclick="removeContent(this, \'sortable\')"><i class="icon_close"></i></span>\
                            <div class="sort-focus">' + data[i].sort_focus + '</div>\
                            <div class="sort-avatar">\
                                <img src="'+ home_url+ avatar_small + '" />\
                            </div>\
                            <div class="sort-story_name">' + data[i].story_name + '</div>\
                        </li>'; 
            $('#sortable').append(html);
        }    
    } else {
        alert('Không có dữ liệu để render');
    }
}