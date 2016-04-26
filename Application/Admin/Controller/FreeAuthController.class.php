<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
use \Think\Page;
use \Org\Tool\Tool;
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
        $page_size = pageSize();
        $page = new Page($counter,$page_size);
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
        $this->assignPage($page,$page_size);
        $this->display();
    }
    /**
     * 文件列表
     */
    public function listFile() {
        /*关键词检索*/
        $search_arr = array('关键词类型','名称','所有者');
        $search_type = I('get.search_type',0,'intval');
        $search_keywrod = trim(I('get.search_keywrod',''));
        $this->assign('search_arr',$search_arr);
        $this->assign('search_type',$search_type);
        $this->assign('search_keywrod',$search_keywrod);

        /*文件类型检索*/
        $fileUploadExt = C('site.fileUploadExt');
        $search_ext_arr = array();
        if(!empty($fileUploadExt)) {
            $search_ext_arr = explode(',',$fileUploadExt);
            array_unshift($search_ext_arr, '文件类型');
        }
        $search_ext = I('get.search_ext',0,'intval');
        $this->assign('search_ext',$search_ext);
        $this->assign('search_ext_arr',$search_ext_arr);

        /*时间检索*/
        $search_time_type = I('get.search_time_type');
        $search_start_time = I('get.search_start_time','');
        $search_end_time = I('get.search_end_time','');
        $this->assign('search_time_type',$search_time_type);
        $this->assign('search_start_time',$search_start_time);
        $this->assign('search_end_time',$search_end_time);

        $where = array();

        if(!empty($search_keywrod)) {
            switch ($search_type) {
                case 1:
                    $where['oname'] = array('like',$search_keywrod.'%');
                    break;
                case 2:
                    $tmp = D('Uinfo')->getUidByRealname($search_keywrod);
                    if($tmp) {
                        $where['uid'] = $tmp;
                    }
                    break;
            }
        }

        if($search_ext) {
            $where['ext'] = $search_ext_arr[$search_ext];
        }

        if($search_start_time && $search_end_time) {
            $where['createtime'] = array('between',array($search_start_time,$search_end_time));
        }elseif ($search_start_time) {
            $where['createtime'] = array('egt',$search_start_time);
        }elseif ($search_end_time) {
            $where['createtime'] = array('elt',$search_end_time);
        }

        $counter = D('File')->where($where)->count();
        $page_size = pageSize();
        $page = new Page($counter,$page_size);
        $result = D('File')->limit($page->firstRow.','.$page->listRows)->order('createtime desc')->where($where)->select();
        //echo D('File')->getLastSql();
        $staticUrl = C('site.staticUrl');
        $imgArr = array('jpg','jpeg','gif','png','bmp');
        foreach($result as $key=>$value) {
            $result[$key]['url'] = $staticUrl.$value['dir'];

            $result[$key]['size'] = Tool::formatSize($value['size']);

            $tmp = D('Uinfo')->getUinfoByUid($value['uid']);
            $result[$key]['realname'] = '未知';
            if(!empty($tmp)) {
                $result[$key]['realname'] = $tmp['realname'];
            }

            $result[$key]['isImg'] = in_array(strtolower($value['ext']),$imgArr);
        }

        $this->assign('result',$result);
        $this->assignPage($page,$page_size);

        //注入ckeditor回调函数ID
        $callBackId = I('get.CKEditorFuncNum',0,'intval');
        $this->assign('callBackId',$callBackId);

        $this->display();
    }
}

