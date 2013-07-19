<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$config ['info'] ['products'] = array (
		'admin/menu' => array (
				'position' => 1,
				'class' => 'typo',
				'link' => '#',
				'modules'=>'products',
				'active' => 'products_admin/(.*)',
				'title' => 'Quản lý sản phẩm',
				'items' => array (
						'admin/products/category' => 'Quản lý danh mục',
						'admin/products/index' => 'Quản lý sản phẩm',
						'admin/products/attr' => 'Tạo dữ liệu sản phẩm',
				) 
		) 
);