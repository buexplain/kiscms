<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
use \Think\Page;
/**
 * 用于各种免权限验证的操作
 */
class FreeAuthController extends BaseController {
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
        $role_ids = I('get.role_ids','');
        $role_ids = array_flip(explode(',',$role_ids));
        foreach ($result as $key => $value) {
            $result[$key]['checked'] = '';
            if(isset($role_ids[$value['role_id']])) {
                $result[$key]['checked'] = 'checked="checked"';
            }
            $result[$key]['ban_txt'] = $value['ban'] ? '是' : '否';
        }
        $this->assign('super_role_id',C('super_role_id'));
        $this->assign('result',$result);
        $this->assignPage($page,$pageSize);
        $this->display();
    }
}

