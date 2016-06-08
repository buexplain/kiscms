<?php
namespace Org\Rbac;
use Org\Arrayhelps\CategoryArray;
/**
 * 基于角色的权限控制类
 *  @author  buexplain
 * @return array
 */
/*
1、必须配置
USER_AUTH_KEY 认证识别号

2、相关的表
CREATE TABLE `gm_role` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `role_name` varchar(50) NOT NULL DEFAULT '' COMMENT '角色名',
  `ban` tinyint(3) unsigned DEFAULT '0' COMMENT '是否禁止 0=否 1=是',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='角色表';

CREATE TABLE `gm_node` (
  `node_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限节点ID',
  `pid` int(10) unsigned NOT NULL COMMENT '父ID',
  `zh_name` varchar(50) NOT NULL DEFAULT '' COMMENT '节点中文名',
  `en_name` varchar(50) NOT NULL DEFAULT '' COMMENT '节点名英文',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1=模块 2=控制器 3=方法 ',
  `is_nav` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否菜单显示 0=否 1=是',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `ban` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁止 0=否 1=是',
  PRIMARY KEY (`node_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='权限节点表';
INSERT INTO `gm_node` VALUES ('1', '0', '根节点', 'Root', '0', '0', '根节点', '0');

CREATE TABLE `gm_role_user` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `role_id` int(10) unsigned NOT NULL COMMENT '角色ID',
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户角色表';

CREATE TABLE `gm_role_node` (
  `role_id` int(10) unsigned NOT NULL COMMENT '角色ID',
  `node_id` int(10) unsigned NOT NULL COMMENT '权限节点ID',
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色权限节点表';

3、免验证的配置 只有key，没有对应的array类型的数组则说明这个环节是免验证的 具体看 isCheck 方法
'NO_AUTH_NODE'=>array('模块'=>array('控制器'=>array('方法')))

5、注意：上面几张表没有经过索引优化
 */
class Rbac {
	/**
	 * 验证权限
	 */
	public static function checking() {
		if(!self::isCheck()) return true;
		$uid = session(C('USER_AUTH_KEY'));
		if(empty($uid)) return false;
		$role_user_node = self::getUserRoleNode($uid);

        $MODULE_NAME = strtolower(MODULE_NAME);
        $CONTROLLER_NAME = strtolower(CONTROLLER_NAME);
        $ACTION_NAME = strtolower(ACTION_NAME);

		if(isset($role_user_node[$MODULE_NAME][$CONTROLLER_NAME][$ACTION_NAME])) return true;
		return false;
	}
	/**
	 * 检查是否需要验证
	 */
	public static function isCheck() {
		$no_auth_node = C('NO_AUTH_NODE');
		if(!isset($no_auth_node[MODULE_NAME])) return true; //没有免检的模块
		if(!is_array($no_auth_node[MODULE_NAME])) return false; //不是数组，整个模块都免检
		if(!isset($no_auth_node[MODULE_NAME][CONTROLLER_NAME])) return true; //没有免检的控制器
		if(!is_array($no_auth_node[MODULE_NAME][CONTROLLER_NAME])) return false; //不是数组 整个控制器都免检
		if(in_array(ACTION_NAME, $no_auth_node[MODULE_NAME][CONTROLLER_NAME])) return false; //有免检的动作
		return true;
	}
	/**
	 * 获取用户节点
	 */
	public static function getUserRoleNode($uid) {
        $prefix = C('DB_PREFIX');
		/*角色表*/
		$role_table = "{$prefix}role";
		/*用户角色表*/
		$role_user_table = "{$prefix}role_user";
		/*角色权限节点表*/
		$role_node_table = "{$prefix}role_node";
		/*权限节点表*/
		$node_table = C('DB_PREFIX').'node';
		/*找出用户的角色*/
		$join = "inner join {$role_table} on {$role_table}.role_id={$role_user_table}.role_id";
		$where = array("{$role_user_table}.uid"=>$uid,"{$role_table}.ban"=>0);
		$field = "{$role_user_table}.role_id";
		$result = M('Role_user')->join($join)->where($where)->field($field)->select();
		//echo M('Role_user')->getLastSql();
		$role_ids = '';
		foreach ($result as $key => $value) {
			$role_ids .= ','.$value['role_id'];
		}
		$role_ids = trim($role_ids,',');
		if(empty($role_ids)) return false;
		/*找出角色下的所有节点*/
		$join = "inner join {$node_table} on {$node_table}.node_id={$role_node_table}.node_id";
		$where = array("{$node_table}.ban"=>0,"{$role_node_table}.role_id"=>array('IN',$role_ids));
		$field = "{$node_table}.node_id,pid,type,en_name";
		$result = M('Role_node')->join($join)->where($where)->field($field)->select();
		//echo M('Role_node')->getLastSql();

		if(empty($result)) return false;

		foreach ($result as $key => $value) {
            $result[$key]['en_name'] = strtolower($value['en_name']);
        }

        $node = array();
        foreach ($result as $key => $value) {
            if($value['type'] == 1) {
                $node[$value['en_name']] = CategoryArray::sons($result,$value['node_id'],'node_id','pid');
                foreach ($node[$value['en_name']] as $key2 => $value2) {
                    if($value2['type'] == 2) {
                        $node[$value['en_name']][$value2['en_name']] = CategoryArray::sons($result,$value2['node_id'],'node_id','pid');
                        foreach ($node[$value['en_name']][$value2['en_name']] as $key3 => $value3) {
                            $node[$value['en_name']][$value2['en_name']][$value3['en_name']] = $value3['node_id'];
                            unset($node[$value['en_name']][$value2['en_name']][$key3]);
                        }
                    }
                    unset($node[$value['en_name']][$key2]);
                }
            }
        }
		return $node;
	}
}