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