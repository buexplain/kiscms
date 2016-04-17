<?php
namespace Home\Controller;
use Home\Common\HomeController;
class IndexController extends HomeController {
    public function index(){
        $result = D('Doc')->order('createtime desc')->where(array('state'=>2))->limit(C('site.page_size'))->select();
        foreach ($result as $key => $value) {
            $result[$key]['content'] = htmlspecialchars_decode($value['content']);
        }
        $this->assign('result',$result);
        $this->display();
    }
}