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
 * 控制重复提交 如果锁定了则返回 true
 * 注意 href="javascript:xxx(this);" 此类写法是 拿不到 this 的
 */
var submit = {
    obj: '',
    classname: 'deny',
    deny: function(obj) {
        this.obj = $(obj);
        if(this.obj.hasClass(this.classname)) {
            return true;
        }
        this.obj.addClass(this.classname);
        this.obj.attr('disabled',true);
        return false;
    },
    allow: function() {
        this.obj.attr('disabled',false);
        this.obj.removeClass(this.classname);
    }
};
/**
 * 等待层
 */
var load = {
    index: -1,
    time:1500,
    wait: function(){
        setTimeout(function(){
            if(load.index == -1) {
                //load.index = layer.load(3,{time:'',offset:'10px'});
            }
        },load.time);
    },
    close: function() {
        if(load.index > -1) {
            layer.close(load.index);
            load.index = -1;
        }else{
            setTimeout(function(){
                layer.close(load.index);
                load.index = -1;
            },load.time+300);
        }
    }
};
/**
 * 规范化的请求
 */
var require = {
    callback_set: function(json) {
        if(json.code == 0) {
            layer.msg(json.msg, {
                icon: 1,
                time: 700
            },function(){
                if(json.data) window.location.href = json.data;
            });
        }else{
            layer.msg(json.msg, {
                icon: 2,
                time: 1300
            });
        }
    },
    data_set: function(id) {
        return form.get(id);
    },
    set: function(obj,id) {
        if(submit.deny(obj)) return false;
        var id = id || '#set';
        var url = $(id).attr('action');
        var data = require.data_set(id);
        load.wait();
        $.post(url,data,function(json){
            load.close();
            require.callback_set(json);
            submit.allow();
        });
    },
    callback_del: function(json) {
        if(json.code == 0) {
            layer.msg(json.msg, {
                icon: 1,
                time: 700
            },function(){
                json.data = json.data || window.location.href;
                if(json.data) window.location.href = json.data;
            });
        }else{
            json.msg = '<span class="error">'+json.msg+'</span>';
            layer.msg(json.msg, {
                icon: 2,
                time: 700
            });
        }
    },
    del: function(obj) {
        var o = $(obj);
        var msg = o.attr("data-msg");
        if(!msg) msg = '您确定要执行吗？';
        var yesbtn = o.attr("data-yesbtn");
        if(!yesbtn) yesbtn = '确定';
        var nobtn = o.attr("data-nobtn");
        if(!nobtn) nobtn = '取消';
        var url = o.attr("data-url");
        if(!url) return;
        layer.confirm(msg, {
            offset: '5px',
            shade: 0, //遮罩透明度
            btn: [yesbtn,nobtn] //按钮
        }, function(index){
            if(submit.deny(o)) return false;
            load.wait();
            $.get(url,{},function(json){
                load.close();
                require.callback_del(json);
                submit.allow();
                layer.close(index);
            });
        });
    }
};

var form = {
    /**
     * 获取表单数据
     * onclick="xxx(this);return false;"
     * 注意表单内不能有与 函数名 xxx 相同的name的元素 
     */
    get: function(formid,is_obj) {
        var is_obj = is_obj || 0;
        var result = $(formid).serializeArray();
        if(is_obj) return result;
        var o = {};
        $.each(result, function(){
            if(o[this.name] !== undefined) {
                if(!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            }else{
                o[this.name] = this.value || '';
            }
        });
        return o;
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
