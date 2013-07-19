<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Mmenu extends Base_Model {
	protected $table = 'menu';
	function __construct() {
		parent::__construct ();
		$this->error = array ();
		$this->rules = array (
				'title' => array (
						'title',
						'',
						'trim|required|max_length[255]' 
				) 
		);
	}
	function toArray() {
		$rows = parent::toArray ();
		foreach ( $rows as &$row ) {
			$row ['_link'] = stringbegin ( $row ['link'], 'site:' ) ? mysiteurl ( preg_replace ( '#^site:(.*+)$#', '$1', $row ['link'] ) ) : $row ['link'];
			$row ['_link_edit'] = mysiteurl ( 'menu/menu_admin/edit/' . $row ['id'] );
			$row ['_link_delete'] = mysiteurl ( 'menu/menu_admin/delete/' . $row ['id'] );
		}
		return $rows;
	}
	
	function _admin_add() {
		$this->error = myvalid ( mypost (), arraygetkey ( $this->rules, array (
				'title' 
		) ) );
		if (empty ( $this->error )) {
			return parent::insert ( array (
					'parent' => mypost ( 'parent' ),
					'type' => mypost ( 'type' ),
					'title' => mypost ( 'title' ),
					'link' => mypost ( 'link' ),
					'before' => mypost ( 'before' ),
					'after' => mypost ( 'after' ),
					'order' => mypost ( 'order' ),
					'login' => mypost ( 'login' ),
					'not_login' => mypost ( 'not_login' ),
					'created' => time (),
					'createby' => strval ( @$this->appmanager->USER ['username'] ),
					'updated' => time (),
					'updateby' => strval ( @$this->appmanager->USER ['username'] ),
					'status' => mypost ( 'status' ) 
			) );
		}
		return false;
	}
	function _admin_edit($id) {
		$this->error = myvalid ( mypost (), arraygetkey ( $this->rules, array (
				'title' 
		) ) );
		
		if (empty ( $this->error )) {
			return parent::update ( array (
					'parent' => mypost ( 'parent' ),
					'type' => mypost ( 'type' ),
					'title' => mypost ( 'title' ),
					'link' => mypost ( 'link' ),
					'before' => mypost ( 'before' ),
					'after' => mypost ( 'after' ),
					'order' => mypost ( 'order' ),
					'login' => mypost ( 'login' ),
					'not_login' => mypost ( 'not_login' ),
					'updated' => time (),
					'updateby' => strval ( @$this->appmanager->USER ['username'] ),
					'status' => mypost ( 'status' ) 
			), array (
					'id' => $id 
			) );
		}
		return false;
	}
}