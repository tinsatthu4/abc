<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class mwidgets_administrator extends Order_Model
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
		}
		return $rows;
	}

	function selectjoincat($where='',$order='',$of=0,$pp=0)
	{
		$rows = parent::select($where,$order,$of,$pp);
		foreach($rows as &$row){
			$row['options']	= json_decode(@$row['options'],true);
		}
		return $rows;
	}

	function insert($data)
	{
		return parent::insert($data);
	}

	function update($data,$where='')
	{
		if(array_key_exists('activeInfo',$data))
		$data['activeInfo'] = json_encode(is_array(@$data['activeInfo'])?$data['activeInfo']:array());
		return parent::update($data,$where);
	}

	function selectPosition($position='header',$id_layout=0,$where='',$order='',$of=0,$pp=0)
	{
		$this->db->join('widgets_cate_relate','widgets.id=widgets_cate_relate.id_widget');
		$this->db->join('widgets_layout','widgets.id=widgets_layout.id_widget');
		$this->db->where('id_cate',$position);
		$this->db->where('id_layout',$id_layout);
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
	function selectWidgetsOfLayout($id_layout,$where='',$order='',$of=0,$pp=0)
	{
		$this->db->join('widgets_layout','id=id_widget');
		$this->db->where("id_layout",$id_layout);
		return parent::select($where,$order,$of,$pp);
	}

}