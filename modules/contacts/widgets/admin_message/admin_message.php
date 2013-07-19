<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_contacts_admin_message extends Widget {
	function __construct()
	{
		parent::__construct();
		$this->load->models(array(
				'contacts/mcontacts_site',
				));
		$this->load->languages(array(
				'contacts/contacts_admin'
				),$this->appmanager->LANGUAGE);
		$this->model = $this->mcontacts_site;
	}
	function render($_options = array())
	{
		$_options['NUMBER'] = $this->model->countrows(array('sc_status'=>0));
		return myview('widget',array(
		'CONTENT' =>array(
				'contacts/widgets/'.(@$_options['layout']?$_options['layout']:'admin_message'),
				$_options 
		) 
		), true );
	}
}
?>