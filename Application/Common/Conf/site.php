<?php
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用
/**
 * 站点配置
 * @author buexplain
 * @return array
 */
return array('site'=>array(
        'httpHost'=>'http://'.$_SERVER['HTTP_HOST'],
        'name'=>'梦想星辰大海',
        'oneDesc'=>'Keep it simple！',
        'title'=>'kiscms官网 - 梦想星辰大海',
        'keywords'=>'kiscms官网,web内容管理框架,ThinkPhP开发,kiscms开发,梦想星辰大海',
        'description'=>'web内容管理框架,基于ThinkPHP3.2.3新版开发,PHP开发最佳实践。',
        'pageSize'=>10, //Home模块的列表分页大小
));