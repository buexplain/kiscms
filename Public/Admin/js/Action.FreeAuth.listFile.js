//将url插入到ckeditor
function returnFile(callBackId,fileName,msg) {
    if(fileName) {
        window.top.opener.CKEDITOR.tools.callFunction(callBackId,fileName,'');
        window.close();
    }else{
        window.top.opener.CKEDITOR.tools.callFunction(callBackId,'',msg);
    }
}
//显示图片
function showImg(img) {
    layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        area: '80%;text-align:center;margin-top:3%',
        skin: 'layui-layer-nobg',
        shade: [0.8, '#000'],
        shadeClose: true,
        content: '<img src="'+img+'">'
    });
}