<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mlayout_category_home extends Home_model
{
	function __construct()
	{
		parent::__construct('layout_cate');
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		$data = parent::select($where,$order,$of,$pp);
		foreach($data as &$val){
			$val['link'] = mysiteurl('layout/'.stringseo($val['title']).'.c'.$val['id']);
			$val['link_paging'] = mysiteurl('layout/'.stringseo($val['title']).'.c'.$val['id'].'/p[x]');
		}
		return $data;
	}
	function selectCATEGORY($where='',$order='',$of=0,$pp=0)
	{
		$this->db->where('pid',0);
		$data = parent::select($where,$order,$of,$pp);
		foreach($data as &$val)
		{
			$val['link'] = mysiteurl('layout/'.stringseo($val['title']).'.c'.$val['id']);
			$val['link_paging'] = mysiteurl('layout/'.stringseo($val['title']).'.c'.$val['id'].'.p[x]');
		}
		return $data;
	}
}
?>