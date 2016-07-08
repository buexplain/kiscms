$(function(){
	init_if_wh();
	fixed_table_header();
    laydatebox();
    openiframe();
    checkbox_all();
    checkbox_one();
    betterTD();
    bindRequire();
});

/**
 * 绑定请求
 */
function bindRequire() {
    requireDispatch.batch.setAutoSubmit('',{'data-tips':'batchTips','data-ajaxSuccess':'batchSuccess'});
    requireDispatch.form.setAutoSubmit('',{'data-ajaxSuccess':'formSuccess'});
}

/**
 * 表单提交回调
 */
function formSuccess(result,formO,buttonO) {
    if(result.code == 0) {
        layer.msg(result.msg, {
            icon: 1,
            time: 700
        },function(){
            if(result.data) window.location.href = result.data;
        });
    }else{
        layer.msg(result.msg, {
            icon: 2,
            time: 1300
        });
    }
}

/**
 * 批量操作，或单个操作，回调
 */
function batchSuccess(json,buttonO) {
    if(json.code == 0) {
        layer.msg(json.msg, {
            icon: 1,
            time: 700
        },function(){
            json.data = json.data || window.location.href;
            if(json.data) window.location.href = json.data;
        });
    }else{
        json.msg = '<span class="error">'+json.msg+'</span>';
        layer.msg(json.msg, {
            icon: 2,
            time: 700
        });
        if(buttonO.attr("data-tipsBak")) buttonO.attr("data-tips",buttonO.attr("data-tipsBak"));
    }
}

/**
 * 批量操作，或单个操作，提示
 */
function batchTips(buttonO) {
    var msg = buttonO.attr("data-msg");
    if(!msg) msg = '您确定要执行吗？';
    var yesbtn = buttonO.attr("data-yesbtn");
    if(!yesbtn) yesbtn = '确定';
    var nobtn = buttonO.attr("data-nobtn");
    if(!nobtn) nobtn = '取消';

    buttonO.attr("data-tipsBak",buttonO.attr("data-tips"));

    layer.confirm(msg, {
        offset: '5px',
        shade: 0,
        btn: [yesbtn,nobtn]
    }, function(index){
        buttonO.attr('data-tips','');
        buttonO.click();
    },function(index){
    });

    return false;
}

/**
 * 列表超出高度显示优化
 */
function betterTD() {
    $('.betterTD').each(function(){
        var o = $(this);
        o.html('<div class="betterShow" title="点击显示隐藏">'+o.html()+'</div>');
    });
    $(".betterShow").on('click',function(){
        var o = $(this);
        var lock = o.attr('lock');
        if(!lock || lock == '0') {
            o.attr('lock',1);
            $(this).css('height','auto');
        }else{
            $(this).attr('style','');
            o.attr('lock',0);
        }
    });
}
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
function pageSize(obj) {
    var num = obj.options[obj.selectedIndex].value;
    cookie.set('pageSize',num,24*30,'/');
    window.location.href = window.location.href;
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
/**
 * js版U方法
 */
function U(controller,action,params) {
    if(!params) params = {};
    var url = '/index.php?'+moduleKey+'='+realModule+'&'+controllerKey+'='+controller+'&'+actionKey+'='+action;
    for(var i in params) {
        url += '&' + i+'='+params[i];
    }
    return url;
}