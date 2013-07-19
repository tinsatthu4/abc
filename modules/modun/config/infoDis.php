<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$config['info']['modun'] = array(
	'adminis/menu'=>array(
		'position'	=>9,
		'class' 	=> 'login',
		'link' 		=> 'administrator/modun',
		'active'	=>	'modun_administrator/(.*)',
		'titlevn' 	=> 'Quản Lý modun khách hàng',
		'titleen' 	=> 'Modules Manager',
		'items'		=>	array(
			'administrator/modun'		=>	'Danh sách modules',
			'administrator/modun/add'	=>	'Thêm mới modules'
		),
	)
);