<?php
$arr = ['oname'=>'xx图片','url'=>'/staticData/test.png'];

if ($_FILES["file"]["error"] > 0) {
    alert(1,'上传失败','');
}else{
    //print_r($_FILES);exit;
    $rootPaht = '../staticData';
    $fileName = uniqid().'.'.strtolower(pathinfo($_FILES["file"]["name"],PATHINFO_EXTENSION));
    $filePath = date('Ymd');
    $rootUrl = '/staticData';
    mkdirs($rootPaht.'/'.$filePath);
    move_uploaded_file($_FILES["file"]["tmp_name"],$rootPaht.'/'.$filePath.'/'.$fileName);
    alert(0,'成功',['name'=>pathinfo($_FILES["file"]["name"],PATHINFO_BASENAME),'url'=>$rootUrl.'/'.$filePath.'/'.$fileName]);
}

/**
 * 递归创建目录
 */
function mkdirs($dir) {
    if (!is_dir($dir)) {
        if (!mkdirs(dirname($dir))) {
            return false;
        }
        if (!mkdir($dir, 0777)) {
            return false;
        }
    }
    return true;
}

/**
 * 返回数据
 * code 成功=0 失败>0
 */
function alert($code,$msg,$data) {
    $arr = array('code'=>$code, 'msg'=>$msg, 'data'=>$data);
    exit(json_encode($arr));
}

