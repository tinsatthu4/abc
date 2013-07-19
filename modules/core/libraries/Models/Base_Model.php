<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Base_Model extends CI_Model {
	protected $table = '';
	protected $insert_id = 0;
	
	function __construct($table = '') {
		parent::__construct ();
		$this->table = $table;
	}
	
	function column() {
		return $this->db->list_fields ( $this->table );
	}
	function insert_id() {
		return $this->insert_id;
	}
	
	function _select($table, $where = '', $order = '', $of = 0, $pp = 0) {
		if (! empty ( $where ))
			if (is_array ( $where ))
				$this->db->where ( $where );
			else
				$this->db->where ( $where, NULL, FALSE );
		
		if (! empty ( $order ))
			if (is_array ( $order ))
				foreach ( $order as $ko => $vo )
					$this->db->order_by ( $ko, $vo );
			else
				$this->db->order_by ( $order );
		
		(intval ( $pp ) == 0) or $this->db->limit ( $pp, $of );
		
		$this->db->from ( $table );
		$rows = $this->db->get ();
		
		return $rows->result_array ();
	}
	function _countrows($table, $where = '') {
		if (! empty ( $where ))
			if (is_array ( $where ))
				$this->db->where ( $where );
			else
				$this->db->where ( $where, NULL, FALSE );
		
		$this->db->from ( $table );
		return $this->db->count_all_results ();
	}
	function _delete($table, $where = '') {
		if (! empty ( $where ))
			if (is_array ( $where ))
				$this->db->where ( $where );
			else
				$this->db->where ( $where, NULL, FALSE );
		$this->db->delete ( $table );
		return true;
	}
	function _insert($table, $data) {
		if (! empty ( $data ))
			$this->db->insert ( $table, $data );
		return true;
	}
	function _update($table, $data, $where = '') {
		if (! empty ( $where ))
			if (is_array ( $where ))
				$this->db->where ( $where );
			else
				$this->db->where ( $where, NULL, FALSE );
		$this->db->update ( $table, $data );
		return true;
	}
	
	function select($where = '', $order = '', $of = 0, $pp = 0) {
		return $this->_select ( $this->table, $where, $order, $of, $pp );
	}
	function countrows($where = '') {
		return $this->_countrows ( $this->table, $where );
	}
	function delete($where = '') {
		$this->_delete ( $this->table, $where );
		return true;
	}
	function insert($data) {
		$this->_insert ( $this->table, $data );
		$this->insert_id = $this->db->insert_id ();
		return (intval ( $this->insert_id ) == 0) ? false : true;
	}
	function update($data, $where = '') {
		return $this->_update ( $this->table, $data, $where );
	}
	
	function check_group($module = null) {
		$action = explode ( '::', $module );
		$where = array (
				'key_group' => $_SESSION ['user'] ['group_id'],
				'key_module' => $action [0] 
		);
		$data = array_shift ( $this->_select ( 'group_modules', $where ) );
		
		if ($_SESSION ['user'] ['group'] != 5) {
			if (empty ( $data )) {
				myview ( 'warring_permission' );
				die ();
			} else {
				if ($data [$action [1]] == 0) {
					myview ( 'warring_permission' );
					die ();
				}
			}
		}
	}
	function makeData($mypost = array()) {
		$data = array ();
		$list = $this->db->list_fields ( $this->table );
		foreach ( $list as $val )
			if (isset ( $mypost [$val] ))
			$data [$val] = $mypost [$val];
		if (in_array ( "sc_utime", $list ))
			$data ['sc_utime'] = time ();
		return $data;
	}
}
class Order_Model extends Base_Model {
	function __construct($table) {
		parent::__construct ( $table );
	}
	
	function delete($where = '') {
		parent::delete ( $where );
		$this->ordersort ( array (
				'sc_order' => 'asc' 
		) );
		return true;
	}
	function orderlist($list = '') {
		if (! empty ( $list ) && is_array ( $list )) {
			foreach ( $list as $id => $val )
				parent::update ( array (
						'sc_order' => $val 
				), array (
						'id' => intval ( $id ) 
				) );
			$this->ordersort ( array (
					'sc_order' => 'asc' 
			) );
			return true;
		}
		return false;
	}
	function ordersort($order = '') {
		if (empty ( $order ))
			return false;
		
		$this->db->query ( 'SET @rank=0;' );
		
		if (! empty ( $order ))
			if (is_array ( $order ))
				foreach ( $order as $ko => $vo )
					$this->db->order_by ( $ko, $vo );
			else
				$this->db->order_by ( $order );
		$this->db->set ( 'sc_order', '@rank:=@rank+1', FALSE );
		parent::update ( array () );
		return true;
	}
	function ordermax() {
		$row = array_shift ( parent::select ( '', array (
				'sc_order' => 'desc' 
		), 0, 1 ) );
		return @$row ['sc_order'];
	}
}
class Level_Model extends Order_Model {
	function __construct($table) {
		parent::__construct ( $table );
	}
	
	function listparent($pid, $where = '', $order = '') {
		$data = array ();
		$this->db->where ( array (
				'id' => $pid 
		) );
		$row = @array_shift ( $this->select ( $where, $order ) );
		while ( ! empty ( $row ) ) {
			$this->db->where ( array (
					'id' => $row ['pid'] 
			) );
			$row = @array_shift ( $this->select ( $where, $order ) );
			if (! empty ( $row ))
				array_unshift ( $data, $row );
		}
		return $data;
	}
	function listchild($id = 0, $where = '', $order = '', $l = 0) {
		$this->db->where ( array (
				'pid' => intval ( $id ) 
		) );
		$rows = $this->select ( $where, $order );
		
		if (!empty( $rows ))
			foreach ( $rows as $row ) {
				$childs = $this->listchild ( $row ['id'], $where, $order, $l + 1 );
				$row ['_pos'] = $l;
				$row ['_txttitle'] = ($l == 0) ? $row ['title'] : str_repeat ( '&nbsp;&nbsp;&nbsp;', $l * 2 ) . '|--- ' . $row ['title'];
				$data [] = $row;
				if (is_array ( $childs ) && ! empty ( $childs ))
					$data = array_merge ( $data, $childs );
			}
		return empty ( $data ) ? array () : $data;
	}

	function delete($where = '') {
		$rows = $this->select ( $where );
		foreach ( $rows as $row ) {
			$this->delete ( array (
					'pid' => $row ['id'] 
			) );
			parent::delete ( array (
					'id' => $row ['id'] 
			) );
		}
		return true;
	}
}
class Administrator_model extends Level_Model
{
	protected $table;
	function __construct($table)
	{
		parent::__construct($table);
		$this->table = $table;
	}
	function insert($data)
	{
		if(isset($data['options']))
			$data['options'] = json_encode($data['options']);
		else $data['options'] = json_encode(array());
		return parent::insert($data);
	}
	function update($data,$where='')
	{
		if(isset($data['options']))
			$data['options'] = json_encode($data['options']);
		return parent::update($data,$where);
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		$data = parent::select($where,$order,$of,$pp);
		foreach($data as &$val){
			if(@$val['images'])
			$val['images'] = @json_decode($val['images'],true);
			@$val['options'] = json_decode($val['options'],true);
		}
		return $data;
	}
	function delete($where = '') {
		$field = $this->db->list_fields($this->table);
		if(isset($field['pid'])){
		$rows = $this->select ( $where );
		foreach ( $rows as $row ) {
			$this->delete ( array (
					'pid' => $row ['id']
			) );
			parent::delete ( array (
					'id' => $row ['id']
			) );
		}
		}
		else parent::_delete($this->table,$where);
	}
}
class Group_Model extends Order_Model {
	
	function __construct($table) {
		parent::__construct ( $table );
	}

}
class Customer_Model extends Base_Model {
	protected $table;
	function __construct($table = '') {
		parent::__construct ( $table );
		$this->table = $table;
	}
	function select($where = '', $order = '', $of = 0, $pp = 0) {
		$this->db->where ( "customer_id", $this->appmanager->CUSTOMER_ID );
		$data = parent::select ( $where, $order, $of, $pp );
		foreach ( $data as &$val ) {
			if (isset ( $val ['options'] ))
				$val ['options'] = json_decode ( @$val ['options'], true );
			if (isset ( $val ['images'] )){
				$val ['images'] = json_decode ( @$val ['images'], true );
				if(is_array($val['images']))
					foreach($val['images'] as &$img)
					$img = base_url($img);
				else $val['images'] = base_url($val['images']);
			}
		}
		return $data;
	}
	function countrows($where = '') {
		$this->db->where ( "customer_id", $this->appmanager->CUSTOMER_ID );
		return parent::countrows ( $where );
	}
	function insert($data) {
		if (isset ( $data ['options'] ))
			if (empty ( $data ['options'] ))
				$data ['options'] = json_encode ( array () );
			else
				$data ['options'] = json_encode ( $data ['options'] );
		if (isset ( $data ['images'] ))
			if (empty ( $data ['images'] ))
				$data ['images'] = json_encode ( array () );
			else
				$data ['images'] = json_encode ( $data ['images'] );
		$data ['customer_id'] = $this->appmanager->CUSTOMER_ID;
		return parent::insert ( $data );
	}
	function update($data, $where = '') {
		$this->db->where ( "customer_id", $this->appmanager->CUSTOMER_ID );
		if (isset ( $data ['options'] ))
			if (empty ( $data ['options'] ))
				$data ['options'] = array ();
			else
				$data ['options'] = json_encode ( $data ['options'] );
		if (isset ( $data ['images'] ))
			if (! empty ( $data ['images'] ))
				$data ['images'] = json_encode ( $data ['images'] );
			else
				$data ['images'] = json_encode ( array () );
		return parent::update ( $data, $where );
	}
	function updateOrder($orders = array(), $where = null) {
		foreach ( $orders as $key => $order ) {
			if ($where != null)
				$this->db->where ( $where );
			parent::update ( array (
					'sc_order' => $order 
			), array (
					"id" => $key 
			) );
		}
	}
	function delete($where) {
		$this->db->where ( "customer_id", $this->appmanager->CUSTOMER_ID );
		return parent::delete ( $where );
	}
	function makeData($mypost = array()) {
		$data = array ();
		$list = $this->db->list_fields ( $this->table );
		foreach ( $list as $val )
			if (isset ( $mypost [$val] ))
				$data [$val] = $mypost [$val];
		if (in_array ( "sc_utime", $list ))
			$data ['sc_utime'] = time ();
		return $data;
	}
}
class CustomerLever_Model extends Customer_Model {
	function __construct($table) {
		parent::__construct ( $table );
	}
	function listparent($pid, $where = '', $order = '') {
		$data = array ();
		$this->db->where ( array (
				'id' => $pid 
		) );
		$row = @array_shift ( $this->select ( $where, $order ) );
		while ( ! empty ( $row ) ) {
			$this->db->where ( array (
					'id' => $row ['pid'] 
			) );
			$row = @array_shift ( $this->select ( $where, $order ) );
			if (! empty ( $row ))
				array_unshift ( $data, $row );
		}
		return $data;
	}

	function listchild($id = 0, $where = '', $order = '', $l = 0) {
		$this->db->where ( array (
				'pid' => intval ( $id ) 
		) );
		$rows = $this->select ( $where, $order );
		if (! empty ( $rows ))
			foreach ( $rows as $row ) {
				$childs = $this->listchild ( $row ['id'], $where, $order, $l + 1 );
				$row ['_pos'] = $l;
				$row ['_txttitle'] = ($l == 0) ? $row ['title'] : str_repeat ( '&nbsp;&nbsp;&nbsp;', $l * 2 ) . '|--- ' . $row ['title'];
				$row ['_postitle'] = '%s<label>' . $row ['title'] . '</label>';
				$row ['parent'] = array (
						'_pos' => $row ['_pos'],
						'_txttitle' => $row ['_txttitle'],
						'_postitle' => $row ['_postitle'],
						'title' => $row ['title'] 
				);
				$data [] = $row;
				if (is_array ( $childs ) && ! empty ( $childs ))
					$data = array_merge ( $data, $childs );
			}
		return empty ( $data ) ? array () : $data;
	}
}
class CustomerRelation_Model extends Customer_Model {
	protected  $table;
	protected  $tableRelation;
	function __construct($table, $tableRelation) {
		parent::__construct ( $table );
		$this->tableRelation = $tableRelation;
		$this->table = $table;
	}
	function insertRelation($id_cate = array(), $id_item) {
		if (! empty ( $id_cate ))
			foreach ( $id_cate as $val )
				parent::_insert ( $this->tableRelation, array (
						'id_cate' => $val,
						'id_item' => $id_item 
				) );
	}
	function delete($ids = array(), $where = '') {
		$this->db->where_in ( "id", $ids );
		if (parent::delete ( $where )) {
			$this->db->where_in ( "id_item", $ids );
			parent::_delete ( $this->tableRelation );
		}
	}
	//Xoa tat ca record cua san pham
	function deleteRelation($id_item) {
		return parent::_delete ( $this->tableRelation, array (
				"id_item" => $id_item 
		) );
	}
	function select($where = '', $order = '', $of = 0, $pp = 0) {
		return $this->selectAll ( $where, $order, $of, $pp );
	}
	//Hien thi tat ca san pham bao gom toan bo id danh muc cua san pham
	function selectAll($where = '', $order = '', $of = 0, $pp = 0) {
		$data = parent::select ( $where, $order, $of, $pp);
		foreach ( $data as $key => &$val ) {
			$relate = parent::_select ( $this->tableRelation, array (
					"id_item" => $val ['id'] 
			) );
			$val ['relation'] = array_keys ( arraymake ( $relate, 'id_cate', 'id_item' ) );
		}
		return $data;
	}
	//Hien thi tat ca san pham cua danh muc
	function selectCategory($id_cate = '', $where = '', $order = '', $of = 0, $pp = 0) {
		if(is_array($id_cate)) $this->db->where_in('id_cate',$id_cate);
		else
		$this->db->where('id_cate',$id_cate);
		$relate = parent::_select($this->tableRelation);
		$id_item = array_keys(arraymake($relate,"id_item","id_cate"));
		if(!empty($id_item))$this->db->where_in ( "id", $id_item );
		else $this->db->where('id',-1);
		return $this->select ( $where, $order, $of, $pp );
	}
	//Hien thi san pham lien quan
	function selectRelation($id_item,$where='',$order='',$of=0,$pp=0)
	{		
		$arr_id_cate = arraymake(parent::_select($this->tableRelation,array('id_item'=>$id_item)),'id_cate','id_cate');
		if(empty($arr_id_cate)) return array();
		$this->db->join($this->tableRelation,"id_item=id");
		$this->db->where_in("id_cate",$arr_id_cate);
		$this->db->where(array("id <>"=>$id_item));
		$this->db->distinct("id");
		$this->db->select($this->table.".*");
		$this->select($where,$order,$of,$pp);
		return $this->select($where,$order,$of,$pp);
	}
	
}
class CustomerLeverRelation_Model extends CustomerLever_Model {
	protected $table;
	protected $tableRelation;
	function __construct($table, $tableRelation) {
		parent::__construct ( $table );
		$this->tableRelation = $tableRelation;
	}
	function selectRelate($where = '', $order = '', $of = 0, $pp = 0) {
		$this->db->join ( $this->tableRelation, 'id = id_cate' );
		$data = $this->select ();
		return $data;
	}
	function delete($ids = array(), $where = '') {
		$this->db->where_in ( "id", $ids );
		if (parent::delete ( $where )) {
			$this->db->where_in ( "pid", $ids );
			parent::update ( array (
					"pid" => 0 
			) );
			$this->db->where_in ( "id_cate", $ids );
			parent::_delete ( $this->tableRelation );
		}
	}

}
class Home_model extends Level_Model
{
	function __construct($table = '')
	{
		parent::__construct($table);
	}
	function select($where='',$order='',$of=0,$pp=0)
	{
		$data = parent::select($where,$order,$of,$pp);
		foreach($data as &$val)
		{
			isset($val['options']) && ($val['options']=json_decode($val['options'],true));
			isset($val['images']) && ($val['images']=json_decode($val['images'],true));
		}
		return $data;
	}
}