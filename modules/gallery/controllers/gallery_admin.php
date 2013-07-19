<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Gallery_admin extends Admin_Controller {
	function __construct() {
		parent::__construct ();
		in_array('gallery',$this->appmanager->MODULES) or show_404();
		$this->load->models(array(
				'gallery/mgallery_admin',
				'gallery/mgallery_category_admin' 
		));
		$this->load->languages(array(
				"core/common",
				"gallery/gallery_admin" 
		),$this->appmanager->LANGUAGE);
		$this->model = $this->mgallery_admin;
		$this->modelC = $this->mgallery_category_admin;
		$this->load->config ( 'form_validate' );
		$this->RU = $this->config;
	}
	function index($page = 1) {
		if (mypost ()) {
			$ids = is_array(mypost ( 'id' ))?mypost ( 'id' ):array();
			$orders = is_array(mypost('order'))?mypost('order'):array();
			switch (mypost ( 'action' )) {
				case 'doadd' :
					myredirect ( "admin/gallery/add" );
					break;
				case 'doedit' :
					myredirect ( "admin/gallery/edit/" . intval ( array_shift ( @$ids ) ) );
					break;
				case 'delete' :
					$this->model->delete ( $ids );
					//clear cache
					$this->clearCacheModun($ids);
					$this->clearCacheModun('',array("gallery/widgets_show_new"),true);						
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
					foreach($orders as $key=>$val)
						$this->model->update(array('sc_order'=>$val),array('id'=>$key));
					//clear cache
					$this->clearCacheModun(array_keys($orders));
					break;
			}
		}
		$this->filter();
		$this->ROWS = $this->model->select ( '', array (
				'sc_order' => 'asc','id'=>'desc'
		) );
		$pp = 25;
		$this->filter();
		$tr = $this->model->countrows();
		$tp = ceil ( $tr / $pp );
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->ROWS = arraysub($this->ROWS,$pp*($page-1), $pp);
		$this->PAGGING = htmlpagging ( $page, $tp, mysiteurl ( "admin/gallery/index/[x]" ) );
		$this->FILTER = $this->startFilter();
		myview ( "index", array (
				"CONTENT" => array (
						"gallery/index",
						$this 
				) 
		) );
	}
	function add() {
		if($this->model->countrows()>=$this->appmanager->CONFIG['numbers_products']){
			$this->ER = array(lang('admin_message_error_numbers'));
			myview ( "index", array (
					'CONTENT' => array (
							'error',
							$this 
					) 
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
							$this->clearCacheModun($id_cate,array("gallery/widgets_category"));
						}
						$this->clearCacheModun('',array("gallery/widgets_show_new"),true);
						$this->SU = lang ( '_dbsu' );
					} else
						$this->ER = lang ( '_dber' );
					(mypost ( 'action' ) == 'save_back' && ! $this->ER) && myredirect ( 'admin/gallery' );
					(mypost ( 'action' ) == 'save' && ! $this->ER) && myredirect ( 'admin/gallery/edit/' . $id );
					break;
			}
		}
		$this->CATEGORY = arraymake ( $this->modelC->listchild (), 'id', 'parent' );
		myview ( "index", array (
				'CONTENT' => array (
						'gallery/add',
						$this 
				) 
		) );
	}
	function edit($id = 0) {
		$id = intval($id);
		$this->ROW = array_shift ( $this->model->select ( array (
				"id" => intval ( $id ) 
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
					foreach ( mypost ( 'images' ) as $val )
						if (isUrlImg ( $val ))
							$data ['images'] [] = getPath($val);
					$data = $this->model->makeData ( $data );
					if ($this->model->update ( $data, array (
							"id" => $id 
					) )) {
						$id_cate = @is_array(mypost('id_cate'))?mypost('id_cate'):array();
						if(count($this->ROW['relation']) > count($id_cate))
							$check = array_diff($this->ROW['relation'],$id_cate);
						else
							$check = array_diff($id_cate,$this->ROW['relation']);
						if(!empty($check)){
							$this->model->deleteRelation ( $id );
							$this->model->insertRelation ( $id_cate, $id );
							//clear cache
							$this->clearCacheModun($check,array("gallery/widgets_category"));
						}
						$this->clearCacheModun('',array("gallery/widgets_show_new"),true);
						$this->SU = lang ( '_dbsu' );
					} else
						$this->ER = lang ( '_dber' );
					(mypost ( 'action' ) == 'save_back' && ! $this->ER) && myredirect ( 'admin/gallery' );
					(mypost ( 'action' ) == 'save_new' && ! $this->ER) && myredirect ( 'admin/gallery/add');
					/* Reload data */
					$this->ROW = array_shift ( $this->model->select ( array (
							"id" => intval ( $id ) 
					) ) );
					break;
			}
		}
		$this->CATEGORY = arraymake ( $this->modelC->listchild (), 'id', 'parent' );
		myview ( "index", array (
				"CONTENT" => array (
						"gallery/edit",
						$this 
				) 
		) );
	}
	function category($page = 1) {
		if (mypost ()) {
			$ids = mypost ( "id" );
			switch (mypost ( "action" )) {
				case "doedit" :
					myredirect ( "admin/gallery/categoryEdit/" . array_shift ( $ids ) );
					break;
				case "doadd" :
					myredirect ( "admin/gallery/categoryAdd" );
					break;
				case "delete" :
					$this->modelC->delete ( $ids );
					$this->clearCacheModun(array(),array('gallery/widgets_show_category'),true);
					break;
				case "active" :
					$this->db->where_in ( "id", $ids );
					$this->modelC->update ( array (
							"sc_status" => 1 
					) );
					$this->clearCacheModun(array(),array('gallery/widgets_show_category'),true);
					break;
				case "inactive" :
					$this->db->where_in ( "id", $ids );
					$this->modelC->update ( array (
							"sc_status" => 0 
					) );
					$this->clearCacheModun(array(),array('gallery/widgets_show_category'),true);
					break;
				case "order" :
					$orders = mypost('order');
					foreach($orders as $key=>$val)
					$this->modelC->update(array('sc_order'=>$val),array('id'=>$key));
					$this->clearCacheModun(array(),array('gallery/widgets_show_category'),true);
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
		$this->LISTCATE = arraysub ( $this->LISTCATE, $pp * ($page - 1), $pp );
		$this->PAGGING = htmlpagging ( $page, $tp, mysiteurl ( "admin/gallery/category/[x]" ) );
		myview ( "index", array (
				"CONTENT" => array (
						"gallery/category",
						$this 
				) 
		) );
	}
	function categoryAdd() {
		if (mypost ()) {
			switch (mypost ( 'action' )) {
				case 'save' :
				case 'save_back' :
				case 'save_new' :
					$this->ER = myvalid ( mypost (), $this->RU->item ( 'ad_gallery_cate' ) );
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
						$this->clearCacheModun(array(),array('gallery/widgets_show_category'),true);
					} else
						$this->ER = lang ( '_dber' );
					(mypost ( 'action' ) == 'save_back' && ! $this->ER) && myredirect ( "admin/gallery/category" );
					(mypost ( 'action' ) == 'save' && ! $this->ER) && myredirect ( "admin/gallery/categoryEdit/" . $id );
					break;
			}
		}
		$this->CATEGORY = arraymerkey ( array (
				0 => lang ( '_pid_root' ) 
		), arraymake ( $this->modelC->listchild ( 0 ), "id", "_txttitle" ) );
		myview ( "index", array (
				"CONTENT" => array (
						"gallery/categoryAdd",
						$this 
				) 
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
					$this->ER = myvalid ( mypost (), $this->RU->item ( 'ad_gallery_cate' ) );
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
						$this->clearCacheModun(array(),array('gallery/widgets_show_category'),true);
					} else
						$this->ER = lang ( '_dber' );
					(mypost ( 'action' ) == 'save_back' && ! $this->ER) && myredirect ( "admin/gallery/category" );
					(mypost ( 'action' ) == 'save_new' && ! $this->ER) && myredirect ( "admin/gallery/categoryAdd" );
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
						"gallery/categoryEdit",
						$this 
				) 
		) );
	}
	protected function filter()
	{
	
		if(isset($_SESSION['gallery_filter_time']) && $_SESSION['gallery_filter_time'] < time())
			$this->clearFilter();
		$flag = false;
		if(mypost('filter_submit')){
			$filter_form = mypost('filter');
			$_SESSION['gallery_filter'] = mypost('filter');
			$_SESSION['gallery_filter_time'] = time()+(2*60);
			$flag = true;
		}
		else if(!@empty($_SESSION['gallery_filter'])){
			$filter_form = $_SESSION['gallery_filter'];
			$flag = true;
		}
		if($flag)
			$filter_form['cate'] != 'all' && $this->db->join('gallery_relate','id=id_item') && $this->db->where('id_cate',intval($filter_form['cate']));
	}
	protected function clearFilter()
	{
		unset($_SESSION['gallery_filter_time']);
		unset($_SESSION['gallery_filter']);
	}
	protected function startFilter()
	{
		$this->CATEGORY = arraymake($this->modelC->listchild(),'id','_txttitle');
		return myview('gallery/other/filter',$this,true);
	}
	protected function clearCacheModun($ids=array(),$path=array("gallery/widgets_show"),$removePath = false)
	{
		$this->load->models(array('modun/mmodun_admin'));
		$this->cachefile->setDir($this->cachefile->getRoot().'/widgets');
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
			}else if(in_array($tmp['path'], $path) && $removePath){
				$this->cachefile->setFilename($tmp['options']['primary_key']);
				$this->cachefile->delete();
			}
			
		}
	}
	
	function widgets_show($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
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
		$this->ROWS = $this->model->select ( '', array (
				'sc_order'=>'asc','id'=>'desc'
		),$pp * ($page - 1), $pp );
		$this->PAGGING = htmlpagging ( $page, $tp, mysiteurl ( "admin/gallery/widgets_show/[x]/".$primary_key."/".$id_page."/".$id_widgets."/".$layout ) );
		$this->SORT = array(
				'default'=>lang('sortNone_widgets'),
				'id'=>lang('sortDesc_widgets'),
				'sc_utime'=>lang('sortUctime_widgets'),
		);
		$this->appmanager->THEME = $themes;
		myview('gallery/widgets/widgets_show',$this);
	}
	function widgets_show_category($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
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
		myview('gallery/widgets/widgets_show_category',$this);
	}
	function widgets_category($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes = 'site/common')
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
		$this->CATEGORY = $this->modelC->listChild (0,'', array (
				'sc_order'=>'asc','id'=>'desc'
		),$pp * ($page - 1), $pp );
		$this->PAGGING = htmlpagging ( $page, $tp, mysiteurl ( "admin/gallery/widgets_category/[x]/".$primary_key."/".$id_page."/".$id_widgets."/".$layout ) );
		$this->SORT = array(
				'default'=>lang('sortNone_widgets'),
				'id'=>lang('sortDesc_widgets'),
				'sc_utime'=>lang('sortUctime_widgets'),
				'title'=>lang('titleDesc_widgets'),
				'asc_title'=>lang('titleAsc_widgets')
		);
		$this->appmanager->THEME = $themes;
		myview('gallery/widgets/widgets_category',$this);
	}
	function widgets_show_new($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
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
		myview('gallery/widgets/widgets_show_new',$this);
	}
	
}