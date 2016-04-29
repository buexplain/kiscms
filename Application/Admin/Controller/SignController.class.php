<?php
namespace Admin\Controller;
use \Admin\Common\BaseController;
use \Think\Verify;
use \Org\Form\CheckForm;
use \Org\Tool\Tool;
use \Think\Hook;
/**
 * 后台登录类
 * @author buexplain
 */
class SignController extends BaseController {
	public $verification_id = 'login';
    public $sign_api = 1;
    public $sign_log = 'sign_log';
	public $verify;
	public function _initialize() {
		$this->verify = new Verify();
        Hook::add($this->sign_log,"Common\Behaviors\SignLogBehavior");
	}
    public function index(){
    	//如果已经登录 直接跳转到后台页面
    	if(Tool::isLogin()) $this->redirect('Index/index');
    	if(IS_POST) {
    		$email = I('post.email','');
    		$passwd = I('post.passwd','');
    		$verification = I('post.verification','');
    		//检查帐号
    		$result = $this->checkEmail($email);
            if(!$result) {
                //检查密码
                $result = $this->checkPasswd($passwd);
                if(!$result) {
                    //检查验证码
                    $result = $this->checkVerification($verification);
                }
            }
            if($result) $this->error($result);
            //验证
            $field = 'uid,email,passwd,salt,ban';
            $ucenter = D('Ucenter')->get($email,$field);
            if(empty($ucenter)) $this->error('帐号错误');
            //是否被禁封
            if($ucenter['ban'] == 2) $this->error('帐号已封，禁止登录');
            //帐号与密码
            if($ucenter['passwd'] != Tool::passwd($passwd,$ucenter['salt'])) $this->error('密码错误');
            //获取用户信息
            $ucenter = array_merge($ucenter,D("Uinfo")->get($ucenter['uid']));
            if($ucenter['utype'] != 2) $this->error('非我司人员，禁止访问');
            $this->setLoginInfo($ucenter);
            Hook::listen($this->sign_log,$this->sign_api);
            //跳转到后台首页
            $this->success(U('Index/index'));
    	}else{
    		$this->assign('site',C('site'));
            $this->display();
    	}
    }
    /**
     * 初始化登录信息
     */
    private function setLoginInfo($ucenter) {
        //变幻session_id
        session('regenerate');
        //写入登录标识
        session(C('USER_AUTH_KEY'),$ucenter['uid']);
        /*写入用户类型标识*/
        session(C('USER_TYPE_KEY'),$ucenter['utype']);
        /*写入其它信息*/
        $callname = $ucenter['nickname'];
        if(empty($callname)) {
            $callname = $ucenter['realname'];
            if(empty($callname)) $callname = $ucenter['email'];
        }
        session('callname',$callname);
    }
    /**
     * 验证邮箱
     */
    private function checkEmail($email) {
        if(empty($email)) return '请输入帐号';
        if(!CheckForm::email($email)) '请输入正确格式的邮箱';
        return '';
    }
    /**
     * 验证验证码
     */
    private function checkVerification($verification) {
        if(empty($verification)) return '请输入验证码';
        if(!$this->verify->check($verification,$this->verification_id)) return '验证码错误';
        return '';
    }
    /**
     * 验证密码
     */
    private function checkPasswd($passwd) {
        if(empty($passwd)) return '请输入密码';
        return '';
    }
    /**
     * 退出登录
     */
    public function loginOut() {
        session('destroy');
    	setcookie(session_name(), '', time() - 3600, '/');
        $this->success(U('Sign/index'));
    }
    /**
     * 验证码生成
     */
    public function verify(){
        $this->verify->length = 4;
        $this->verify->imageW = 150;
        $this->verify->imageH = 43;
        $this->verify->fontSize = 20;
        $this->verify->useCurve = false;
        $this->verify->entry($this->verification_id);
    }
}