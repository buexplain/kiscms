<?php
namespace Admin\Common;
use \Think\Controller;
use \Org\Rbac\Rbac;
use \Org\Tool\Tool;
/**
 * 后台基础类
 * @author buexplain
 */
class BaseController extends Controller{
    private $tVar = array();
    protected $realModule = 'Admin';
    public function _initialize() {
        if(!Tool::isAdminLogin()) $this->redirect('Sign/index');
        /*检查权限*/
        if(!Rbac::checking()) $this->error('权限不够 '.MODULE_NAME.'-->'.CONTROLLER_NAME.'-->'.ACTION_NAME);
        /*iframe页面右侧顶部默认菜单*/
        $iframe_btn_arr = array();
        $iframe_btn_arr[] = array('刷新',U('',I('get.')));
        $iframe_btn_arr[] = array('顶部','#backtop');
        $iframe_btn_arr[] = array('返回','javascript:history.go(- 1);');
        $this->assign('iframe_btn_arr',$iframe_btn_arr);
        $this->assign('btn_arr',array());
        /*站点信息*/
        $this->assign('site',C('site'));
        $this->assign('controller_name',CONTROLLER_NAME);
        $this->assign('action_name',ACTION_NAME);
        /*读取映射模块名*/
        $tmp = array_flip(C('URL_MODULE_MAP'));
        if(!empty($tmp) && isset($tmp[strtolower(MODULE_NAME)])) {
            $this->realModule = ucwords($tmp[strtolower(MODULE_NAME)]);
        }
        $this->assign('realModule',$this->realModule);
    }
    /**
     * 操作错误跳转的快捷方法
     */
    protected function error($msg='error',$data='',$code=1) {
        if(IS_AJAX) {
            $tmp = $this->ajaxData($code,$msg,$data);
            $this->ajaxReturn($tmp);
        }
        parent::error($msg,$data);
    }
    /**
     * 操作成功跳转的快捷方法
     * 第三个参数不用 无效
     */
    protected function success($data='',$msg='success',$void='') {
        if(IS_AJAX) {
            $tmp = $this->ajaxData(0,$msg,$data);
            $this->ajaxReturn($tmp);
        }
        parent::success($msg,$data);
    }
    /**
     * 封装ajax返回格式
     */
    protected function ajaxData($code,$msg,$data) {
        return array(
            'code'=> $code,
            'msg' => $msg,
            'data'=> $data
        );
    }
    /**
     * 开启事务
     */
    protected function startTrans() {
        M()->startTrans();
    }
    /**
     * 回滚事务
     */
    protected function rollback() {
        M()->rollback();
    }
    /**
     * 提交事务
     */
    protected function commit() {
        M()->commit();
    }
    /**
     * 注入分页
     */
    public function assignPage(\Think\Page $page,$pageSize) {
        $this->assign('pageSize',$pageSize);
        $this->assign('page',$page->show());
        $this->assign('totalrows',$page->totalRows);
        $html = '';
        $pageSizeArr = C('pageSizeArr');
        foreach ($pageSizeArr as $key => $value) {
            if($value == $pageSize) {
                $html .= '<option class="hand" selected="selected" onclick="pageSize('.$key.')" value="'.$value.'">'.$value.'页</option>';
            }else{
                $html .= '<option class="hand" onclick="pageSize('.$key.')" value="'.$value.'">'.$value.'页</option>';
            }
        }
        $this->assign('pageSize_html',$html);
    }
    /*配置文件写入*/
    public function insertConfig($filename,$config,$desc='说明：'){
        $config_file = CONF_PATH.$filename.'.php';
        $result = file_put_contents(
            $config_file,
            "<?php \nif(!defined('THINK_PATH')) exit('非法调用');\n//{$desc}\nreturn " . stripslashes(var_export($config, true)) . ";\n?>"
            , LOCK_EX
        );
        if($result === false) return $result;
        return $this->addConfig($filename);
    }
    /**
     * 给系统配置文件添加配置文件扩展
     */
    private function addConfig($filename) {
        //读取 config.php
        $config = file_get_contents(CONF_PATH.'config.php');
        preg_match_all("~LOAD_EXT_CONFIG'.*?'(.*?)'~", $config, $matches);
        if(!isset($matches[1][0])) return false;
        if(stripos($matches[1][0],$filename) === false) {
            $config = str_replace($matches[1][0],$matches[1][0].','.$filename,$config);
            $result = file_put_contents(CONF_PATH.'config.php',$config,LOCK_EX);
            if($result === false) return $result;
        }
        //更新配置到 大C函数
        C(load_config(CONF_PATH.$filename.'.php'));
        return true;
    }

}