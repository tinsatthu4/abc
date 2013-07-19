<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

$config ['info'] ['user'] = array (
		'adminis/menu' => array (
				'position' => 2,
				'class' => 'login',
				'link' => '',
				'active' => 'user_administrator/(.*)',
				'titlevn' => 'Quản lý thành viên (đại lý)',
				'titleen' => 'User Manager',
				'items' => array (
						'administrator/user' => 'Danh sách thành viên',
						'administrator/user/add' => 'Thêm mới thành viên' 
				) 
		) 
);