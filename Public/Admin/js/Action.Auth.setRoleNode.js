var setting = {
	check: {
		enable: true,
		chkboxType: { "Y" : "p", "N" : "ps" }
	},
	data: {
		simpleData: {
			enable: true
		}
	},
	callback: {
		onCheck:onCheck
	}
};

/**
 * 获取选中的节点
 */
function onCheck(e,treeId,treeNode) {
	var treeObj = $.fn.zTree.getZTreeObj("nodetree");
    var nodes = treeObj.getCheckedNodes(true);
    var node_ids = "";
    for(var i=0;i<nodes.length;i++){
    	if(node_ids == '') {
    		node_ids += nodes[i].id;	
    	}else{
    		node_ids += "," + nodes[i].id;
    	}
    	
	}
	$("#node_id").attr('value',node_ids);
}