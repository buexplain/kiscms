function onClick_sidebartree(e,treeId, treeNode) {
	var zTree = $.fn.zTree.getZTreeObj("sidebartree");
	zTree.expandNode(treeNode);
}
var setting = {
	view: {
		dblClickExpand: false,
	},
	data: {
		simpleData: {
			enable: true
		}
	},
	callback: {
		onClick: onClick_sidebartree
	}
};