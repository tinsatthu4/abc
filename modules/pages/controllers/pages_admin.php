<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class pages_admin extends Admin_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->models (array(
				'modun/mmodun_admin',
				'pages/mpage_admin',
				'widgets/mwidgets_admin',
				'layout/mlayout_home',
		));
		$this->load->languages(array('core/common','pages/pages_admin'),$this->appmanager->LANGUAGE);
		$this->model = $this->mmodun_admin;
		$this->modelP = $this->mpage_admin;
		$this->modelW = $this->mwidgets_admin;
		$this->IDS = $this->modelW->selectWidget(@$_SESSION['customer']['id_layout']);	
	}
	function index() {
		$this->load->model('modun/mmodun_default');
		$this->default = $this->mmodun_default;
		$ids = $this->modelP->selectPageOfLayout($_SESSION['customer']['id_layout']);
		$this->PAGES = $this->modelP->selectPage($ids,0,'',array('sc_order'=>'asc'));
		$this->LAYOUT = array_shift($this->mlayout_home->select(array('id'=>$_SESSION['customer']['id_layout'])));
		$ids = mypost('id');
		switch(mypost('action'))
		{
			case 'edit':
			myredirect('admin/pages/edit/'.intval(array_shift(mypost('id'))));
			exit();
			break;
			case 'refesh':
			$id_page = array_shift($ids);
			$default = array_shift($this->default->select(array('id_layout'=>$_SESSION['customer']['id_layout'])));
			$arr = array('header','footer','left','right','content_top','content_bottom');
			$data = array();
			foreach($arr as $val)
			$data[$val] = $default[$val];
			if($this->model->countrows(array('id_page'=>$id_page)))
			$this->model->updateDefault($data,array("id_page"=>$id_page));
			else 
			{
				$data['id_page'] = $id_page;
				$this->model->insert($data);				
			}
			break;
			case 'refeshall':
			$default = array_shift($this->default->select(array('id_layout'=>$_SESSION['customer']['id_layout'])));
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
			break;
			case 'view':break;
		}
		myview ("index",array(
				"CONTENT" => array (
						"pages/index",
						$this 
				) ,
				'TITLE'=>lang('pages')
		) );
	}
	function updatePage($module='admin/pages')
	{
		$this->load->model('modun/mmodun_default');
		$this->default = $this->mmodun_default;
		$ids = $this->modelP->selectPageOfLayout($_SESSION['customer']['id_layout']);
		$this->PAGES = $this->modelP->selectPage($ids,0,'',array('sc_order'=>'asc'));
		$default = array_shift($this->default->select(array('id_layout'=>$_SESSION['customer']['id_layout'])));
		$arr = array('header','footer','left','right','content_top','content_bottom');
		foreach($arr as $val)
			if(!empty($default[$val])) $data[$val] = $default[$val];
		else $data[$val] = array();
		$data['customer_id'] = $_SESSION['customer']['id'];
		$this->model->delete();
		foreach($this->PAGES as $value)
		{
			$data['id_page'] = $value['id'];
			$this->model->insertDefault($data);
		}
		if($module != '')
		myredirect($module);
	}
	function edit($id = 0) {
		$this->ROW = array_shift($this->model->select(array("id_page"=>intval($id))));
		($this->ROW) or show_404();
		$this->PAGE = array_shift($this->modelP->select(array('id'=>$id)));
		$position = array(
		'header'=>$this->modelW->selectPosition('header',$_SESSION['customer']['id_layout'],array('sc_status'=>1),array('sc_order'=>'asc')),
		'footer'=>$this->modelW->selectPosition('footer',$_SESSION['customer']['id_layout'],array('sc_status'=>1),array('sc_order'=>'asc')),
		'left'=>$this->modelW->selectPosition('left',$_SESSION['customer']['id_layout'],array('sc_status'=>1),array('sc_order'=>'asc')),
		'right'=>$this->modelW->selectPosition('right',$_SESSION['customer']['id_layout'],array('sc_status'=>1),array('sc_order'=>'asc')),
		'content_top'=>$this->modelW->selectPosition('content_top',$_SESSION['customer']['id_layout'],array('sc_status'=>1),array('sc_order'=>'asc')),
		'content_bottom'=>$this->modelW->selectPosition('content_bottom',$_SESSION['customer']['id_layout'],array('sc_status'=>1),array('sc_order'=>'asc'))		
		);
		$this->ID = $id;		
		$this->HEADER = arraymerkey(array(0=>lang('no_select')),arraymake($position['header'],"id","title"));
		$this->FOOTER = arraymerkey(array(0=>lang('no_select')),arraymake($position['footer'], "id","title"));
		$this->LEFT = arraymerkey(array(0=>lang('no_select')),arraymake($position['left'], "id","title"));
		$this->RIGHT = arraymerkey(array(0=>lang('no_select')),arraymake($position['right'],"id","title"));
		$this->CONTENT_TOP = arraymerkey(array(0=>lang('no_select')),arraymake($position['content_top'], "id","title"));
		$this->CONTENT_BOTTOM = arraymerkey(array(0=>lang('no_select')),arraymake($position['content_bottom'],"id","title"));
		switch(mypost('action'))
		{
			case 'add_modules': 
				$check = $this->save($this->ROW,mypost(),$position);
				if(!$check) $this->ER = lang('er_db');
				else $this->SU = lang('su_db');
				$this->ROW = array_shift($this->model->select(array("id_page"=>intval($id))));				
			break;
			case 'updateConfig':
				$_POST['id'] = intval($id);
				$this->updateConfig();
				$this->ROW = array_shift($this->model->select(array("id_page"=>intval($id))));
			break;
		}
		myview ( "pages", array (
		"CONTENT" => array (
		"pages/edit",$this
		),
		'TITLE'=>lang('layout_modules')
		) );
	}
	function order(){
		$check = true;
		$array_check = array("header","footer","left","right","content_top","content_bottom");
		$layout = array_shift(array_keys(mypost()));
		if(mypost() && in_array($layout,$array_check))
		{
			$ROW = array_shift($this->model->select(array("id_page"=>intval(mypost('id')))));
			$ROW or show_404();
			$ROW = $ROW[$layout];
			$ROW = arraymakes($ROW, 'primary_key',array("primary_key","id","title","path","options"));
			if(mypost('delete') == 'delete') {
				$check = $this->model->clear($layout,mypost('id'));
			}
			else
			{
				$data[$layout] = array();
				foreach(mypost($layout) as $val){
					if(array_key_exists($val,$ROW))
						$data[$layout] = array_merge($data[$layout],array($ROW[$val]));
				}
				$check = $this->model->update($data,array("id_page"=>intval(mypost('id'))));
			}
			$this->cachefile->appendDir("widgets");
			$this->cachefile->setFilename(mypost('primary_key'));
			if($this->cachefile->checkCache()) $this->cachefile->delete();
			return $check;
		}
	}
	protected function save($ROW,$mypost,$position) {
		$wheader = arraymakes($position['header'],'id', array('id','title','path','options'));
		$wfooter = arraymakes($position['footer'],'id', array('id','title','path','options'));
		$wleft = arraymakes($position['left'],'id', array('id','title','path','options'));
		$wright = arraymakes($position['right'],'id', array('id','title','path','options'));
		$wcontent_top = arraymakes($position['content_top'],'id', array('id','title','path','options'));
		$wcontent_bottom = arraymakes($position['content_bottom'],'id', array('id','title','path','options'));
		$arr = array('wheader'=>'header',
					'wfooter'=>'footer',
					'wleft'=>'left',
					'wright'=>'right',
					'wcontent_top'=>'content_top',
					'wcontent_bottom'=>'content_bottom'
				);
		foreach(mypost() as $key=>$val)
		{
			if(is_array(@${$key}) && array_key_exists($val,${$key}))
			{
				$tmp = array(
						"primary_key"=>md5(microtime().rand(0,1000)),
						"id"=>@${$key}[$val]['id'],
						"path"=>@${$key}[$val]['path'],
						"title"=>@${$key}[$val]['title'],
						"options"=>@${$key}[$val]['options']
				);
				$data[$arr[$key]] = array_merge($ROW[$arr[$key]],array($tmp));
			}
		}
		if(!empty($data)) 
		return $this->model->update($data,array("id_page"=>$ROW['id_page']));
		return false;
	}
	/**
	 * Thay doi thu tu menu
	 */
	function config_nav_website()
	{
		if($this->input->is_ajax_request() && mypost('orders')){
			saveMyconfig('config_nav_top',json_encode(mypost('orders'))); 
			$this->cachefile->config('menu_top',$this->cachefile->getRoot().'/common');
			$this->cachefile->delete();
			exit();
		}
		$this->load->model(array('pages_customer/mpages_customer_admin'));
		$ids = $this->modelP->selectPageOfLayout($_SESSION['customer']['id_layout']);
		$this->PAGES_DEFAULT = arraymake($this->modelP->selectPage($ids,0,array('pid'=>0),array('sc_order'=>'asc')),'method','title');
		array_pop($this->PAGES_DEFAULT);
		$this->PAGES = arraymake($this->mpages_customer_admin->select('',array('id'=>'desc')),'id','title');
		$orders = getMyconfig('config_nav_top');
		if(!empty($orders))
		$orders = json_decode($orders['value'],true);
		$total = arraymerkey($this->PAGES_DEFAULT,$this->PAGES);
		$this->ROWS = array();
		foreach($orders as $key) if(isset($total[$key])){
		$this->ROWS[$key] = $total[$key]; unset($total['key']);
		}
		$this->ROWS = arraymerkey($this->ROWS,$total);
		
		myview ("index",array(
		"CONTENT" => array (
				"pages/config_nav_top",
				$this 
		) ,
		'TITLE'=>lang('config_nav_top')
		) );		
	}
	/*FUNCTION AJAX HO TRO TUONG TAC BEN NGOAI*/
	function ajaxAddmodules()
	{
		$this->input->is_ajax_request() or show_404();
		$positions = array("left","right","header","content_bottom","content_top","footer");
		$id_widgets = @intval(mypost('id_widgets'));
		$id_page = @intval(mypost('id_page'));
		$position = @mypost('position');
		$WIDGETS = array_shift($this->modelW->select(array("id"=>$id_widgets,"sc_status"=>1)));
		$ROW = array_shift($this->model->select(array("id_page"=>$id_page)));
		if(!@$ROW || !@$WIDGETS || !@in_array($position,$positions)) { echo json_encode(array("error"=>true)); exit(); }
		$tmp = $data = array(
				"primary_key"=>md5(microtime().rand(0,1000)),
				"id"=>$WIDGETS['id'],
				"path"=>$WIDGETS['path'],
				"title"=>$WIDGETS['title'],
				"options"=>array(
					"layout"=>$position,
					"id_page"=>$id_page,
					"path"=>$WIDGETS['path'],
					"id_widget"=>$WIDGETS['id'],
					"layout"=>$position,
					"title"=>$WIDGETS['title'],		
				),
		);
		$data = array_merge($ROW[$position],array($data));
		if($this->model->update(array($position=>$data),array("id_page"=>$id_page))){
			$this->load->models(array('layout/mlayout_site'));
			$themes = $this->mlayout_site->getLayout($_SESSION['customer']['id_layout']);
			$this->appmanager->THEME = $themes;
			$this->appmanager->CUSTOMER = $_SESSION['customer'];
			$this->appmanager->IS_ADMIN_LAYOUT = true;
			$widmanager = new WidManager();
			$html = $widmanager->render($tmp['path'],array_merge($tmp['options'],array('primary_key'=>$tmp['primary_key'])));
			echo json_encode(array("html"=>$html));
		}else 
			echo json_encode(array("error"=>true));
		
	}
	function ajaxOrders()
	{
		$this->input->is_ajax_request() or show_404();
		if(!$this->order())
		echo json_encode(array("error"=>true));
	}
	function ajaxDelete()
	{
		$this->input->is_ajax_request() or show_404();
		$id_page = @intval(mypost('id_page'));
		$primary_key = @mypost('primary_key');
		$layout = @mypost('layout');
		$ROW = array_shift($this->model->select(array("id_page"=>$id_page)));
		if(!in_array($layout,array("header","left","right","content_top","content_bottom","footer"))){
			echo json_encode(array(
					"error"=>true,
					"message"=>"Error",
			));
			exit();		
		}
		if(!@$ROW) {
			echo json_encode(array(
					"error"=>true,
					"message"=>"Error",
			)); 
			exit();
		}
		$data = $ROW[$layout];
		$tmp = arraymake($data, "primary_key","primary_key");
		unset($tmp[$primary_key]);
		$_POST = array();
		$_POST[$layout] = array_values($tmp);
		$_POST['id'] = $id_page;
		$_POST['primary_key'] = $primary_key;
		if(count($data) == 1) $_POST['delete'] = 'delete';
		
		if(!$this->order())
			echo json_encode(array("error"=>true));
		else {
			$this->cachefile->setFile($primary_key);
			$this->cachefile->appendDir("widgets");
			echo json_encode(array("success"=>true)); 
		}
		exit();	
	}
}
?>