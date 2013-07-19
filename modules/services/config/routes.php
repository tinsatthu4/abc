<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$route['services/services_home/(.*).i(\d+)'] = 'services_home/detail/$2';
?>