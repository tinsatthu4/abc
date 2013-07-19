<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Mpage_admin extends Level_Model
{
	function __construct()
	{
		parent::__construct('pages');
	}
	function selectPageOfLayout($id_layout=0)
	{
		$data = parent::_select('pages_layout',array("id_layout"=>$id_layout));
		return arraymake($data,"id_page","id_page");
	}

	function selectPage($ids,$id=0,$where='',$order='',$l=0)
	{
		$this->db->where_in('id',$ids);
		$this->db->where(array(
					'pid' => intval($id)
			));
		$rows = $this->select ( $where, $order );	
		if (!empty( $rows ))
			foreach ( $rows as $row ) {
			$childs = $this->selectPage ($ids,$row ['id'], $where, $order, $l + 1 );
			$row ['_pos'] = $l;
			$row ['_txttitle'] = ($l == 0) ? $row ['title'] : str_repeat ( '&nbsp;&nbsp;&nbsp;', $l * 2 ) . '|--- ' . $row ['title'];
			$data [] = $row;
			if (is_array ( $childs ) && ! empty ( $childs ))
				$data = array_merge ( $data, $childs );
		}
		return empty ( $data ) ? array () : $data;
	}
}
?>