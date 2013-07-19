<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Mlayout_home extends Home_model
{
	function __construct()
	{
		parent::__construct('layout');
	}
	function countrowsCategory($id_cate,$where='')
	{
		$this->db->join('layout_relate','id_item=id');
		$this->db->where("id_cate",$id_cate);
		return parent::countrows($where);
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		$data = parent::select($where,$order,$of,$pp);
		foreach($data as &$val)
		{
			$val['link'] = mysiteurl('layout/'.stringseo($val['title']).'.i'.$val['id']);
		}
		return $data;
	}
	function selectCategory($id_cate,$where='',$order='',$of=0,$pp=0)
	{
		$this->db->join('layout_relate','id_item=id');
		$this->db->where("id_cate",$id_cate);
		return $this->select($where,$order,$of,$pp);
	}
	
}
?>