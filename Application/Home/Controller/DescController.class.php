<?php
namespace Home\Controller;
use Home\Common\HomeController;
class DescController extends HomeController {
    public function index(){
        $doc_id = I('get.doc_id',0,'intval');
        $result = D('Doc')->get($doc_id,1);
        $this->assign('result',$result);
        $this->assign('site_title',$result['title'].' - '.C('site.name'));
        if(!empty($result['keywords'])) $this->assign('site_keywords',$result['keywords']);
        $this->display();
    }
}