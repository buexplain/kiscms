/*添加扩展数据*/
function add_ext(obj) {
    var obj = $(obj);
    var value = obj.val();
    if(value == 0) return;
    var txt = obj.html();
    var id = 'ext_' + value;
    var formid = 'formid_' + value;
    if(document.getElementById(id) != null) return;
    var  nav = '';
    nav += '<li class="">';
    nav += '    <a href="#'+id+'">';
    nav += '        '+txt;
    nav += '        &nbsp;';
    nav += '        <button aria-label="Close" class="close" type="button" data-value="'+value+'" data-id="'+id+'" onclick="del_ext(this)">';
    nav += '            <span aria-hidden="true">×</span>';
    nav += '        </button>';
    nav += '    </a>';
    nav += '</li>';
    var panes = '';
    panes += '<div id="'+id+'" data-formid="'+formid+'" class="tab-pane">';
    var data = {};
    data.doc_ext_id = value;
    $.post(U('Doc','createExtForm'),data,function(json){
        if(json.code == 0) {
            panes += '<form id="'+formid+'">';
            panes += json.data;
            panes += '</form></div>';
            $("#myTab").append(nav);
            $("#myTabContent").append(panes);
            laydatebox();
            start_tab(id);
        }
    });
}
/*初始化标签切换*/
function start_tab(id) {
    var id = id || '';
    var obj = $('#myTab a');
    obj.unbind('click');
    obj.click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    if(id) {
        $('#myTab a[href="#'+id+'"]').tab('show');
    }else{
        $('#myTab li:eq(1) a').tab('show');
    }
}
/*删除扩展数据*/
function del_ext(obj) {
    var obj = $(obj);
    var id = obj.data('id');
    layer.confirm('您确定要执行吗？', {
        shade: 0, //遮罩透明度
        btn: ['确定','取消'] //按钮
    }, function(index){
        var data = {};
        data.doc_id = doc_id;
        data.doc_ext_id = obj.data('value');
        $.post(U('Doc','delDocExtData'),data,function(json){
            layer.close(index);
            if(json.code == 0) {
                obj.parent().parent().remove();
                $('#'+id).remove();
            }
        });
    });
}
/*自定义提交数据*/
function getFormData(formO) {
    var data = {};
    $('.tab-pane').each(function(i){
        var formid = $(this).data('formid');
        data[formid] = form.get('#'+formid);
        data[formid]['doc_id'] = doc_id;
    });
    //console.log(data);
    return data;
}
$(function(){
    start_tab();
});