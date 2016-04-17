/**
 * 弹出角色列表后的匿名回调函数
 */
var set_role_id = function(data,obj) {
	var result = pick_key_value(data,'value','role_name',0);
	var obj_parent = $(obj).parent();
	obj_parent.find("input[type='text']").val(result.value);
	obj_parent.find("input[type='hidden']").val(result.key);
}