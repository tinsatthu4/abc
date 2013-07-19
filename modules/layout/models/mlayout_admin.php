<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mlayout_admin extends Base_Model
{
	function __construct()
	{
		parent::__construct('layout');
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		$data = parent::select($where,$order,$of,$pp);
		foreach($data as &$val)
			$val['options'] = json_decode($val['options'],true);
		return $data;
	}
}
?>