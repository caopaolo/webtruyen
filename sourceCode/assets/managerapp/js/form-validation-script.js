var check_unload = 1;
$.validator.setDefaults({
    submitHandler: function(form) {
        check_unload = 0;
        form.submit();
    }
});
var Script = function () {
    $().ready(function() {        
        // validate the videoupload form when it is submitted
        $(".form_video").validate({
            ignore: [],
            rules: {
                temp_file: {
                    required: function () {
                        CKEDITOR.instances['content'].updateElement();
                        // Neu la edit
                        if (typeof $('#resource_url').val() != 'undefined') {
                            if ($('#resource_url').val() != '') {
                                return false;
                            }      
                        }
                        if ($('#content').val() != '') {
                            return false;
                        }
                        return true;
                    }
                },
                content: {
                    required: function () {
                        // Neu la edit
                        if (typeof $('#resource_url').val() != 'undefined') {
                            if ($('#resource_url').val() != '') {
                                return false;
                            }      
                        }
                        
                        if ($('#temp_file').val() != '') {
                            return false;
                        }
                        return true;
                    }
                },
                resource_url: {
                    required: function () {
                        // Neu la edit
                        if (typeof $('#temp_file').val() != 'undefined') {
                            if ($('#temp_file').val() != '') {
                                return false;
                            }      
                        }
                        if ($('#content').val() != '') {
                            return false;
                        }
                        return true;
                    }
                },
            },
            messages: {                
                temp_file: {
                    required: "Bạn chưa up file nào lên"
                }
            },
            errorPlacement: function(error, element) 
            {
                if (element.attr("name") == "content") {
                    error.insertAfter("#cke_content");
                } else if (element.attr("name") == "tag") {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
        
        // validate the content form when it is submitted
        $(".form_content").validate({
            ignore: [],
            rules: {
                temp_file: {
                    required: true
                },
                content: {
                    required: {
                        depends: function() {
                            CKEDITOR.instances['content'].updateElement();
                            // Neu co up file thi ko can check content
                            if (typeof $('#temp_file').val() != 'undefined') {
                                return false;
                            }
                            return true;
                        }
                    },
                }
            },
            messages: {                
                temp_file: {
                    required: "Bạn chưa up file nào lên"
                }
            },
            errorPlacement: function(error, element) 
            {
                if (element.attr("name") == "content") {
                    error.insertAfter("#cke_content");
                } else if (element.attr("name") == "tag") {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
        
        // validate the form when it is submitted
        $(".form-validate").validate();
    });
}();
var myEvent = window.attachEvent || window.addEventListener;
var chkevent = window.attachEvent ? 'onbeforeunload' : 'beforeunload'; /// make IE7, IE8 compatable
myEvent(chkevent, function(e) { // For >=IE7, Chrome, Firefox
    if (check_unload == 1) {
        var confirmationMessage = 'Bạn có chắc muốn rời khỏi trang này không?';
        (e || window.event).returnValue = confirmationMessage;
        return confirmationMessage;    
    }
});