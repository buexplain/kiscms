/**
 * cookie读写
 */
var cookie = {
    set: function (name,value,hours,path) {
        if(!arguments[2]) hours=1;
        if(!arguments[3]) path='/';
        var exp = new Date();exp.setTime(exp.getTime() + hours*60*60*1000);
        document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString()+";path="+path;
    },
    get: function (name) {
        var cookie_start = document.cookie.indexOf(name);
        var cookie_end = document.cookie.indexOf(";", cookie_start);
        return cookie_start == -1 ? '' : unescape(document.cookie.substring(cookie_start + name.length + 1, (cookie_end > cookie_start ? cookie_end : document.cookie.length)));
    }
};
/**
 * 从一个二维数组中提取出一键值对的数据来。
 * 以对象格式返回
 */
function pick_key_value(data,key,value,is_one,delimiter) {
    if(typeof is_one == undefined) is_one = 1;
    var delimiter = delimiter || ',';
    var result = {key:"",value:""};
    for(var i=0,len=data.length;i<len;i++) {
        if(result.key == '') {
            result.key = data[i][key];
        }else{
            result.key += delimiter + data[i][key];
        }
        if(result.value == '') {
            result.value = data[i][value];
        }else{
            result.value += delimiter + data[i][value];
        }
        if(is_one) break;
    }
    return result;
}
/**
 * 获取选中 checkbox 的属性
 */
function get_checked_attr(all_attr,data_checkbox_target) {
    var all_attr = all_attr || 0;
    var target = data_checkbox_target || 'checkbox-one';
    var value = '';
    var array = new Array();
    $("."+target).each(function(){
        var obj = $(this);
        if(obj.attr('type') != 'checkbox') {
            obj = $(this).find("input[type='checkbox']");
        }
       // console.log(obj[0].attributes);
        if(obj.prop('checked')) {
            if(all_attr) {
                var tmp_array = new Array();
                var attributes = obj[0].attributes;
                for(var i=0;i<attributes.length;i++) {
                    tmp_array[attributes[i].nodeName] = attributes[i].nodeValue;
                }
                array.push(tmp_array);
                //console.log(tmp_array);
            }else{
                if(value == '') {
                    value += obj.val();
                }else{
                    value += ','+obj.val();
                }
            }
        }
    });
    if(all_attr) return array;
    return value;
}
/**
 * 光标位置
 */
var cursor = {
    /**
     * 设置 光标位置
     */
    set: function(o, pos) {
        if (o.setSelectionRange) {
            o.focus();
            o.setSelectionRange(pos, pos);
        } else if (o.createTextRange) {
            var range = o.createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    },
    /**
     * 获取 光标所在位置
     */
    get: function(o) {
        var CaretPos = 0; // IE Support
        if (document.selection) {
            o.focus();
            var Sel = document.selection.createRange();
            Sel.moveStart('character', -o.value.length);
            CaretPos = Sel.text.length;
        }else if (o.selectionStart || o.selectionStart == '0') { // Firefox support
            CaretPos = o.selectionStart;
        }
        return CaretPos;
    }
};