<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
$route ['news/news_site/tim-kiem'] = 
$route ['news/news_site/tim-kiem/'] = 'news_site/search';
$route ['news/news_site/tim-kiem/(.*)/p(\d+)'] = 'news_site/search/$1/$2';
$route ['news/news_site/p(\d+)'] = 'news_site/index/$1';
$route ['news/news_site/(.*).i(\d+)'] = 'news_site/detail/$2';
$route ['news/news_site/(.*).c(\d+)'] = 'news_site/category/$2';
$route ['news/news_site/(.*).c(\d+).p(\d+)'] = 'news_site/category/$2/$3';