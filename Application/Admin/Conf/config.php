<?php
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用
/**
 * 系统配置文件
 * @author buexplain
 * @return array
 */
return array(
	/*'配置项'=>'配置值'*/
	'LOAD_EXT_CONFIG' => 'admin',//加载配置文件
	/*自动开启session*/
	'SESSION_AUTO_START'=>true,
    /*显示页面Trace信息*/
    'SHOW_PAGE_TRACE' =>false,
    /*免验证节点*/
    'NO_AUTH_NODE'=>array(
		'Admin'=>array(
			'Index'=>array('index','main'),
			'FreeAuth'=>'',
            'Doc'=>array('createExtForm'),
		),
	),
);