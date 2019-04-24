<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * 过滤内容转化为文本 I函数可选 用法 Input('post.name','','htmltotxt')
 * @author Huliangming<215628355@qq.com>
 * @param $string
 * @return string
 */
function htmltotxt($string)
{    
    $string = htmlspecialchars_decode($string);//先 反转为正常html
	$string = str_replace ( '"', '＂', $string );	
    $string = str_replace ( "'", '＇', $string );
    $string = htmlspecialchars(strip_tags ( $string ));     
    return $string;
}
/**
 * 过滤内容转化为数字,支持长数字 I函数可选 用法 I('post.name','','_int')
 * @author Huliangming<215628355@qq.com>
 * @param $string
 * @return string
 */
function _int($string)
{   
    $string = explode('.',$string);
    $string = preg_replace('/[^\d]+/isu','',$string[0]);
    if(empty($string)) $string = 0;
    return  $string;  
}
/**
 * 将数字限定在 指定 范围
 * @author Huliangming<215628355@qq.com>
 * @param $string
 * @return string
 */
// function int_range($number,int $min=0,int $max=5)
// {   
//     $number = intval($number);
//     if($number < $min) $number = $min;
//     if($number > $max) $number = $max;
//     return $number;  
// }
/**
 * 将数字限定在 0-5 范围 适用于评论分数
 * @author Huliangming<215628355@qq.com>
 * @param $string
 * @return string
 */
function _int05($string)
{   
    $string = int_range($string,0,5);
    return $string;  
}

/**
 * 去掉所有特殊字符
 * @author Huliangming<215628355@qq.com>
 * @param $string
 * @return string
 */
function _special($string){
    
     $string = preg_replace("/[ '.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/isu",'',$string);
     return  $string;
}
/**
 * 去掉所有逗号间隔的关键字里的特殊字符,并去掉重复值和空值
 * @author Huliangming<215628355@qq.com>
 * @param $string
 * @return string
 */
function _keywords($keywords,$split=","){
	$arr = explode(',',$keywords);
	$arr = array_unique($arr);
    foreach($arr as $k=>$val){
		$arr[$k] = _special($val);
		if(!$arr[$k]) unset($arr[$k]);
	}
	$keywords = join($split,$arr);
	return $keywords ;
}

/*
 * 过虑一个逗号间隔的字符串，将其转换为数组，并去掉重复值和空值
 * @author Huliangming<215628355@qq.com>
 * @param $string
 * @return array
 */
function str2Arr($string,$empty=true){
    if(is_array($string)) return $string;
    $string = explode(',',$string);
    $string = array_unique($string);
    if(!$empty)return $string;
    foreach($string as $k=>$val){
        if(empty($val)) unset($string[$k]);        
    }
    return $string;    
}
/*
 * 强制转换为数组，哪怕没有定义
 * @author Huliangming<215628355@qq.com>
 * @param $string
 * @return array
 */
function _array($name){        
        
    return isset($name) ? (array)$name : array();
}

/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @author Huliangming<215628355@qq.com>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
* @param mixed  $isarr 是否支持数组过滤
 * @return mixed
 */
 // function I($name,$default='',$filter=null,$isarr=false) {       
 //      if($isarr){          
 //          return input($name.'/a',$default,$filter);
 //      }      
 //      return input($name.'/s',$default,$filter);
 // }
 //格式化文件大小
 function sizecount($filesize) {
     if($filesize >= 1073741824) {
      $filesize = round($filesize / 1073741824 * 100) / 100 . ' gb';
     } elseif($filesize >= 1048576) {
      $filesize = round($filesize / 1048576 * 100) / 100 . ' mb';
     } elseif($filesize >= 1024) {
      $filesize = round($filesize / 1024 * 100) / 100 . ' kb';
     } else {
      $filesize = $filesize . ' bytes';
     }
     return $filesize;
}