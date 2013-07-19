<?php 
class Widget_orders_admin_message extends Widget
{
	function __construct()
	{
		parent::__construct();
		$this->load->models(array(
				'orders/morders_admin'
				));
		$this->load->languages(array(
				'orders/orders_admin'
				),$this->appmanager->LANGUAGE);
		$this->model = $this->morders_admin;
	}
	function render($_options = array())
	{
		return myview('widget',array(
				"CONTENT"=>array(
				'orders/widgets/admin_message',
				$_options
				)),true);
	}
}
?>