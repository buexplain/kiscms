/**
 * 关闭全部
 */
function collapseAll(id) {
    $('#'+id).treetable('collapseAll');
}

/**
 * 展开全部
 */
function expandAll(id) {
    $('#'+id).treetable('expandAll');
}

/**
 * 初始化树形表格
 */
$("#nodetree").treetable({expandable: true});
$(function(){
    expandAll('nodetree');
});