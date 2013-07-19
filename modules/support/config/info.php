<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

$config ['info'] ['support'] = array (
		'adminis/menu' => array (
				'position' => 2,
				'class' => 'login',
				'link' => 'administrator/support',
				'active' => 'support_administrator/(.*)',
				'titlevn' => 'Quản lý bài viết',
				'titleen' => 'Support Manager',
				'items' => array (
						'administrator/support'=>'Danh sách bài viết',
						'administrator/support/add' => 'Thêm bài viết',
				) 
		)		
);