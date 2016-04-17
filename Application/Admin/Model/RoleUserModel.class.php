<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
class RoleUserModel extends BaseModel{
    /**
    * 根据角色id删除uid
    */
    public function delUidByRoleId($role_id) {
        $role_id = (array)$role_id;
        return $this->where(array('role_id'=>array('IN',implode(',',$role_id))))->delete();
    }
    /**
    * 根据用户id删除角色id
    */
    public function delRoleIdByUid($uid) {
    	//注意不能删除用户的超级角色
    	return $this->where(array('uid'=>$uid,'role_id'=>array('NEQ',C('super_role_id'))))->delete();
    }
    /**
    * 添加用户角色
    */
    public function addUserRoleId($uid,$role_ids) {
    	$role_ids = (array) $role_ids;
    	$data = array();
    	foreach ($role_ids as $key => $role_id) {
    		//注意不能添加超级角色
    		if($role_id <= 0 || $role_id == C('super_role_id')) continue;
    		$data[$key] = array('uid'=>$uid,'role_id'=>$role_id);
    	}
    	return $this->addAll($data); // 如果插入条数过多 则 array_chunk 分组
    }
    /**
    * 根据uid获取用户对应的角色id
    */
    public function getRoleIdByUid($uid,$is_unset_ban=false) {
        $result = $this->getRoleIdRoleNameByUid($uid,$is_unset_ban);
        return implode(',',array_keys($result));
    }
    /**
     * 根据uid获取用户的角色id与角色名
     */
    public function getRoleIdRoleNameByUid($uid,$is_unset_ban=false) {
        $role_table = C('DB_PREFIX').'role';
        $field = "{$role_table}.role_id,{$role_table}.role_name";
        $tmp = $this->getRoleByUid($uid,$field,$is_unset_ban);
        $result = array();
        foreach ($tmp as $key => $value) {
            $result[$value['role_id']] = $value['role_name'];
        }
        return $result;
    }
    /**
     * 根据用户id获取角色信息
     */
    public function getRoleByUid($uid,$field='*',$is_unset_ban=false) {
        /*角色表*/
        $role_table = C('DB_PREFIX').'role';
        /*用户角色表*/
        $role_user_table = C('DB_PREFIX').'role_user';
        if($field == '*') $field = "{$role_table}.*";   
        /*找出用户的角色*/
        $join = "inner join {$role_table} on {$role_table}.role_id={$role_user_table}.role_id";
        $where = array("{$role_user_table}.uid"=>$uid);
        if($is_unset_ban) $where["{$role_table}.ban"] = 0;
        return $this->join($join)->field($field)->where($where)->field($field)->select(); 
    }
}