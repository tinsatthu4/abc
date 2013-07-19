<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$config ['info'] ['orders'] = array (
		'admin/menu' => array (
				'position' => 2,
				'class' => 'typo',
				'modules'=>'orders',
				'link' => mysiteurl('admin/orders'),
				'active' => 'orders_admin/(.*)',
				'title' => 'Quản lý đơn hàng',
		) 
);
?>