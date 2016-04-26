<?php
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用
/**
 * 系统配置文件
 * @author buexplain
 * @return array
 */
return array(
	/*'配置项'=>'配置值'*/
	'LOAD_EXT_CONFIG'=>'db,vars,site',//加载配置文件
	/*跳转提示*/
    'TMPL_ACTION_ERROR' => 'Public/Jump/index.html',
    'TMPL_ACTION_SUCCESS' => 'Public/Jump/index.html',
    /*登录标识*/
    'USER_AUTH_KEY'=>'login_uid',
    /*用户类型*/
    'USER_TYPE_KEY'=>'login_utype',
    /*URL设置*/
    'URL_MODEL'=>2,
    /*关闭session的自动开启*/
    'SESSION_AUTO_START'=>false,
    /*数据缓存设置*/
    'DATA_CACHE_TIME'=>1,
    /*模块映射，保护后台*/
    'URL_MODULE_MAP'=>array('admin'=>'admin'),
);