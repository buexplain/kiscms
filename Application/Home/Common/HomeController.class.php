<?php
namespace Home\Common;
use Common\Controller\BaseController;
/**
 * Home基础控制器
 * @author buexplain
 */
class HomeController extends BaseController{
    public function _initialize() {
        $this->assign('nav',$this->nav());
        $this->seo();
        $this->assign('urlRouterOn',C('URL_ROUTER_ON'));
        $this->assign('tag',$this->tag());
        $this->assign('friend',$this->friend());
        $this->assign('cid',I('get.cid',0,'intval'));
        $this->assign('doc_id',I('get.doc_id',0,'intval'));
        $this->assign('time',I('get.time',''));
        $this->assign('timeFile',$this->timeFile());
    }
    /**
     * 生成导航数组
     */
    public function nav() {
        $result = D('DocCategory')->getTopCategory();
        foreach ($result as $key => &$value) {
            if(empty($value['route'])) $value['route'] = 'List';
        }
        return $result;
    }
    /**
     * 标签
     */
    public function tag() {
        return D('DocCategory')->getSonCategory(1);
    }
    /**
     * 归档
     */
    public function timeFile() {
        return D('Doc')->timeFile();
    }
    /**
     * 注入seo
     */
    public function seo() {
        $site = C('site');
        $this->assign('site',$site);
        $this->assign('site_keywords',$site['keywords']);
        $this->assign('site_description',$site['description']);
        $this->assign('site_title',$site['title']);
    }
    /**
     * 友情链接
     */
    public function friend() {
        return C('friend');
    }
}