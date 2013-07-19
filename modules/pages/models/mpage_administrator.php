<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Mpage_administrator extends Level_Model {
	function __construct() {
		parent::__construct ( 'pages' );
		@session_start ();
	}
	
	function select($where = '', $order = '', $of = 0, $pp = 0) {
		$rows = parent::select ( $where, $order, $of, $pp );
		foreach ( $rows as &$row ) {
			$row ['activeInfo'] = json_decode ( @$row ['activeInfo'], true );
		}
		return $rows;
	}
	function insert($data) {
		return parent::insert ( $data );
	}
	function update($data, $where = '') {
		if (array_key_exists ( 'activeInfo', $data ))
			$data ['activeInfo'] = json_encode ( is_array ( @$data ['activeInfo'] ) ? $data ['activeInfo'] : array () );
		return parent::update ( $data, $where );
	}

}