<?php
namespace Home\Controller;
use Home\Common\HomeController;
use \Think\Page;
class TimeFileController extends HomeController {
    public function index(){
        $time = $shortTime = I('get.time','');
        $time = strtotime($time);
        if(!$time) $this->error();
        $time_end = date('Y-m-d H:i:s',mktime(0, 0, 0, date("m",$time)+1, date("d",$time),   date("Y",$time)));
        $time = date('Y-m-d H:i:s',$time);
        $where = array();
        $where['state'] = 2;
        $where['createtime'] = array('between',array($time,$time_end));
        $counter = D('Doc')->where($where)->count();
        $pageSize = C('site.pageSize') * 5;
        $page = new Page($counter,$pageSize);
        $field = 'doc_id,title,createtime';
        $result  = D('Doc')->field()->limit($page->firstRow.','.$page->listRows)->order('createtime desc')->where($where)->select();
        //echo D('Doc')->getLastSql();
        foreach ($result as $key => $value) {
            $result[$key]['content'] = htmlspecialchars_decode($value['content']);
        }
        $this->assign('result',$result);
        $this->assign('site_title',"{$shortTime}归档 - ".C('site.name'));
        $this->assignPage($page,$pageSize);
        $this->display();
    }
}