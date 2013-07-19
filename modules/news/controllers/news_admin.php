<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class News_admin extends Admin_Controller {
	function __construct() {
		parent::__construct ();
		in_array('news',$this->appmanager->MODULES) or show_404();
		$this->load->languages ( array (
				'core/common',
				'news/news_admin' 
		), $this->appmanager->LANGUAGE );
		$this->load->helpers ( array (
				'core/ckeditor',
				'form' 
		) );
		$this->load->models ( array (
				'news/mnews_admin',
				'news/mnews_category_admin' 
		) );
		$this->model = $this->mnews_admin;
		$this->modelC = $this->mnews_category_admin;
		$this->load->config ( 'form_validate' );
		$this->RU = $this->config;
	}
	function index($page = 1) {
		if (mypost ()) {
			$ids = is_array(mypost ( 'id' ))?mypost ( 'id' ):array();
			$orders = is_array(mypost('order'))?mypost('order'):array();
			switch (mypost ( 'action' )) {
				case 'doadd' :
					myredirect ( "admin/news/add" );
					break;
				case 'doedit' :
					myredirect ( "admin/news/edit/" . intval ( array_shift ( @$ids ) ) );
					break;
				case 'delete' :
					$this->model->delete ( $ids );
					//clear cache
					$this->clearCacheModun($ids);
					$this->clearCacheModun('',array("news/widgets_show_new"),true);
					break;
				case 'active' :
					$this->db->where_in ( "id", $ids );
					$this->model->update ( array (
							"sc_status" => 1 
					) );
					break;
				case 'inactive' :
					$this->db->where_in ( "id", $ids );
					$this->model->update ( array (
							"sc_status" => 0 
					) );
					break;
				case 'order' :
					$this->model->updateOrder ( $orders );
					//clear cache
					$this->clearCacheModun(array_keys($orders));
					break;
			}
		}
		$pp = 25;
		$this->filter();
		$tr = $this->model->countrows();
		$tp = ceil ( $tr / $pp );
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->filter();
		$this->ROWS = $this->model->select ( '', array (
				'sc_order' => 'asc','id'=>'desc' 
		),$pp*($page-1),$pp );
		$this->PAGGING = htmlpagging($page,$tp,mysiteurl('admin/news/index/[x]'));
		$this->FILTER = $this->startFilter();
		myview ( "index", array (
				"CONTENT" => array (
						"news/index",
						$this 
				) ,
				'TITLE'=>lang('news')
		) );
	}
	function add() {
	if($this->model->countrows()>=$this->appmanager->CONFIG['numbers_news']){
		$this->ER = array(lang('admin_message_error_numbers'));
		myview ( "index", array (
		"CONTENT" => array (
		"error",
		$this
		) ,
		'TITLE'=>lang('news_add')
		) );
		exit();
	}
		if (mypost ()) {
			switch (mypost ( 'action' )) {
				case 'save_back' :
				case 'save_new' :
				case 'save' :
					$this->ER = myvalid ( mypost (), $this->RU->item ( 'ad_pro_cate' ) );
					if ($this->ER)
						break;
						/* Tao du lieu */
					$data = mypost ();
					$data ['slug'] = empty ( $_POST ['slug'] ) ? stringseo ( mypost ( 'title' ) ) : stringseo ( mypost ( 'slug' ) );
					$data ['sc_ctime'] = time ();
					$data ['images'] = null;
					$data['options']['title'] = empty($data['options']['title'])?$data['title']:$data['options']['title'];
					$data['options']['description'] = empty($data['options']['description'])?stringsummary($data['content'],160):$data['options']['description'];
					$data['summary'] = empty($data['summary'])?stringsummary($data['content'],250):$data['summary'];
					foreach ( mypost ( 'images' ) as $val )
						if (isUrlImg ( $val ))
							$data ['images'] [] = getPath($val);
					$data = $this->model->makeData ( $data );
					if ($this->model->insert ( $data )) {
						$id = $this->model->insert_id ();
						$id_cate = @mypost ( 'id_cate' );
						if(@is_array($id_cate) && !empty($id_cate)){
							$this->model->insertRelation ( $id_cate, $id );
							//clear cache
							$this->clearCacheModun($id_cate,array("news/widgets_category"));
						}
						$this->SU = lang ( '_dbsu' );
						$this->clearCacheModun('',array("news/widgets_show_new"),true);
					} else
						$this->ER = lang ( '_dber' );
					(mypost ( 'action' ) == 'save_back' && ! $this->ER) && myredirect ( 'admin/news' );
					(mypost ( 'action' ) == 'save' && ! $this->ER) && myredirect ( 'admin/news/edit/' . $id );
					break;
			}
		}
		$this->CATEGORY = arraymake ( $this->modelC->listchild (), 'id', 'parent' );
		myview ( "index", array (
				"CONTENT" => array (
						"news/add",
						$this 
				) ,
				'TITLE'=>lang('news_add')
		) );
	}
	function edit($id = 0) {
		$id = intval($id);
		$this->ROW = array_shift ( $this->model->selectAll ( array (
				"id" => $id
		) ) );
		if (! $this->ROW)
			show_404 ();
		if (mypost ()) {
			switch (mypost ( 'action' )) {
				case 'save' :
				case 'save_new' :
				case 'save_back' :
					$this->ER = myvalid ( mypost (), $this->RU->item ( 'ad_pro_cate' ) );
					if ($this->ER)
						break;
						/* Tao du lieu */
					$data = mypost ();
					$data ['slug'] = empty ( $_POST ['slug'] ) ? stringseo ( mypost ( 'title' ) ) : stringseo ( mypost ( 'slug' ) );
					$data ['images'] = array ();
					$data['options']['title'] = empty($data['options']['title'])?$data['title']:$data['options']['title'];
					$data['options']['description'] = empty($data['options']['description'])?stringsummary($data['content'],160):$data['options']['description'];
					$data['summary'] = empty($data['summary'])?stringsummary($data['content'],250):$data['summary'];				
					foreach ( mypost ( 'images' ) as $val )
						if (isUrlImg ( $val ))
							$data ['images'] [] = getPath($val);
					$data = $this->model->makeData ( $data );
					if ($this->model->update ( $data, array (
							"id" => $id 
					) )) {						
						//clear cache
						$this->clearCacheModun(array($id));
						
						$id_cate = @is_array(mypost('id_cate'))?mypost('id_cate'):array();
						if(count($this->ROW['relation']) > count($id_cate))
							$check = array_diff($this->ROW['relation'],$id_cate);
						else
							$check = array_diff($id_cate,$this->ROW['relation']);
						if(!empty($check)){
							$this->model->deleteRelation ( $id );
							$this->model->insertRelation ( $id_cate, $id );
							//clear cache
							$this->clearCacheModun($check,array("news/widgets_category"));
						}		
						$this->SU = lang ( '_dbsu' );
						$this->clearCacheModun('',array("news/widgets_show_new"),true);
					} else
						$this->ER = lang ( '_dber' );
					(mypost ( 'action' ) == 'save_back' && ! $this->ER) && myredirect ( 'admin/news' );
					(mypost ( 'action' ) == 'save_new' && ! $this->ER) && myredirect ( 'admin/news/add');
					/* Reload data */
					$this->ROW = array_shift ( $this->model->select ( array (
							"id" => $id
					) ) );
					break;
			}
		}
		$this->CATEGORY = arraymake ( $this->modelC->listchild (), 'id', 'parent' );
		
		myview ( "index", array (
				"CONTENT" => array (
						"news/edit",
						$this 
				) ,
				'TITLE'=>lang('news_module')
		) );
	}
	function category($page = 1) {
		if (mypost ()) {
			$ids = is_array(mypost ( "id" ))?mypost('id'):array();
			switch (mypost ( "action" )) {
				case "doedit" :
					myredirect ( "admin/news/categoryEdit/" . array_shift ( $ids ) );
					break;
				case "doadd" :
					myredirect ( "admin/news/categoryAdd" );
					break;
				case "delete" :
					$this->modelC->delete ( $ids );
					$this->clearCacheModun(array(),array('news/widgets_show_category'),true);
					break;
				case "active" :
					$this->db->where_in ( "id", $ids );
					$this->modelC->update ( array (
							"sc_status" => 1 
					) );
					$this->clearCacheModun(array(),array('news/widgets_show_category'),true);
					break;
				case "inactive" :
					$this->db->where_in ( "id", $ids );
					$this->modelC->update ( array (
							"sc_status" => 0 
					) );
					$this->clearCacheModun(array(),array('news/widgets_show_category'),true);
					break;
				case "order" :
					$this->modelC->updateOrder ( mypost ( 'order' ) );
					$this->clearCacheModun(array(),array('news/widgets_show_category'),true);
					break;
			}
		}
		$pp = 25;
		$tr = $this->modelC->countrows ();
		$tp = ceil ( $tr / $pp );
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->LISTCATE = $this->modelC->listchild ( 0, '', array (
				"sc_order" => "asc" ,'id'=>'desc',
		) );
		$this->LISTCATE = arraysub($this->LISTCATE,$pp*($page-1),$pp);
		$this->PAGGING = htmlpagging ( $page, $tp, mysiteurl ( "admin/news/category/[x]" ) );
		myview ( "index", array (
				"CONTENT" => array (
						"news/category",
						$this 
				),
				'TITLE'=>lang('news_cate')
		) );
	}
	function categoryAdd() {
		if (mypost ()) {
			switch (mypost ( 'action' )) {
				case 'save' :
				case 'save_back' :
				case 'save_new' :
					$this->ER = myvalid ( mypost (), array (
							$this->RU->item ( 'title' ) 
					) );
					if ($this->ER)
						break;
						/* Tao data import db */
					$data = mypost ();
					$data ['sc_ctime'] = $data ['sc_utime'] = time ();
					$data ['slug'] = (mypost ( 'slug' ) == '') ? stringseo ( mypost ( 'title' ) ) : stringseo ( mypost ( 'slug' ) );
					$data = $this->modelC->makeData ( $data );
					if (($this->modelC->insert ( $data ))) {
						$id = $this->db->insert_id ();
						$this->SU = lang ( '_dbsu' );
						$this->clearCacheModun(array(),array('news/widgets_show_category'),true);
					} else
						$this->ER = lang ( '_dber' );
					(mypost ( 'action' ) == 'save_back' && ! $this->ER) && myredirect ( "admin/news/category" );
					(mypost ( 'action' ) == 'save' && ! $this->ER) && myredirect ( "admin/news/categoryEdit/" . $id );
					break;
			}
		}
		$this->CATEGORY = arraymerkey ( array (
				0 => lang ( '_pid_root' ) 
		), arraymake ( $this->modelC->listchild ( 0 ), "id", "_txttitle" ) );
		myview ( "index", array (
				"CONTENT" => array (
						"news/categoryAdd",
						$this 
				) ,
				'TITLE'=>lang('news_cate_add')
		) );
	}
	function categoryEdit($id = 0) {
		$this->ROW = array_shift ( $this->modelC->select ( array (
				"id" => $id 
		) ) );
		($this->ROW) or show_404 ();
		if (mypost ()) {
			switch (mypost ( 'action' )) {
				case 'save' :
				case 'save_back' :
				case 'save_new' :
					$this->ER = myvalid ( mypost (), array (
							$this->RU->item ( 'title' ) 
					) );
					if ($this->ER)
						break;
						/* Tao data import db */
					$data = mypost ();
					$data ['sc_utime'] = time ();
					$data ['slug'] = (mypost ( 'slug' ) == '') ? stringseo ( mypost ( 'title' ) ) : stringseo ( mypost ( 'slug' ) );
					$data = $this->modelC->makeData ( $data );
					if (($this->modelC->update ( $data, array (
							"id" => $id 
					) ))) {
						$this->SU = lang ( '_dbsu' );
						$this->ROW = $data;
						$this->clearCacheModun(array(),array('news/widgets_show_category'),true);
					} else
						$this->ER = lang ( '_dber' );
					(mypost ( 'action' ) == 'save_back' && ! $this->ER) && myredirect ( "admin/news/category" );
					(mypost ( 'action' ) == 'save_new' && ! $this->ER) && myredirect ( "admin/news/categoryAdd" );
					break;
			}
		}
		$this->CATEGORY = arraymerkey ( array (
				0 => lang ( '_pid_root' ) 
		), arraymake ( $this->modelC->listchild ( 0, array (
				"id <>" => $id 
		) ), "id", "_txttitle" ) );
		myview ( "index", array (
				"CONTENT" => array (
						"news/categoryEdit",
						$this 
				) ,
				'TITLE'=>lang('news_cate_edit')
		) );
	}
	protected function filter()
	{
	
		if(isset($_SESSION['news_filter_time']) && $_SESSION['news_filter_time'] < time())
			$this->clearFilter();
		$flag = false;
		if(mypost('filter_submit')){
			$filter_form = mypost('filter');
			$_SESSION['news_filter'] = mypost('filter');
			$_SESSION['news_filter_time'] = time()+(5*60);
			$flag = true;
		}
		else if(!@empty($_SESSION['news_filter'])){
			$filter_form = $_SESSION['news_filter'];
			$flag = true;
		}
		if($flag)
			$filter_form['cate'] != 'all' && $this->db->join('news_relate','id=id_item') && $this->db->where('id_cate',intval($filter_form['cate']));	
	}
	protected function clearFilter()
	{
		unset($_SESSION['news_filter_time']);
		unset($_SESSION['news_filter']);
	}
	protected function startFilter()
	{
		$this->CATEGORY = arraymake($this->modelC->listchild(),'id','_txttitle');
		return myview('news/other/filter',$this,true);
	}
	//Xoa cache cua modun khi san pham dx cap nhat
	protected function clearCacheModun($ids=array(),$path=array("news/widgets_show"),$removePath = false)
	{
		$this->load->models(array('modun/mmodun_admin'));
		$this->cachefile->setDir($this->cachefile->getRoot()."/widgets");
		$moduns = $this->mmodun_admin->select();
		$layout = array("header","footer","left","right","content_top","content_bottom");
		foreach($moduns as $modun)
			foreach($layout as $val){
			$tmps = arraymakes($modun[$val],"primary_key",array("path","options"));
			foreach($tmps as $tmp)
				if(isset($tmp['options']['key'])
						&& array_intersect($tmp['options']['key'],$ids)
						&& in_array($tmp['path'],$path)){
				$this->cachefile->setFilename($tmp['options']['primary_key']);
				$this->cachefile->delete();
			}else if(in_array($tmp['path'],$path) && $removePath){
				$this->cachefile->setFilename($tmp['options']['primary_key']);
				$this->cachefile->delete();
			}
		}
	}
	function widgets_show($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes = 'site/common')
	{
		$this->input->is_ajax_request() or show_404();
		$this->load->models(array('modun/mmodun_admin'));
		$ROW= array_shift($this->mmodun_admin->select(array('id_page'=>intval($id_page))));
		$ROW = $ROW[$layout];
		foreach($ROW as $key=>&$val)
			if($val['primary_key']==$primary_key && $val['id'] == $id_widgets)
			$this->OPTIONS = $val['options'];
		isset($this->OPTIONS) or show_404();
		if(mypost('action_widgets')){
			switch(mypost('action_widgets'))
			{
				case 'cpanel_config_modules' : 
					foreach($ROW as &$val)
					if($val['primary_key']==$primary_key && $val['id'] == intval($id_widgets)){
					$val['options']['title'] = mypost('title');	
					$val['options']['hidetitle'] = @intval(mypost('hidetitle'));					
					$val['options']['sort'] = mypost('sort');
					if(@is_array(mypost('id')))
						$val['options']['key'] = array_merge_recursive(@$val['options']['key']?$val['options']['key']:array(),mypost('id'));
					if(@is_array(mypost('modules'))){
						$tmp = $val['options']['key'];
						foreach($tmp as $key=>$value)
							if(in_array($value, mypost('modules'))) unset($tmp[$key]);
						$val['options']['key'] = array_values($tmp);
					}
					$options = $val['options'];
					}
					$data[$layout] = $ROW;
					if($this->mmodun_admin->update($data,array('id_page'=>intval($id_page))))
						$this->OPTIONS = $options;
				break;
			}
			$this->cachefile->setFilename('widgets/'.$this->OPTIONS['primary_key']);
			$this->cachefile->delete();
		}
		$this->MODULES = array();
		if(@is_array($this->OPTIONS['key']) && !empty($this->OPTIONS['key']))
		{
			$this->db->where_in("id",$this->OPTIONS['key']);
			$this->MODULES = $this->model->select('',array('sc_order'=>'asc','id'=>'desc'));	
		}
		$tr = $this->model->countrows ();
		$pp = 25;
		$tp = ceil ( $tr / $pp );
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->filter();
		$this->NEWS = $this->model->select ( '', array (
				'sc_order'=>'asc','sc_utime'=>'desc'
		),$pp * ($page - 1), $pp );
		$this->PAGGING = htmlpagging ( $page, $tp, mysiteurl ( "admin/news/widgets_show/[x]/".$primary_key."/".$id_page."/".$id_widgets."/".$layout ) );
		$this->SORT = array(
		'default'=>lang('sortNone_widgets'),
		'id'=>lang('sortDesc_widgets'),
		'sc_utime'=>lang('sortUctime_widgets'),			
		);
		$this->appmanager->THEME = $themes;
		myview('news/widgets/widgets_show',$this);
	}	
	function widgets_show_category($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes = 'site/common')
	{
		$this->input->is_ajax_request() or show_404();
		$this->load->models(array('modun/mmodun_admin'));
		$ROW= array_shift($this->mmodun_admin->select(array('id_page'=>intval($id_page))));
		$ROW = $ROW[$layout];
		foreach($ROW as $key=>&$val)
			if($val['primary_key']==$primary_key && $val['id'] == $id_widgets)
			$this->OPTIONS = $val['options'];
		isset($this->OPTIONS) or show_404();
		if(mypost('action_widgets')){
			switch(mypost('action_widgets'))
			{
				case 'cpanel_config_modules':
					foreach($ROW as &$val)
						if($val['primary_key']==$primary_key && $val['id'] == intval($id_widgets)){
						$val['options']['title'] = mypost('title');
						$val['options']['hidetitle'] = @intval(mypost('hidetitle'));				
						$options = $val['options'];
					}
					$data[$layout] = $ROW;
					if($this->mmodun_admin->update($data,array('id_page'=>intval($id_page))))
						$this->OPTIONS = $options;
					break;
			}
			$this->cachefile->setFilename('widgets/'.$this->OPTIONS['primary_key']);
			$this->cachefile->delete();
		}
		$this->appmanager->THEME = $themes;		
		myview('news/widgets/widgets_show_category',$this);
	}
	function widgets_category($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes = "site/common")
	{
		$this->input->is_ajax_request() or show_404();
		$this->load->models(array('modun/mmodun_admin'));
		$ROW= array_shift($this->mmodun_admin->select(array('id_page'=>intval($id_page))));
		$ROW = $ROW[$layout];
		foreach($ROW as $key=>&$val)
			if($val['primary_key']==$primary_key && $val['id'] == $id_widgets)
			$this->OPTIONS = $val['options'];
		isset($this->OPTIONS) or show_404();
		if(mypost('action_widgets')){
			switch(mypost('action_widgets'))
			{
				case 'cpanel_config_modules' :
					foreach($ROW as &$val)
						if($val['primary_key']==$primary_key && $val['id'] == intval($id_widgets)){
						$val['options']['title'] = mypost('title');
						$val['options']['hidetitle'] = @intval(mypost('hidetitle'));
						$val['options']['limit'] = mypost('limit');
						$val['options']['sort'] = mypost('sort');
						if(@is_array(mypost('id')))
							$val['options']['key'] = array_merge_recursive(@$val['options']['key']?$val['options']['key']:array(),mypost('id'));
						if(@is_array(mypost('modules'))){
							$tmp = $val['options']['key'];
							foreach($tmp as $key=>$value)
								if(in_array($value, mypost('modules'))) unset($tmp[$key]);
							$val['options']['key'] = array_values($tmp);
						}
						$options = $val['options'];
					}
					$data[$layout] = $ROW;
					if($this->mmodun_admin->update($data,array('id_page'=>intval($id_page))))
						$this->OPTIONS = $options;
					break;		
			}
			$this->cachefile->setFilename('widgets/'.$this->OPTIONS['primary_key']);
			$this->cachefile->delete();
		}
		$this->MODULES = array();
		if(@is_array($this->OPTIONS['key']) && !empty($this->OPTIONS['key']))
		{
			$where = "id IN (".implode(',', $this->OPTIONS['key']).")";
			$this->MODULES = $this->modelC->listChild(0,$where,array('sc_order'=>'asc','id'=>'desc'));
		}
		$tr = $this->modelC->countrows ();
		$pp = 25;
		$tp = ceil ( $tr / $pp );
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->CATEGORY = $this->modelC->listChild (0, '', array (
				'sc_order'=>'asc','id'=>'desc'
		),$pp * ($page - 1), $pp );
		$this->PAGGING = htmlpagging ( $page, $tp, mysiteurl ( "admin/news/widgets_category/[x]/".$primary_key."/".$id_page."/".$id_widgets."/".$layout ) );
		$this->SORT = array(
				'default'=>lang('sortNone_widgets'),
				'id'=>lang('sortDesc_widgets'),
				'sc_utime'=>lang('sortUctime_widgets'),
				'title'=>lang('titleDesc_widgets'),
				'asc_title'=>lang('titleAsc_widgets')
		);
		$this->appmanager->THEME = $themes;
		myview('news/widgets/widgets_category',$this);
	}
	function widgets_show_new($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes = 'site/common')
	{
		$this->input->is_ajax_request() or show_404();
		$this->load->models(array('modun/mmodun_admin'));
		$ROW= array_shift($this->mmodun_admin->select(array('id_page'=>intval($id_page))));
		$ROW = $ROW[$layout];
		foreach($ROW as $key=>&$val)
			if($val['primary_key']==$primary_key && $val['id'] == $id_widgets)
			$this->OPTIONS = $val['options'];
		isset($this->OPTIONS) or show_404();
		if(mypost('action_widgets')){
			switch(mypost('action_widgets'))
			{
				case 'cpanel_config_modules':
					foreach($ROW as &$val)
						if($val['primary_key']==$primary_key && $val['id'] == intval($id_widgets)){
						$val['options']['title'] = mypost('title');
						$val['options']['hidetitle'] = @intval(mypost('hidetitle'));
						$val['options']['limit'] = mypost('limit');
						$options = $val['options'];
					}
					$data[$layout] = $ROW;
					if($this->mmodun_admin->update($data,array('id_page'=>intval($id_page))))
						$this->OPTIONS = $options;
					break;
			}
			$this->cachefile->setFilename('widgets/'.$this->OPTIONS['primary_key']);
			$this->cachefile->delete();
		}
		$this->appmanager->THEME = $themes;
		myview('news/widgets/widgets_show_new',$this);
	}
	
}