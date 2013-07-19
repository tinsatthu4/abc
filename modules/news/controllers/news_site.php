<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class news_site extends Site_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->languages ( array (
				'core/common',
				'news/news_site' 
		), $this->appmanager->LANGUAGE );
		$this->load->models(array(
				'pages/mpage_site',
				'modun/mmodun_site',
				'news/mnews_site',
				'news/mnews_category_site'
		));
		$this->modelM = $this->mmodun_site;
		$this->model = $this->mnews_site;
		$this->modelC = $this->mnews_category_site;
		$this->modelP = $this->mpage_site;
	}
	function index($page = 1) {
		/*
		 * Lay widget cho pages
		*/
		$this->modelP->checkPage($this->appmanager->ID_LAYOUT,__METHOD__)
		or show_404();
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or $WIDGETS = $this->modelM->create(__METHOD__);
		
		/*CODE MODULES HERE*/
		$tr = $this->model->countrows(0,array('sc_status'=>1));
		$pp = temp_perpage();
		$tp = ceil($tr/$pp);
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->ROWS = $this->model->select(array('sc_status'=>1),array('sc_order'=>'asc'),$pp*($page-1),$pp);
		$this->PAGING = temp_paging($page,$tp,mysiteurl('news/p[x]'));	
		empty($this->ROWS) && $this->ER = lang('nodata');
		$this->BREADCRUMBS = array(
				array(
						"title"=>lang('br_home'),
						"link"=>mysiteurl()
				),
				array(
						"title"=>lang("news_index"),
						"link"=>"javascript:void()",
				)
		);
		
		myview ( 'index', array (
				'CONTENT' => array (
						'news/index',
						$this 
				),
				'WIDGETS'=> $WIDGETS,
				'METHOD'=>'news_site::index',
		) );
	}
	function detail($id = 0) {
		/*
		 * Lay widget cho pages
		*/
		$this->modelP->checkPage($this->appmanager->ID_LAYOUT,__METHOD__)
		or show_404();
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or $WIDGETS = $this->modelM->create(__METHOD__);
		
		/*CODE MODULES HERE*/
		$this->ROW = array_shift($this->model->select(array('sc_status'=>1,'id'=>intval($id))));
		$this->ROW or show_404();
		$this->RELATION = $this->model->selectRelation(intval($id),array('sc_status'=>1,"id <>"=>intval($id)),array('sc_order'=>"asc"),0,temp_perpage());
		$category = array_values(arraymakes($this->modelC->selectByItem($id), "id",array("title","link")));
		$this->BREADCRUMBS = array(
				array(
						"title"=>lang('br_home'),
						"link"=>mysiteurl(),
				),
				array(
						"title"=>lang("news_index"),
						"link"=>mysiteurl("news"),
				));
		if(!empty($category))
			$this->BREADCRUMBS = array_merge($this->BREADCRUMBS,$category);
		$this->BREADCRUMBS[] =array(
				"title"=>$this->ROW['title'],
				"link"=>"javascript:void()",
		);
		
		myview ( 'index', array (
		'CONTENT' => array (
		'news/detail',
		$this
		),
		'WIDGETS'=> $WIDGETS,
		'METHOD'=>'news_site::index',
		'TITLE'=>@$this->ROW['options']['title'],
		'KEYWORDS'=>@$this->ROW['options']['keywords'],
		'DESCRIPTION'=>@$this->ROW['options']['description'],
		'FACEBOOK'=>array(
			'image'=>@isUrlImg($this->ROW['images'][0])?base_url(thumb($this->ROW['images'][0],100,100)):mythemeurl("media/images/no-images.jpg"),
		),
		) );
	}
	function category($id=0,$page=1) {
		/*
		 * Lay widget cho pages
		*/
		$this->modelP->checkPage($this->appmanager->ID_LAYOUT,__METHOD__)
		or show_404();
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or $WIDGETS = $this->modelM->create(__METHOD__);
		
		/*CODE MODULES HERE*/
		$this->CATEGORY = $ROW = array_shift($this->modelC->select(array('id'=>$id,'sc_status'=>1)));
		$ROW or show_404();
		$tr = $this->model->countrows($id,array('sc_status'=>1));
		$pp = temp_perpage();
		$tp = ceil($tr/$pp);
		$this->ROWS = $this->model->selectCategory($id,array('sc_status'=>1),array('sc_order'=>'asc'),$pp*($page-1),$pp);
		$this->PAGING = temp_paging($page,$tp,$this->CATEGORY['link_paging']);
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		empty($this->ROWS) && $this->ER = lang('nodata');
		$this->BREADCRUMBS = array(
				array(
						"title"=>lang('br_home'),
						"link"=>mysiteurl()
				),
				array(
						"title"=>lang("news_index"),
						"link"=>mysiteurl("news"),
				),
				array(
						"title"=>$this->CATEGORY['title'],
						"link"=>"javascript:void()",
				),
		);
		myview ( 'index', array (
		'CONTENT' => array (
		'news/category',
		$this
		),
		'WIDGETS'=> $WIDGETS,
		'METHOD'=>'news_site::index',
		'TITLE'=>$ROW['title'],
		'KEYWORDS'=>$ROW['options']['keywords'],
		'DESCRIPTION'=>$ROW['options']['description'],
		'OPTIONS'=>array(
			"category_id"=>$id,
		),
		) );
	}
	function search($like='',$page=1)
	{
		/*
		 * Lay widget cho pages
		*/
		$this->modelP->checkPage($this->appmanager->ID_LAYOUT,__METHOD__)
		or show_404();
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or $WIDGETS = $this->modelM->create(__METHOD__);
		
		/*CODE MODULES HERE*/
		if(mypost()) $like = mypost('search');
		($like == '')&&show_404();
		$br_title = $like;
		$like = preg_replace('/[^a-zA-Z0-9]/',' ', stringaccent(trim($like)));
		$this->db->like('title',$like);
		$tr = $this->model->countrows(0,array('sc_status'=>1));
		$pp = temp_perpage();
		$tp = ceil($tr/$pp);
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->db->like('title',$like);
		$this->ROWS = $this->model->select(array('sc_status'=>1),array('sc_order'=>'asc','id'=>'desc'),$pp*($page-1),$pp);
		$this->PAGING = temp_paging($page,$tp,mysiteurl('products/search/'.str_replace(" ","-",$like).'/p[x]'));
		empty($this->ROWS) && $this->ER = lang('nodata');
		$this->BREADCRUMBS = array(
				array(
						"title"=>lang('br_home'),
						"link"=>mysiteurl(),
				),
				array(
						"title"=>lang('pro_index'),
						"link"=>mysiteurl("products"),
				),
				array(
						"title"=>lang("pro_search"),
						"link"=>mysiteurl('news/search/'.str_replace(" ","-",$like)),
				),
				array(
						"title"=>$br_title,
						"link"=>mysiteurl('news/search/'.str_replace(" ","-",$like)),
				)
		);
		myview ( 'index', array (
		'CONTENT' => array (
		'news/search',
		$this
		),
		'WIDGETS'=> $WIDGETS,
		'TITLE'=>lang('search'),
		'METHOD'=>'news_site::index',
		"NOFOLLOW"=>true,
		));
	}
}