<?php
namespace Org\Tool;
class Tool {
    /**
     * 生成用户密码
     * @author buexplain
     * @return string
     */
    public static function passwd($string,$salt) {
        return md5(substr(md5($string).md5($salt),16,48));
    }
    /**
     * 判断是否登录
     */
    public static function isLogin() {
        $login_uid = session(C('USER_AUTH_KEY'));
        return !empty($login_uid);
    }
    /**
     * 判断是否后台登陆
     */
    public static function isAdminLogin() {
        $login_uid = session(C('USER_AUTH_KEY'));
        $user_type_key = session(C('USER_TYPE_KEY'));
        //检查是否登录 && 检查是否允许后台登陆
        if(!empty($login_uid) && $user_type_key == 2) return true;
        return false;
    }
    /**
     * 递归创建文件夹
     */
    public static function mkdirs($dir,$mode=0777,$recursive=true) {
        if (!is_dir($dir)) {
            if (!Tool::mkdirs(dirname($dir),$mode,$recursive)) {
                return false;
            }
            if (!mkdir($dir, $mode,$recursive)) {
                return false;
            }else{
                chmod($dir,$mode);
            }
        }
        return true;
    }
    /**
     * 将 1=男,2=女 格式的字符解析成数组
     */
    public static function parseFormValue($form_value) {
        $result = array();
        try {
            $form_value = explode(',',$form_value);
            foreach ($form_value as $key => $value) {
                $value = explode('=',$value);
                if(isset($value[0]) && isset($value[1])) $result[$value[0]] = $value[1];
            }
        } catch (Exception $e) {

        }
        return $result;
    }
    /**
     * 字节转换
     */
    public static function formatSize($size, $dec=2){
        $a = array('B', 'K', 'M', 'G', 'TB', 'PB');
        $pos = 0;
        while ($size >= 1024) {
            $size /= 1024;
            $pos++;
        }
        return round($size,$dec).$a[$pos];
    }
    /**
     * 带单位的大小转换成字节
     */
    public static function parseSize($str) {
        if(!is_string($str)) return $str;

        $muls = array(
            't'=> 1099511627776,
            'g'=> 1073741824,
            'm'=> 1048576,
            'k'=> 1024,
            'b'=> 1
        );

        $str = strtolower($str);
        $size = intval($str);

        $pattern = "#[tgmkb]#";
        preg_match($pattern, $str,$arr);
        if(isset($arr[0]) && isset($muls[$arr[0]])) {
            $size *= $muls[$arr[0]];
        }

        return $size;
    }
    /**
     * 输出文件
     * @param $filename 文件名称 用于浏览器对话框
     * @param $fileurl 文件路径
     */
    public static function outputFile($filename,$fileurl) {
        $file_handle = fopen($fileurl,"r");
        if($file_handle === false) return '打开文件失败。';
        $filesize = filesize($fileurl);
        if($filesize === false) return '读取文件大小失败。';
        $time_limit = intval(($filesize / 1024) / 100);
        if($time_limit > 60) set_time_limit($time_limit);
        // 设置 header 头
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length:".$filesize);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        if (preg_match("/msie/", strtolower($_SERVER["HTTP_USER_AGENT"]))) {
        header('Content-Disposition: attachment; filename='.rawurlencode($filename).';');
        }else if(preg_match("/firefox/", strtolower($_SERVER["HTTP_USER_AGENT"]))) {
        header('Content-Disposition: attachment; filename*='.$filename.';');
        }else{
        header('Content-Disposition: attachment; filename='.$filename.';');
        }
        header("Content-Transfer-Encoding: binary");
        while (!feof($file_handle)) {
            echo fread($file_handle,1024);
        }
        fclose($file_handle);
        return '';
    }
    /**
     * 根据UserAgent检查用户浏览设备
     * @return pc 默认为PC，wap 手机  wx 微信
     */
    public static function visitDev() {
        static $dev;
        if(!empty($dev)) return $dev;
        $regex_match = "/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
        $regex_match .= "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|meizu|miui|ucweb";
        $regex_match .= "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
        $regex_match .= "symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
        $regex_match .= "jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
        $regex_match .= ")/i";
        $dev = 'pc';
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE']) || (isset($_SERVER['HTTP_USER_AGENT']) && preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT'])))) {
            $dev = 'wap';
        }
        if(isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $dev = 'wx';
        }
        return $dev;
    }
    /**
     * 发送邮件
     */
    public static function sendMail($address,$subject,$body) {
        vendor('phpMailer.PHPMailerAutoload');
        PHPMailerAutoload('phpmailer');
        $mail = new \PHPMailer();

        $mail->isSMTP();
        $mail->Host = C('MAIL_HOST');
        $mail->Port = C('MAIL_PORT');
        $mail->Username = C('MAIL_USERNAME');
        $mail->Password = C('MAIL_PWD');

        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(C('MAIL_FROM'), C('site.title'));
        $mail->addAddress($address);

        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body    = $body;

        if(!$mail->send()) {
            return $mail->ErrorInfo;
        }else{
            return '';
        }
    }
}