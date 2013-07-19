<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

if (! function_exists ( 'htmlerror' )) {
	function htmlerror($arr, $showkey = false, $class = "alert") {
		if (! is_array ( $arr ) || empty ( $arr ))
			return '';
		
		$dd = '';
		if (! $showkey)
			$dd .= '<ul><li>' . implode ( '</li><li>', array_values ( $arr ) ) . '</li></ul>';
		else
			foreach ( $arr as $k => $v )
				if (is_array ( $v ) && ! empty ( $v ))
					$dd .= $k . '<ul><li>' . implode ( '</li><li>', $v ) . '</li></ul>';
		return '<dl class="' . $class . '"><dt></dt><dd>' . $dd . '</dd></dl>';
	}
}

if (! function_exists ( 'htmlinput' )) {
	function htmlinput($type, $name, $value = '', $option = '') {
		return '<input type="' . $type . '" name="' . $name . '" id="' . $name . '" value="' . $value . '" ' . $option . ' />';
	}
}

if (! function_exists ( 'htmlhide' )) {
	function htmlhide($name, $value = '', $option = '') {
		return '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" ' . $option . ' />';
	}
}
if (! function_exists ( 'htmltext' )) {
	function htmltext($name, $value = '', $option = '') {
		return '<input type="text" name="' . $name . '" id="' . $name . '" value="' . $value . '" ' . $option . ' />';
	}
}
if (! function_exists ( 'htmlpass' )) {
	function htmlpass($name, $value = '', $option = '') {
		return '<input type="password" name="' . $name . '" id="' . $name . '" value="' . $value . '" ' . $option . ' />';
	}
}
if (! function_exists ( 'htmlarea' )) {
	function htmlarea($name, $value = '', $option = '') {
		return '<textarea name="' . $name . '" id="' . $name . '" ' . $option . '>' . $value . '</textarea>';
	}
}
if (! function_exists ( 'htmlimg' )) {
	function htmlimg($src = '', $options = '') {
		return '<img src="' . $src . '" ' . $options . ' />';
	}
}
if (! function_exists ( 'htmla' )) {
	function htmla($href = '', $content = '', $options = '') {
		return '<a href="' . $href . '" ' . $options . ' >' . $content . '</a>';
	}
}
if (! function_exists ( 'htmlselect' )) {
	function htmlselect($name, $array, $value = '', $option = '') {
		$data = '<select id="' . $name . '" name="' . $name . '" ' . $option . '>';
		if (is_array ( $array ))
			foreach ( $array as $k => $v )
				if (is_array ( $v ))
					$data .= '<option ' . (($value == $k) ? 'selected="selected"' : '') . ' value="' . $k . '" ' . $v [1] . '>' . $v [0] . '</option>';
				else
					$data .= '<option ' . (($value == $k) ? 'selected="selected"' : '') . ' value="' . $k . '">' . $v . '</option>';
		$data .= '</select>';
		return $data;
	}
}
if (! function_exists ( 'htmlselectlist' )) {
	function htmlselectlist($name, $array, $value = '', $option = '') {
		if (! is_array ( $array ))
			return '';
		return htmlselect ( $name, array_combine ( $array, $array ), $value, $option = '' );
	}
}

if (! function_exists ( 'htmlradio' )) {
	function htmlradio($name, $value = '', $check = false, $option = '') {
		return '<input type="radio" id="' . $name . '" name="' . $name . '" value="' . $value . '" ' . $option . ' ' . (($check) ? 'checked' : '') . ' />';
	}
}

if (! function_exists ( 'htmlcheck' )) {
	function htmlcheck($name, $value = '', $check = false, $option = '') {
		return '<input type="checkbox" id="' . $name . '" name="' . $name . '" value="' . $value . '" ' . $option . ' ' . (($check) ? 'checked="checked"' : '') . ' />';
	}
}

if (! function_exists ( 'htmlpagging' )) {
	function htmlpagging($current, $totalpage, $prefix, $options = array()) {
		$options = array_merge ( array (
				'report' => 'Trang: %s of %s',
				'first' => '|<',
				'prev' => '<<',
				'next' => '>>',
				'last' => '>|',
				'numpage' => 5 
		), $options );
		
		$rs = '';
		if ($totalpage <= 1)
			return $rs;
		
		if ($totalpage <= $options ['numpage']) {
			$from = 1;
			$to = $totalpage;
		} else {
			if ($current < ceil ( $options ['numpage'] / 2 )) {
				$from = 1;
				$to = $options ['numpage'];
			} else if ($current > $totalpage - (ceil ( $options ['numpage'] / 2 ) - 1)) {
				$to = $totalpage;
				$from = ($totalpage - $options ['numpage']) + 1;
			} else {
				$from = $current - (ceil ( $options ['numpage'] / 2 ) - 1);
				$to = $current + (ceil ( $options ['numpage'] / 2 ) - 1);
			}
		}
		
		// $rs .= '<li><span
		// class="report">'.sprintf($options['report'],$current,$totalpage).'</span>
		// ';
		
		if ($current == 1) {
			$rs .= '<li><a href="javascript:void();" >' . $options ['first'] . '</a> ';
			$rs .= '<li><a href="javascript:void();" >' . $options ['prev'] . '</a> ';
		} else {
			$rs .= '<li><a href="' . str_replace ( '[x]', 1, $prefix ) . '" >' . $options ['first'] . '</a> ';
			$rs .= '<li><a href="' . str_replace ( '[x]', $current - 1, $prefix ) . '" class="button buttonprev">' . $options ['prev'] . '</a> ';
		}
		
		for($from; $from <= $to; $from ++)
			if ($current == $from)
				$rs .= '<li  ><a href="javascript:void();" class="active" >' . $from . '</a> ';
			else
				$rs .= '<li><a href="' . str_replace ( '[x]', $from, $prefix ) . '">' . $from . '</a> ';
		
		if ($current == $totalpage) {
			$rs .= '<li><a href="javascript:void();" class="button buttonnext disable">' . $options ['next'] . '</a> ';
			$rs .= '<li><a href="javascript:void();" class="button buttonlast disable">' . $options ['last'] . '</a> ';
		} else {
			$rs .= '<li><a href="' . str_replace ( '[x]', $current + 1, $prefix ) . '" class="button buttonnext">' . $options ['next'] . '</a> ';
			$rs .= '<li><a href="' . str_replace ( '[x]', $totalpage, $prefix ) . '" class="button buttonlast">' . $options ['last'] . '</a> ';
		}
		
		return $rs;
	}
}

if (! function_exists ( 'htmlopenbox' )) {
	function htmlopenbox($k) {
		return '<div class="box' . $k . 'head"><span></span></div><div class="box' . $k . 'body"><div class="box' . $k . 'bg">';
	}
}
if (! function_exists ( 'htmlclosebox' )) {
	function htmlclosebox($k) {
		return '</div></div><div class="box' . $k . 'foot"><span></span></div>';
	}
}

if (! function_exists ( 'htmlaction' )) {
	function htmlaction($actions, $before = '', $after = '') {
		$rs = '';
		foreach ( $actions as $action )
			$rs .= '<a ' . @$action [3] . ' href="javascript:void()void(0);" onClick="' . $before . $action [2] . $after . '" title="' . $action [1] . '">' . $action [0] . '<span>' . $action [1] . '</span></a>';
		return $rs;
	}

}