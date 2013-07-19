<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Mcontacts_site extends Customer_Model
{
	protected $table = 'contacts';
	function __construct()
	{
		parent::__construct($this->table);
	}
}
?>