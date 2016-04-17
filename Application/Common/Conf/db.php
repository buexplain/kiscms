<?php
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用
/**
 * 数据库配置文件
 * @author buexplain
 * @return array
 */
return array(
    //数据库设置
    'DB_TYPE'   => 'mysql',
    'DB_HOST'   => 'localhost',
    'DB_NAME'   =>  'kiscms',
    'DB_USER'   =>  'root',
    'DB_PWD'    =>  'root',
    'DB_PORT'   =>  '3306',
    'DB_PREFIX' =>  'kis_',
);