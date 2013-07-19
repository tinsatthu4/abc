<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mproducts_cate_admin extends CustomerLever_Model {
	function __construct() {
		parent::__construct ( 'products_cate' );
	}
	function selectRelate($where = '', $order = '', $of = 0, $pp = 0) {
		$this->db->join ( 'products_relate', 'id = id_cate' );
		$data = $this->select ($where,$order,$of,$pp);
		return $data;
	}
	function delete($ids = array(), $where = '') {
		$this->db->where_in ( "id", $ids );
		if (parent::delete ( $where )) {
			$this->db->where_in ( "pid", $ids );
			parent::update ( array (
					"pid" => 0 
			) );
			$this->db->where_in ( "id_cate", $ids );
			parent::_delete ( "products_relate" );
		}
	}
}