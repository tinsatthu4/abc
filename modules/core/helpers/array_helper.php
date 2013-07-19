<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

if (! function_exists ( 'arraygetlist' )) {
	function arraygetlist($arr, $cols, $default = false) {
		$data = array ();
		if (empty ( $arr ) || empty ( $cols ))
			return $data;
		foreach ( $arr as $k => $v )
			if (in_array ( $k, $cols ) || ($default == true))
				$data [$k] = @$arr [$k];
		return $data;
	}
}

if (! function_exists ( 'arraypagging' )) {
	function arraypagging($arr, $pp, $current) {
		$data = array ();
		if (empty ( $arr ))
			return $data;
		foreach ( $arr as $k => $row )
			$data [(($k + 1) + ($pp * ($current - 1)))] = $row;
		return $data;
	}
}

if (! function_exists ( 'arraydate' )) {
	function arraydate($time) {
		return array (
				'h' => date ( "h", $time ),
				'H' => date ( "H", $time ),
				'i' => date ( "i", $time ),
				's' => date ( "s", $time ),
				
				'Y' => date ( "Y", $time ),
				'y' => date ( "y", $time ),
				
				'm' => date ( "m", $time ),
				'F' => date ( "F", $time ),
				
				'l' => date ( "l", $time ),
				'D' => date ( "D", $time ),
				'd' => date ( "d", $time ) 
		);
	}
}

if (! function_exists ( 'arraymake' )) {
	function arraymake($arr, $col, $val) {
		$rs = array ();
		if (is_array ( $arr ))
			foreach ( $arr as $row )
				$rs [@$row [$col]] = @$row [$val];
		return $rs;
	}
}
if (! function_exists ( 'arraymakes' )) {
	function arraymakes($arr, $col, $val) {
		$rs = array ();
		if (is_array ( $arr ))
			foreach ( $arr as $row )
			if(is_array($val))
			{
				$rs[@$row[$col]] = array();
				foreach($val as $value)
				$rs[@$row[$col]] = arraymerkey($rs[@$row[$col]],array(
								$value=>@$row[$value]
									));
			}
			else
			$rs [@$row [$col]] = @$row [$val];
		return $rs;
	}
}

if (! function_exists ( 'arrayget' )) {
	function arrayget($arr, $col) {
		$rs = array ();
		
		if (empty ( $arr ) || empty ( $col ))
			return array ();
		
		foreach ( $arr as $row )
			$rs [$row [$col]] = 1;
		
		return array_keys ( $rs );
	}
}

if (! function_exists ( 'arrayreplace' )) {
	function arrayreplace($arr, $col, $arrindex) {
		
		if (empty ( $arr ) || empty ( $col ) || empty ( $arrindex ))
			return array ();
		
		foreach ( $arr as &$row )
			$row [$col] = @$arrindex [$row [$col]];
		
		return $arr;
	}
}

if (! function_exists ( 'arrayfilter' )) {
	function arrayfilter($arr, $col, $con) {
		$data = array ();
		
		if (empty ( $col ))
			return $arr;
		
		if (! is_array ( $col ))
			$col = array (
					$col 
			);
		
		$ope = substr ( $con, 0, 1 );
		
		if (is_array ( $arr ))
			foreach ( $arr as $row ) {
				$str = $row [$col [0]];
				for($i = 1; $i < count ( $col ); $i ++)
					$str = @$str [$col [$i]];
				switch ($ope) {
					case '<' :
						if (floatval ( $str ) < floatval ( substr ( $con, 1, count ( $con ) ) ))
							$data [] = $row;
						break;
					case '>' :
						if (floatval ( $str ) > floatval ( substr ( $con, 1, count ( $con ) ) ))
							$data [] = $row;
						break;
					default :
						if ($con == $str)
							$data [] = $row;
						break;
				}
			}
		return $data;
	}
}

if (! function_exists ( 'arraymervalue' )) {
	function arraymervalue($armer, $arvalue) {
		if (is_array ( $arvalue ) && is_array ( $arvalue )) {
			foreach ( $arvalue as $key => $val )
				$armer [$key] = $val;
		}
		return $armer;
	}
}

if (! function_exists ( 'arraymerkey' )) {
	function arraymerkey() {
		$data = array ();
		$lstarr = func_get_args ();
		if (is_array ( $lstarr ))
			foreach ( $lstarr as $arr )
				if (is_array ( $arr ))
					foreach ( $arr as $k => $v )
						$data [$k] = $v;
		return $data;
	}
}
if (! function_exists ( 'arraymer' )) {
	function arraymer() {
		$data = array ();
		$lstarr = func_get_args ();
		if (is_array ( $lstarr ))
			foreach ( $lstarr as $arr )
				if (is_array ( $arr ))
					$data = array_merge ( $data, $arr );
		return $data;
	}
}
if (! function_exists ( 'arraysub' )) {
	function arraysub($rows, $offset, $pp) {
		$rs = array ();
		if (! is_array ( $rows ) || empty ( $rows ))
			return $rs;
		$total = count($rows);
		$pp = ($pp+$offset) < $total ? ($pp+$offset) : $total;
		
		for($from = $offset; $from < $pp; $from ++) {
			if (! empty ( $rows [$from] ))
				$rs [] = $rows [$from];
		}
		return $rs;
	}
}
if (! function_exists ( 'arraysort' )) {
	function arraysort($array, $on, $order = SORT_ASC) {
		$new_array = array ();
		$sortable_array = array ();
		if (count ( $array ) > 0) {
			foreach ( $array as $k => $v ) {
				if (is_array ( $v )) {
					foreach ( $v as $k2 => $v2 ) {
						if ($k2 == $on) {
							$sortable_array [$k] = $v2;
						}
					}
				} else {
					$sortable_array [$k] = $v;
				}
			}
			switch ($order) {
				case SORT_ASC :
					asort ( $sortable_array );
					break;
				case SORT_DESC :
					arsort ( $sortable_array );
					break;
			}
			
			foreach ( $sortable_array as $k => $v ) {
				$new_array [$k] = $array [$k];
			}
		}
		return $new_array;
	}
}

if(!function_exists('xmlToArray'))
{
	function xmlToArray($pathFile)
	{
		$xml = @simplexml_load_file($pathFile);
		$arr = @json_decode(@json_encode($xml),true);
		return $arr;
	}
}