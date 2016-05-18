<?php
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用
/**
 * 上传配置
 * 如果要跨域上传，则需要配置 fileCrossDomain fileStaticUrl fileUploadUrl 三项即可
 * 注意，此处跨域上传采用的是html5的CORS协议实现
 * @author buexplain
 * @return array
 */
$m = C('VAR_MODULE');
$c = C('VAR_CONTROLLER');
$a = C('VAR_ACTION');
return array(
    'fileCrossDomain'=>'', //跨域上传允许头，没有跨域需求，请勿设置。生成环境，请勿设置 *
    'fileStaticUrl'=>'/', //静态文件地址 必须以斜杠结尾
    'fileUploadUrl'=>"/index.php?{$m}=Upload&{$c}=FileUpload&{$a}=index", //上传地址
    'fileHasUrl'=>"/index.php?{$m}=Upload&{$c}=FileUpload&{$a}=has", //秒传判断文件是否存在
    'fileBrowseUrl'=>"/index.php?{$m}=Upload&{$c}=FileBrowse&{$a}=index", //浏览地址
    'fileFormUrl'=>"/index.php?{$m}=Upload&{$c}=FileForm&{$a}=index", //上传表单
    'fileUploadExt'=>'gif,jpg,jpeg,png,bmp,pdf,txt',//文件上传类型限制
);