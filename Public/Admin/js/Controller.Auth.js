$(function(){
    /**
     * 初始化树形表格
     */
    $("#nodetree").treetable({expandable: true});

    /**
     * 初始化展开节点
     */
    $('#nodetree').treetable('expandAll');
});

/**
 * 关闭展开全部节点
 */
function collapseExpandAll(obj) {
    var o = $(obj);
    if(parseInt(o.attr('lock'))) {
        o.attr('lock',0);
        o.html('关闭节点');
        $('#nodetree').treetable('expandAll');
    }else{
        o.attr('lock',1);
        o.html('展开节点');
        $('#nodetree').treetable('collapseAll');
    }
    //console.log(o);
}

/**
 * 删除节点成功后的回调
 */
function delNodeSuccess(json,buttonO) {
    if(json.code == 0) {
        layer.msg(json.msg, {
            icon: 1,
            time: 700
        },function(){
            buttonO.parent().parent().remove();
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
