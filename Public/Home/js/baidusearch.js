var baidusearch = {};
baidusearch.cse = '';
baidusearch.sid = '14542714977060765306'; //百度API引擎ID
baidusearch.load = function() {
    var script = document.createElement("script"); 
    script.id = 'baidusearch';
    script.type = "text/javascript";
    script.charset = "utf-8";
    script.src = "http://zhannei.baidu.com/api/customsearch/apiaccept?sid="+baidusearch.sid+"&v=2.0";
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(script, s);
}
baidusearch.init = function() {
    var keywords = $("#search").val();
    if(!keywords) {
        alert('请输入关键词');
        return;
    } 
    baidusearch.cse = new BCse.Search(baidusearch.sid); 
    baidusearch.cse.getResult(keywords, baidusearch.show);   
}
baidusearch.show = function(data) {
    console.log(data);
}
baidusearch.load();