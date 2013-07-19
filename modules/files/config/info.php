<?php if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$config ['info'] ['files'] = array (
		'admin/menu' => array (
				'position' => 4,
				'class' => 'typo',
				'link' => mysiteurl('admin/files'),
				'modules'=>'files',
				'active' => 'files_admin/(.*)',
				'title' => 'Quản lý tập tin',
		) 
);