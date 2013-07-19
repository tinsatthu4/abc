<?php
class Mnews_site extends CustomerRelation_Model {
	protected $table = 'news';
	protected $tableRelation = 'news_relate';
	function __construct() {
		parent::__construct ( $this->table, $this->tableRelation );
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
		return $this->link(parent::select($where,$order,$of,$pp));
	}
	function selectCategory($id_cate='',$where='',$order='',$of=0,$pp=0)
	{
		$data = parent::selectCategory($id_cate,$where,$order,$of,$pp);
		return $this->link($data);
	}
	function link($data)
	{
		foreach($data as &$val)
		$val['link'] = mysiteurl('tin-tuc/'.$val['slug'].'.i'.$val['id']);
		return $data;
	}
	function selectRelation($id_item,$where='',$order='',$of=0,$pp=0)
	{
		return $this->link(parent::selectRelation($id_item,$where,$order,$of,$pp));
	}
}
?>