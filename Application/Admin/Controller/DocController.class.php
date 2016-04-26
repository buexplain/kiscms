<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
use \Think\Page;
use \Org\Tool\Tool;
class DocController extends BaseController {
    public function listDoc() {
        /*关键词检索*/
        $search_arr = array('关键词类型','ID','标题');
        $search_type = I('get.search_type',0,'intval');
        $search_keywrod = I('get.search_keywrod','');
        $this->assign('search_arr',$search_arr);
        $this->assign('search_type',$search_type);
        $this->assign('search_keywrod',$search_keywrod);

        /*时间检索*/
        $search_time_arr = array('时间类型','创建时间','更新时间','发布时间');
        $search_time_type = I('get.search_time_type');
        $search_start_time = I('get.search_start_time','');
        $search_end_time = I('get.search_end_time','');
        $this->assign('search_time_arr',$search_time_arr);
        $this->assign('search_time_type',$search_time_type);
        $this->assign('search_start_time',$search_start_time);
        $this->assign('search_end_time',$search_end_time);

        /*分类检索*/
        $cid = I('get.cid',0,'intval');
        $this->assign('cid',$cid);
        $this->assign('cname',implode(',',D('DocCategory')->getCnameByCid($cid)));
        $cid_tree = D('DocCategory')->getCategoryZtree(array($cid=>$cid));
        $cid_tree = json_decode($cid_tree,true);
        array_unshift($cid_tree,array('id'=>-1,'pId'=>0,'name'=>'清除分类条件','nocheck'=>'true'));
        $cid_tree = json_encode($cid_tree);
        //print_r();
        $this->assign('cid_tree',$cid_tree);

        /*状态检索*/
        $search_doc_state = I('get.search_doc_state',0,'intval');
        $this->assign('search_doc_state',$search_doc_state);
        $search_doc_state_arr = C('doc_state');
        $search_doc_state_arr[0] = '文档状态';
        ksort($search_doc_state_arr);
        $this->assign('search_doc_state_arr',$search_doc_state_arr);

        $where = array();
        $prefix = C('DB_PREFIX');
        if(!empty($search_keywrod)) {
            switch ($search_type) {
                case 1:
                    $where["{$prefix}doc.doc_id"] = $search_keywrod;
                    break;
                case 2:
                    $where["{$prefix}doc.title"] = array('like',$search_keywrod.'%');
                    break;
            }
        }

        if($search_time_type) {
            switch ($search_time_type) {
                case 1:
                    $search_time_type = "{$prefix}doc.createtime";
                    break;
                case 2:
                    $search_time_type = "{$prefix}doc.edittime";
                    break;
                case 3:
                    $search_time_type = "{$prefix}doc.pushtime";
                    break;
            }
            if($search_start_time && $search_end_time) {
                $where[$search_time_type] = array('between',array($search_start_time,$search_end_time));
            }elseif ($search_start_time) {
                $where[$search_time_type] = array('egt',$search_start_time);
            }elseif ($search_end_time) {
                $where[$search_time_type] = array('elt',$search_end_time);
            }
        }

        if($cid) {
            $join = "{$prefix}doc INNER JOIN {$prefix}doc_category_relation ON {$prefix}doc.doc_id={$prefix}doc_category_relation.doc_id";
            $field = "{$prefix}doc_category_relation.cid,{$prefix}doc.*";
            $where["{$prefix}doc_category_relation.cid"] = $cid;
        }else{
            $field = "*";
            $join = '';
        }

        if($search_doc_state) {
            $where["{$prefix}doc.state"] = $search_doc_state;
        }

        $counter = D('Doc')->join($join)->field($field)->where($where)->count();
        $page_size = pageSize();
        $page = new Page($counter,$page_size);
        $result  = D('Doc')->join($join)->field($field)->limit($page->firstRow.','.$page->listRows)->where($where)->select();
        //echo D('Doc')->getLastSql();
        foreach ($result as $key => $value) {
            $result[$key]['handle'] = '<a href="javascript:;" data-url="'.U('Doc/setState',array('doc_id'=>$value['doc_id'])).'" class="deltips">状态</a>';
            $result[$key]['handle'] .= '<a href="'.U('Doc/addDoc',array('doc_id'=>$value['doc_id'])).'">编辑</a>';
            $result[$key]['handle'] .= '<a href="'.U('Doc/addDocExtData',array('doc_id'=>$value['doc_id'])).'">扩展</a>';
            $result[$key]['handle'] .= '<a href="javascript:;" data-url="'.U('Doc/delDoc',array('doc_id'=>$value['doc_id'])).'" class="deltips">删除</a>';
        }

        $this->assignPage($page,$page_size);
        $this->assign('result',$result);

        $btn_arr = array();
        $btn_arr[] = array('添加文档',U('Doc/addDoc'));
        $this->assign('btn_arr',$btn_arr);
        $this->assign('doc_state',C('doc_state'));
        $this->display();
    }
    /**
     * 添加文档
     */
    public function addDoc() {
        if(IS_POST) {
            $doc_id = I('post.doc_id',0,'intval');
            $cid = I('post.cid','','filterNumStr');
            if(empty($cid)) $this->error('请选择分类');
            $Doc = D('Doc');
            $data = $Doc->create();
            if(!$data) $this->error($Doc->getError());
            $this->startTrans();
            if($doc_id) {
                $Doc->edittime = date('Y-m-d H:i:s');
                $result = $Doc->save();
                $url = U('Doc/listDoc');
            }else{
                $Doc->edittime = $Doc->createtime= date('Y-m-d H:i:s');
                $Doc->create_id = session(C('USER_AUTH_KEY'));
                $result = $doc_id = $Doc->add();
                $url = U('Doc/addDoc');
            }
            if($result !== false) {
                $result = D('DocCategoryRelation')->setCidByDocId($doc_id,explode(',', $cid));
            }
            if($result === false) {
                $this->rollback();
                $this->error();
            }
            $this->commit();
            $this->success($url);
        }else{
            $doc_id = I('get.doc_id',0,'intval');
            //获取文档数据
            $result = D('Doc')->get($doc_id);
            //获取分类
            $result['cid'] = D('DocCategoryRelation')->getCidByDocId($doc_id);
            $result['cid_txt'] = D('DocCategory')->getCnameByCid($result['cid']);
            $result['cid_txt'] = implode(',', $result['cid_txt']);
            $result['cid_tree'] = D('DocCategory')->getCategoryZtree($result['cid']);
            $result['cid'] = implode(',', $result['cid']);
            $this->assign('result',$result);
            $this->assign('fileBrowseUrl',C('site.fileBrowseUrl'));
            $this->assign('fileFormUrl',C('site.fileFormUrl'));
            $this->display();
        }
    }
    /**
     * 删除文档
     */
    public function delDoc() {
        $doc_id = I('get.doc_id',0,'intval');
        $Doc = D('Doc');
        $this->startTrans();
        $result = $Doc->delete($doc_id);
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
     * 更新文档状态
     */
    public function setState() {
        $doc_id = I('get.doc_id',0);
        $result = D('Doc')->setState($doc_id);
        if($result === false) $this->error();
        $this->success(U('Doc/listDoc',I('get.')));
    }
    /**
     * 添加文档的扩展数据
     */
    public function addDocExtData() {
        if(IS_POST) {
            $post = I('post.');
            $data = array();
            $DocExtField = D('DocExtField');
            foreach ($post as $key => $value) {
                $field = $DocExtField->getBydocExtId($value['doc_ext_id']);
                foreach ($field as $key2 => $value2) {
                    $tmp = array('doc_id'=>$value['doc_id'],'doc_ext_id'=>$value['doc_ext_id'],'doc_ext_field_id'=>$value2['doc_ext_field_id'],'value'=>implode(',',((array) $value[$value2['field_name']])));
                    $data[] = $tmp;
                }
            }
            //print_r($post);exit;
            $result = D('DocExtValue')->addAll($data,array(),true);
            if($result === false) $this->error();
            $this->success(U('Doc/listDoc'));
        }else{
            $doc_id = I('get.doc_id',0,'intval');
            $this->assign('doc_id',$doc_id);
            $doc_ext = D('DocExt')->getAll();
            array_unshift($doc_ext,array('doc_ext_id'=>0,'ext_name'=>'添加扩展类型'));
            $this->assign('doc_ext',$doc_ext);
            $tmp = D('DocExtValue')->getByDocId($doc_id);
            $result = array('panes'=>'','tabs'=>'');
            foreach ($tmp as $key => $value) {
                $field = D('DocExtField')->getBydocExtId($key);
                $ext_name = D('DocExt')->getExtNameByDocExtId($key);
                $panes = '<div class="tab-pane" data-formid="formid_'.$key.'" id="ext_'.$key.'"><form id="formid_'.$key.'"><input type="hidden" name="doc_ext_id" value="'.$key.'">';
                foreach ($field as $key2 => $value2) {
                    $field[$key2]['form_value_def'] = $value2['form_value_def'] = $value[$value2['doc_ext_field_id']];
                    $panes .= $this->ExtForm($value2);
                }
                $panes .= '</form></div>';
                $result['panes'] .= $panes;
                $result['tabs'] .= '<li class="">
                                        <a href="#ext_'.$key.'">
                                            '.$ext_name.' &nbsp;
                                            <button onclick="del_ext(this)" data-id="ext_'.$key.'" data-value="'.$key.'" type="button"
                                            class="close" aria-label="Close">
                                                <span aria-hidden="true">
                                                    ×
                                                </span>
                                            </button>
                                        </a>
                                    </li>';
            }
            //print_r($result);exit;
            $this->assign('result',$result);
            $this->display();
        }
    }
    /**
     * 删除文档扩展数据
     */
    public function delDocExtData() {
        if(IS_POST) {
            $doc_id = I('post.doc_id',0,'intval');
            $doc_ext_id = I('post.doc_ext_id',0,'intval');
            $result = D('DocExtValue')->delDocExtDataByDocIdDocExtId($doc_id,$doc_ext_id);
            if($result === false) {
                $this->error();
            }
            $this->success();
        }
    }
    /**
     * 生成文档扩展的表单
     */
    public function createExtForm() {
        $doc_ext_id = I('post.doc_ext_id',0,'intval');
        $result = D('DocExtField')->getBydocExtId($doc_ext_id);
        if(empty($result)) $this->error();
        $html = '<input type="hidden" name="doc_ext_id" value="'.$doc_ext_id.'">';
        foreach ($result as $key => $value) {
            $html .= $this->ExtForm($value);
        }
        $this->success($html);
    }
    private function ExtForm($data) {
        $html = '';
        if(!isset($data['form_value_def'])) $data['form_value_def'] = '';
        switch ($data['form_type']) {
            case 'select':
                $form_value = Tool::parseFormValue($data['form_value']);
                $html .='<div class="form-group">';
                $html .='    <label>';
                $html .='        '.$data['field_desc'];
                $html .='    </label>';
                $html .='    <select class="form-control input-sm" name="'.$data['field_name'].'">';
                foreach ($form_value as $key => $value) {
                    if($key == $data['form_value_def']) {
                        $html .='<option value="'.$key.'" selected="selected">'.$value.'</option>';
                    }else{
                        $html .='<option value="'.$key.'">'.$value.'</option>';
                    }
                }
                $html .='    </select>';
                $html .='</div>';
                break;
            case 'checkbox':
                $form_value = Tool::parseFormValue($data['form_value']);
                $data['form_value_def'] = explode(',',$data['form_value_def']);
                $html .='<div class="form-group">';
                $html .='    <label>';
                $html .='        '.$data['field_desc'].'&nbsp;';
                $html .='    </label>';
                foreach ($form_value as $key => $value) {
                    $html .='    <label class="checkbox-inline">';
                    if(in_array($key, $data['form_value_def'])) {
                        $html .='        <input type="checkbox" name="'.$data['field_name'].'" value="'.$key.'" checked="checked"> '.$value;
                    }else{
                        $html .='        <input type="checkbox" name="'.$data['field_name'].'" value="'.$key.'"> '.$value;
                    }
                    $html .='    </label>';
                }
                $html .='</div>';
                break;
            case 'radio':
                $form_value = Tool::parseFormValue($data['form_value']);
                $html .='<div class="form-group">';
                $html .='    <label>';
                $html .='        '.$data['field_desc'].'&nbsp;';
                $html .='    </label>';
                foreach ($form_value as $key => $value) {
                    $html .='<label class="radio-inline">';
                    if($key == $data['form_value_def']) {
                        $html .='    <input type="radio" name="'.$data['field_name'].'" value="'.$key.'" checked="checked"> '.$value;
                    }else{
                        $html .='    <input type="radio" name="'.$data['field_name'].'" value="'.$key.'"> '.$value;
                    }
                    $html .='</label>';
                }
                $html .='</div>';
                break;
            case 'date':
                if($data['form_value_def']) $data['form_value'] = $data['form_value_def'];
                $html .='<div class="form-group">';
                $html .='    <label>';
                $html .='        '.$data['field_desc'];
                $html .='    </label>';
                $html .='    <input type="text" name="'.$data['field_name'].'" value="'.$data['form_value'].'" autocomplete="off"  placeholder="'.$data['field_desc'].'" class="form-control input-sm laydatebox" readonly>';
                $html .='</div>';
                break;
            case 'text':
                if($data['form_value_def']) $data['form_value'] = $data['form_value_def'];
                $html .='<div class="form-group">';
                $html .='    <label>';
                $html .='        '.$data['field_desc'];
                $html .='    </label>';
                $html .='    <input type="text" name="'.$data['field_name'].'" value="'.$data['form_value'].'" autocomplete="off"  placeholder="'.$data['field_desc'].'" class="form-control input-sm">';
                $html .='</div>';
                break;
            case 'textarea':
                if($data['form_value_def']) $data['form_value'] = $data['form_value_def'];
                $html .='<div class="form-group">';
                $html .='    <label>';
                $html .='        '.$data['field_desc'];
                $html .='    </label>';
                $html .='    <textarea name="'.$data['field_name'].'" placeholder="'.$data['field_desc'].'" rows="3" class="form-control">'.$data['form_value'].'</textarea>';
                $html .='</div>';
                break;
        }
        return $html;
    }
}