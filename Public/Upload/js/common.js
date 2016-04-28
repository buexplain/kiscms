$(function(){
    laydatebox();
});
//日期
function laydatebox(params) {
    if(typeof params == 'undefined') params = {istime: true, format: 'YYYY-MM-DD hh:mm:ss'};
    params.istime = true;
    $(".laydatebox").each(function(o){
        $(this).unbind('click');
        $(this).on('click',function(){
            laydate(params);
        });
    });
}
//选择每页条数
function pageSize(num) {
    cookie.set('pageSize',num,24*30,'/');
    window.location.href = window.location.href;
}