<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

$config ['info'] ['news'] = array (
		'admin/menu' => array (
				'position' => 3,
				'class' => 'typo',
				'link' => '',
				'modules'=>'news',
				'active' => 'news_admin/(.*)',
				'title' => 'Quản lý tin tức',
				'items' => array (
						'admin/news/category' => 'Danh mục tin tức',
						'admin/news/index' => 'Danh sách tin tức',
						'admin/news/add' => 'Thêm tin tức' 
				) 
		)
);