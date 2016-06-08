<?php
namespace Home\Model;
use Common\Model\BaseModel;
use \Org\Util\String;
use \Org\Tool\Tool;
class UcenterModel extends BaseModel{
    protected $_link = array(
        'Uinfo'=>array(
            'mapping_type' => self::HAS_ONE,
            'foreign_key'   => 'uid',
        )
    );
    public function getUinfoByEmail($email) {
        $result = $this->field('uid,ban')->where(array('email'=>$email))->relation(true)->find();
        if(empty($result)) return $result;
        $result = array_merge($result,$result['Uinfo']);
        unset($result['Uinfo']);
        return $result;
    }
    public function addUser($email,$passwd,$ban,$nickname) {
        $data = array();
        $data['email'] = $email;
        $data['salt'] = String::randString();
        $data['passwd'] = Tool::passwd($passwd,$data['salt']);
        $data['ban'] = $ban;
        $data['regtime'] = date('Y-m-d H:i:s');
        $data['regip'] = get_client_ip();
        $data['Uinfo'] = array('email'=>$email,'nickname'=>$nickname,'utype'=>1);

        return $this->relation(true)->add($data);
    }
    public function getEmailByUid($uid) {
        return D('Ucenter')->where(array('uid'=>$uid,'ban'=>1))->getField('email');
    }
}