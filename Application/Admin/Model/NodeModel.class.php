<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class NodeModel extends BaseModel{
    protected $_validate = array(
        array('zh_name','require','请填写节点中文名',1),
        array('en_name','require','请填写节点名英文',1),
        array('type',array(1,2,3),'请选择节点类型',1,'in'),
        array('is_nav',array(0,1),'请选择显示类型',1,'in'),
        array('remark','require','请填写备注',1),
        array('ban',array(0,1),'请选择是否禁用',1,'in'),
        array('pid',"gtZeroInt",'pid错误',1,'callback'),
   );
   /**
    * 根据node_id获取权限节点信息
    */
   public function get($node_id) {
        return $this->where(array('node_id'=>$node_id))->find();
   }
   /**
    * 根据node_id获取其一级子节点权限节点信息
    */
   public function son($node_id) {
        return $this->where(array('pid'=>$node_id))->select();
   }
   /**
    * 根据node_id删除权限节点信息
    */
   public function del($node_id) {
        return $this->where(array('node_id'=>$node_id))->delete();
   }
   /**
    * 根据角色获得role_id拥有的节点
    */
   public function getNodeByRoleId($role_ids,$where=array(),$field='pid,type,en_name,zh_name') {
        /*角色权限节点表*/
        $role_node_table = C('DB_PREFIX').'role_node';
        /*权限节点表*/
        $node_table = C('DB_PREFIX').'node';
        $join = "inner join {$role_node_table} on {$node_table}.node_id={$role_node_table}.node_id";
        $where = array_merge($where,array("{$node_table}.ban"=>0,"{$role_node_table}.role_id"=>array('IN',$role_ids)));
        $field = "distinct {$node_table}.node_id,".$field;
        return $this->join($join)->where($where)->field($field)->select();
   }
}