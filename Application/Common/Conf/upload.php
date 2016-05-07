<?php
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用
/**
 * 上传配置
 * @author buexplain
 * @return array
 */
$m = C('VAR_MODULE');
$c = C('VAR_CONTROLLER');
$a = C('VAR_ACTION');
return array(
    'fileStaticUrl'=>'/', //静态文件地址 必须以斜杠结尾
    'fileHasUrl'=>"/index.php?{$m}=Upload&{$c}=FileUpload&{$a}=has", //秒传判断文件是否存在
    'fileUploadUrl'=>"/index.php?{$m}=Upload&{$c}=FileUpload&{$a}=index", //上传地址
    'fileBrowseUrl'=>"/index.php?{$m}=Upload&{$c}=FileBrowse&{$a}=index", //浏览地址
    'fileFormUrl'=>"/index.php?{$m}=Upload&{$c}=FileForm&{$a}=index", //上传表单
    'fileUploadExt'=>'gif,jpg,jpeg,png,bmp,pdf,txt',//文件上传类型限制
);