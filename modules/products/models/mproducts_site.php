<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mproducts_site extends CustomerRelation_Model {
	protected $table = "products";
	protected $tableRealation = "products_relate";
	function __construct() {
		parent::__construct ( $this->table, $this->tableRealation );
	}
	function countrows($id_cate=0,$where='')
	{
		if($id_cate != 0){
			$this->db->join($this->tableRelation,'id_item=id');
			$this->db->where('id_cate',$id_cate);
		}
		return parent::countrows($where);
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		$data =  $this->link(parent::select($where,$order,$of,$pp));
		$ATTR = array_shift($this->_select('products_attr',array('customer_id'=>$this->appmanager->CUSTOMER_ID)));
		foreach($data as &$val)
			@$val['ATTR'] = @json_decode(@$ATTR['options']);
		return $data;
	}
	function selectCategory($id_cate='',$where='',$order='',$of=0,$pp=0)
	{
		$data = parent::selectCategory($id_cate,$where,$order,$of,$pp);
		return $this->link($data);
	}
	function link($data)
	{
		foreach($data as &$val){
			$val['link'] = mysiteurl('products/'.$val['slug'].'.i'.$val['id']);
		}
		return $data;
	}
	function selectRelation($id_item,$where,$order,$of,$pp)
	{
		$arr_id_cate = arraymake(parent::_select($this->tableRelation,array('id_item'=>$id_item)),'id_cate','id_cate');
		if(empty($arr_id_cate)) return array();
		$this->db->join($this->tableRelation,"id_item=id");
		$this->db->where_in("id_cate",$arr_id_cate);
		$this->db->where(array("id <>"=>$id_item));
		$this->db->distinct("id");
		$this->db->select($this->table.".*");
		$this->select($where,$order,$of,$pp);
		return $this->link($this->select($where,$order,$of,$pp));
	}
}