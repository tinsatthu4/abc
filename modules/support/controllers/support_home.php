<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class support_home extends Home_controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->languages(array(
				'core/home',
				'support/support_attr',
				),$this->appmanager->LANGUAGE);
		$this->load->models(array('support/msupport_home'));
		$this->model = $this->msupport_home;
	}
	function index($page=1)
	{
		$tr = $this->model->countrows(array('sc_status'=>1));
		$pp = temp_perpage();
		$tp = ceil($tr/$pp);		
		$page = $page>1?($page<$tp?$page:($tp<1?1:$tp)):1;
		$this->PAGGING = temp_paging($page,$tp,mysiteurl('support/p[x]'));
		$this->ROWS = $this->model->select(array('sc_status'=>1),array('sc_order'=>'asc'),$pp*($page-1),$pp);
		$this->BREADCRUMBS = array(
				array(
						'title'=>lang('text_home'),
						'href'=>mysiteurl(),
				),		
				array(
						'title'=>lang('text_support'),
				),		
		);
		myview('index',array('CONTENT'=>array('support/index',$this),
		'METHOD'=>__METHOD__,
		'title'=>lang('title'),
		'description'=>lang('description'),
		));
	}
	function detail($id = 0)
	{
		$this->ROW = array_shift($this->model->select(array('sc_status'=>1,'id'=>intval($id))));
		$this->ROW or show_404();
		$this->ROWS = $this->model->select(array('sc_status'=>1,'id <>'=>intval($id)),array('id'=>'desc'),0,10);
		$this->BREADCRUMBS = array(
				array(
						'title'=>lang('text_home'),
						'href'=>mysiteurl(),
				),

				array(
						'title'=>lang('text_support'),
						'href'=>mysiteurl('support'),
				),
				
				array(
						'title'=>$this->ROW['title'],
				),
		);
		myview('index',array('CONTENT'=>array('support/detail',$this),
		'METHOD'=>'support_home::index',
		'TITLE'=>$this->ROW['options']['title'],
		'DESCRIPTION'=>$this->ROW['options']['description'],
		'KEYWORDS'=>$this->ROW['options']['keywords'],
		'FACEBOOK'=>array(
		'IMAGES'=>base_url($this->ROW['options']['images']),
		)
		));
	}
	function services($id=0)
	{	
		$this->ROW = array_shift($this->model->select(array('id'=>intval($id))));
		$this->ROW or show_404();
		$this->ROWS = $this->model->select(array('id <>'=>intval($id)),array('sc_order'=>'asc'),0,10);
		$this->BREADCRUMBS = array(
				array(
						'title'=>lang('text_home'),
						'href'=>mysiteurl(),
				),
				array(
						'title'=>$this->ROW['title'],
				),
		);
		myview('index',array('CONTENT'=>array('support/services',$this),
		'METHOD'=>__METHOD__,
		'TITLE'=>$this->ROW['options']['title'],
		'DESCRIPTION'=>$this->ROW['options']['description'],
		'KEYWORDS'=>$this->ROW['options']['keywords'],
		'FACEBOOK'=>array(
		'IMAGES'=>base_url($this->ROW['options']['images']),
		)
		));
	}
}
?>
