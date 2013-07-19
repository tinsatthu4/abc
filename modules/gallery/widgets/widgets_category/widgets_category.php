<?php 
class Widget_gallery_widgets_category extends Widget
{
	function __construct()
	{
		parent::__construct();
		$this->load->model(array(
			'gallery/mgallery_site',	
		));
		$this->model = $this->mgallery_site;
	}
	function render($_options = array())
	{
		$this->cachefile->setDirectory("cache/".$this->appmanager->CUSTOMER['email']."/widgets");
		$this->cachefile->setFilename($_options['primary_key']);
		
		if($this->cachefile->checkCache()) $_options['ROWS'] = $this->cachefile->get();
		else
		{
			$_options['ROWS'] = array();
			$orders = array("sc_order"=>"asc","id"=>"desc");
			if(@in_array($_options['sort'],array('sc_utime','title')))
				$orders = array($_options['sort']=>"desc");
			if(@in_array($_options['sort'],array('asc_title')))
				$orders = array(str_replace('asc_','',$_options['sort'])=>"asc");
			if(@is_array($_options['key']) && !empty($_options['key'])){
				$this->db->join('gallery_relate','id=id_item');
				$this->db->where_in("id_cate",$_options['key']);
				$_options['ROWS'] = $this->model->select(array('sc_status'=>1),$orders,0,intval(isset($_options['limit'])?$_options['limit']:10));
			}
		}		
		if(!$this->cachefile->checkCache())
			$this->cachefile->create($_options['ROWS']);		
		
		
		return myview('widget',array(
			'CONTENT' =>array(
					'gallery/widgets/'.$_options['layout'],
				$_options 
			) 
		), true );
	}
}

?>