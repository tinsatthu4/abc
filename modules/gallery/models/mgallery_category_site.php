<?php
class mgallery_category_site extends CustomerLever_Model {
	function __construct() {
		parent::__construct ( "gallery_cate", "gallery_relate" );
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
			$val['link'] = mysiteurl('gallery/'.$val['slug'].'.c'.$val['id']);
			$val['link_paging'] = mysiteurl('gallery/'.$val['slug'].'.c'.$val['id'].'.p[x]');
		}
		return $data;
	}
}
?>