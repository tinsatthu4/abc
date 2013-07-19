<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_comments_admin_message extends Widget
{
	function __construct()
	{
		parent::__construct();
		$this->load->languages(array(
				'comments/comments_admin'
		),$this->appmanager->LANGUAGE);
		$this->load->models(array('comments/mcomments_admin'));
		$this->model = $this->mcomments_admin;
	}
	function render($_options = array())
	{
		$_options['ROWS'] = $this->model->select(array("sc_status"=>1));
		$_options['NUMBER'] = count($_options['ROWS']);
		return myview('widget',array(
				'CONTENT' =>array(
						'comments/widgets/admin_message',
						$_options
				)
		), true );
	}
}

?>