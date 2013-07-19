<?php
class mgallery_site extends CustomerRelation_Model {
	protected $tableRelate = "gallery_relate";
	protected $table = "gallery";
	function __construct() {
		parent::__construct ( $this->table, $this->tableRelate );
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
			$val['link'] = mysiteurl('gallery/'.$val['slug'].'.i'.$val['id']);
		return $data;
	}
}
?>