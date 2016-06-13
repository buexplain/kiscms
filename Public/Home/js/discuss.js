function manageBottomReplyForm(doc_id) {
    $("#discussDoc").append('<div class="discuss-form">'+createReplyFormHTML(0,doc_id)+'</div>');
}
/**
 * 注入回复表单
 */
function addReplyForm(o,id,doc_id) {
    var o = $(o).parent();
    var length = o.find('.discuss-form').length;
    $('.discuss-form').remove();
    if(length > 0) {
        manageBottomReplyForm(doc_id);
    }else{
        var html = '<div class="discuss-form">'+createReplyFormHTML(id,doc_id)+'</div>';
        o.append(html);
    }
}
/**
 * 刷新回复验证码
 */
function refreshReplyCaptcha(o) {
    var o = $(o);
    var url = o.attr('data-url');
    url = url + '?' + Math.random();
    o.attr('src',url);
    o.parent().parent().find('input[name="captcha"]').val('');
}
/**
 * 生成回复表单
 */
function createReplyFormHTML(pid,doc_id) {
    var url = '/Home/Discuss/verify.html';
    var action = '/Home/Discuss/addDiscuss.html';

    var pid = pid || 0; //评论引用id
    var formID = 'formID-'+pid;
    var src = url + '?' + Math.random();

    var html = '<form id="'+formID+'" action="'+action+'">';
    html += '<div class="form-group">';
    html += '    <label>邮箱</label>';
    html += '    <input type="text" placeholder="邮箱" name="email" class="form-control input-sm" onblur="getNickname(this)">';
    html += '</div>';
    html += '<div class="form-group">';
    html += '    <label>昵称</label>';
    html += '    <input type="text" placeholder="昵称" name="nickname" class="form-control input-sm">';
    html += '</div>';
    html += '<div class="form-group">';
    html += '    <label>评论</label>';
    html += '    <textarea class="form-control" placeholder="说点什么吧……" name="content" rows="3"></textarea>';
    html += '    <span class="discuss-date"><a href="javascript:;" onclick="addCodeTag(this)">插入代码</a></span>';
    html += '</div>';
    html += '<div class="form-group">';
    html += '    <label>验证码</label>';
    html += '    <div class="form-group">';
    html += '        <img src="'+src+'" data-url="'+url+'" title="click refresh" onclick="refreshReplyCaptcha(this);">';
    html += '    </div>';
    html += '    <div class="form-group">';
    html += '        <input type="text" autocomplete="off" placeholder="验证码" name="captcha" class="form-control input-sm">';
    html += '    </div>';
    html += '</div>';
    html += '<input type="hidden" name="pid" value="'+pid+'">';
    html += '<input type="hidden" name="doc_id" value="'+doc_id+'">';
    html += '<button class="btn btn-default btn-sm" type="submit" onclick="require.set(this,\'#'+formID+'\');return false;">提交</button>';
    html +='</form>';
    return html;
}
/**
 * 根据邮箱读取昵称
 */
function getNickname(o) {
    var o = $(o);
    var email = o.val();

    var reg = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
    if(!reg.test(email)) return ;

    var url = '/Home/Discuss/getNicknameByEmail.html';
    $.post(url,{email:email},function(json){
        if(json.code == 0) {
            o.parent().parent().find('input[name="nickname"]').val(json.data);
        }
    });
}
/**
 * 重新提交后的回调函数
 */
require.callback_set = function(json,obj) {
    if(json.code == 0) {
        layer.msg(json.msg, {
            icon: 1,
            time: 700,
        },function(){
            window.location.href = window.location.href;
        });
    }else{
        layer.msg(json.msg, {
            icon: 2,
            time: 1500,
        },function(){
            if(json.code == 2) $(obj).parent().find('img').click();
        });
    }
}
/**
 * 渲染评论模板
 */
function addDiscussList(discuss) {
    var discussTpl = document.getElementById('discussTpl').innerHTML;
    laytpl(discussTpl).render(discuss, function(html){
        $("#discussList").append(html);
        document.getElementById("discussList").scrollIntoView(true);
    });
}

/**
 * 插入code tag
 */
function addCodeTag(o) {
    var o = $(o);
    var txtO = o.parent().parent().find('textarea').eq(0);
    var con = txtO.val();

    var pos = cursor.get(txtO[0]);

    var conStart = con.substr(0,pos);
    var conEnd = con.substr(pos,con.length);

    con = conStart+'<pre>'+String.fromCharCode(10)+String.fromCharCode(13)+'</pre>'+conEnd;

    txtO.val(con);

    cursor.set(txtO[0],pos+6);
    //console.log(con);
}

$(function(){
    manageBottomReplyForm(doc_id);

    if(typeof discuss.length == 'number') {
        $("#discussContainer").hide();
    }else{
        addDiscussList(discuss);
    }
});
