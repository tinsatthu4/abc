<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

$config ['info'] ['services'] = array (
		'adminis/menu' => array (
				'position' => 2,
				'class' => 'login',
				'link' => '',
				'modules'=>'services',
				'active' => 'services_administrator/(.*)',
				'titlevn' => 'Quản lý dịch vụ',
				'titleen' => 'Page Manager',
				'items' => array (
						'administrator/services' => 'Danh sách dịch vụ',
						'administrator/services/add' => 'Thêm dịch vụ', 
						'administrator/services/managerorder' => 'Thống kê' 
				) 
		)
);