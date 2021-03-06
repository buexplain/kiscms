var requestDispatch = {
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
    lockSubmit:function(obj,isLock){
        if(isLock == undefined) var isLock = 2;
        if(isLock == 0){
            setTimeout(function(){
                obj.attr('disabled',false);
            },2000);
            return false;
        }else if(isLock == 1){
            obj.attr('disabled',true);
            return true;
        }
        if(obj.attr('disabled')) return true;
        return false;
    },
    form:{
        setAutoSubmit:function(index,attr){
            var index = index || 'form';
            var attr = attr || {};
            attr['onclick'] = 'requestDispatch.form.submit(this);return false;';
            $(index).each(function(i){
                var formO = $(this);
                formO.addClass('requestDispatchForm-'+i);
                attr['requestDispatchForm'] = i;
                var button = formO.find("button[type='submit']");
                var input  = formO.find("input[type='submit']");
                for(var j in attr) {
                    if(!button.attr(j)) {
                        button.attr(j,attr[j]);
                    }
                    if(!input.attr(j)) {
                        input.attr(j,attr[j]);
                    }
                }
            });
        },
        submit:function(obj){
            var buttonO = $(obj);
            if(requestDispatch.lockSubmit(buttonO)) return;

            var formO = $('.requestDispatchForm-'+buttonO.attr('requestDispatchForm')).eq(0);

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

            var data = getData ? requestDispatch.evalFunc(getData,{formO:formO}) : requestDispatch.getFormData(formO);

            if(tips && tips != '0') {
                if(isNaN(parseInt(tips))) {
                    var tmp = requestDispatch.evalFunc(tips);
                }else{
                    var tmp = confirm('确定要提交吗？');
                }
                if(!tmp) return;
            }
            requestDispatch.lockSubmit(buttonO,1);
            $.ajax({
                type:type,
                url:url,
                data:data,
                async:false,
                success:function(result){
                    requestDispatch.lockSubmit(buttonO,0);
                    if(success) {
                        requestDispatch.evalFunc(success,{result:result,formO:formO,buttonO:buttonO});
                    }else{
                        requestDispatch.form.success(result,formO,buttonO);
                    }
                },
                error:function(ajaxObj, textStatus, errorThrown){
                    requestDispatch.lockSubmit(buttonO,0);
                    if(error) {
                        requestDispatch.evalFunc(error,{formO:formO,buttonO:buttonO,ajaxObj:ajaxObj,textStatus:textStatus,errorThrown:errorThrown});
                    }else{
                        requestDispatch.form.error(formO,buttonO,ajaxObj,textStatus,errorThrown);
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
            alert(errorThrown+"！\n抱歉，服务器内部错误！");
        }
    },
    batch:{
        setAutoSubmit:function(index,attr){
            var index = index || '.batch';
            var attr = attr || {};
            attr['onclick'] = 'requestDispatch.batch.submit(this);';
            $(index).each(function(i){
                var o = $(this);
                for(var j in attr) {
                    if(!o.attr(j)) {
                        o.attr(j,attr[j]);
                    }
                }
            });
        },
        submit:function(obj){
            var buttonO = $(obj);
			if(requestDispatch.lockSubmit(buttonO)) return;

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

            var data = getData ? requestDispatch.evalFunc(getData,{buttonO:buttonO}) : requestDispatch.getDataAttr(buttonO);

            delete data['ajaxType'];
            delete data['ajaxSuccess'];
            delete data['ajaxError'];
            delete data['url'];
            delete data['getData'];
            delete data['tips'];

            if(tips && tips != '0') {
                if(isNaN(parseInt(tips))) {
                    var tmp = requestDispatch.evalFunc(tips,{buttonO:buttonO});
                }else{
                    var tmp = confirm('确定要执行吗？');
                }
                if(!tmp) return;
            }
			requestDispatch.lockSubmit(buttonO,1);
            $.ajax({
                type:type,
                url:url,
                data:data,
                async:false,
                success:function(result){
                    requestDispatch.lockSubmit(buttonO,0);
                    if(success) {
                        requestDispatch.evalFunc(success,{result:result,buttonO:buttonO});
                    }else{
                        requestDispatch.batch.success(result,buttonO);
                    }
                },
                error:function(ajaxObj, textStatus, errorThrown){
                    requestDispatch.lockSubmit(buttonO,0);
                    if(error) {
                        requestDispatch.evalFunc(error,{buttonO:buttonO,ajaxObj:ajaxObj,textStatus:textStatus,errorThrown:errorThrown});
                    }else{
                        requestDispatch.batch.error(buttonO,ajaxObj,textStatus,errorThrown);
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