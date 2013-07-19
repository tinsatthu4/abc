<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Widget_widgets_widgets_google_maps extends Widget
{
	function __construct(){
		parent::__construct();
	}
	
	function render($_options = array())
	{
		$this->cachefile->config($_options['primary_key'],"cache/".$this->appmanager->CUSTOMER['email']."/widgets");
		if($this->cachefile->checkCache()) $_options = $this->cachefile->get();
		if(!$this->cachefile->checkCache())
			$this->cachefile->create($_options);
		return myview('widget',array(
				'CONTENT' =>array(
						'widgets/widgets_google_maps/'.$_options['layout'],
						$_options
				)
		), true );
	}
}
?>