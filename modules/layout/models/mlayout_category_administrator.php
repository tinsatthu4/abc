<?php 
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class Mlayout_category_administrator extends Administrator_model
{
	protected $table = 'layout_cate';
	function __construct()
	{
		parent::__construct($this->table);
	}
}
?>