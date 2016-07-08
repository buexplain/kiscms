var requireDispatch = {
    evalFunc:function(){
        if(arguments.length == 0) {
            alert('eval函数参数错误！');
            return false;
        }

        var params = arguments[1];
        params = params || {};

        var funcStr = arguments[0]+'(';
        for(var i in params) {
            funcStr += i+',';
            eval('var '+i+' = params[i]');
        }
        if(funcStr.lastIndexOf(',') == funcStr.length -1) {
            funcStr = funcStr.substr(0,funcStr.length -1);
        }
        funcStr += ')';

        return eval(funcStr);
    },
    getFormData:function(formO){
        var data = formO.serializeArray();
        var json = {};
        $.each(data, function(){
            if(json[this.name] !== undefined) {
                if(!json[this.name].push) {
                    json[this.name] = [json[this.name]];
                }
                json[this.name].push(this.value || '');
            }else{
                json[this.name] = this.value || '';
            }
        });
        return json;
    },
    getDataAttr:function(buttonO){
        var DOMobject = buttonO[0];
        var result = {};
        for(var i=0,len=DOMobject.attributes.length;i<len;i++) {
            if(DOMobject.attributes[i]['nodeName'].substr(0,5) == 'data-') {
                var key = DOMobject.attributes[i]['nodeName'].substr(5,DOMobject.attributes[i]['nodeName'].length);
                key = $.trim(key);
                if(key) result[key] = DOMobject.attributes[i]['nodeValue'];
            }
        }
        return result;
    },
    form:{
        setAutoSubmit:function(index,attr){
            var index = index || 'form';
            var attr = attr || {};
            attr['onclick'] = 'requireDispatch.form.submit(this);return false;';
            $(index).each(function(i){
                var formO = $(this);
                formO.addClass('requireDispatchForm-'+i);
                attr['requireDispatchForm'] = i;
                formO.find("button[type='submit']").attr(attr);
                formO.find("input[type='submit']").attr(attr);
            });
        },
        submit:function(obj){
            var buttonO = $(obj);
            var formO = $('.requireDispatchForm-'+buttonO.attr('requireDispatchForm')).eq(0);

            var type = formO.attr('method');
            if(!type) type = buttonO.attr('data-ajaxType');
            type = type || 'post';

            var success = buttonO.attr('data-ajaxSuccess');
            success = success || '';

            var error = buttonO.attr('data-ajaxError');
            error = error || '';

            var tips = buttonO.attr('data-tips');
            tips = tips || '';

            var getData = buttonO.attr('data-getData');
            getData = getData || '';

            var url = formO.attr('action');
            if(!url) url = buttonO.attr('data-url');

            var data = getData ? requireDispatch.evalFunc(getData,{formO:formO}) : requireDispatch.getFormData(formO);

            if(tips && tips != '0') {
                if(isNaN(parseInt(tips))) {
                    var tmp = requireDispatch.evalFunc(tips);
                }else{
                    var tmp = confirm('确定要提交吗？');
                }
                if(!tmp) return;
            }

            $.ajax({
                type:type,
                url:url,
                data:data,
                async:false,
                success:function(result){
                    if(success) {
                        requireDispatch.evalFunc(success,{result:result,formO:formO,buttonO:buttonO});
                    }else{
                        requireDispatch.form.success(result,formO,buttonO);
                    }
                },
                error:function(ajaxObj, textStatus, errorThrown){
                    if(error) {
                        requireDispatch.evalFunc(error,{formO:formO,buttonO:buttonO,ajaxObj:ajaxObj,textStatus:textStatus,errorThrown:errorThrown});
                    }else{
                        requireDispatch.form.error(formO,buttonO,ajaxObj,textStatus,errorThrown);
                    }
                }
            });
        },
        success:function(result,formO,buttonO){
            if(typeof result.msg == 'undefined') {
                alert(result);
            }else{
                alert(result.msg);
            }
            var skip = buttonO.attr('data-skip');
            if(skip) window.location.href = skip;
        },
        error:function(formO,buttonO,ajaxObj,textStatus,errorThrown){
            alert('抱歉，服务器内部错误！');
        }
    },
    batch:{
        setAutoSubmit:function(index,attr){
            var index = index || '.batch';
            var attr = attr || {};
            attr['onclick'] = 'requireDispatch.batch.submit(this);';
            $(index).each(function(i){
                var o = $(this);
                o.attr(attr);
            });
        },
        submit:function(obj){
            var buttonO = $(obj);

            var type = buttonO.attr('data-ajaxType');
            type = type || 'post';

            var success = buttonO.attr('data-ajaxSuccess');
            success = success || '';

            var error = buttonO.attr('data-ajaxError');
            error = error || '';

            var url = buttonO.attr('data-url');

            var tips = buttonO.attr('data-tips');
            tips = tips || '';

            var getData = buttonO.attr('data-getData');
            getData = getData || '';

            var data = getData ? requireDispatch.evalFunc(getData,{buttonO:buttonO}) : requireDispatch.getDataAttr(buttonO);

            delete data['ajaxType'];
            delete data['ajaxSuccess'];
            delete data['ajaxError'];
            delete data['url'];
            delete data['getData'];
            delete data['tips'];

            if(tips && tips != '0') {
                if(isNaN(parseInt(tips))) {
                    var tmp = requireDispatch.evalFunc(tips,{buttonO:buttonO});
                }else{
                    var tmp = confirm('确定要执行吗？');
                }
                if(!tmp) return;
            }

            $.ajax({
                type:type,
                url:url,
                data:data,
                async:false,
                success:function(result){
                    if(success) {
                        requireDispatch.evalFunc(success,{result:result,buttonO:buttonO});
                    }else{
                        requireDispatch.batch.success(result,buttonO);
                    }
                },
                error:function(ajaxObj, textStatus, errorThrown){
                    if(error) {
                        requireDispatch.evalFunc(error,{buttonO:buttonO,ajaxObj:ajaxObj,textStatus:textStatus,errorThrown:errorThrown});
                    }else{
                        requireDispatch.batch.error(buttonO,ajaxObj,textStatus,errorThrown);
                    }
                }
            });
        },
        success:function(result,buttonO){
            if(typeof result.msg == 'undefined') {
                alert(result);
            }else{
                alert(result.msg);
            }
        },
        error:function(buttonO,ajaxObj,textStatus,errorThrown){
            alert('抱歉，服务器内部错误！');
        }
    }
}