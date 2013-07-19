<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class menu_administrator extends Administrator_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->languages ( array (
				'core/common',
				'menu/menu',
				'common' 
		), $this->appmanager->LANGUAGE );
	}
	function index() {
		myview ( 'index', array (
				'CONTENT' => array (
						'menu/index',
						$this 
				) 
		) );
	}
	function add() {
		myview ( 'index', array (
				'CONTENT' => array (
						'menu/add',
						$this 
				) 
		) );
	}
}