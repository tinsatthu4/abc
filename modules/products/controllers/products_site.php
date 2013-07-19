<?php
if (! defined ( 'BASEPATH' ))
exit('No direct script access allowed');
class products_site extends Site_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->models(array(
				'modun/mmodun_site',
				'pages/mpage_site',
				'products/mproducts_site',
				'products/mproducts_cate_site',
				'products/mattr'
				));
		$this->load->languages(array(
				'products/products_site'
				),$this->appmanager->LANGUAGE);
		$this->modelM = $this->mmodun_site;
		$this->model = $this->mproducts_site;
		$this->modelC = $this->mproducts_cate_site;
		$this->modelA = $this->mattr;
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
		$this->ROWS = $this->model->select(array('sc_status'=>1),array('sc_order'=>'asc','id'=>'desc'),$pp*($page-1),$pp);
		$this->PAGING = temp_paging($page,$tp,mycustomerurl('products/p[x]'));
		empty($this->ROWS) && $this->ER = lang('noupdate');
		$this->BREADCRUMBS = array(
			array(
			"title"=>lang('br_home'),
			"link"=>mysiteurl()		
			),
			array(
			"title"=>lang("pro_index"),
			"link"=>"javascript:void()",
			)		
		);
		myview ( 'index', array (
				'CONTENT' => array (
						'products/index',
						$this 
				),
				'WIDGETS'=> $WIDGETS,
				'METHOD'=>strtolower(__METHOD__),
				'METHOD_PAGE'=>strtolower(__METHOD__),
				"OPTIONS"=>array(),
		) );
	}
	function detail($id=0) {
		/*
		 * Lay widget cho pages
		*/
		$this->modelP->checkPage($this->appmanager->ID_LAYOUT,__METHOD__)
		or show_404();
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or $WIDGETS = $this->modelM->create(__METHOD__);
		
		/*CODE MODULES HERE*/
		$id = intval($id);
		$this->cachefile->config("detail_".$id,$this->cachefile->getDir()."/products");
		($this->cachefile->checkCache() && $this->ROW = $this->cachefile->get())
		or
		($this->ROW = array_shift($this->model->select(array('sc_status'=>1,'id'=>intval($id)))));
		$this->ROW or show_404();
		!$this->cachefile->checkCache() && $this->cachefile->create($this->ROW);
		$this->ATTR = array_shift($this->modelA->select());
		$this->RELATION = $this->model->selectRelation(intval($id),array('sc_status'=>1,"id <>"=>intval($id)),array('sc_order'=>'asc','id'=>'desc'),0,temp_perpage());
		$category = array_values(arraymakes($this->modelC->selectByItem($id), "id",array("title","link")));
		$this->BREADCRUMBS = array(
			array(
					"title"=>lang('br_home'),
					"link"=>mysiteurl(),
			),
			array(
					"title"=>lang("pro_index"),
					"link"=>mysiteurl("products"),
			));
		if(!empty($category))
			$this->BREADCRUMBS = array_merge($this->BREADCRUMBS,$category);
		$this->BREADCRUMBS[] =array(
					"title"=>$this->ROW['title'],
					"link"=>"javascript:void()",
			);		
		
		myview ( 'index', array (
		'CONTENT' => array (
		'products/detail',
		$this
		),
		'WIDGETS'=> $WIDGETS,
		'METHOD'=>'products_site::index',
		'METHOD_PAGE'=>strtolower(__METHOD__),		
		'TITLE'=>$this->ROW['options']['title'],
		'KEYWORDS'=>$this->ROW['options']['keywords'],
		'DESCRIPTION'=>$this->ROW['options']['description'],
		'FACEBOOK'=>array(
		'image'=>@isUrlImg($this->ROW['images'][0])?base_url(thumb(getPath($this->ROW['images'][0]),100,100)):"",
		),
		));
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
		$this->CATEGORY = array_shift($this->modelC->select(array('id'=>$id,'sc_status'=>1)));
		$this->CATEGORY or show_404();
		$tr = $this->model->countrows($id,array('sc_status'=>1));
		$pp = temp_perpage();
		$tp = ceil($tr/$pp);
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));	
		$this->ROWS = $this->model->selectCategory($id,array('sc_status'=>1),array('sc_order'=>'asc','id'=>'desc'),$pp*($page-1),$pp);
		$this->PAGING = temp_paging($page,$tp,$this->CATEGORY['link_paging']);
		empty($this->ROWS) && $this->ER = lang('noupdate');
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
					"title"=>$this->CATEGORY['title'],
					"link"=>"javascript:void()",
			)
		);
		myview ( 'index', array (
		'CONTENT' => array (
		'products/category',
		$this
		),
		'WIDGETS'=> $WIDGETS,
		'METHOD'=>'products_site::index',
		'TITLE'=>$this->CATEGORY['title'],
		'KEYWORDS'=>$this->CATEGORY['options']['keywords'],
		'DESCRIPTION'=>$this->CATEGORY['options']['description'],
		"OPTIONS"=>array(
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
		$this->PAGING = temp_paging($page,$tp,mysiteurl('products/tim-kiem/'.str_replace(" ","-",$like).'/[x]'));
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
						"link"=>mysiteurl('products/search/'.str_replace(" ","-",$like)),
				),
				array(
						"title"=>$br_title,
						"link"=>mysiteurl('products/search/'.str_replace(" ","-",$like)),
				)
		);
		
		myview ( 'index', array (
		'CONTENT' => array (
		'products/search',
		$this
		),
		'WIDGETS'=> $WIDGETS,
		'TITLE'=>lang('search'),
		'METHOD'=>'products_site::index',
		'NOFOLLOW'=>true,
		));
	}
}