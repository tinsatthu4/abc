<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mcomments_administrator extends Administrator_model
{
	protected $table = 'comments';
	function __construct()
	{
		parent::__construct($this->table);
	}
}
?>