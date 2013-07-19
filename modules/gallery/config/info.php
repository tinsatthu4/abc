<?php if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$config ['info'] ['gallery'] = array (
		'admin/menu' => array (
				'position' => 3,
				'class' => 'typo',
				'link' => '',
				'modules'=>'gallery',
				'active' => 'gallery_admin/(.*)',
				'title' => 'Quản lý album ảnh',
				'items' => array (
						'admin/gallery/category' => 'Danh mục album',
						'admin/gallery' => 'Quản lý album',
						'admin/gallery/add' => 'Thêm album' 
				) 
		) 
);