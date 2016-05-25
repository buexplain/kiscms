<?php
namespace Home\Controller;
use Home\Common\HomeController;
use \Think\Page;
class ListController extends HomeController {
    public function index(){
        $cid = I('get.cid',0,'intval');
        $where = array();
        $prefix = C('DB_PREFIX');
        $join = "{$prefix}doc INNER JOIN {$prefix}doc_category_relation ON {$prefix}doc.doc_id={$prefix}doc_category_relation.doc_id";
        $field = "{$prefix}doc_category_relation.cid,{$prefix}doc.doc_id,{$prefix}doc.title,{$prefix}doc.createtime";
        $where["{$prefix}doc_category_relation.cid"] = $cid;
        $where['state'] = 2;
        $counter = D('Doc')->join($join)->field($field)->where($where)->count();
        $pageSize = C('site.pageSize');
        $page = new Page($counter,$pageSize);
        $result  = D('Doc')->order("{$prefix}doc.createtime desc")->join($join)->field($field)->limit($page->firstRow.','.$page->listRows)->where($where)->select();
        //echo D('Doc')->getLastSql();
        foreach ($result as $key => $value) {
            $result[$key]['content'] = htmlspecialchars_decode($value['content']);
        }
        $this->assign('result',$result);
        $this->assignPage($page,$pageSize);
        $this->assign('site_title',D('DocCategory')->getCnameByCid($cid).' - '.C('site.name'));
        $this->display();
    }
}