<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Mpages_customer_admin extends CustomerLever_Model
{
	protected $table = 'pages_customer';
	function __construct()
	{
		parent::__construct($this->table);
	}
}
?>