<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class RoleModel extends BaseModel{
    protected $_validate = array(
        array('role_name','require','请填写角色名',1),
        array('remark','require','请填写备注',1),
        array('ban',array(0,1),'请选择是否禁用',1,'in'),
    );
    /**
    * 根据role_id获取角色信息
    */
    public function get($role_id,$field='*') {
        return $this->where(array('role_id'=>$role_id))->field($field)->find();
    }
    /**
    * 根据role_id删除角色信息
    */
    public function del($role_id) {
        return $this->where(array('role_id'=>$role_id))->delete();
    }
    /**
    * 根据角色id字符串获取角色名称数组
    */
    public function getRoleNameByRoleId($role_ids,$is_unset_ban=false) {
        $where = array('role_id'=>array('IN',$role_ids));
        if($is_unset_ban) {
            $where['ban'] = 0;
        }
        $tmp = $this->field('role_id,role_name,ban')->where($where)->select();
        $result = array();
        foreach ($tmp as $key => $value) {
           $result[$value['role_id']] = $value['role_name'];
        }
        return $result;
    }

}