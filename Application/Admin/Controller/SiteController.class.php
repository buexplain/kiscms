<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
class SiteController extends BaseController {
    public function listCache() {
        $cache = S(array());
        $result = $cache->clear();
        if($result) {
            $yesNo = 'success';
        }else{
            $yesNo = 'danger';
        }
        $msg = $yesNo;
        $this->assign('msg',$msg);
        $this->assign('yesNo',$yesNo);
        $this->display();
    }
}