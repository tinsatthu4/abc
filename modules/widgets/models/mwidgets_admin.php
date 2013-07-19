<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class mwidgets_admin extends Order_Model
{
	function __construct()
	{
		parent::__construct('widgets');
	}
	function selectPosition($position='header',$id_layout=0,$where='',$order='',$of=0,$pp=0)
	{
		$this->db->join('widgets_layout','widgets.id=id_widget');
		$this->db->where(array(
				'id_layout'=>$id_layout,
				'widget_cate'=>$position
				));
		$this->db->select('widgets.*');
		return $this->decode(parent::select($where,$order,$of,$pp));
	}
	function selectWidget($id_layout=0)
	{
		$this->db->where('id_layout',$id_layout);
		$data = arraymake(parent::_select('widgets_layout'),'id_widget','id_widget');
		return $data;
	}
	protected function decode($widgets)
	{
		foreach($widgets as &$val)
			$val['options'] = json_decode($val['options'],true);
		return $widgets;
	}
	function selectTotal($id_layout,$where='',$order='',$of=0,$pp=0){
		$this->db->join("widgets_layout","id_widget=id");
		$this->db->where("id_layout",$id_layout);
		$this->db->distinct("id");
		$this->db->select($this->table.".*");
		return $this->decode(parent::select($where,$order,$of,$pp));
	}

}
?>