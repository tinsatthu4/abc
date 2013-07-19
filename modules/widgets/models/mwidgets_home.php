<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class mwidgets_home extends Order_Model
{
	protected $table = 'widgets';
	function __construct()
	{
		parent::__construct($this->table);	
	}

	function select($where='',$order='',$of=0,$pp=0)
	{
		$rows = parent::select($where,$order,$of,$pp);
		foreach($rows as &$row){
			$row['options']	= json_decode(@$row['options'],true);
			$row['link'] = mysiteurl('widgets/'.stringseo($row['title'].'.i'.$row['id']));
		}
		return $rows;
	}


}