<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mmodun_site extends Customer_Model {
	protected $table = 'modun';
	function __construct() {
		parent::__construct ( $this->table );
	}
	function select($method='site::index',$where='',$order='',$of=0,$pp=0)
	{
		$method = strtolower($method);
		$this->db->join('pages','id_page = pages.id');
		$this->db->select('modun.*');
		$this->db->where('method',$method);
		$data = parent::select($where,$order,$of,$pp);
		$arr = array('header','footer','left','right','content_top','content_bottom');
		
		foreach($data as &$val)
		{
			foreach($arr as $value)
			{
				$val[$value] = json_decode($val[$value],true);
				foreach ($val[$value] as &$vl){
				$vl['options']['id_page'] = $val['id_page'];
				$vl['options']['path'] = $vl['path'];
				$vl['options']['id_widget'] = $vl['id'];
				$vl['options']['layout'] = $value;
				$vl['options']['primary_key'] = $vl['primary_key'];
				if(!isset($vl['options']['title']) || empty($vl['options']['title'])) 
				$vl['options']['title'] = $vl['title'];
				}
			}
		}
		return $data;
	}
	function create($method)
	{
		$id_page = array_shift(parent::_select('pages',array("method"=>$method)));
		$layout = $this->appmanager->LAYOUT;
		
		$data = array(
			"id_page"=>$id_page['id'],
			"header"=>$layout['options']['header'],
			"footer"=>$layout['options']['footer'],	
			"left"=>$layout['options']['left'],
			"right"=>$layout['options']['right'],
			"content_top"=>$layout['options']['content_top'],
			"content_bottom"=>$layout['options']['content_bottom'],
			"customer_id"=>$this->appmanager->CUSTOMER_ID,			
		);
		parent::insert($data);
		return 	array(
			"id_page"=>$id_page['id'],
			"header"=>json_decode($layout['options']['header'],true),
			"footer"=>json_decode($layout['options']['footer'],true),	
			"left"=>json_decode($layout['options']['left'],true),
			"right"=>json_decode($layout['options']['right'],true),
			"content_top"=>json_decode($layout['options']['content_top'],true),
			"content_bottom"=>json_decode($layout['options']['content_bottom'],true),
			"customer_id"=>$this->appmanager->CUSTOMER_ID,			
		);
	}
}
?>