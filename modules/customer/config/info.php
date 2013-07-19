<?php if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$config ['info'] ['customer'] = array (
		'adminis/menu' => array (
				'position' => 3,
				'class' => 'login',
				'link' => '',
				'active' => 'customer_administrator/(.*)',
				'titlevn' => 'Quản lý khách hàng',
				'titleen' => 'Manager customer',
				'items' => array (
						'administrator/customer/index' => 'Danh sách khách hàng' 
				) 
		) 
);