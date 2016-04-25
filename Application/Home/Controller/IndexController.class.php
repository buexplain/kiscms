<?php
namespace Home\Controller;
use Home\Common\HomeController;
class IndexController extends HomeController {
    public function index(){
        $result = D('Doc')->getIndexList();
        $this->assign('result',$result);
        $this->display();
    }
}