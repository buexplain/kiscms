<?php
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用
/**
 * 站点配置
 * @author buexplain
 * @return array
 */
$result = array(
	'site'=>array(
		'http_host'=>'http://'.$_SERVER['HTTP_HOST'],
		'name'=>'梦想星辰大海',
        'one_desc'=>'Keep it simple！',
        'title'=>'kiscms官网 - 梦想星辰大海',
        'keywords'=>'kiscms官网,web内容管理框架,ThinkPhP开发,kiscms开发,梦想星辰大海',
        'description'=>'web内容管理框架,基于ThinkPHP3.2.3新版开发,PHP开发最佳实践。',
        'friend_url'=>array(),
        'page_size'=>10,
        'staticUrl'=>'/', //静态文件地址 必须以斜杠结尾
        'fileHasUrl'=>'/Upload/FileUpload/has.html', //判断文件是否存在
        'fileUploadUrl'=>'/Upload/FileUpload/index.html', //上传地址
        'fileBrowseUrl'=>'/Upload/FileManager/index.html', //浏览地址
        'fileFormUrl'=>'/Upload/FileForm/index.html', //上传表单
        'fileUploadExt'=>'gif,jpg,jpeg,png,bmp,pdf,txt',//文件上传类型限制
	),
);
//友情链接配置
$result['site']['friend_url'][] = array('name'=>'fgreen','url'=>'http://www.fgreen.org');
return $result;