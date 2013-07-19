<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$route ['gallery/gallery_site/tim-kiem'] = 'gallery_site/search';
$route ['gallery/gallery_site/tim-kiem/(.*)/p(\d+)'] = 'gallery_site/search/$1/$2';
$route ['gallery/gallery_site/p(\d+)'] = 'gallery_site/index/$1';
$route ['gallery/gallery_site/(.*).i(\d+)'] = 'gallery_site/detail/$2';
$route ['gallery/gallery_site/(.*).c(\d+)'] = 'gallery_site/category/$2';
$route ['gallery/gallery_site/(.*).c(\d+).p(\d+)'] = 'gallery_site/category/$2/$3';