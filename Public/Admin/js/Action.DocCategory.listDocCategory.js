var setting = {
    view: {
        addHoverDom: addHoverDom,
        removeHoverDom: removeHoverDom,
        selectedMulti: false,
        txtSelectedEnable: false
    },
    edit: {
        enable: true,
        editNameSelectAll: true,
        showRemoveBtn: showRemoveBtn,
        showRenameBtn: showRenameBtn
    },
    data: {
        simpleData: {
            enable: true
        }
    },
    callback: {
        beforeEditName: beforeEditName,
        beforeRemove: beforeRemove
    }
};
/**
 * 控制删除按钮的显示
 */
function showRemoveBtn(treeId, treeNode) {
	//console.log(treeNode);
	if(treeNode.isParent) return false //是父节点 不能显示删除
	return true;
}
/**
 * 控制编辑按钮的显示
 */
function showRenameBtn(treeId, treeNode) {
	return true;
}
/**
 * 编辑
 */
function beforeEditName(treeId, treeNode) {
    window.location.href = '/Admin/DocCategory/addDocCategory/cid/'+treeNode.id+'.html';
}
/**
 * 删除节点之前
 */
function beforeRemove(treeId, treeNode) {
    if(!confirm("确认删除 节点 -- " + treeNode.name + " 吗？")) return false;
    return delNode(treeNode.id);
}
/**
 * 删除节点
 */
function delNode(node_id) {
	var url = '/Admin/DocCategory/delDocCategory/';
	var data = {"cid":node_id};
	var s = false;
	$.ajax({
		type: "POST",
		url: url,
		data: data,
		async: false,
		success: function(json){
			if(json.code == 0) {
                s = true;
            }else{
                alert(json.msg);
                s = false;
            }
		}
	});
    return s;
}
/**
 * 添加新增按钮
 */
function addHoverDom(treeId, treeNode) {
    var sObj = $("#" + treeNode.tId + "_span");
    if(treeNode.depth == 5) return;
    if (treeNode.editNameFlag || $("#addBtn_" + treeNode.tId).length > 0) return;
    var addStr = "<span class='button add' id='addBtn_" + treeNode.tId + "' title='add node' onfocus='this.blur();'></span>";
    sObj.after(addStr);
    var btn = $("#addBtn_" + treeNode.tId);
    if (btn) btn.bind("click",function() {
        window.location.href = '/Admin/DocCategory/addDocCategory/pid/'+treeNode.id+'.html';
        return false;
    });
}
/**
 * 移除新增按钮
 */
function removeHoverDom(treeId, treeNode) {
    $("#addBtn_" + treeNode.tId).unbind().remove();
}