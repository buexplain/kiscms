<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
use \Think\Page;
class AuthController extends BaseController {
    /**
     * 权限节点列表
     */
    public function listNode() {
        $result = D('Node')->order('pid asc,node_id asc')->select();
        $new_result = array();
        foreach ($result as $key => $value) {

            $tmp = array(
                'id'=>$value['node_id'],
                'pId'=>$value['pid'],'name'=>$value['zh_name'].'（'.$value['en_name'].'）',
                'open'=>'true'
            );

            if($value['node_id'] > 1) {
                $str = '';
                switch ($value['type']) {
                    case 1:
                        $str .= '模块';
                        break;
                    case 2:
                        $str .= '控制器';
                        break;
                    case 3:
                        $str .= '方法';
                        break;
                }
                $str .= '--';
                if($value['is_nav']) {
                    $str .= '菜单';
                }else{
                    $str .= '隐藏';
                }
                $str .= '--';
                if($value['ban']) {
                    $str .= '禁止';
                }else{
                    $str .= '启用';
                }
                $str .= '--->';
                $tmp['name'] = $str.$tmp['name'];
            }

            $new_result[] = $tmp;
        }
        $new_result = json_encode($new_result);
        //print_r($new_result);
        $this->assign('result',$new_result);
        $this->display();
    }
    /**
     * 添加、编辑节点
     */
    public function addNode() {
        $node = D('Node');
        if(IS_POST) {
            if(!$node->create()) $this->error($node->getError());
            $this->startTrans();
            $node_id = I('post.node_id',0,'intval');
            if($node->type == 2) { //如果是控制器，那么将每个单词首字母改为大写
                $node->en_name = ucwords($node->en_name);
            }
            if(empty($node_id)) {
                $url = U('Auth/addNode',I('post.'));
                $result = $node->add();
                if($result !== false) {
                    $result = D('RoleNode')->data(array('node_id'=>$result,'role_id'=>C('super_role_id')))->add();
                }
            }else{
                $url = U('Auth/listNode');
                $result = $node->save();
            }
            if($result === false) {
                $this->rollback();
                $this->error();
            }
            $this->commit();
            $this->success($url);
        }else{
            $node_id = I('get.node_id',0,'intval');
            $pid = I('get.pid',1,'intval');
            $result = $node->get($node_id);
            if(empty($result)) {
                $result = array('pid'=>$pid);
            }
            $this->assign('result',$result);
            $this->display();
        }
    }
    /**
     * 删除节点
     */
    public function delNode() {
        $node_id = I('post.node_id',0,'intval');
        if($node_id == 1) {
            $this->error('不能删除根节点');
        }else{
            if(D('Node')->son($node_id)) {
                $this->error('请先删除其子级');
            }else{
                $this->startTrans();
                $result = D('Node')->del($node_id);
                if($result !== false) {
                    $result = D('RoleNode')->delRoleIdByNodeId($node_id);
                }
                if($result === false) {
                    $this->rollback();
                    $this->error();
                }else{
                    $this->commit();
                    $this->success();
                }
            }
        }
    }
    /**
     * 角色列表
     */
    public function listRole() {
        $search_arr = array('关键词类型','角色ID','角色名');
        $search_type = I('get.search_type',0,'intval');
        $search_keywrod = I('get.search_keywrod','');
        $this->assign('search_arr',$search_arr);
        $this->assign('search_type',$search_type);
        $this->assign('search_keywrod',$search_keywrod);

        $where = array();

        if(!empty($search_keywrod)) {
            switch ($search_type) {
                case 1:
                    $where['role_id'] = $search_keywrod;
                    break;
                case 2:
                    $where['role_name'] = array('like',$search_keywrod.'%');
                    break;
            }
        }

        $counter = D('Role')->where($where)->count();
        $pageSize = pageSize();
        $page = new Page($counter,$pageSize);
        $result = D('Role')->limit($page->firstRow.','.$page->listRows)->where($where)->select();
        //echo D('Role')->getLastSql();
        foreach ($result as $key => $value) {
            $result[$key]['handle'] = '<a href="'.U('Auth/addRole',array('role_id'=>$value['role_id'])).'">编辑</a>';
            $result[$key]['handle'] .= '<a href="'.U('Auth/setRoleNode',array('role_id'=>$value['role_id'])).'">权限</a>';
            $result[$key]['handle'] .= '<a href="javascript:void(0)" class="batch" data-url="'.U('Auth/delRole',array('role_id'=>$value['role_id'])).'">删除</a>';
            $result[$key]['ban_txt'] = $value['ban'] ? '是' : '否';

            /*超级管理员禁止操作*/
            if($value['role_id'] == C('super_role_id')) {
                $result[$key]['handle'] = '无';
            }
        }

        $this->assign('result',$result);
        $this->assignPage($page,$pageSize);
        $btn_arr = array();
        $btn_arr[] = array('添加角色',U('Auth/addRole'));
        $this->assign('btn_arr',$btn_arr);
        $this->display();
    }
    /**
     * 添加、编辑角色
     */
    public function addRole() {
        $role = D('Role');
        if(IS_POST) {
            if(!$role->create()) $this->error($role->getError());
            $role_id = I('post.role_id',0,'intval');
            if(empty($role_id)) {
                $url = U('Auth/addRole');
                $result = $role->add();
            }else{
                $url = U('Auth/listRole');
                if($role_id == C('super_role_id')) $this->error('超级管理员禁止操作');
                $result = $role->save();
            }
            if($result === false) $this->error();
            $this->success($url);
        }else{
            $role_id = I('get.role_id',0,'intval');
            $this->assign('result',$role->get($role_id));
            $this->display();
        }
    }
    /**
     * 删除角色
     */
    public function delRole() {
        $role_id = I('get.role_id',0,'intval');
        if($role_id == C('super_role_id')) $this->error('超级管理员禁止操作');
        $this->startTrans();
        $result = D('Role')->del($role_id);
        if($result !== false) {
            $result = D('RoleNode')->delNodeIdByRoleId($role_id);
            if($result !== false) {
                $result = D('RoleUser')->delUidByRoleId($role_id);
            }
        }
        if($result === false) {
            $this->rollback();
            $this->error();
        }
        $this->commit();
        $this->success();
    }
    /**
     * 设置角色的权限节点
     */
    public function setRoleNode() {
        if(IS_POST) {
            $role_id = I('post.role_id',0,'intval');
            if(empty($role_id) || $role_id == C('super_role_id')) $this->error();
            $node_id = I('post.node_id','');
            if(empty($node_id)) $this->error('请勾选权限节点');
            $node_id = explode(',', $node_id);
            $this->startTrans();
            $result = D('RoleNode')->delNodeIdByRoleId($role_id);
            if($result === false) {
                $this->rollback();
                $this->error();
            }
            foreach ($node_id as $key => $value) {
                $value = intval($value);
                if($value <= 0) continue;
                $result = D('RoleNode')->data(array('role_id'=>$role_id,'node_id'=>$value))->add();
                if($result === false) {
                    $this->rollback();
                    $this->error();
                    break;
                }
            }
            $this->commit();
            $this->success(U('Auth/listRole'));
        }else{
            $role_id = I('get.role_id',0,'intval');
            if(empty($role_id) || $role_id == C('super_role_id')) $this->error();
            $this->assign('role_id',$role_id);
            $node_ids = D('RoleNode')->getNodeByRoleId($role_id);
            $this->assign('node_id',implode(',', $node_ids));
            $new_result = array();
            $result = D('Node')->order('pid asc,node_id asc')->select();
            foreach ($result as $key => $value) {
                $tmp = array(
                    'id'=>$value['node_id'],
                    'pId'=>$value['pid'],
                    'name'=>$value['zh_name'],
                    'open'=>'true'
                );
                if(isset($node_ids[$value['node_id']])) $tmp['checked'] = 'true';
                $new_result[] = $tmp;
            }
            $new_result = json_encode($new_result);
            $this->assign('result',$new_result);
            $this->display();
        }
    }
}