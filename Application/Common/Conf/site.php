<?php
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用

$m = C('VAR_MODULE');
$c = C('VAR_CONTROLLER');
$a = C('VAR_ACTION');

/**
 * 站点配置
 * @author buexplain
 * @return array
 */
$result = array(
    'site'=>array(
        'httpHost'=>'http://'.$_SERVER['HTTP_HOST'],
        'name'=>'梦想星辰大海',
        'oneDesc'=>'Keep it simple！',
        'title'=>'kiscms官网 - 梦想星辰大海',
        'keywords'=>'kiscms官网,web内容管理框架,ThinkPhP开发,kiscms开发,梦想星辰大海',
        'description'=>'web内容管理框架,基于ThinkPHP3.2.3新版开发,PHP开发最佳实践。',
        'friendUrl'=>array(),
        'pageSize'=>10, //Home模块的列表分页大小
        'staticUrl'=>'/', //静态文件地址 必须以斜杠结尾
        'fileHasUrl'=>"/index.php?{$m}=Upload&{$c}=FileUpload&{$a}=has", //判断文件是否存在
        'fileUploadUrl'=>"/index.php?{$m}=Upload&{$c}=FileUpload&{$a}=index", //上传地址
        'fileBrowseUrl'=>"/index.php?{$m}=Upload&{$c}=FileBrowse&{$a}=index", //浏览地址
        'fileFormUrl'=>"/index.php?{$m}=Upload&{$c}=FileForm&{$a}=index", //上传表单
        'fileUploadExt'=>'gif,jpg,jpeg,png,bmp,pdf,txt',//文件上传类型限制
    ),
);
//友情链接配置
$result['site']['friendUrl'][] = array('title'=>'fgreen','name'=>'fgreen','url'=>'http://www.fgreen.org');
$result['site']['friendUrl'][] = array('title'=>'nginx中文站','name'=>'nginx','url'=>'http://www.nginx.cn');

return $result;