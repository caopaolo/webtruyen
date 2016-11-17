/*
 * jQuery File Upload Plugin JS Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* global $, window */
$(function () {
    'use strict';
    var content_id = $('#id').val(); // Bien global cho script up file
    
    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        acceptFileTypes: /(\.|\/)(mp4)$/i,
        url: '/manageapp/upload-video?content_id=' + content_id,
        maxNumberOfFiles: 1,
        maxChunkSize: 1000000,
        recalculateProgress: false,
        fileInput: $('.fileUpload-input'),
        add: function (e, data) {
            var that = this;
            $.getJSON('/manageapp/upload-video', {content_id: content_id, file: data.files[0].name}, function (result) {
                var file = result.file;
                data.uploadedBytes = file && file.size;
                if (file != null && file.size) {
                    if (data.uploadedBytes < data.files[0].size) {
                        $("#fileupload").find(".files").empty();    
                    }
                }
                $.blueimp.fileupload.prototype
                    .options.add.call(that, e, data);
            });
        },
        maxRetries: 5,
        retryTimeout: 500,
        fail: function (e, data) {
            // jQuery Widget Factory uses "namespace-widgetname" since version 1.10.0:
            var fu = $(this).data('blueimp-fileupload') || $(this).data('fileupload'),
                retries = data.context.data('retries') || 0,
                retry = function () {
                    $.getJSON('/manageapp/upload-video', {content_id: content_id, file: data.files[0].name})
                        .done(function (result) {
                            var file = result.file;
                            data.uploadedBytes = file && file.size;
                            // clear the previous data:
                            data.data = null;
                            data.submit();
                        })
                        .fail(function () {
                            fu._trigger('fail', e, data);
                        });
                };
            if (data.errorThrown !== 'abort' &&
                    data.uploadedBytes < data.files[0].size &&
                    retries < fu.options.maxRetries) {
                retries += 1;
                data.context.data('retries', retries);
                window.setTimeout(retry, retries * fu.options.retryTimeout);
                return;
            }
            
            // Xoa file neu nguoi dung cancle
            if ((data.errorThrown === 'abort') && ("files" in data)) {
                var file_delete = data.files[0].name;
                $.ajax({
                    url: '/manageapp/upload-video?content_id=' + content_id + '&file=' + file_delete,
                    dataType: 'json',
                    type: 'DELETE',
                    success: function (data) {
                        if (data[file_delete] == true) {
                            console.log('Delete successfully: ' + file_delete);    
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }
            
            data.context.removeData('retries');
            $.blueimp.fileupload.prototype
                .options.fail.call(this, e, data);
        },
        destroyed: function (e, data) {
            if ($('#temp_file').attr('_edit') == 1) {
                $('#temp_file').val('temp');    
            } else {
                $('#temp_file').val('');
            }
        }
    }).bind('fileuploaddone', function (e, data) {
        var files = data._response.result.files;
        if("url" in files[0]) {
            $('#temp_file').val(files[0].name + '|' + files[0].type);    
        }
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    // Load existing files:
    $('#fileupload').addClass('fileupload-processing');
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#fileupload')[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, $.Event('done'), {result: result});
        // Gan gia tri cho truong temp_file
        var files = result.files;
        if(files.length > 0) {
            if (files[0].complete == 1) {
                $('#temp_file').val(files[0].name + '|' + files[0].type);    
            }    
        }
    });
});
