<?php
namespace Home\Controller;
use Home\Common\HomeController;
use \Think\Page;
class TimeFileController extends HomeController {
    public function index(){
        $time = I('get.time','');
        $time = strtotime($time);
        if(!$time) $this->error();
        $time_end = date('Y-m-d H:i:s',$time + 86399);
        $time = date('Y-m-d H:i:s',$time);
        $where = array();
        $where['state'] = 2;
        $where['createtime'] = array('between',array($time,$time_end));
        $counter = D('Doc')->where($where)->count();
        $page_size = C('site.page_size') * 3;
        $page = new Page($counter,$page_size);
        $field = 'doc_id,title,createtime';
        $result  = D('Doc')->field()->limit($page->firstRow.','.$page->listRows)->order('createtime desc')->where($where)->select();
        //echo D('Doc')->getLastSql();
        foreach ($result as $key => $value) {
            $result[$key]['content'] = htmlspecialchars_decode($value['content']);
        }
        $this->assign('result',$result);
        $this->assign('site_title','归档 - '.C('site.name'));
        $this->assignPage($page,$page_size);
        $this->display();
    }
}