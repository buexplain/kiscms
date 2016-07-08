<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
use \Think\Page;
class DocExtController extends BaseController {
    /**
     * 扩展列表
     */
    public function listDocExt() {
        /*关键词检索*/
        $search_arr = array('关键词类型','ID','扩展名');
        $search_type = I('get.search_type',0,'intval');
        $search_keywrod = I('get.search_keywrod','');
        $this->assign('search_arr',$search_arr);
        $this->assign('search_type',$search_type);
        $this->assign('search_keywrod',$search_keywrod);

        $where = array();
        if(!empty($search_keywrod)) {
            switch ($search_type) {
                case 1:
                    $where['doc_ext_id'] = $search_keywrod;
                    break;
                case 2:
                    $where['ext_name'] = array('like',$search_keywrod.'%');
                    break;
            }
        }

        $counter = D('DocExt')->where($where)->count();
        $pageSize = pageSize();
        $page = new Page($counter,$pageSize);

        $result  = D('DocExt')->limit($page->firstRow.','.$page->listRows)->order('sort asc')->where($where)->select();
        //echo D('DocExt')->getLastSql();
        foreach ($result as $key => $value) {
            $result[$key]['handle'] = '<a href="'.U('DocExt/listField',array('doc_ext_id'=>$value['doc_ext_id'])).'">字段</a>';
            $result[$key]['handle'] .= '<a href="'.U('DocExt/addDocExt',array('doc_ext_id'=>$value['doc_ext_id'])).'">编辑</a>';
            $result[$key]['handle'] .= '<a href="javascript:;" data-url="'.U('DocExt/delDocExt',array('doc_ext_id'=>$value['doc_ext_id'])).'" class="batch">删除</a>';
        }

        $this->assignPage($page,$pageSize);
        $this->assign('result',$result);

        $btn_arr = array();
        $btn_arr[] = array('添加扩展',U('DocExt/addDocExt'));
        $this->assign('btn_arr',$btn_arr);

    	$this->display();
    }
    /**
     * 添加、编辑扩展
     */
    public function addDocExt() {
        if(IS_POST) {
            $doc_ext_id = I('post.doc_ext_id',0,'intval');
            $DocExt = D('DocExt');
            if(!$DocExt->create()) $this->error($DocExt->getError());
            if($doc_ext_id) {
                $url = U('DocExt/listDocExt');
                $result = $DocExt->save();
            }else{
                $url = U('DocExt/addDocExt');
                $result = $DocExt->add();
            }
            if($result === false) {
                $this->error();
            }
            $this->success($url);
        }else{
            $doc_ext_id = I('get.doc_ext_id',0,'intval');
            if($doc_ext_id) {
                $result = D('DocExt')->get($doc_ext_id);
            }else{
                $result['doc_ext_id'] = $result['sort'] = 0;
            }
            $this->assign('result',$result);
            $this->display();
        }
    }
    /**
     * 删除扩展
     */
    public function delDocExt() {
        $doc_ext_id = I('get.doc_ext_id',0,'intval');
        $this->startTrans();
        $result = D('DocExt')->delete($doc_ext_id);
        if($result !== false) {
            $result = D('DocExtField')->delBydocExtId($doc_ext_id);
        }
        if($result !== false) {
            $result = D('DocExtValue')->delByDocExtId($doc_ext_id);
        }
        if($result === false) {
            $this->rollback();
            $this->error();
        }
        $this->commit();
        $this->success();
    }
    /**
     * 字段管理
     */
    public function listField() {
        $DocExtField = D('DocExtField');
        $doc_ext_id = I('get.doc_ext_id',0,'intval');
        $result = $DocExtField->getBydocExtId($doc_ext_id);
        foreach ($result as $key => $value) {
            $result[$key]['handle'] = '<a href="'.U('DocExt/addField',array('doc_ext_field_id'=>$value['doc_ext_field_id'])).'">编辑</a>';
            $result[$key]['handle'] .= '<a href="javascript:void(0)" class="batch" data-url="'.U('DocExt/delField',array('doc_ext_field_id'=>$value['doc_ext_field_id'])).'">删除</a>';
        }
        $this->assign('result',$result);
        $this->assign('form_type',C('form_type'));
        $btn_arr = array();
        $btn_arr[] = array('添加字段',U('DocExt/addField',array('doc_ext_id'=>$doc_ext_id)));
        $this->assign('btn_arr',$btn_arr);

        $this->display();
    }
    /**
     * 添加、编辑字段
     */
    public function addField() {
        if(IS_POST) {
            $doc_ext_field_id = I('post.doc_ext_field_id',0,'intval');
            $DocExtField = D('DocExtField');

            $rules = array(
                array('field_name','require','请填写字段英文名',1),
                array('field_name','','字段英文名已经存在',0,'unique',1),
                array('field_desc','require','请填写字段中文名',1),
                array('form_type',array_keys(C('form_type')),'请选择表单类型',1,'in'),
                array('sort','number','请填写排序整数',2),
            );

            if(!$DocExtField->validate($rules)->create()) $this->error($DocExtField->getError());
            if($doc_ext_field_id) {
                $url = U('DocExt/listField',array('doc_ext_id'=>I('post.doc_ext_id',0,'intval')));
                $result = $DocExtField->save();
            }else{
                $url = U('DocExt/addField',array('doc_ext_id'=>I('post.doc_ext_id',0,'intval')));
                $result = $DocExtField->add();
            }
            if($result === false) {
                $this->error();
            }
            $this->success($url);
        }else{
            $doc_ext_field_id = I('get.doc_ext_field_id',0,'intval');
            $doc_ext_id = I('get.doc_ext_id',0,'intval');
            if($doc_ext_field_id) {
                $result = D('DocExtField')->get($doc_ext_field_id);
            }else{
                $result['doc_ext_field_id'] = $doc_ext_field_id;
                $result['doc_ext_id'] = $doc_ext_id;
            }
            $this->assign('form_type',C('form_type'));
            $this->assign('result',$result);
            $this->display();
        }
    }
    /**
     * 删除字段
     */
    public function delField() {
        $doc_ext_field_id = I('get.doc_ext_field_id',0,'intval');
        $this->startTrans();
        $result = D('DocExtField')->delete($doc_ext_field_id);
        if($result !== false) {
            $result = D('DocExtValue')->delByDocExtFieldId($doc_ext_field_id);
        }
        if($result === false) {
            $this->rollback();
            $this->error();
        }
        $this->commit();
        $this->success();
    }
}