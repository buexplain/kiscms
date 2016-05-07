<?php
namespace Upload\Controller;
use Upload\Common\BaseController;
use \Think\Page;
use Org\Tool\Tool;
class FileBrowseController extends BaseController {
    /**
     * 文件列表
     */
    public function index() {
        /*关键词检索*/
        $search_arr = array('关键词类型','名称','所有者');
        $search_type = I('get.search_type',0,'intval');
        $search_keywrod = trim(I('get.search_keywrod',''));
        $this->assign('search_arr',$search_arr);
        $this->assign('search_type',$search_type);
        $this->assign('search_keywrod',$search_keywrod);

        /*文件类型检索*/
        $fileUploadExt = C('fileUploadExt');
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
        $pageSize = pageSize();
        $page = new Page($counter,$pageSize);
        $result = D('File')->limit($page->firstRow.','.$page->listRows)->order('createtime desc')->where($where)->select();
        //echo D('File')->getLastSql();
        $imgArr = array('jpg','jpeg','gif','png','bmp');
        foreach($result as $key=>$value) {
            $result[$key]['url'] = C('fileStaticUrl').$value['dir'];

            $result[$key]['size'] = Tool::formatSize($value['size']);

            $tmp = D('Uinfo')->getUinfoByUid($value['uid']);
            $result[$key]['realname'] = '未知';
            if(!empty($tmp)) {
                $result[$key]['realname'] = $tmp['realname'];
            }

            $result[$key]['isImg'] = in_array(strtolower($value['ext']),$imgArr);
        }

        $this->assign('result',$result);
        $this->assignPage($page,$pageSize);

        //注入ckeditor回调函数ID
        $callBackId = I('get.CKEditorFuncNum',0,'intval');
        $this->assign('callBackId',$callBackId);

        $this->display();
    }
}