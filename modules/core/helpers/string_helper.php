<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('isUrlImg'))
{
	function isUrlImg($url)
	{
		$url = strtolower($url);
		$pattern = '#^(http://|https://){1}(www)?(.*)\.(jpg|png|gif|ico|JPG|PNG|GIF|ICO){1}$#';
		return preg_match($pattern,$url);
	}
}
if(!function_exists('isPathImg'))
{
	function isPathImg($path)
	{
		$pattern = '#^([a-zA-Z0-9\/\-_]+)\.(jpg|png|gif|ico|JPG|PNG|GIF|ICO){1}$#';
		return preg_match($pattern,$path);
	}
}
if(!function_exists('stringsummary'))
{
	function stringsummary($str,$number = 165)
	{
		$str = preg_replace('/\s+/',' ',trim(strip_tags($str)));
		if(empty($str[$number])) return $str;
		if($str[$number] != ' '){
		for($i = $number; ($str[$i]!=' ') && $i>0;$i--);
		if($i < 1) return $str;
		return substr($str,0,$i);
		}
		return substr($str,0,$number);
	}
}
if(!function_exists('stringmatch'))
{
	function stringmatch($reg,$str)
	{
		return @preg_match('/'.addcslashes($reg,'/').'/',$str);
	}
}


if(!function_exists('stringrandom'))
{
	function stringrandom($arr,$length)
	{
		$key = null;
		for($i=0;$i<$length;$i++)
			$key .= array_rand($arr);
		return $key;
	}
}

if(!function_exists('stringparse'))
{
	function stringparse($arr,$str)
	{
		if(!is_array($arr)) return $str;
		foreach($arr as $k=>$v)
			$str = @str_replace('{'.$k.'}',$v,$str);
		return $str;
	}
}
if (! function_exists('stringend'))
{
	function stringend($str,$end)
	{
		return preg_match('/'.$end.'$/i',$str);
	}
}
if (! function_exists('stringbegin'))
{
	function stringbegin($str,$begin)
	{
		return preg_match('/^'.str_replace("/","\\/",str_replace("\\","\\\\",$begin)).'/i',$str);
	}
}
if (! function_exists('stringaccent'))
{
	function stringaccent($str)
	{
		$str = preg_replace('/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/','A',$str);
		$str = preg_replace('/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/','E',$str);  
		$str = preg_replace('/Ì|Í|Ị|Ỉ|Ĩ/','I',$str);
		$str = preg_replace('/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/','O',$str);
		$str = preg_replace('/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/','U',$str);
		$str = preg_replace('/Ỳ|Ý|Ỵ|Ỷ|Ỹ/','Y',$str);
		$str = preg_replace('/Đ/','D',$str);
		
		$str = preg_replace('/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/','a',$str);
		$str = preg_replace('/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/','e',$str);  
		$str = preg_replace('/ì|í|ị|ỉ|ĩ/','i',$str);  
		$str = preg_replace('/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/','o',$str);
		$str = preg_replace('/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/','u',$str);  
		$str = preg_replace('/ỳ|ý|ỵ|ỷ|ỹ/','y',$str);  
		$str = preg_replace('/đ/','d',$str);
		return $str;
	}
}
if (! function_exists('stringseo'))
{
	function stringseo($str)
	{
    return strtolower(preg_replace("/[\s_]/", "-",trim(preg_replace("/[\s-]+/", " ",preg_replace("/[^a-zA-Z0-9_\s-]/", "",stringaccent($str))))));
	}
}
if (! function_exists('stringlimit'))
{
	function stringlimit($str, $len,$strend) {
    $str = trim($str);
    if (strlen($str) <= $len) return $str;
    $str = substr($str, 0, $len);
    if ($str != "") {
        if (!substr_count($str, " ")) return $str." ...";
        while (strlen($str) && ($str[strlen($str) - 1] != " ")) $str = substr($str, 0, -1);
        $str = substr($str, 0, -1);
    }
    return $str.$strend;
} 
}

if (! function_exists('truncateString_'))
{
	function truncateString_($str, $len, $charset="UTF-8"){
        $str = html_entity_decode($str, ENT_QUOTES, $charset);   
        if(mb_strlen($str, $charset)> $len){
            $arr = explode(' ', $str);
            $str = mb_substr($str, 0, $len, $charset);
            $arrRes = explode(' ', $str);
            $last = $arr[count($arrRes)-1];
            unset($arr);
            if(strcasecmp($arrRes[count($arrRes)-1], $last))   unset($arrRes[count($arrRes)-1]);
          return implode(' ', $arrRes)."...";   
       }
        return $str;
	}
}
?>