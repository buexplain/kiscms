<?php
namespace Admin\Controller;
use Admin\Common\BaseController;
use Org\Arrayhelps\CategoryArray;
use Org\Tool\Tool;
class IndexController extends BaseController {
    public function index() {
    	//系统首页
    	$this->assign('main_url',U('Index/main'));
    	//系统右上角菜单
    	$right_top_menu = array();
        $callname = session('callname');
        if($callname) $right_top_menu[] = array("welcome：{$callname}",'','_self');
    	$right_top_menu[] = array('首页',C('site.httpHost'),'_blank');
    	$right_top_menu[] = array('退出',U('Sign/loginOut'),'_self');
    	$this->assign('right_top_menu',$right_top_menu);
        //获取菜单导航
        $this->sidebarTree();
    	$this->display();
    }
    /**
     * 获取左侧菜单导航
     */
    public function sidebarTree() {
        $uid = session(C('USER_AUTH_KEY'));
        $role_ids = D('RoleUser')->getRoleIdByUid($uid,true);
        if(empty($role_ids)) $this->error('你的帐号没有角色！请联系管理员',U('Sign/loginOut'));
        $tmp = D('Node')->getNodeByRoleId($role_ids,array('is_nav'=>1),'en_name,zh_name,pid,type');
        foreach ($tmp as $key => $value) {
            $result[$value['node_id']] = $value;
        }
        $sidebar = array();
        foreach ($result as $key => $value) {
            $tmp = array();
            $tmp['id'] = $value['node_id'];
            $tmp['pId'] = $value['pid'];
            $tmp['name'] = $value['zh_name'];
            $tmp['target'] = 'boxcontent';

            if($value['type'] == 2) {
                $tmp['open'] = true;
            }elseif($value['type'] == 3){
                $tmp['url'] = U($result[$value['pid']]['en_name'].'/'.$value['en_name']);
            }

            $sidebar[] = $tmp;
        }
        //print_r($sidebar);exit;
        $this->assign('sidebartree',json_encode($sidebar));
    }
    /**
     * 后台主页
     */
    public function main() {
        //读取mysql版本
        $tmp = M()->query('select VERSION() as version');
        if(!empty($tmp) && isset($tmp[0]['version'])) {
            $mysqlVersion = $tmp[0]['version'];
        }else{
            $mysqlVersion = '未知';
        }
        $this->assign('mysqlVersion',$mysqlVersion);

        //计算空间大小
        $diskFreeSpace = disk_free_space(ENTRY);
        if($diskFreeSpace) {
            $diskFreeSpace = Tool::formatSize($diskFreeSpace);
        }else{
            $diskFreeSpace = '未知';
        }
        $diskTotalSpace = disk_total_space(ENTRY);
        if($diskTotalSpace) {
            $diskTotalSpace = Tool::formatSize($diskTotalSpace);
        }else{
            $diskTotalSpace = '未知';
        }
        $this->assign('diskFreeSpace',$diskFreeSpace);
        $this->assign('diskTotalSpace',$diskTotalSpace);

        $this->display();
    }
}