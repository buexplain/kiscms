$(function(){
	init_wh();
});

$(window).resize(function(){
	init_wh();
});

/**
 * 计算主体宽高度
 */
function init_wh() {
	var w = $(window).width();
	var h = $(window).height();
	var boxman_h = h - 50 - 6;
	$("#boxman").height(boxman_h);
	$("#boxsidebar").height(boxman_h);
	$("#boxsidebartree").height(boxman_h - 21);
	$("#boxcontent").width(w - $("#boxsidebar").width() - 2).height(boxman_h); //-2是为了保留一个像素的兼容ie
}
