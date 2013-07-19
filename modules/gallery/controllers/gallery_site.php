<?php 
if(!defined('BASEPATH'))
	exit('No direct script access allowed');
class gallery_site extends Site_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->models(array(
				'pages/mpage_site',
				'modun/mmodun_site',
				'gallery/mgallery_site',
				'gallery/mgallery_category_site'
				));
		$this->load->languages(array(
				'gallery/gallery_site'
				),$this->appmanager->LANGUAGE);
		$this->modelM = $this->mmodun_site;
		$this->model = $this->mgallery_site;
		$this->modelC = $this->mgallery_category_site;
		$this->modelP = $this->mpage_site;
	}
	function index($page=1)
	{
		/*
		 * Lay widget cho pages
		*/
		$this->modelP->checkPage($this->appmanager->ID_LAYOUT,__METHOD__)
		or show_404();
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or $WIDGETS = $this->modelM->create(__METHOD__);
		
		/*CODE MODULES HERE*/
		$pp = temp_perpage();
		$tr = $this->model->countrows(0,array('sc_status'=>1));
		$tp = ceil($tr/$pp);
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->PAGING = temp_paging($page,$tp,mysiteurl('gallery/p[x]'));
		$this->ROWS = $this->model->select(array('sc_status'=>1),array('sc_order'=>'asc','id'=>'desc'),$pp*($page-1),$pp);
		$this->BREADCRUMBS = array(
				array(
						"title"=>lang('br_home'),
						"link"=>mysiteurl()
				),
				array(
						"title"=>lang("gallery_index"),
						"link"=>"javascript:void()",
				)
		);
		myview ( 'index', array (
		'CONTENT' => array (
		'gallery/index',
		$this
		),
		'WIDGETS'=> $WIDGETS,
		'METHOD'=>'gallery_site::index',
		) );
	}
	function category($id=0,$page=1)
	{
		/*
		 * Lay widget cho pages
		*/
		$this->modelP->checkPage($this->appmanager->ID_LAYOUT,__METHOD__)
		or show_404();
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or $WIDGETS = $this->modelM->create(__METHOD__);
		
		/*CODE MODULES HERE*/		
		$id = intval($id);
		$this->CATEGORY = array_shift($this->modelC->select(array('id'=>$id,'sc_status'=>1)));
		$this->CATEGORY or show_404();
		$tr = $this->model->countrows($id,array('sc_status'=>1));
		$pp = temp_perpage();
		$tp = ceil($tr/$pp);
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->ROWS = $this->model->selectCategory($id,array('sc_status'=>1),array('sc_order'=>'asc','id'=>'desc'),$pp*($page-1),$pp);
		$this->PAGING = temp_paging($page,$tp,$this->CATEGORY['link_paging']);
		$this->BREADCRUMBS = array(
				array(
					"title"=>lang('br_home'),
					"link"=>mysiteurl(),
				),
				array(
					"title"=>lang("gallery_index"),
					"link"=>"javascript:void()",
				),
				array(
					"title"=>$this->CATEGORY['title'],
					"link"=>"javascript:void()",	
				),
		);
		myview ( 'index', array (
		'CONTENT' => array (
		'gallery/category',
		$this
		),
		'WIDGETS'=> $WIDGETS,
		'METHOD'=>'gallery_site::index',
		'TITLE'=>$this->CATEGORY['title'],
		'KEYWORDS'=>$this->CATEGORY['options']['keywords'],
		'DESCRIPTION'=>$this->CATEGORY['options']['description'],
		'OPTIONS'=>array(
			'category_id'=>$id,
		),
		));		
	}
	function detail($id=0)
	{
		/*
		 * Lay widget cho pages
		*/
		$this->modelP->checkPage($this->appmanager->ID_LAYOUT,__METHOD__)
		or show_404();
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or $WIDGETS = $this->modelM->create(__METHOD__);
		
		/*CODE MODULES HERE*/
		$id = intval($id);
		$this->ROW = array_shift($this->model->select(array('sc_status'=>1,'id'=>intval($id))));
		$this->ROW or show_404();
		$this->RELATION = $this->model->selectRelation(intval($id),array('sc_status'=>1,"id <>"=>intval($id)),array('sc_order'=>'asc','id'=>'desc'),0,temp_perpage());
		$this->BREADCRUMBS = array(
			array(
					"title"=>lang('br_home'),
					"link"=>mysiteurl()
			),
			array(
					"title"=>lang("gallery_index"),
					"link"=>mysiteurl("gallery"),
			),
			array(
					"title"=>$this->ROW['title'],
					"link"=>"javascript:void()",
			)
		);
		myview ( 'index', array (
		'CONTENT' => array (
		'gallery/detail',
		$this
		),
		'WIDGETS'=> $WIDGETS,
		'METHOD'=>'gallery_site::index',
		'METHOD_PAGE'=>strtolower(__METHOD__),		
		'TITLE'=>$this->ROW['options']['title'],
		'KEYWORDS'=>$this->ROW['options']['keywords'],
		'DESCRIPTION'=>$this->ROW['options']['description'],
		'FACEBOOK'=>array(
		'image'=>base_url(thumb(getPath($this->ROW['images'][0]),100,100)),
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
		if(mypost()) $like = stringaccent(mypost('search'));
		($like == '')&&show_404();
		$like = preg_replace('/[^a-zA-Z0-9]/',' ', $like);
		$this->db->like('title',$like);
		$tr = $this->model->countrows(0,array('sc_status'=>1));
		$pp = temp_perpage();
		$tp = ceil($tr/$pp);
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->db->like('title',$like);
		$this->ROWS = $this->model->select(array('sc_status'=>1),array('sc_order'=>'asc','id'=>'desc'),$pp*($page-1),$pp);
		$this->PAGING = temp_paging($page,$tp,mysiteurl('gallery/tim-kiem/'.$like.'/p[x]'));
		myview ('index', array (
		'CONTENT' => array (
		'gallery/search',
		$this
		),
		'WIDGETS'=> $WIDGETS,
		'TITLE'=>lang('search'),
		'METHOD'=>'gallery_site::index',
		'NOFOLLOW'=>true,
		));
	}
}
?>