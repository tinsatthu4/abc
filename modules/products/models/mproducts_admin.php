<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mproducts_admin extends CustomerRelation_Model {
	protected $table = "products";
	protected $tableRealation = "products_relate";
	function __construct() {
		parent::__construct ( $this->table, $this->tableRealation );
	}
}