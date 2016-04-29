<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
use \Think\Page;
use \Org\Form\CheckForm;
class UserController extends BaseController {
    /**
     * 帐号列表
     */
    public function listAccounts() {
        /*关键词检索*/
        $search_arr = array('关键词类型','用户ID','邮箱','注册IP');
        $search_type = I('get.search_type',0,'intval');
        $search_keywrod = I('get.search_keywrod','');
        $this->assign('search_arr',$search_arr);
        $this->assign('search_type',$search_type);
        $this->assign('search_keywrod',$search_keywrod);

        /*时间检索*/
        $search_start_time = I('get.search_start_time','');
        $search_end_time = I('get.search_end_time','');
        $this->assign('search_start_time',$search_start_time);
        $this->assign('search_end_time',$search_end_time);

        /*状态检索*/
        $search_ban = I('get.search_ban',0,'intval');
        $this->assign('search_ban',$search_ban);
        $search_ban_arr = C('user_ban');
        $search_ban_arr[0] = '用户状态';
        ksort($search_ban_arr);
        $this->assign('search_ban_arr',$search_ban_arr);

        $where = array();
        if(!empty($search_keywrod)) {
            switch ($search_type) {
                case 1:
                    $where['uid'] = $search_keywrod;
                    break;
                case 2:
                    $where['email'] = array('like',$search_keywrod.'%');
                    break;
                case 3:
                    $where['regip'] = $search_keywrod;
                    break;
            }
        }

        if($search_start_time && $search_end_time) {
            $where['regtime'] = array('between',array($search_start_time,$search_end_time));
        }elseif ($search_start_time) {
            $where['regtime'] = array('egt',$search_start_time);
        }elseif ($search_end_time) {
            $where['regtime'] = array('elt',$search_end_time);
        }

        if($search_ban) {
            $where['ban'] = $search_ban;
        }

        $counter = D('Ucenter')->where($where)->count();
        $pageSize = pageSize();
        $page = new Page($counter,$pageSize);

        $result  = D('Ucenter')->order('uid desc')->limit($page->firstRow.','.$page->listRows)->where($where)->select();
        //echo D('Ucenter')->getLastSql();
        foreach ($result as $key => $value) {
            $result[$key]['handle'] = '<a href="javascript:;" data-url="'.U('User/setBan',array('uid'=>$value['uid'])).'" class="deltips">状态</a>';
            $result[$key]['handle'] .= '<a href="'.U('User/setPasswd',array('uid'=>$value['uid'])).'">密码</a>';
            $result[$key]['handle'] .= '<a href="'.U('User/listLoginLog',array('uid'=>$value['uid'])).'">日志</a>';
        }

        $this->assignPage($page,$pageSize);
        $this->assign('result',$result);
        $this->assign('user_ban',C('user_ban'));

        $btn_arr = array();
        $btn_arr[] = array('添加帐号',U('User/addAccounts'));
        $this->assign('btn_arr',$btn_arr);
        $this->display();
    }
    /**
     * 用户登录日志
     */
    public function listLoginLog() {
        $uid = I('get.uid',0,'intval');
        $where = array('uid'=>$uid);
        $counter = D('Usign')->where($where)->count();
        $pageSize = pageSize();
        $page = new Page($counter,$pageSize);
        $result = D('Usign')->limit($page->firstRow.','.$page->listRows)->where($where)->order('sign_time desc')->select();
        $this->assign('result',$result);
        $this->assignPage($page,$pageSize);
        $this->assign('sign_api',C('sign_api'));
        $this->display();
    }
    /**
     * 添加，编辑帐号
     */
    public function addAccounts() {
        if(IS_POST) {
            $uid = I('post.uid',0,'intval');
            $data = D('Ucenter')->create();
            if(!$data) $this->error(D('Ucenter')->getError());
            $result = D('Ucenter')->addUser($data['email'],$data['passwd'],$data['ban']);
            if($result === false) $this->error();
            $url = U('User/addAccounts');
            $this->success($url);
        }else{
            $this->assign('user_ban',C('user_ban'));
            $this->display();
        }
    }
    /**
     * 修改密码
     */
    public function setPasswd() {
        if(IS_POST) {
            $passwd = I('post.passwd','');
            if(!CheckForm::passwd($passwd)) $this->error('密码太短，密码长度请大于六');
            $uid = I('post.uid',0,'intval');
            if($uid <= 0) $this->error();
            $result = D('Ucenter')->setPasswd($passwd,$uid);
            if($result === false) {
                $this->error();
            }
            $url = U('User/listAccounts');
            $this->success($url);
        }else{
            $uid = I('get.uid',0,'intval');
            if($uid <= 0) $this->error();
            $this->assign('uid',$uid);
            $this->display();
        }
    }
    /**
     * 禁封帐号
     */
    public function setBan() {
        $uid = I('get.uid',0,'intval');
        if($uid <= 0) $this->error();
        $result = D('Ucenter')->setBan($uid);
        if($result === false) $this->error();
        $this->success();
    }
    /**
     * 用户信息列表
     */
    public function listUinfo() {
        /*关键词检索*/
        $search_arr = array('关键词类型','用户ID','手机','邮箱');
        $search_type = I('get.search_type',0,'intval');
        $search_keywrod = I('get.search_keywrod','');
        $this->assign('search_arr',$search_arr);
        $this->assign('search_type',$search_type);
        $this->assign('search_keywrod',$search_keywrod);
        /*类型检索*/
        $search_utype = I('get.search_utype',0,'intval');
        $this->assign('search_utype',$search_utype);
        $search_utype_arr = C('user_utype');
        $search_utype_arr[0] = '用户类型';
        ksort($search_utype_arr);
        $this->assign('search_utype_arr',$search_utype_arr);

        $where = array(); //用户信息
        if(!empty($search_keywrod)) {
            switch ($search_type) {
                case 1:
                    $where['uid'] = $search_keywrod;
                    break;
                case 2:
                    if(strlen($search_keywrod) == 11) {
                        $where['mobile'] = $search_keywrod;
                    }else{
                        $where['mobile'] = array('like',$search_keywrod.'%');
                    }
                    break;
                case 3:
                    $where['email'] = array('like',$search_keywrod.'%');
                    break;
            }
        }

        if($search_utype) {
            $where['utype'] = $search_utype;
        }

        $counter = D('Uinfo')->where($where)->count();
        $pageSize = pageSize();
        $page = new Page($counter,$pageSize);

        $result  = D('Uinfo')->order('uid desc')->limit($page->firstRow.','.$page->listRows)->where($where)->select();
        //echo D('Uinfo')->getLastSql();
        foreach ($result as $key => $value) {
            $result[$key]['handle'] = '<a href="'.U('User/addUinfo',array('uid'=>$value['uid'])).'">编辑</a>';
        }
        $this->assignPage($page,$pageSize);
        $this->assign('result',$result);
        $this->assign('user_utype',C('user_utype'));
        $this->display();
    }
    /**
     * 添加，编辑用户信息
     */
    public function addUinfo() {
        if(IS_POST) {
            $data = D('Uinfo')->create();
            if(!$data) $this->error(D('Uinfo')->getError());
            $uid = I('post.uid',0,'intval');
            if(!empty($data['mobile']) && !D('Uinfo')->mobileIsNotRepeat($data['mobile'],$uid)) $this->error('手机号已存在');
            if(!empty($data['email']) && !D('Uinfo')->emailIsNotRepeat($data['email'],$uid)) $this->error('邮箱号已存在');
            $this->startTrans();
            $result = D('Uinfo')->save();
            if($result !== false) {
                $role_id_array = filterNumStr(I('post.role_ids',''),true);
                $role_id_array = unsetArrayByvalue($role_id_array,0);
                $result = D('RoleUser')->delRoleIdByUid($uid);
                if(count($role_id_array) > 0 && $result !== false) {
                    $result = D('RoleUser')->addUserRoleId($uid,$role_id_array);
                }
            }
            if($result === false) {
                $this->rollback();
                $this->error();
            }
            $this->commit();
            $this->success(U('User/listUinfo'));
        }else{
            $uid = I('get.uid',0,'intval');
            $result = D('Uinfo')->get($uid);
            $this->assign('result',$result);
            $this->assign('user_utype',C('user_utype'));
            $this->assign('user_sex',C('user_sex'));
            $tmp = D('RoleUser')->getRoleIdRoleNameByUid($uid);
            unset($tmp[C('super_role_id')]);
            $this->assign('role_ids',implode(',',array_keys($tmp)));
            $this->assign('role_names',implode(',',array_values($tmp)));
            $this->display();
        }
    }
    /**
     * 个人管理
     */
    public function addMeInfo() {
        $uid = session(C('USER_AUTH_KEY'));
        if(IS_POST) {
            $passwd = I('post.passwd','');
            if(!empty($passwd) && !CheckForm::passwd($passwd)) $this->error('密码太短，密码长度请大于六');

            $rules = array();
            $rules[] = array('mobile',"isMobile",'请填写正确格式的手机号码',2,'callback');
            $rules[] = array('email',"isEmail",'请填写正确格式的邮箱地址',2,'callback');
            $rules[] = array('sex',array(1,2,3),'请选择性别',1,'in');

            $data = D('Uinfo')->validate($rules)->create();
            if(!$data) $this->error(D('Uinfo')->getError());

            if(!empty($data['mobile']) && !D('Uinfo')->mobileIsNotRepeat($data['mobile'],$uid)) $this->error('手机号已存在');
            if(!empty($data['email']) && !D('Uinfo')->emailIsNotRepeat($data['email'],$uid)) $this->error('邮箱号已存在');

            $this->startTrans();
            $result = D('Uinfo')->where(array('uid'=>$uid))->save($data);
            if(!empty($passwd) && $result !== false) {
                $result = D('Ucenter')->setPasswd($passwd,$uid);
            }
            if($result === false) {
                $this->rollback();
                $this->error();
            }
            $this->commit();

            $url = '';
            if(!empty($passwd)) {
                $url = U('Sign/loginOut');
            }

            $this->success($url);
        }else{
            $result = D('Uinfo')->get($uid);
            $this->assign('result',$result);
            $this->assign('user_sex',C('user_sex'));
            $this->display();
        }
    }

}