<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Orders_admin extends Admin_Controller {
	function __construct() {
		parent::__construct ();
		in_array('orders',$this->appmanager->MODULES) or show_404();
		$this->load->models ( array (
				'orders/morders_admin',
		));
		$this->model = $this->morders_admin;
		$this->load->languages ( array (
				'core/common',
				'orders/orders_admin',
				'customer/customer' 
		), $this->appmanager->LANGUAGE );
	}

	function index($page = 1) {
		if (mypost ()) {
			$ids = mypost ( 'id' );
			switch (mypost ( 'action' )) {
				case 'view' :
					myredirect ( 'admin/orders/detail/' . intval ( array_shift ( $ids ) ) );
					break;
				case 'delete' :
					$this->db->where_in ( "id", $ids );
					$this->model->delete ( '' );
					$this->db->where_in ( "id_order", $ids );
					$this->model->deleteOrders_info ();
					break;
			}
		}
		$this->ROWS = $this->model->select ( '', array (
				"sc_status" => "asc" 
		) );
	
		myview ( "index", array (
				"CONTENT" => array (
						"orders/index",
						$this 
				) 
		) );
	}
	function detail($id = 0) {
		if (mypost ( 'id' ))
			$id = intval ( array_shift ( mypost ( 'id' ) ) );
		$this->ROW = array_shift ( $this->model->select ( array (
				"id" => $id 
		) ) );
		$this->ROW or show_404 ();
		if (mypost ( 'history' )) {
			$data = array (
					'sc_status' => intval ( mypost ( 'status' ) )
			);
			if ($this->model->update ( $data, array (
					"id" => $id
			) ))
				$this->ROW ['sc_status'] = mypost ( 'status' );
		}
		myview ( "index", array (
				"CONTENT" => array (
						"orders/detail",
						$this 
				) 
		) );
	}
	function attr()
	{
		$this->load->models(array('orders/mattr'));
		$this->modelA = $this->mattr;
		$this->ROW = @array_shift($this->modelA->select());
		if(mypost())
		{
			$data['options'] = mypost('options');
			foreach($data['options'] as $i=>&$val)
				if(empty($val)) unset($data['options'][$i]);
			else $val = strip_tags($val);
			if(!empty($data['options'])){
				($this->ROW && $this->modelA->update($data)) or $this->modelA->insert($data);
				$this->ROW['options'] = $data['options'];
			}
		}
		myview ( "index", array (
		"CONTENT" => array (
		"orders/attr",
		$this
		)
		) );
	}
}
?>