<?php if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$config ['info']['contacts'] = array (
		'admin/menu' => array (
			'position' => 4,
			'class' => 'typo',
			'modules'=>'contacts',
			'link' => mysiteurl('admin/contacts'),
			'active' => 'contacts_admin/(.*)',
			'title' => 'Quản lý liên hệ',
			'items'=>array(
			'admin/contacts/index'=>'Danh sách liên hệ',	
			)
		) 
);