<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Themes {
	function __construct($items = array()) {
		if (is_array ( $items ))
			foreach ( $items as $k => $v )
				$this->{$k} = $v;
		
		CI::$APP->load->helpers ( array (
				'download',
				'url',
				'file',
				'directory',
				'string',
				'core/dir' 
		) );
		
		CI::$APP->load->libraries ( array (
				'zip',
				'core/Unzip',
				'upload' 
		) );
		
		log_message ( 'debug', 'Themes Class Initialized' );
	}
	function getList($path = '') {
		$list = array ();
		$rows = @directory_map ( CI::$APP->config->item ( 'themes_path' ) . $path, false );
		foreach ( $rows as $key => $row )
			if (is_file ( CI::$APP->config->item ( 'themes_path' ) . $key . '/' . $key . '.php' ))
				$list [$key] = @require (CI::$APP->config->item ( 'themes_path' ) . $key . '/' . $key . '.php');
		return $list;
	}
	function install($fileupload) {
	}
	function uninstall($widget) {
	}
	function download($widget) {
	}
	function view($_view, $_data = array()) {
		foreach ( get_object_vars ( CI::$APP ) as $_key => $_ob )
			$this->{$_key} = & CI::$APP->{$_key};
		
		$_view = trim ( $_view, '/' );
		$_themes = trim ( CI::$APP->appmanager->THEME, '/' ) . '/';
		$_module = @array_shift ( @explode ( '/', $_view ) ) . '/';
		$_module_view = @implode ( '/', @array_slice ( @explode ( '/', $_view ), 1 ) );
		
		// check:application/views/themes
		$_file = is_file ( @$_file ) ? $_file : APPPATH . 'views/' . $_themes . $_view . '.phtml';
		// check:application/views/themes/modules
		$_file = is_file ( @$_file ) ? $_file : APPPATH . 'views/' . $_themes . 'modules/' . $_module . $_module_view . '.phtml';
		// check:themes
		$_file = is_file ( @$_file ) ? $_file : CI::$APP->config->item ( 'themes_path' ) . $_themes . $_view . '.phtml';
		// check:themes/modules
		$_file = is_file ( @$_file ) ? $_file : CI::$APP->config->item ( 'themes_path' ) . $_themes . 'modules/' . $_module . $_module_view . '.phtml';
		// check:modules/views
		$_file = is_file ( @$_file ) ? $_file : CI::$APP->config->item ( 'mod_path' ) . $_module . 'views/' . $_module_view . '.phtml';
		
		if (! is_file ( $_file ))
			return '';
		
		ob_start ();
		extract ( empty ( $_data ) ? get_object_vars ( CI::$APP->appmanager ) : (is_array ( $_data ) ? array_merge ( get_object_vars ( CI::$APP->appmanager ), $_data ) : array_merge ( get_object_vars ( CI::$APP->appmanager ), get_object_vars ( $_data ) )) );
		
		require ($_file);
		$_content = ob_get_clean ();
		return $_content;
	}
}