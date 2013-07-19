<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Pages_customer_site extends Site_Controller
{
	function __construct(){
		parent::__construct();
		$this->load->languages(array("pages_customer/pages_customer_site"),$this->appmanager->LANGUAGE);
		$this->load->models(array(
				'pages_customer/mpages_customer_site',
				'pages/mpage_site',
				'modun/mmodun_site',
				));
		$this->modelM = $this->mmodun_site;
		$this->modelP = $this->mpage_site;
		$this->model = $this->mpages_customer_site;
	}
	
	function index($id = 0)
	{
		/*
		 * Lay widget cho pages
		*/
		$this->modelP->checkPage($this->appmanager->ID_LAYOUT,__METHOD__)
		or show_404();
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or $WIDGETS = $this->modelM->create(__METHOD__);
		
		$id = intval($id);
		$this->cachefile->appendDir('pages_customer');
		$this->cachefile->setFilename($id);
		($this->cachefile->checkCache() && $this->ROW = $this->cachefile->get())
		or
		($this->ROW=array_shift($this->model->select(array('sc_status'=>1,'id'=>$id))))
		or show_404();
		!$this->cachefile->checkCache() && $this->cachefile->create($this->ROW);
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or show_404();
		$this->BREADCRUMBS = array(
			array(
					"title"=>lang('br_home'),
					"link"=>mysiteurl()
			),
			array(
					"title"=>$this->ROW['title'],
					"link"=>"javascript:void()",
			)
		);
		myview('index',array('CONTENT'=>array('pages_customer/index',$this),
		'TITLE'=>$this->ROW['options']['title'],
		'KEYWORDS'=>$this->ROW['options']['keywords'],
		'DESCRIPTION'=>$this->ROW['options']['description'],
		'PAGE_ACTIVE'=>intval($id),
		'WIDGETS'=> $WIDGETS,
		'METHOD'=>$id,
		));
	}
}
?>