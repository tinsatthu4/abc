<?php 
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class layout_home extends Home_controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->languages(array('core/home','layout/layout_home'),$this->appmanager->LANGUAGE);
		$this->load->models(array(
				'layout/mlayout_home',
				'layout/mlayout_category_home',
				));
		$this->model = $this->mlayout_home;
		$this->modelC = $this->mlayout_category_home;
		$this->cachefile->setDir('cache_common/layout');
	}
	function index($page=1)
	{
		$pp = 27;
		$tr = $this->model->countrows(array('sc_status'=>1));
		$tp = ceil($tr/$pp);
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->PAGGING = temp_paging($page,$tp,mysiteurl('layout/index/[x]'));
		$this->ROWS = $this->model->select(array('sc_status'=>1),array('id'=>'desc'),$pp*($page-1),$pp);
		$this->BREADCRUMBS = array(
				array(
						'title'=>lang('text_home'),
						'href'=>mysiteurl(),
				),
				array('title'=>lang('text_title'))
		);
		myview('index',array('CONTENT'=>array('layout/category',$this),
		'TITLE'=>lang("title"),
		'DESCRIPTION'=>lang('description'),
		'KEYWORDS'=>lang('keywords'),
		'METHOD'=>__METHOD__
		));
	}
	function category($id=0,$page=1){
		$this->CATEGORY = array_shift($this->modelC->select(array('id'=>$id,'sc_status'=>1)));
		$this->CATEGORY or show_404();
		$this->BREADCRUMBS = array(
			array(
				'title'=>lang('text_home'),
				'href'=>mysiteurl(),		
			),
			array(
				'title'=>lang('text_title'),
				'href'=>mysiteurl('layout'),		
			),	
			array('title'=>$this->CATEGORY['title']),	
		);
		$pp = 27;
		$tr = $this->model->countrowsCategory($id,array('sc_status'=>1));
		$tp = ceil($tr/$pp);
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		
		$this->PAGGING = temp_paging($page,$tp,str_replace('[id]',$id,$this->CATEGORY['link_paging']));
		$this->ROWS = $this->model->selectCategory($id,array('sc_status'=>1),array('sc_order'=>'asc','id'=>'desc'),$pp*($page-1),$pp);
		myview('index',array('CONTENT'=>array('layout/category',$this),
		'TITLE'=>$this->CATEGORY['title'],
		'DESCRIPTION'=>$this->CATEGORY['options']['description'],
		'KEYWORDS'=>$this->CATEGORY['options']['keywords'],
		'FACEBOOK'=>array(
			'image'=>base_url(thumb($this->CATEGORY['options']['images']?$this->CATEGORY['options']['images']:$_SESSION['user']['logo'],100,100))
		),
		'CATEGORY'=>array(
			'category_id'=>$this->CATEGORY['id'],
		),
		'METHOD'=>__METHOD__
		));
	}
	function detail($id=0){
		$id = intval($id);
		$this->cachefile->setFile('detail_'.$id);
		($this->cachefile->checkCache() && $this->ROW = $this->cachefile->get())
		or
		($this->ROW = array_shift($this->model->select(array('id'=>$id,'sc_status'=>1))));
		$this->ROW or show_404();
		if(!$this->cachefile->checkCache()) $this->cachefile->create($this->ROW);
		$this->BREADCRUMBS = array(
				array(
						'title'=>lang('text_home'),
						'href'=>mysiteurl(),
				),
				array(
						'title'=>lang('text_title'),
						'href'=>mysiteurl('layout'),
				),
				array('title'=>$this->ROW['title']),
		);
		myview('index',array('CONTENT'=>array('layout/detail',$this),
		'TITLE'=>@$this->ROW['options']['title'],
		'DESCRIPTION'=>@$this->ROW['options']['description'],
		'KEYWORDS'=>@$this->ROW['options']['keywords'],
		'FACEBOOK'=>array(
		'image'=>base_url(thumb(@$this->ROW['images'][0]?@$this->ROW['images'][0]:@$_SESSION['user']['logo'],100,100))
		),
		'METHOD'=>__METHOD__,
		));		
	}
	
	/*
	 * Import du lieu mau khi nguoi dung active layout
	 * Chi cap nhan goi toi bang ajax
	 */
	function activeLayout()
	{
		!$this->input->is_ajax_request() && show_404() && exit();
		$this->appmanager->IS_LOGIN or show_404();
		$id = intval(mypost('id'));
		$layout = array_shift($this->model->select(array('id'=>$id,'sc_status'=>1)));	
		!$layout && show_404() && exit() ;
		$this->load->models(array(
				'customer/mcustomer_home',
				));
		$data['id_layout'] = $id;
		
		if($this->mcustomer_home->update($data,array("id"=>$_SESSION['customer']['id']))){
			$this->clearModun();
			$this->clearCache();
			if($_SESSION['customer']['id_layout'] == 0 && !empty($layout['sql_file']) && file_exists($layout['sql_file']))
			{
				$string = read_file($layout['sql_file']);
				$query = str_replace('[x]',$_SESSION['customer']['id'], $string);
				$query = explode('21232f297a57a5a743894a0e4a801fc3',$query);
				foreach($query as $val)
				@$this->db->query($val);
			}
			$_SESSION['customer']['id_layout'] = $id;
			$redirect = $_SESSION['customer']['subdomain'].'.smartwebvn.com';
			$encode_redirect = base64_encode($redirect);
			$data = array(
					"error"=>false,
					'message'=>lang('succ_activeL'),
					'redirect'=>prep_url($redirect."/admin/customer/login/".$encode_redirect),
					);
			echo json_encode($data); exit();
		}
	}
	/*
	 * Insert modun default for template
	 */
	protected function updatePage($id_layout)
	{
		$this->appmanager->CUSTOMER_ID = $_SESSION['customer']['id'];
		$this->load->models (array(
				'modun/mmodun_admin',
				'pages/mpage_admin',
				'layout/mlayout_home',
				'modun/mmodun_default'
		));
		$this->model = $this->mmodun_admin;
		$this->modelP = $this->mpage_admin;
		$this->default = $this->mmodun_default;
		$ids = $this->modelP->selectPageOfLayout(intval($id_layout));
		$this->PAGES = $this->modelP->selectPage($ids,0,'',array('sc_order'=>'asc'));
		$default = array_shift($this->default->select(array('id_layout'=>$id_layout)));
		$arr = array('header','footer','left','right','content_top','content_bottom');
		foreach($arr as $val)
			if(!empty($default[$val])) $data[$val] = $default[$val];
		else $data[$val] = array();
		$data['customer_id'] = $_SESSION['customer']['id'];
		$this->model->delete('');
		foreach($this->PAGES as $value)
		{
			$data['id_page'] = $value['id'];
			$this->model->insertDefault($data);
		}
	}
	protected function getQuery($id = 0,$pa = 0)
	{
		if($id == 0)
		{
			$this->db->join('layout_cate','layout_cate.id=layout.pid');
			$this->db->where('layout_cate.pid',$pa);
			$this->db->select('layout.*');
		}
		else $this->db->where('pid',intval($id));
	}
	protected function clearModun()
	{
		$this->db->delete('modun',array('customer_id'=>$_SESSION['customer']['id']));
	}
	protected function clearCache()
	{
		@delete_files('cache/'.$_SESSION['customer']['email'],true);
	}
}

?>