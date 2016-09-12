<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
use \Org\Arrayhelps\CategoryArray;
class DocCategoryController extends BaseController {
    public function listDocCategory() {
        $result = D('DocCategory')->order('sort desc')->select();

        foreach ($result as $key => $value) {
            $value['handle'] = '<a href="'.U('DocCategory/addDocCategory',array('pid'=>$value['cid'])).'">添加子类</a>';

            $value['handle'] .= '<a href="'.U('DocCategory/addDocCategory',array('cid'=>$value['cid'])).'">编辑</a>';

            $tmp = CategoryArray::son($result,$value['cid'],'cid');
            if(!empty($tmp)) { //跳过没有子类的分类类
                $result[$key] = $value;
                continue;
            }

            $value['handle'] .= '<a href="javascript:;" data-ajaxsuccess="delDocCategorySuccess" data-url="'.U('DocCategory/delDocCategory',array('cid'=>$value['cid'])).'" class="batch">删除</a>';

            $result[$key] = $value;
        }

        $result = CategoryArray::child($result, 0,'cid','pid');
        $this->assign('result',$result);

        $btn_arr = array();
        $btn_arr[] = array('添加顶级',U('DocCategory/addDocCategory'));
        $btn_arr[] = array('关闭节点',"javascript:;",array('onclick'=>'collapseExpandAll(this)'));
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
                $url = U('DocCategory/listDocCategory');
            }else{
                $url = U('DocCategory/addDocCategory',I('post.'));
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
        $cid = I('get.cid',0,'intval');
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