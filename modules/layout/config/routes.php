<?php 
if(!defined('BASEPATH'))
	exit('No direct script access allowed');
$route['layout/layout_home/(\d+)'] = 'layout_home/index/$1';
$route['layout/layout_home/(.*).c(\d+)'] = 'layout_home/category/$2';
$route['layout/layout_home/(.*).c(\d+).p(\d+)'] = 'layout_home/category/$2/$3';
$route['layout/layout_home/(.*).i(\d+)'] = 'layout_home/detail/$2';
$route['layout/layout_home/kich-hoat/(\d+)'] = 'layout_home/activeLayout/$1';
?>