<?php
namespace Org\Arrayhelps;
/**
 * 无限级分类的数组处理
 * @author  buexplain
 * @return array
 */
class CategoryArray {
    /**
     * @author buexplain
     * @param  child 方法的返回值
     * @param  回调函数
     * @param  每个子节点的key值
     */
    public static function arrayMapTree($result,$callback,$child='son') {
        if(!is_callable($callback)) return false;
        foreach ($result as $key => $value) {
            call_user_func($callback,$value);
            if(!empty($value[$child])) {
                self::arrayMapTree($value['son'],$callback,$child);
            }
        }
    }
    /**
     * 取分类的所有子分类 返回 树形结构数组
     * @author  buexplain
     * @return array
     */
    public static function child($array, $id, $idname='id',$pidname='pid',$child='son') {
        $tree = array();
        $new_array = array();
        foreach ($array as $key => $value) {
            $new_array[$value[$idname]] =& $array[$key];
        }
        foreach ($array as $key => $value) {
            $pid =  $value[$pidname];
            if ($id == $pid) {
                $tree[] =& $array[$key];
            }else{
                if (isset($new_array[$pid])) {
                    $parent =& $new_array[$pid];
                    $parent[$child][] =& $array[$key];
                }
            }
        }
        return $tree;
    }
    /**
     * 取分类的父分类 返回 平面结构数组
     *  @author  buexplain
     *  @return array
     */
    public static function father($array,$id,$idname='id',$pidname='pid') {
        static $new_array = false;
        if(!$new_array) {
            $new_array = array();
            foreach ($array as $key => $value) {
                $new_array[$value[$idname]] =& $array[$key];
            }
        }
        $top_array = array();
        if ($new_array[$id][$pidname] > 0) {
            $top_array = self::father($new_array,$array[$id][$pidname],$idname='id',$pidname='pid');
        }
        array_push($top_array, $new_array[$id]);
        return $top_array;
    }
    /**
     * 取分类的平级分类 返回 平面结构数组
     * @author  buexplain
     * @return array
     */
    public static function brother($array,$id,$idname='id',$pidname='pid') {
        $brother = array();
        foreach($array as $v) {
            if($v[$pidname] == $array[$id][$pidname]) $brother[] = $v;
        }
        return $brother;
    }
    /**
     * 取分类的一级子分类 返回 平面结构数组
     * @author  buexplain
     * @return array
     */
    public static function son($array,$id,$idname='id',$pidname='pid') {
        $son = array();
        foreach($array as $v) {
            if($v[$pidname] == $id) $son[] = $v;
        }
        return $son;
    }
    /**
     * 取分类的所有子分类，返回平级
     */
    public static function sons($array, $id, $idname='id',$pidname='pid',&$planeArr = array()) {
        foreach ($array as $key => $value) {
            if($id == $value[$pidname]) {
                $planeArr[] = $value;
                self::sons($array, $value[$idname], $idname,$pidname,$planeArr);
            }
        }
        return $planeArr;
    }
    /**
     *  $result = CategoryArray::child($result,0,'id','pid');
     *  $a =  CategoryArray::option($result,'id','title');
     *  echo '<select>';
     *  echo $a;
     *  echo '</select>';
     */
    public static function option($array,$index,$title,$i=0) {
        $string = '';
        $icon = '';
        if($i > 0) {
            for($j=0;$j<$i;$j++) {
                $icon .= '&nbsp;&nbsp;';
            }
            $icon .= '└─&nbsp;';
        }
        foreach ($array as $key => $value) {
            $string .= '<option value="'.$value[$index].'">'.$icon.$value[$title].'</option>';
            $string .= self::option($value['son'],$index,$title,++$i);
        }
        return $string;
    }
}
