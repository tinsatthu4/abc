<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_comments_administrator_message extends Widget
{
	function __construct()
	{
		parent::__construct();

		$this->load->models(array('comments/mcomments_administrator'));
		$this->model = $this->mcomments_administrator;
	}
	function render($_options = array())
	{
		$_options['ROWS'] = $this->model->select(array("sc_status"=>1));
		$_options['NUMBER'] = count($_options['ROWS']);
		return myview('widget',array(
				'CONTENT' =>array(
						'comments/widgets/administrator_message',
						$_options
				)
		), true );
	}
}

?>