<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mcomments_admin extends Customer_Model
{
	protected $table = 'comments';
	function __construct()
	{
		parent::__construct($this->table);
	}
}
?>