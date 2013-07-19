<?php if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$config ['info'] ['core'] = array (
		'module' => 'core',
		'author' => 'Mr Quang',
		'email' => 'vantaiit@gmail.com',
		'name' => 'Core',
		'description' => 'Core Modules Description',
		'version' => 1,
		'changelog' => '',
		
		'adminis/menu' => array (
				'position' => 1,
				'class' => 'dash',
				'hide' => 0,
				'link' => 'administrator',
				'active' => 'administrator',
				'titlevn' => 'Dashboard',
				'titleen' => 'Dashboard',
				'items' => array (
						'administrator' => 'Home' 
				) 
		) 
);	