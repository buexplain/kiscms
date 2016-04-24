(function() {
    CKEDITOR.dialog.add("upload",
    function(a) {
        //console.log(a.config);
        return {
            title: "上传附件", //调用dialog时显示的名称
            minWidth: "800px",
            minHeight:"600px",
            contents: [{
                id: "tab1",
                label: "",
                title: "",
                expand: true,
                width: "640px",
                height: "360px",
                padding: 0,
                elements: [{
                    type: "html",
                    style: "height: 260px;width: 540px;",
                    html:'<iframe name="ckIframePlupload" width="100%" scrolling="no" height="100%" frameborder="no" allowtransparency="yes" marginheight="0" 0″="" border="0″ marginwidth=" src="'+a.config.fileFormUrl+'"></iframe>'
                }]
            }],
            onShow: function() {
                var iframe = document.getElementsByName('ckIframePlupload');
                console.log();
                if(iframe[0].contentWindow.clearFileList) {
                    iframe[0].contentWindow.clearFileList(); //清空上传页面中的列表
                }
                uploadData = []; //清空上传结果数组
            },
            onOk: function() {
                //console.log(uploadData);
                var html = '';
                for(var i=0,l=uploadData.length;i<l;i++) {
                    var ext = uploadData[i].ext;
                    html += '<p>';
                    if(ext=='png'||ext=='jpg'||ext=='jpeg'||ext=='gif'||ext=='bmp'||ext=='ico') {
                        html += '<img alt="'+uploadData[i].oname+'" src="'+uploadData[i].url+'">';
                    }else if(ext=='swf'||ext=='flv') {
                        html += '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"><param name="allowFullScreen" value="true" /><param name="quality" value="high" /><param name="movie" value="'+uploadData[i].url+'" /><embed allowfullscreen="true" pluginspage="http://www.macromedia.com/go/getflashplayer" quality="high" src="'+uploadData[i].url+'" type="application/x-shockwave-flash"></embed></object>';
                    }else{
                        html += '<a href="'+uploadData[i].url+'" target="_blank" title="'+uploadData[i].oname+'">'+uploadData[i].oname+'</a>';
                    }
                    html += '</p>';
                }
                uploadData = [];
                if(html!='') a.insertHtml(html);
            }
        }
    })
})();
//上传结果数组，文件上传结束后会压入数据
var uploadData = [];
//上传成功后的回调函数
var callBackUpload = function(data) {
    uploadData.push(data);
}