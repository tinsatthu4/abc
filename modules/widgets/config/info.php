<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$config['info']['widgets'] = array(
	'adminis/menu'=>array(
			'position'	=>2,
			'class' 	=> 'login',
			'link' 		=> 'administrator/widgets',
			'active'	=>	'widgets_administrator/(.*)',
			'titlevn' 	=> 'Quản Lý widgets',
			'titleen' 	=> 'widgets Manager',
			'items'		=>	array(
				'administrator/widgets'				=>	'Danh sách widgets',
				'administrator/widgets/add'			=>	'Thêm mới widgets'
			),
		)
);