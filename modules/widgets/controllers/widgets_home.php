<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Widgets_home extends Home_controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->languages(array('core/home'),$this->appmanager->LANGUAGE);
		$this->load->models(array('widgets/mwidgets_home'));
		$this->model = $this->mwidgets_home;
	}
	
	function index($page=1){
		$pp = 25;
		$tr = $this->model->countrows ();
		$tp = ceil ( $tr / $pp );
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->PAGGING 	= temp_paging($page,$tp,mysiteurl('widgets/p[x]'));
		$this->ROWS = $this->model->select ( array('sc_status'=>1), array (
								'sc_order' => 'desc'
							), $pp * ($page - 1), $pp );
		$this->BREADCRUMBS = array(
			array(
				'title'=>lang('text_home'),
				'href'=>mysiteurl(),
			),
			array(
				'title'=>lang('text_module_widgets'),	
			),		
		);
		myview('index',array('CONTENT'=>array('widgets/index',$this),
		'TITLE'=>lang('text_module_widgets'),
		'KEYWORDS'=>$this->appmanager->USER_OPTIONS['keywords'],
		'DESCRIPTION'=>$this->appmanager->USER_OPTIONS['description'],
		'METHOD'=>__METHOD__
		));
	}
	
	function detail($id = 0)
	{
		$this->ROW = array_shift($this->model->select(array('sc_status'=>1,'id'=>$id)));
		$this->ROW or show_404();
		$this->BREADCRUMBS = array(
			array(
				'title'=>lang('text_home'),
				'href'=>mysiteurl(),
			),
			array(
				'title'=>lang('text_module_widgets'),
				'href'=>mysiteurl('widgets'),
			),
			array(
				'title'=>$this->ROW['title'],		
			)
		);
		myview('index',array('CONTENT'=>array('widgets/detail',$this),
		'TITLE'=>$this->ROW['title'],
		'KEYWORDS'=>@$this->ROW['options']['keywords'],
		'DESCRIPTION'=>@$this->ROW['options']['description'],
		'METHOD'=>__METHOD__
		));
		
	}
}
?>