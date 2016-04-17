$(function(){
	init_if_wh();
	fixed_table_header();
    deltips();
    set();
    laydatebox();
    openiframe();
    checkbox_all();
    checkbox_one();
});
/**
 * 初始化框架宽高
 */
function init_if_wh() {
	$("#ifman").css({'margin-top':$("#ifheader").height()});
}
/**
 * 自动生成固定的表头 
 */
function fixed_table_header() {
    $(".fixedboxtablehead").each(function(){
        var o = $(this);
        var tr = o.find('tr').eq(0);
        var col = '<colgroup>';
        var total_width = 0;
        tr.find('th').each(function(){
            total_width += $(this).width();
            col += '<col width="'+$(this).width()+'" />';
        });

        if($(window).width() < total_width) return;
        col += '</colgroup>';

        o.append(col);

        var table = '<table ';
        var nodeName = nodeValue = '';
        var i = 0;
        var attributes = o.context.attributes;
        for(; i<attributes.length; i++) {
            nodeName = attributes[i].nodeName;
            nodeValue = attributes[i].nodeValue;
            if(nodeName == 'class') {
                nodeValue = nodeValue.replace('fixedboxtablehead','boxtablehead');
            }
            table += ' ' + nodeName + '=' + '"' + nodeValue + '"';
        }
        table += '>' + col + tr.prop("outerHTML") + '</table>';
        o.before(table);
        o.prev('table').css({'width':o.width(),'margin':0}); //修正一下css console.log();
    });
}
/**
 * 选择每页条数
 */
function page_size(num) {
    cookie.set('page_size',num,24*30,'/');
    window.location.href = window.location.href;
}
/**
 * 删除前的提示
 */
function deltips() {
    $(".deltips").click(function(i) {
        require.del(this);
    });
}
/**
 * 添加编辑
 */
function set() {
    $("#set").find("button[type='submit']").attr('onclick',"require.set(this);return false;");
}
/**
 * 日期
 */
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
/**
 * 弹出iframe
 */
function openiframe() {
    $(".openiframe").each(function(i){
        var o = $(this);
        var title = o.attr('data-title');
        if(!title) title = '信息';
        var width = o.attr('data-width');
        if(!width) width = '700px';
        var height = o.attr('data-height');
        if(!height) height = '450px';
        var url = o.attr('data-url');
        window.openiframe = {};
        window.openiframe.obj = this;
        //设置匿名的回调函数
        window.openiframe.callback = o.attr('data-callback');
        if(window.openiframe.callback) {
            window.openiframe.callback = eval('window.'+window.openiframe.callback);
        }
        //设置是否获取全部属性
        window.openiframe.all_attr = o.attr('data-all-attr');
        if(!url) return;
        o.on('click',function(){
            layer.open({
                title: title,
                type: 2,
                area: [width, height],
                fix: false, //不固定
                maxmin: true,
                content: url
            });
        });
    });
}
/**
 * 回调弹出层
 */
function callback_openiframe(data) {
    if(typeof window.parent.openiframe.callback == 'function') {
        window.parent.openiframe.callback(data,window.parent.openiframe.obj);
    }
}
/**
 * checkbox 单选
 */
function checkbox_one() {
    $('.checkbox-one').click(function(event){
        var obj = $(this).find("input[type='checkbox']");
        obj.prop('checked',!obj.prop('checked'));
        event.preventDefault(); //阻止捕获
    });
    $('.checkbox-one').find("input[type='checkbox']").click(function(event){
        event.stopPropagation();  //阻止冒泡
    });
}
/**
 * checkbox 全选
 */
function checkbox_all() {
    $('.checkbox-all').click(function(){
        var target = $(this).attr('data-checkbox-target');
        target = target || 'checkbox-one';
        $('.'+target).find("input[type='checkbox']").prop('checked',$(this).prop('checked'));
        $('.'+target).prop('checked',$(this).prop('checked'));
    });
}
/**
 * 返回弹出层的选中项
 */
function return_pop_layer_checked(data_checkbox_target) {
    var target = data_checkbox_target || 'checkbox-one'; //input的父级class或其本身class
    var all_attr = window.parent.openiframe.all_attr || 0;
    var data = get_checked_attr(all_attr,target);
    callback_openiframe(data);
    var index = window.parent.layer.getFrameIndex(window.name); //获取窗口索引
    window.parent.layer.close(index);
}
