<?php

class Widget_news_widgets_show_category extends Widget {
	function __construct() {
		parent::__construct ();
		$this->load->models(array('news/mnews_category_site'));
		$this->load->languages(array(
				'news/news_site'
		),$this->appmanager->LANGUAGE);
		$this->model = $this->mnews_category_site;
	}
	function render($_options = array()){
		$this->cachefile->setDirectory("cache/".$this->appmanager->CUSTOMER['email']."/widgets");
		$this->cachefile->setFilename($_options['primary_key']);
		if($this->cachefile->checkCache()) $_options['ROWS'] = $this->cachefile->get();
		else {
		$_options ['ROWS'] = $this->model->listchild(0,array('sc_status'=>1),array('sc_order'=>'asc','id'=>'desc'),0);
		}
		if(!$this->cachefile->checkCache())
			$this->cachefile->create($_options['ROWS']);
		return myview('widget', array (
				'CONTENT' => array (
						'news/widgets/'.$_options['layout']."_category",
						$_options 
				) 
		),true);
	}
}