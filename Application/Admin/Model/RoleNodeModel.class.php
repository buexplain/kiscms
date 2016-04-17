<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class RoleNodeModel extends BaseModel{
   /**
    * 根据role_id获取角色的节点
    */
   public function getNodeByRoleId($role_id) {
        $result = $this->field('node_id')->where(array('role_id'=>$role_id))->select();
        $node_id = array();
        foreach ($result as $key => $value) {
        	$node_id[$value['node_id']] = $value['node_id'];	
        }
        return $node_id;
   }
   /**
    * 根据role_id删除节点id
    */
   public function delNodeIdByRoleId($role_id) {
   		$role_id = (array)$role_id;
   		return $this->where(array('role_id'=>array('IN',implode(',',$role_id))))->delete();
   }
   /**
    * 根据节点id删除角色id
    */
   public function delRoleIdByNodeId($node_id) {
   		$node_id = (array)$node_id;
   		return $this->where(array('node_id'=>array('IN',implode(',',$node_id))))->delete();
   }
}