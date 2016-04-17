function refresh_verification(src){
    src = src + '?' + Math.random();
    $("#verification_img").attr('src',src).prev().val('');
}
/**
 * 登录回调函数
 */
require.callback_set = function(json) {
    if(json.code == 0) {
        layer.msg(json.msg, {
            icon: 1,
            time: 700,
            offset: '10px'
        },function(){
            window.location.href = json.data;
        });
    }else{
        layer.msg(json.msg, {
            icon: 2,
            time: 1000,
            offset: '10px'
        },function(){
            $("#verification_img").click();
        });
    }
}