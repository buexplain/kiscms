<?php
namespace Home\Controller;
use Home\Common\HomeController;
class SingleController extends HomeController {
    public function index(){
        $cid = I('get.cid',0,'intval');
        $result = D('Doc')->getDocByCid($cid);
        foreach ($result as $key => $value) {
            $result[$key]['content'] = htmlspecialchars_decode($value['content']);
        }
        $this->assign('result',$result);
        $this->assign('site_title',D('DocCategory')->getCnameByCid($cid).' - '.C('site.name'));
        if(!empty($result[0]['keywords'])) $this->assign('site_keywords',$result[0]['keywords']);
        $this->display();
    }
}