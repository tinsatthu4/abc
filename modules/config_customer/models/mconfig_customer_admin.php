<?php 
class Mconfig_customer_admin extends Customer_Model
{
	protected $table = 'config_customer';
	function __construct()
	{
		parent::__construct($this->table);
	}
}
?>