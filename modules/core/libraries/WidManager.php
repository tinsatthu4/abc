<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class WidManager {
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
		
		log_message ( 'debug', 'WidManager Class Initialized' );
	}
	function getList($module = '') {
	}
	function install($fileupload) {
	}
	function uninstall($widget) {
	}
	function download($widget) {
	}
	function get($_path) {
		$_path = trim ( $_path, '/' );
		$_module = @array_shift ( @explode ( '/', $_path ) );
		$_widget = @array_pop ( @explode ( '/', $_path ) );
		$_class = 'Widget_' . str_replace ( '/', '_', $_path );
		
		! is_file ( CI::$APP->config->item ( 'mod_path' ) . $_module . '/widgets/' . $_widget . '/' . $_widget . '.php' ) or require_once (CI::$APP->config->item ( 'mod_path' ) . $_module . '/widgets/' . $_widget . '/' . $_widget . '.php');
		if (class_exists ( $_class ))
			return new $_class ();
		return false;
	}
	function render($_path, $_options = array()) {
		$ob = self::get ( $_path );
		return is_object ( $ob ) ? $ob->render ( $_options ) : '';
	}
	function config($_path, $_options = array()) {
		$ob = self::get ( $_path );
		return is_object ( $ob ) ? $ob->config ( $_options ) : '';
	}
}

class Widget extends MX_Controller {
	function __construct() {
		parent::__construct ();
	}
	function view($_view, $_data = array()) {
		$_module = @array_shift ( array_slice ( explode ( '_', get_class ( $this ) ), 1 ) );
		$_path = @implode ( '_', array_slice ( explode ( '_', get_class ( $this ) ), 2 ) );
		
		$_file = $this->config->item ( 'themes_path' ) . trim ( $this->appmanager->THEME, '/' ) . '/widgets/' . $_module . '/' . $_path . '/' . $_view . '.phtml';
		$_file = is_file ( $_file ) ? $_file : $this->config->item ( 'mod_path' ) . $_module . '/widgets/' . $_path . '/views/' . $_view . '.phtml';
		ob_start ();
		extract ( empty ( $_data ) ? get_object_vars ( $this->appmanager ) : (is_array ( $_data ) ? array_merge ( get_object_vars ( $this->appmanager ), $_data ) : array_merge ( get_object_vars ( $this->appmanager ), get_object_vars ( $_data ) )) );
		require ($_file);
		$_content = ob_get_clean ();
		return $_content;
	}
	function config($_options = array()) {
		return $this->view ( 'config', $_options );
	}
	function render($_options = array()) {
		return $this->view ( 'render', $_options );
	}
}