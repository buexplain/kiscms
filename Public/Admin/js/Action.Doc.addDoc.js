var setting = {
    check: {
        enable: true,
        chkboxType:  { "Y" : "p", "N" : "ps" }
    },
    view: {
        dblClickExpand: false
    },
    data: {
        simpleData: {
            enable: true
        }
    },
    callback: {
        beforeClick: beforeClick,
        onCheck: onCheck
    }
};
/**
 * 点击文字
 */
function beforeClick(treeId, treeNode) {
    return false;
    var zTree = $.fn.zTree.getZTreeObj("cidTree");
    zTree.checkNode(treeNode, !treeNode.checked, null, true);
    return false;
}
/**
 * 选择分类
 */
function onCheck(e, treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj("cidTree"),
    nodes = zTree.getCheckedNodes(true),
    cid_txt = "",
    cid = "";
    for (var i=0, l=nodes.length; i<l; i++) {
        cid_txt += nodes[i].name + ",";
        cid += nodes[i].id + ",";
    }
    if (cid_txt.length > 0 ) cid_txt = cid_txt.substring(0, cid_txt.length-1);
    if (cid.length > 0 ) cid = cid.substring(0, cid.length-1);
    var cidObj = $("#cid_txt");
    cidObj.parent().find('input[type="hidden"]').val(cid);
    cidObj.val(cid_txt);
}
/**
 * 显示分类
 */
function showCid() {
    $("#cidContent").slideDown("fast");
    $("body").bind("mousedown", onBodyDown);
}
/**
 * 隐藏分类
 */
function hideCid() {
    $("#cidContent").fadeOut("fast");
    $("body").unbind("mousedown");
}
/**
 * 鼠标点击空白处关闭下拉
 */
function onBodyDown(event) {
    //console.log(event.target.id);
    if(event.target.id == 'cid_txt' || event.target.id == 'cidTree' || $(event.target).parents("#cidContent").length>0) return;
    hideCid();
}