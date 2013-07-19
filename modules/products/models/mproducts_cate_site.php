<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mproducts_cate_site extends CustomerLever_Model {
	function __construct() {
		parent::__construct ( 'products_cate' );
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		return $this->link(parent::select($where,$order,$of,$pp));
	}
	function listchild($id=0,$where='',$order='',$l=0)
	{
		return $this->link(parent::listchild($id,$where,$order,$l));
		
	}
	function link($data=array())
	{
		foreach($data as &$val)
		{
			$val['link'] = mysiteurl('products/'.$val['slug'].'.c'.$val['id']);
			$val['link_paging'] = mysiteurl('products/'.$val['slug'].'.c'.$val['id'].'.p[x]');
		}
		return $data;
	}
	function selectByItem($id_item,$where='',$order='',$of=0,$pp=0)
	{
		$this->db->join('products_relate','id_cate=id');
		$this->db->where('id_item',$id_item);
		return $this->select($where,$order,$of,$pp);
	}
}