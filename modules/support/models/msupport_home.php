<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class msupport_home extends Home_model
{
	function __construct()
	{
		parent::__construct('support');
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		$data = parent::select($where,$order,$of,$pp);
		foreach($data as &$val)
		{
			$val['link'] = mysiteurl('support/'.$val['slug'].'.i'.$val['id']);
		}
		return $data;
	}
}
?>