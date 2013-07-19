<?php
class Mnews_category_site extends CustomerLever_Model {
	protected $table = 'news_cate';
	function __construct() {
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
	function link($data=array())
	{
		foreach($data as &$val)
		{
			$val['link'] = mysiteurl('tin-tuc/'.$val['slug'].'.c'.$val['id']);
			$val['link_paging'] = mysiteurl('tin-tuc/'.$val['slug'].'.c'.$val['id'].'.p[x]');
		}
		return $data;
	}
	function selectByItem($id_item,$where='',$order='',$of=0,$pp=0)
	{
		$this->db->join('news_relate','id_cate=id');
		$this->db->where('id_item',$id_item);
		return $this->select($where,$order,$of,$pp);
	}
}
?>