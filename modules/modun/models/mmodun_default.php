<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mmodun_default extends Base_Model {
	protected $table = 'modun_default';
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
		if(isset($data['header']))
		$data['header'] = json_encode($data['header']);
		if(isset($data['footer']))
		$data['footer'] = json_encode($data['footer']);
		if(isset($data['left']))
		$data['left'] = json_encode($data['left']);
		if(isset($data['right']))
		$data['right'] = json_encode($data['right']);
		if(isset($data['content_top']))
		$data['content_top'] = json_encode($data['content_top']);
		if(isset($data['content_bottom']))
		$data['content_bottom'] = json_encode($data['content_bottom']);
		return parent::update($data,$where);
	}
	function clear($position='header',$id=0)
	{
		$data[$position] = json_encode(array());
		return parent::update($data,array("id_page"=>$id));
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		$data = parent::select($where,$order,$of,$pp);
		foreach($data as &$val)
		{
			$val['header'] = json_decode($val['header'],true);
			$val['footer'] = json_decode($val['footer'],true);
			$val['left'] = json_decode($val['left'],true);
			$val['right'] = json_decode($val['right'],true);
			$val['content_top'] = json_decode($val['content_top'],true);
			$val['content_bottom'] = json_decode($val['content_bottom'],true);
		}
		return $data;
	}
}
?>