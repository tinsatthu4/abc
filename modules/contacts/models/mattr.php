<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mattr extends Customer_Model
{
	protected $table='contacts_attr';
	function __construct()
	{
		parent::__construct($this->table);
	}
}
?>