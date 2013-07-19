<?php
$config ['info'] ['pages_customer'] = array (
		'admin/menu' => array (
				'position' => 2,
				'class' => 'typo',
				'link' => '',
				'modules'=>'pages_customer',
				'active' => 'pages_customer_admin/(.*)',
				'title' => 'Quản lý trang',
				'items' => array (
						'admin/pages_customer' => 'Danh sách trang',
						'admin/pages_customer/add' => 'Tạo trang' 
				) 
		),
);
?>