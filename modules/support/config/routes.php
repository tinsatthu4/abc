<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$route['support/support_home/p(\d+)'] = 'support_home/index/$1';
$route['support/support_home/(.*).i(\d+)'] = 'support_home/detail/$2';
?>