<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
class DocCategoryController extends BaseController {
    public function listDocCategory() {
        $this->assign('result',D('DocCategory')->getCategoryZtree());
        $btn_arr = array();
        $btn_arr[] = array('添加顶级',U('/Admin/DocCategory/addDocCategory'));
        $this->assign('btn_arr',$btn_arr);

    	$this->display();
    }
    /**
     * 添加、编辑分类
     */
    public function addDocCategory() {
        if(IS_POST) {
            $cid = I('post.cid',0,'intval');
            $DocCategory = D('DocCategory');
            if(!$DocCategory->create()) $this->error($DocCategory->getError());
            if($cid) {
                $result = $DocCategory->save();
                $url = U('/Admin/DocCategory/listDocCategory');
            }else{
                $url = U('/Admin/DocCategory/addDocCategory',I('post.'));
                $DocCategory->depth = $DocCategory->getDepthByPid($DocCategory->pid);
                if($DocCategory->depth > 5) $this->error('分类层级不能大于五级');
                $result = $DocCategory->add();
            }
            if($result === false) {
                $this->error();
            }
            $this->success($url);
        }else{
            $cid = I('get.cid',0,'intval');
            if($cid) {
                $result = D('DocCategory')->get($cid);
            }else{
                $result['pid'] = I('get.pid',0,'intval');
                $result['cid'] = $result['sort'] = 0;
            }
            $this->assign('result',$result);
            $this->display();
        }
    }
    /**
     * 删除分类
     */
    public function delDocCategory() {
        $cid = I('post.cid',0,'intval');
        $DocCategory = D('DocCategory');
        if($DocCategory->son($cid)) $this->error('请先删除子分类');
        $this->startTrans();
        $result = $DocCategory->delete($cid);
        if($result !== false) {
            $result = D('DocCategoryRelation')->delDocIdByCid($cid);
        }
        if($result === false) {
            $this->rollback();
            $this->error();
        }
        $this->commit();
        $this->success(); 
    }
}