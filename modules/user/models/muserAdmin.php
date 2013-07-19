<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class MuserAdmin extends Order_Model {
	function __construct() {
		parent::__construct ( 'user' );
	}
	
	function select($where = '', $order = '', $of = 0, $pp = 0) {
		$rows = parent::select ( $where, $order, $of, $pp );
		foreach ( $rows as &$row )
			$row ['activeInfo'] = json_decode ( @$row ['activeInfo'], true );
		return $rows;
	}
	function insert($data) {
		return parent::insert ( $data );
	}
	function update($data, $where = '') {
		return parent::update ( $data, $where );
	}

}