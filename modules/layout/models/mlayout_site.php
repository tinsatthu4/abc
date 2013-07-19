<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mlayout_site extends Base_Model
{
	function __construct()
	{
		parent::__construct('layout');
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		$data = parent::select($where,$order,$of,$pp);
		foreach($data as &$val){
			$val['options'] = json_decode($val['options'],true);
			$val['options']['header'] = base64_decode($val['options']['header']);
			$val['options']['footer'] = base64_decode($val['options']['footer']);
			$val['options']['left'] = base64_decode($val['options']['left']);
			$val['options']['right'] = base64_decode($val['options']['right']);
			$val['options']['content_top'] = base64_decode($val['options']['content_top']);
			$val['options']['content_bottom'] = base64_decode($val['options']['content_bottom']);
		}
		return $data;
	}
	function getLayout($id_layout = 0)
	{
		$data = array_shift(parent::select(array('id'=>$id_layout)));
		return $data['key'];
	}
}
?>