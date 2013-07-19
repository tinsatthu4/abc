<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$route['widgets/widgets_home/p(\d+)'] = 'widgets_home/index/$1';
$route['widgets/widgets_home/(.*).i(\d+)'] = 'widgets_home/detail/$2';
?>