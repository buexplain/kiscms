<?php
namespace Home\Controller;
use Home\Common\HomeController;
class DescController extends HomeController {
    public function index(){
        $doc_id = I('get.doc_id',0,'intval');
        $result = D('Doc')->get($doc_id,1);
        if(empty($result)) {
            $this->error('not found');
        }
        $this->upViews($doc_id);
        $this->assign('result',$result);
        $this->assign('site_title',$result['title'].' - '.C('site.name'));
        if(!empty($result['keywords'])) $this->assign('site_keywords',$result['keywords']);

        $page = 1;
        $pagesize = 200; //默认取两百条数据出来 先不做分页
        $discuss = D('DocDiscuss')->getDiscussByDocID($doc_id,$page,$pagesize);
        $this->assign('discuss',json_encode($discuss));

        $this->display();
    }
    private function upViews($doc_id) {
        $cookieKey = 'view_doc_id';
        $doc_ids = cookie($cookieKey);
        $doc_id_arr = array();
        if(!empty($doc_ids)) {
            $doc_id_arr = explode(',', $doc_ids);
        }
        if(!in_array($doc_id, $doc_id_arr)) {
            $doc_id_arr[] = $doc_id;
            cookie($cookieKey,implode(',', $doc_id_arr),86400);
            D('Doc')->upViews($doc_id);
        }
    }
}