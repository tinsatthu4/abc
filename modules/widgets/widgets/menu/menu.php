<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widget_widgets_menu extends Widget
{
	function __construct()
	{
		parent::__construct();
		$this->load->models(array(
				'pages/mpage_site',
				));
		$this->model = $this->mpage_site;

	}
	function render($_options = array())
	{
		$_options['ROWS'] = $this->model->getPage($this->appmanager->ID_LAYOUT);
		return myview('widget',array(
				'CONTENT' =>array(
						'widgets/menu/'.$_options['layout'] ,
						$_options
				)
		), true );
	}
}

?>