<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Mattr extends Customer_Model {
	protected $table = "products_attr";
	function __construct() {
		parent::__construct ( $this->table);
	}
}