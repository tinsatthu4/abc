<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$route['pages_customer/pages_customer_site/(.*).i(\d+)'] = 'pages_customer_site/index/$2';
?>