<html>

    <head>
        <meta charset="UTF-8">
        <title>
            上传附件
        </title>
        <script src="/jquery.js"></script>
        <script src="/plupload-2.1.8/js/plupload.full.min.js"></script>
        <script>
            jQuery.extend({
               json_decode: function(strJson) {
                 return eval("(" + strJson + ")");
               },
               json_encode: function(object) {
                    var type = typeof object;
                    if ('object' == type) {
                    if (Array == object.constructor) type = 'array';
                    else if (RegExp == object.constructor) type = 'regexp';
                        else type = 'object';
                    }
                    switch (type) {
                        case 'undefined':
                        case 'unknown':
                            return;
                            break;
                        case 'function':
                        case 'boolean':
                        case 'regexp':
                            return object.toString();
                            break;
                        case 'number':
                            return isFinite(object) ? object.toString() : 'null';
                            break;
                        case 'string':
                            return '"' + object.replace(/(\\|\")/g, "\\$1").replace(/\n|\r|\t/g, function() {
                                var a = arguments[0];
                                return (a == '\n') ? '\\n': (a == '\r') ? '\\r': (a == '\t') ? '\\t': ""
                            }) + '"';
                            break;
                        case 'object':
                            if (object === null) return 'null';
                            var results = [];
                            for (var property in object) {
                                var value = jQuery.toJSON(object[property]);
                                if (value !== undefined) results.push(jQuery.toJSON(property) + ':' + value);
                            }
                            return '{' + results.join(',') + '}';
                            break;
                    case 'array':
                        var results = [];
                        for (var i = 0; i < object.length; i++) {
                            var value = jQuery.toJSON(object[i]);
                            if (value !== undefined) results.push(value);
                        }
                        return '[' + results.join(',') + ']';
                        break;
                    }
               }
            });
        </script>
        <style>
            body,p{
                padding: 0;
                margin: 0;
                border: 0;
                font-size: 12px;
            }
            a{text-decoration:none;}

            .info{width: 100%;height: 230px;overflow: auto;}
            table.gridtable {
                font-family:verdana,arial,sans-serif;
                font-size:12px;
                color:#333333;
                border-collapse:collapse;
                width: 100%;
            }
            table.gridtable tr{
                border-bottom: 1px solid #ccc;
            }
            table.gridtable td,table.gridtable th {
                padding:8px;
            }

            .success{
                color: rgb(20, 111, 56);
            }
            .error{
                color: red;
            }
        </style>
    </head>

    <body>
        <input type="button" id="browse" value="选择文件">
        <input type="button" id="upload-btn" value="开始上传">
        <div class="info">
            <table class="gridtable" id="file-list">
            </table>
        </div>
        <script>
            //实例化一个plupload上传对象
            var uploader = new plupload.Uploader({
                browse_button: 'browse',
                url: '/php/upload.php',
                flash_swf_url: '/plupload-2.1.8/js/Moxie.swf',
                silverlight_xap_url: '/plupload-2.1.8/js/Moxie.xap',
                filters: {
                  prevent_duplicates : true //不允许队列中存在重复文件
                }
            });
            //初始化
            uploader.init();
            //选择文件的时候触发的事件
            uploader.bind('FilesAdded',function(up, files) {
                var o = $('#file-list');
                for (var i = 0,len = files.length; i<len; i++) {
                    var file = files[i];
                    var html = '';
                    html += '<tr id="'+file.id+'">';
                    html += '    <td>'+file.name+'</td>';
                    html += '    <td>'+plupload.formatSize(file.size)+'</td>';
                    html += '    <td class="success"></td>';
                    html += '    <td><a href="javascript:;" onclick="removeFile(\''+file.id+'\')">移除</a></td>';
                    html += '</tr>';
                    o.append(html);
                }
                //console.log(up);
            });
            //上传过程中触发的事件
            uploader.bind('UploadProgress',function(up, file) {
                $("#"+file.id).find('td').eq(2).html(file.percent+'%').removeClass('error'); //显示进度
            });
            //单个文件上传完毕后触发的事件
            uploader.bind('FileUploaded',function(up,file,responseObject){
                //console.log(responseObject);
                var tmp = $.json_decode(responseObject.response);
                if(tmp.code == 0) {
                    $("#"+file.id).find('td').eq(3).html('');
                    top.PluploadData.push(tmp.data);
                }else{
                    showErr(file.id,tmp.msg);
                }
            });
            //错误提示
            uploader.bind('Error',function(up, error) {
                var file = error.file;
                var html = 'code：'+error.code+'<br>HTTPStatus：'+error.status+'<br>message：'+error.message;
                showErr(file.id,html);
            });
            //移除文件后触发的事件
            uploader.bind('FilesRemoved',function(up,files){
                var file = files[0];
                $("#"+file.id).remove();
            });
            //上传按钮
            $('#upload-btn').click(function() {
                uploader.start(); //开始上传
            });
            //移除上传队列中的文件
            function removeFile(id) {
                uploader.removeFile(id);
            }
            //清空上传列表
            function clearFileList() {
                $("#file-list").find('tr').each(function(){
                    removeFile($(this).attr('id'));
                });
            }
            //显示错误
            function showErr(id,html) {
                $("#"+id).find('td').eq(2).html(html).addClass('error');
            }
        </script>
    </body>

</html>