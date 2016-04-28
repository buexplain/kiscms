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
    	$right_top_menu[] = array('首页',C('site.http_host'),'_blank');
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
        $result = D('Node')->getNodeByRoleId($role_ids,array('is_nav'=>1),'en_name,zh_name,pid,type');
        $result = CategoryArray::child($result,2,'node_id'); //只支持单个模块的菜单显示
        $new_result = array();
        foreach ($result as $key => $value) {
            $tmp = array();
            $tmp['id'] = $value['node_id'];
            $tmp['pId'] = $value['pid'];
            $tmp['name'] = $value['zh_name'];
            $tmp['open'] = true;
            $new_result[] = $tmp;
            if(count($value['son']) > 0) {
                foreach ($value['son'] as $key2 => $value2) {
                    $tmp = array();
                    $tmp['id'] = $value2['node_id'];
                    $tmp['pId'] = $value2['pid'];
                    $tmp['name'] = $value2['zh_name'];
                    if($value2['type'] == 2) {
                        $tmp['url'] = U($value2['en_name'].'/list'.$value2['en_name']);
                    }else{
                        $tmp['url'] = U($value['en_name'].'/'.$value2['en_name']);
                    }
                    $tmp['target'] = 'boxcontent';
                    $new_result[] = $tmp;
                }
            }
        }
        $this->assign('sidebartree',json_encode($new_result));
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