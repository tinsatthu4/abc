<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mmodun_admin extends Customer_Model {
	protected $table = 'modun';
	function __construct() {
		parent::__construct ( $this->table );
	}
	function insert($data)
	{
		$data['header'] = json_encode((isset($data['header'])&&!empty($data['header']))?$data['header']:array());
		$data['footer'] = json_encode((isset($data['footer'])&&!empty($data['footer']))?$data['footer']:array());
		$data['left'] = json_encode((isset($data['left'])&&!empty($data['left']))?$data['left']:array());
		$data['right'] = json_encode((isset($data['right'])&&!empty($data['right']))?$data['right']:array());
		$data['content_top'] = json_encode((isset($data['content_top'])&&!empty($data['content_top']))?$data['content_top']:array());
		$data['content_bottom'] = json_encode((isset($data['content_bottom'])&&!empty($data['content_bottom']))?$data['content_bottom']:array());
		return parent::insert($data);		
	}
	function update($data,$where='')
	{
		if(isset($data['header'])&&!empty($data['header']))
		$data['header'] = json_encode($data['header']);
		if(isset($data['footer'])&&!empty($data['footer']))
		$data['footer'] = json_encode($data['footer']);
		if(isset($data['left'])&&!empty($data['left']))
		$data['left'] = json_encode($data['left']);
		if(isset($data['right'])&&!empty($data['right']))
		$data['right'] = json_encode($data['right']);
		if(isset($data['content_top'])&&!empty($data['content_top']))
		$data['content_top'] = json_encode($data['content_top']);
		if(isset($data['content_bottom'])&&!empty($data['content_bottom']))
		$data['content_bottom'] = json_encode($data['content_bottom']);
		return parent::update($data,$where);
	}
	function insertDefault($data,$where='')
	{
		$arr = array('header','footer','left','right','content_top','content_bottom');
		foreach($arr as $val)
		if(empty($data[$val])) $data[$val] = json_encode(array());
		else $data[$val] = json_encode($data[$val]);
		parent::_insert($this->table,$data);
	}
	function updateDefault($data,$where='')
	{
		$arr = array('header','footer','left','right','content_top','content_bottom');
		foreach($arr as $val)
			if(empty($data[$val])) $data[$val] = json_encode(array());
		else $data[$val] = json_encode($data[$val]);
		parent::_update($this->table,$data,$where);
	}
	function clear($position='header',$id=0)
	{
		$data[$position] = json_encode(array());
		return parent::update($data,array("id_page"=>$id));
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		$data = parent::select($where,$order,$of,$pp);
		$arr = array('header','footer','left','right','content_top','content_bottom');
		foreach($data as &$val)
		{
			foreach($arr as $value)
			{
				$val[$value] = json_decode($val[$value],true);
				foreach ($val[$value] as &$vl){
					$vl['options']['layout'] = $value;
					$vl['options']['primary_key'] = $vl['primary_key'];
					if(!isset($vl['options']['title']) || empty($vl['options']['title']))
						$vl['options']['title'] = $vl['title'];
				}
			}
		}
		return $data;
	}

	
}
?>