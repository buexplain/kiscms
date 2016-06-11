<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
use \Think\Page;
class DocRecyController extends BaseController {
    private $recyState = 3; //逻辑删除状态
    /**
     * 文档回收站列表
     */
    public function listDocRecy() {
        /*关键词检索*/
        $search_arr = array('关键词类型','ID','标题');
        $search_type = I('get.search_type',0,'intval');
        $search_keywrod = I('get.search_keywrod','');
        $this->assign('search_arr',$search_arr);
        $this->assign('search_type',$search_type);
        $this->assign('search_keywrod',$search_keywrod);

        $where = array();
        $where['state'] = $this->recyState;

        if(!empty($search_keywrod)) {
            switch ($search_type) {
                case 1:
                    $where['doc_id'] = $search_keywrod;
                    break;
                case 2:
                    $where['title'] = array('like',$search_keywrod.'%');
                    break;
            }
        }

        $field = "*";
        $counter = D('Doc')->field($field)->where($where)->count();
        $pageSize = pageSize();
        $page = new Page($counter,$pageSize);
        $result  = D('Doc')->order('doc_id desc')->field($field)->limit($page->firstRow.','.$page->listRows)->where($where)->select();
        //echo D('Doc')->getLastSql();
        foreach ($result as $key => $value) {
            $result[$key]['handle'] .= '<a href="javascript:;" data-url="'.U('DocRecy/resetDoc',array('doc_id'=>$value['doc_id'])).'" class="deltips">还原</a>';
            $result[$key]['handle'] .= '<a href="javascript:;" data-url="'.U('DocRecy/delDoc',array('doc_id'=>$value['doc_id'])).'" class="deltips">删除</a>';
        }

        $this->assignPage($page,$pageSize);
        $this->assign('result',$result);

        $this->display();
    }
    /**
     * 物理删除文档
     */
    public function delDoc() {
        $doc_id = filterNumStr(I('get.doc_id'));
        if(empty($doc_id)) {
            $this->error('缺省参数');
        }
        $Doc = D('Doc');
        $where = array();
        $where['state'] = $this->recyState;
        $where['doc_id'] = array('IN',$doc_id);
        $this->startTrans();
        $result = $Doc->where($where)->delete($doc_id);
        if($result !== false) {
            $result = D('DocCategoryRelation')->delCidByDocId($doc_id);
        }
        if($result !== false) {
            $result = D('DocExtValue')->delByDocId($doc_id);
        }
        if($result === false) {
            $this->rollback();
            $this->error();
        }
        $this->commit();
        $this->success();
    }
    /**
     * 还原文档
     */
    public function resetDoc() {
        $doc_id = filterNumStr(I('get.doc_id'));
        if(empty($doc_id)) {
            $this->error('缺省参数');
        }
        $Doc = D('Doc');
        $where = array();
        $where['doc_id'] = array('IN',$doc_id);
        $this->startTrans();
        $result = $Doc->data(array('state'=>1))->where($where)->save();
        if($result === false) {
            $this->rollback();
            $this->error();
        }
        $this->commit();
        $this->success();
    }
}