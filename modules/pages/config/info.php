<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

$config ['info'] ['pages'] = array (
		'adminis/menu' => array (
				'position' => 2,
				'class' => 'login',
				'link' => '',
				'modules'=>'pages',
				'active' => 'pages_administrator/(.*)',
				'titlevn' => 'Quản lý Page',
				'titleen' => 'Page Manager',
				'items' => array (
						'administrator/pages' => 'Danh sách page',
						'administrator/pages/add' => 'Thêm mới page' 
				) 
		),
		'admin/menu' => array (
				'position' => 10,
				'class' => 'typo',
				'modules'=>'orders',
				'active'=>'pages_admin/(.*)',
				'link' => mysiteurl(),
				'title' => 'Quản lý giao diện',
		) 
);