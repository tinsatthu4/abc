<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class menu_site extends Site_Controller {
	function __construct() {
		parent::__construct ();
	}
	function index() {
		$this->appmanager->METHOD = __METHOD__;
		myview ( 'index', array (
				'CONTENT' => array (
						'products/index',
						$this 
				) 
		) );
	}
	function detail() {
		$this->appmanager->METHOD = __METHOD__;
		myview ( 'index', array (
				'CONTENT' => array (
						'products/detail',
						$this 
				) 
		) );
	}
	function category() {
		$this->appmanager->METHOD = __METHOD__;
		myview ( 'index', array (
				'CONTENT' => array (
						'products/category',
						$this 
				) 
		) );
	}
}