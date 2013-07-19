<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class orders_site extends Site_Controller
{
	protected $key;
	protected $limit;
	function __construct(){
		parent::__construct();
		$this->key = md5('cart_'.$this->appmanager->CUSTOMER_ID);
		$this->limit = md5('tinsatthu4');
		$this->load->models(array(
				'pages/mpage_site',
				'modun/mmodun_site',
				'orders/morders_site',
				'orders/mattr',
				'products/mproducts_site'
				));
		$this->load->languages(array(
				'orders/orders_site'
				),$this->appmanager->LANGUAGE);
		$this->modelM = $this->mmodun_site;
		$this->model = $this->morders_site;
		$this->modelA = $this->mattr;
		$this->modelP = $this->mproducts_site;
		$this->modelPage = $this->mpage_site;
		$this->clear();
	}
	function index()
	{
		/*
		 * Lay widget cho pages
		*/
		$this->modelPage->checkPage($this->appmanager->ID_LAYOUT,__METHOD__)
		or show_404();
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or $WIDGETS = $this->modelM->create(__METHOD__);
		
		/*CODE MODULES HERE*/		
		$this->BREADCRUMBS = array(
				array(
						"title"=>lang("br_home"),
						"link"=>mysiteurl(),
				),
				array(
						"title"=>lang("orders_index"),
						"link"=>"javascript:void()",
				),
		);
	
		switch(mypost('action'))
		{
			case 'update':
			$number = @mypost('number');
			foreach($_SESSION[$this->key]['item'] as $key=>&$val)
				$val['number'] = intval(@$number[$val['id']])<=0?1:intval(@$number[$val['id']]);
			break;
			case 'deleteall':
			$this->destroy();	
			break;
			case 'delete':
			$id = @intval(mypost('id_cart'));
			@$cart = $_SESSION[$this->key]['item'];
			$number = intval(@$cart[$id]['number']);			
			unset($cart[$id]);
			@$_SESSION[$this->key]['item'] = @$cart;
			@$_SESSION[$this->key]['total_cart'] = @$_SESSION[$this->key]['total_cart']-@$number;
			if(empty($_SESSION[$this->key]['item'])) $this->destroy();
			break;
			case 'payment':
			$ruleslist = array(
				array('options[name]','lang:name','required|trim|strip_tags'),
				array('options[address]','lang:name','required|trim|strip_tags'),
				array('options[phone]','lang:name','required|trim|strip_tags'),
				array('options[note]','lang:name','required|trim|strip_tags'),
			);
			$this->ER = myvalid(mypost(), $ruleslist);
			if(!@$this->ER){
			($this->check()) or myredirect('orders');	
			$this->STATUS = $this->payment();
			if($this->STATUS) $this->SU = lang("success");
			myview('index',array('CONTENT'=>array('orders/payment',$this),
			'METHOD'=>'orders_site::index',
			'WIDGETS'=> $WIDGETS,
			));
			exit();	
			}
			$this->ER = lang('error_not_full');
			break;
		}
		if(@$_SESSION[$this->key])
		{
			$ids = arraymake($_SESSION[$this->key]['item'],'id','id');
			$this->db->where_in('id',$ids);
			$this->ROWS = $this->modelP->select(array('sc_status'=>1));
			$this->ATTR = @array_shift($this->modelA->select());
			$this->ATTR = @$this->ATTR?$this->ATTR['options']:lang('attr_orders');
			$this->CART = $_SESSION[$this->key]['item'];
			$this->NUMBER = arraymake($_SESSION[$this->key]['item'],'id','number');
		}else
			$this->ER = lang("nodata");
		myview('index',array('CONTENT'=>array('orders/index',$this),
		'METHOD'=>'orders_site::index',
		'WIDGETS'=> $WIDGETS,
		));
	}
	function add()
	{	if(!$this->input->is_ajax_request()) show_404();
		($this->input->is_ajax_request() && mypost()) or show_404();
		$id = @mypost('id');
		$row = @array_shift($this->modelP->select(array('id'=>$id,'sc_status'=>1)));
		$row or show_404();
		$key = $this->key;
		if(!isset($_SESSION[$this->key]))
			$_SESSION[$this->key] = array(
					$this->limit =>(time()+(60*60)),
					'total_cart'=>0,
					'item'=>array()
			);
		$cart = $_SESSION[$this->key]['item'];
		if(isset($cart[$id]))
			++$cart[$id]['number'];
		else
			$cart[$id] = array(
					'id'=>$id,
					'number'=>1,
					'price'=>$this->getPrice($row)
			);
		$_SESSION[$this->key]['item'] = $cart;
		$number = array_sum(arraymake($cart,'id','number'));
		$_SESSION[$this->key]['total_cart'] = $number;
		echo json_encode($number);
	}
	protected function payment()
	{
		$ids = arraymake($_SESSION[$this->key]['item'], 'id', 'id');
		$CART = $_SESSION[$this->key]['item'];
		$this->db->where_in('id',$ids);
		$ROWS = $this->modelP->select(array('sc_status'=>1));
		$data = array();
		$total = 0;
		foreach($ROWS as $val)
		{
			$data[] = array(
					'id_pro'=>$val['id'],
					'number'=>$CART[$val['id']]['number'],
					'price'=>$CART[$val['id']]['price'],
					);
			$total += $CART[$val['id']]['number']*$CART[$val['id']]['price'];
		}
		if($this->model->insert(array(
				'options'=>mypost('options'),
				'total'=>$total,
				'sc_utime'=>time(),
				'sc_ctime'=>time(),
				)))
		{
			$id = $this->db->insert_id();
			foreach($data as &$val)
			{
				$val['id_order'] = $id;
				$this->model->insertOrders_info($val);
			}
			$this->destroy();
			return true;
		}
		return false;
	}
	protected function destroy()
	{
		$_SESSION[$this->key] = null;
		unset($_SESSION[$this->key]);
	}
	protected function clear()
	{
		foreach($_SESSION as $key=>&$val)
			if(isset($val[$this->limit])&&$val[$this->limit] < time())
			unset($_SESSION[$key]);
	}
	protected function check()
	{
		if(isset($_SESSION[$this->key])) return true;
		return false;
	}
	protected function getPrice($product)
	{
		return $product['price_sale']>0?$product['price_sale']:$product['price'];
	}
}
?>