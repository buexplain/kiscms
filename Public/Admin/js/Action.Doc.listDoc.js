var setting = {
    check: {
        enable: true,
        chkStyle: "radio",
        radioType: "all"
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
        onClick: onClick,
        onCheck: onCheck
    }
};
function onClick(e, treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj("cidTree");
    zTree.checkNode(treeNode, !treeNode.checked, null, true);
    if(treeNode.id == -1) {
        var cidObj = $("#cid_txt");
        cidObj.parent().find('input[type="hidden"]').val(0);
        cidObj.val('');
    }
    return false;
}

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
function showCid() {
    $("#cidContent").slideDown("fast");
    $("body").bind("mousedown", onBodyDown);
}
function hideCid() {
    $("#cidContent").fadeOut("fast");
    $("body").unbind("mousedown");
}
function onBodyDown(event) {
    if(event.target.id == 'cid_txt' || event.target.id == 'cidTree' || $(event.target).parents("#cidContent").length>0) return;
    hideCid();
    //console.log(event.target.id);
}