<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$route ['products/products_site/tim-kiem'] = 
$route ['products/products_site/tim-kiem/'] = 'products_site/search';
$route ['products/products_site/tim-kiem/(.*)'] = 'products_site/search/$1';
$route ['products/products_site/tim-kiem/(.*)/(\d+)'] = 'products_site/search/$1/$2';

$route ['products/products_site/search'] = 
$route ['products/products_site/search/'] = 'products_site/search';

$route ['products/products_site/search/(.*)'] = 'products_site/search/$1';
$route ['products/products_site/search/(.*)/p(\d+)'] = 'products_site/search/$1/$2';

$route ['products/products_site/p(\d+)'] = 'products_site/index/$1';
$route ['products/products_site/(.*).i(\d+)'] = 'products_site/detail/$2';
$route ['products/products_site/(.*).c(\d+)'] = 'products_site/category/$2';
$route ['products/products_site/(.*).c(\d+).p(\d+)'] = 'products_site/category/$2/$3';