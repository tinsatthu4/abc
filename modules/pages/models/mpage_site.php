<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mpage_site extends Level_Model
{
	function __construct()
	{
		parent::__construct('pages');
	}
	function getPage($id_layout=0)
	{
		$this->db->join('pages_layout','id = id_page');
		$this->db->where('id_layout',$id_layout);
		$this->db->select('pages.*');
		$data = parent::select(array('pid'=>0),array('sc_order'=>'asc'));
		foreach($data as &$val)
		{
			$link = explode('_',$val['method']);
			$link = ($link[0]=='site::index')?mycustomerurl():mycustomerurl($link[0]);
			$val['link'] = $link;
		}
		return $data;
	}
	function checkPage($layout_id,$method_page)
	{
		$this->db->join('pages_layout','id_page=id');
		return parent::countrows(array(
				'method'=>$method_page,
				'id_layout'=>$layout_id,
				'sc_status'=>1
		));
	}	
}
?>