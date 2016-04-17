<?php
namespace Admin\Model;
use Admin\Common\BaseModel;
use \Org\Util\String;
use \Org\Tool\Tool;
use \Org\Form\CheckForm;
class UcenterModel extends BaseModel{
    protected $_validate = array(
        array('email',"isEmail",'请填写正确格式的邮箱地址',1,'callback'),
        array('email',"emailIsNotRepeat",'邮箱地址已存在',1,'callback'),
        array('passwd','6,50','密码太短，密码长度请大于六',1,'length'),
        array('ban',array(1,2,3),'请选择状态',1,'in'),
    );
    /**
     * 判断邮箱号是否重复
     */
    public function emailIsNotRepeat($email,$uid=0) {
        $where = array('email'=>$email);
        if($uid > 0) $where['uid'] = array('NEQ',$uid);
        $uid = $this->where($where)->getField('uid');
        if(empty($uid)) return true;
        return false;
    }
    /**
     * 添加用户
     */
    public function addUser($email,$passwd,$ban) {
        $ucenter_data = array('email'=>$email,'ban'=>$ban);
        $ucenter_data['salt'] = String::randString();
        $ucenter_data['passwd'] = Tool::passwd($passwd,$ucenter_data['salt']);
        $ucenter_data['regtime'] = date('Y-m-d H:i:s');
        $ucenter_data['regip'] = get_client_ip();
        $this->startTrans();
        $result = $this->where(array('uid'=>$uid))->data($ucenter_data)->add();
        if($result) {
            $result = M('Uinfo')->data(array('email'=>$email,'uid'=>$result))->add();
        }
        if(!$result) {
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }
    /**
    * 根据邮箱 或 手机号码 或 uid 查找用户
    */
    public function get($mixed,$field='*') {
		if(stripos($mixed, '@') === false) {
            $where = array('uid'=>$mixed);
		}else{
			$where = array('email'=>$mixed);
		}
		return $this->where($where)->field($field)->find();
    }
    public function getFieldByUid($uid,$field) {
        return $this->where(array('uid'=>$uid))->getField($field);
    }
    /**
     * 设置新密码
     */
    public function setPasswd($passwd,$uid) {
        $ucenter_data = array();
        $ucenter_data['salt'] = String::randString();
        $ucenter_data['passwd'] = Tool::passwd($passwd,$ucenter_data['salt']);
        return $this->where(array('uid'=>$uid))->data($ucenter_data)->save();
    }
    /**
     * 禁封帐号
     */
    public function setBan($uid) {
        $result = $this->get($uid,'ban');
        if(empty($result)) return false;
        $ban = 1;
        if($result['ban'] == 1) $ban = 2;
        return $this->where(array('uid'=>$uid))->data(array('ban'=>$ban))->save();
    }
}