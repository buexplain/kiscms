<?php
if(!defined('THINK_PATH')) exit('非法调用');//防止被外部系统调用
/**
 * 获取分页大小
 */
function pageSize() {
    $size = I('cookie.pageSize',0,'intval');
    $pageSizeArr = C('pageSizeArr');
    if(isset($pageSizeArr[$size])) return $pageSizeArr[$size];
    return $pageSizeArr[1];
}
/**
 * 输出一个select下拉
 */
function htmlSelectOption($current,$arr) {
    $html = '';
    foreach ($arr as $key => $value) {
        if($key == $current) {
            $html .= '<option selected="selected" value="'.$key.'">'.$value.'</option>';
        }else{
            $html .= '<option value="'.$key.'">'.$value.'</option>';
        }
    }
    return $html;
}
/**
 * 过滤逗号分割的数字字符串为逗号分割的纯数字字符串
 * @param  $num_str 例：filterNumStr('1,2，3,a5，b0') == '1,2,3,0'
 */
function filterNumStr($num_str,$is_return_array=false) {
    $num_str = array_unique(array_map('intval',explode(',', str_replace('，',',',$num_str))));
    if($is_return_array) return $num_str;
    return implode(',',$num_str);
}
/**
 * 删除一维数组中特定元素值的元素
 */
function unsetArrayByvalue($array,$mixed,$keep_key=false) {
    if($keep_key) return array_diff_key($array,array_flip(array_keys($array,$mixed)));
    return array_diff($array, array($mixed));
}
/**
 * 隐藏字符串的中间部分
 * 例：hideCenterStr('刘备'); // 刘*
 */
function hideCenterStr($str) {
    preg_match_all("/./u", $str, $arr);
    $count = count($arr[0]);
    $hideNum =  ceil($count / 3);
    foreach ($arr[0] as $key => $value) {
        if($key >= ($count/2 - $hideNum/2) && $key < ($count/2 + $hideNum/2)) $arr[0][$key] = '*';
    }
    return implode('',$arr[0]);
}
