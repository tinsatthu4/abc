<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

$config ['info'] ['layout'] = array (
		'adminis/menu' => array (
				'position' => 2,
				'class' => 'login',
				'link' => 'administrator/layout',
				'active' => 'layout_administrator/(.*)',
				'titlevn' => 'Quản lý giao diện',
				'titleen' => 'Layout Manager',
				'items' => array (
						'administrator/layout/category'=>'Danh mục giao diện',
						'administrator/layout' => 'Danh sách giao diện',
				) 
		),
		'admin/menu' => array (
				'position' => 5,
				'class' => 'typo',
				'link' => mysiteurl('admin/pages'),
				'modules'=>'layout',
				'active' => 'pages_admin/(.*)',
				'title' => 'Quản lý giao diện',
				'items' => array(
				'admin/pages/index'=>'Thay đổi modules',
				'admin/pages/config_nav_website'=>'Sắp xếp menu',		
				),
		)			
);