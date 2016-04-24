<?php
namespace Home\Controller;
use Home\Common\HomeController;
class IndexController extends HomeController {
    public function index(){
        $field = 'doc_id,title,createtime';
        $result = D('Doc')->field($field)->order('createtime desc')->where(array('state'=>2))->limit(C('site.page_size'))->select();
        $this->assign('result',$result);
        $this->display();
    }
}