<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class mlayout_administrator extends Order_Model {
	function __construct() {
		parent::__construct ( 'layout' );
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		$data = parent::select($where,$order,$of,$pp);
		foreach($data as &$val)
			$val['relation'] = arraymake(parent::_select("layout_relate",array('id_item'=>$val['id'])),"id_cate","id_cate");
		return $data;
	}
	function selectAll($where='',$order='',$of=0,$pp=0)
	{
		$this->db->join('layout_cate','layout_cate.id=layout.pid');
		$this->db->select('layout.*,layout_cate.title as cate_title');
		return parent::select($where,$order,$of,$pp);
	}
}