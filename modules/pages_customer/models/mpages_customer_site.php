<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Mpages_customer_site extends CustomerLever_Model
{
	protected $table = 'pages_customer';
	function __construct()
	{
		parent::__construct($this->table);
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		return $this->link(parent::select($where,$order,$of,$pp));
	}
	function listchild($id=0,$where='',$order='',$l=0)
	{
		return $this->link(parent::listchild($id,$where,$order,$l));
	}
	function link($data)
	{
		foreach($data as &$val){
			$val['link'] = mysiteurl('pages_customer/'.$val['slug'].'-i'.$val['id']);
			$val['method'] = 'pages_customer_site::index';
		}
		return $data;
	}
}
?>