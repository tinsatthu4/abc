<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class products_admin extends Admin_Controller {
	function __construct(){
		parent::__construct();
		@in_array('products',$this->appmanager->MODULES) or show_404();
		$this->load->helpers(array(
				'core/ckeditor',
				'form' 
		));
		$this->load->languages(array(
				'core/common',
				'products/products_admin' 
		), $this->appmanager->LANGUAGE );
		$this->load->models(array(
				'products/mproducts_admin',
				'products/mproducts_cate_admin',
				'products/mattr',
		));
		$this->model = $this->mproducts_admin;
		$this->modelC = $this->mproducts_cate_admin;
		$this->modelA = $this->mattr;
		$this->config->load('form_validate');
		$this->PATHCACHE = "cache/".$_SESSION['customer']['email'];
	}
	function index($page = 1) {
		if (mypost ()) {
			$ids = is_array(mypost ( 'id' ))?mypost('id'):array();
			$orders = is_array(mypost ( 'order' ))?mypost ( 'order' ):array();
			switch (mypost ( 'action' )) {
				case 'doadd' :
					myredirect ( 'admin/products/add' );
					break;
				case 'doedit' :
					myredirect ( 'admin/products/edit/' . $ids [0] );
					break;
				case 'delete' :
					$this->model->delete ($ids);
					//clear cache
					$this->clearCacheModun($ids);
					$this->clearCacheProducts($ids);
					$this->clearCacheModun('',array("products/widgets_show_new"),true);
					break;
				case 'active' :
					$this->db->where_in ( "id", $ids );
					$this->model->update ( array (
							"sc_status" => 1 
					) );
					$this->clearCacheProducts($ids);
					break;
				case 'inactive' :
					$this->db->where_in ( "id", $ids );
					$this->model->update ( array (
							"sc_status" => 0 
					) );
					$this->clearCacheProducts($ids);
					break;
				case 'activehot' :
					$this->db->where_in ( "id", $ids );
					$this->model->update ( array (
							"hot" => 1 
					) );
					break;
				case 'inactivehot' :
					$this->db->where_in ( "id", $ids );
					$this->model->update ( array (
							"hot" => 0 
					) );
					break;
				case 'order' :
					foreach ( $orders as $key => $order )
						$this->model->update ( array (
								"sc_order" => $order 
						), array (
								"id" => $key 
						) );
					//clear cache
					$this->clearCacheModun(array_keys($orders));
					break;
			}
		}
		$this->filter();
		$tr = $this->model->countrows ();
		$pp = temp_perpage();
		$tp = ceil ( $tr / $pp );
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->filter();
		$this->ROWS = $this->model->select ( '', array (
				'sc_order'=>'asc','sc_utime'=>'desc','id'=>'desc' 
		),$pp * ($page - 1), $pp );
		$this->PAGGING = htmlpagging ( $page, $tp, mysiteurl ( "admin/products/index/[x]" ) );
		$this->FILTER = $this->startFilter();
		myview ( 'index', array (
				"CONTENT" => array (
						"products/index",
						$this 
				),
				'TITLE'=>lang('pro_index') 
		) );
	}
	function add() {
	if($this->model->countrows()>=$this->appmanager->CONFIG['numbers_products']){
		$this->ER = array(lang('admin_message_error_numbers'));
		myview ( 'index', array (
		"CONTENT" => array ("error",
		$this
		) ,
		'TITLE'=> lang('pro_add')
		) );
		exit();
	}
	
	$this->ATTR = array_shift($this->modelA->select());
		if (mypost ()) {
			switch (mypost ( 'action' )) {
				case 'save_new' :
				case 'save_back' :
				case 'save' :
					$this->ER = array ();
					$this->ER = array_merge ( myvalid ( mypost (), $this->config->item ( 'ad_pro' ) ), $this->ER );
					/* Tao du lieu import vao DB */
					$data = mypost ();
					$data ['slug'] = empty($_POST['slug'])?stringseo(mypost('title')):stringseo(mypost('slug'));
					$data ['sc_ctime'] = time ();
					$data ['images'] = array ();
					$data ['options']['title'] = empty($data['options']['title'])?$data['title']:$data['options']['title'];
					$data ['options']['description'] = empty($data['options']['description'])?stringsummary($data['content'],160):$data['options']['description'];
					$data ['options']['attr'] =  mypost('attr');	
					foreach ( mypost ( 'images' ) as $val )
						if (isUrlImg ( $val ))
							$data ['images'] [] = getPath($val);
					$data = $this->model->makeData ( $data );
						/* End */
					if ($this->ER) break;
					if ($this->model->insert ( $data )) {
						$id = $this->model->insert_id ();
						$id_cate = @mypost ( 'id_cate' );
						if(@is_array($id_cate) && !empty($id_cate)){
							$this->model->insertRelation ( $id_cate, $id );
							//clear cache
							$this->clearCacheModun($id_cate,array("products/widgets_category"));							
						}
						$this->clearCacheModun('',array("products/widgets_show_new"),true);

						! (mypost ( 'action' ) == 'save') or myredirect ( 'admin/products/edit/' . $id );
						! (mypost ( 'action' ) == 'save_back') or myredirect ( 'admin/products' );
					} else
						$this->ER = lang ( 'pro_err' );
					break;
			}
		}
		$this->CATEGORY = arraymake ( $this->modelC->listchild ( 0 ), 'id', 'parent' );
		
		myview ( 'index', array (
				"CONTENT" => array (
						"products/add",
						$this 
				) ,
				'TITLE'=> lang('pro_add')
		) );
	}
	function edit($id = 0) {
		$id = intval($id);
		$this->ROW = array_shift($this->model->selectAll(array(
				"id" => $id 
		)));
		$this->ROW or show_404();
		$this->ATTR = array_shift($this->modelA->select());
		if (mypost ()) {
			switch (mypost ( 'action' )) {
				case 'save' :
				case 'save_back' :
				case 'save_new' :
					$this->ER = array();
					$this->ER = array_merge ( myvalid ( mypost (), $this->config->item ( 'ad_pro' ) ), $this->ER );
					if ($this->ER)
						break;
						/* Update du lieu vao database */
					//$data = mypost ();
					$data = mypost();
					$data ['slug'] = empty ( $_POST ['slug'] ) ? stringseo ( mypost ( 'title' ) ) : stringseo ( mypost ( 'slug' ) );
					$data ['images'] = array ();
					$data ['hot'] = @$data['hot']?$data['hot']:0;
					$data ['options']['title'] = empty($data['options']['title'])?$data['title']:$data['options']['title'];
					$data ['options']['description'] = empty($data['options']['description'])?stringsummary($data['content'],160):$data['options']['description'];
					$data ['options']['attr'] = mypost('attr');
					$data ['sc_utime'] = time();
					foreach ( mypost ( 'images' ) as $val )
						if (isUrlImg ( $val ))
							$data ['images'] [] = getPath($val);				
					$data = $this->model->makeData ( $data );
					if ($this->model->update ( $data, array (
							"id" => $id 
					) )) {
						//clear cache
						$this->clearCacheModun(array($id));
						$this->clearCacheProducts(array($id));
						//clear cache
						$id_cate = @is_array(mypost('id_cate'))?mypost('id_cate'):array();
						if(count($this->ROW['relation']) > count($id_cate))
							$check = array_diff($this->ROW['relation'],$id_cate);
						else
							$check = array_diff($id_cate,$this->ROW['relation']);
						if(!empty($check)){				
						$this->model->deleteRelation ( $id );
						$this->model->insertRelation ( $id_cate, $id );
						//clear cache
						$this->clearCacheModun($check,array("products/widgets_category"));							
						}
						$this->clearCacheModun('',array("products/widgets_show_new"),true);
					}
					! (mypost ( 'action' ) == "save_back") or myredirect ( "admin/products" );
					! (mypost ( 'action' ) == "save_new") or myredirect ( "admin/products/add" );
					break;
			}
			/* Reload data */
			$this->ROW = array_shift ( $this->model->select ( array (
					"id" => $id 
			) ) );
		}
		$this->CATEGORY = arraymake ( $this->modelC->listchild ( 0 ), 'id', 'parent' );

		myview ( 'index', array (
				'CONTENT' => array (
						'products/edit',
						$this 
				) ,
				'TITLE'=> lang('pro_edit')
		) );
	}
	function category($page = 1) {
		if (mypost ()) {
			$ids = mypost ( 'id' );
			$orders = mypost ( 'order' );
			$this->CATEGORY = arraymake ( arraymer ( array (
					array (
							'id' => 0,
							'_txttitle' => lang ( 'pro_pid' ) 
					) 
			), $this->modelC->listchild ( 0, array (
					"id <>" => intval ( @$ids [0] ) 
			) ) ), 'id', '_txttitle' );
			switch (mypost ( 'action' )) {
				case 'save_new' :
				case 'save_back' :
				case 'save' :
					$this->ER = myvalid ( mypost (), $this->config->item ( 'ad_pro_cate' ) );
					if ($this->ER) {
						if (isset ( $_POST ['id'] )) {
							$this->ACTION = 'add';
							$this->ROW = mypost ();
						} else
							$this->ACTION = 'edit';
						break;
					}
					$_POST ['slug'] = stringseo ( myvalue ( mypost ( 'slug' ), mypost ( 'title' ) ) );
					if (empty ( $_POST ['id'] )) {
						$data = array (
								"pid" => mypost ( 'pid' ),
								"title" => mypost ( 'title' ),
								"slug" => stringseo ( myvalue ( mypost ( 'slug' ), mypost ( 'title' ) ) ),
								'options' => mypost ( 'options' ),
								"sc_ctime" => time (),
								"sc_utime" => time () 
						);
						if ($this->modelC->insert ( $data )) {
							$_POST ['id'] = $this->modelC->insert_id ();
							$this->SU = lang ( 'pro_suC' );
							$this->clearCacheModun(array(),array("products/widgets_show_category"),true);
						} else
							$this->ER = lang ( 'pro_erC' );
						$this->CATEGORY = arraymake ( arraymer ( array (
								array (
										'id' => 0,
										'_txttitle' => lang ( 'pro_pid' ) 
								) 
						), $this->modelC->listchild ( 0 ) ), 'id', '_txttitle' );
					} else {
						$data = array (
								"pid" => mypost ( 'pid' ),
								"title" => mypost ( 'title' ),
								"slug" => mypost ( 'slug' ),
								'options' => mypost ( 'options' ),
								"sc_utime" => time () 
						);
						if ($this->modelC->update ( $data, array (
								"id" => intval ( mypost ( 'id' ) ) 
						) )) {
							$this->ROW = mypost ();
							$this->SU = lang ( 'pro_suC' );
							$this->clearCacheModun(array(),array("products/widgets_show_category"),true);
						} else {
							$this->ROW = array_shift ( $this->modelC->select ( array (
									"id" => mypost ( 'id' ) 
							) ) );
							$this->ER = lang ( 'pro_erC' );
						}
						$this->CATEGORY = arraymake ( arraymer ( array (
								array (
										'id' => 0,
										'_txttitle' => lang ( 'pro_pid' ) 
								) 
						), $this->modelC->listchild ( 0, array (
								"id <>" => mypost ( "id" ) 
						) ) ), 'id', '_txttitle' );
					}
					! (mypost ( 'action' ) == 'save_back') or myredirect ( 'admin/products/category' );
					if ((mypost ( 'action' ) == 'save')) {
						$this->ROW = empty ( $this->ROW ) ? mypost () : $this->ROW;
						$this->ACTION = "edit";
					} else {
						$this->ROW = null;
						$this->ACTION = "add";
					}
					break;
				case 'doadd' :
					$this->ACTION = "add";
					break;
				case 'doedit' :
					$this->db->where_in ( "id", $ids );
					$this->ROW = array_shift ( $this->modelC->select () );
					$this->ACTION = "edit";

					break;
				case 'delete' :
					$this->modelC->delete ( $ids );
					$this->clearCacheModun($ids,array("products/widgets_category"));
					$this->clearCacheModun(array(),array("products/widgets_show_category"),true);
					break;
				case 'active' :
					$this->db->where_in ( "id", $ids );
					$this->modelC->update ( array (
							"sc_status" => 1 
					) );
					$this->clearCacheModun(array(),array("products/widgets_show_category"),true);
					break;
				case 'inactive' :
					$this->db->where_in ( "id", $ids );
					$this->modelC->update ( array (
							"sc_status" => 0 
					) );
					$this->clearCacheModun(array(),array("products/widgets_show_category"),true);
					break;
				case 'order' :
					foreach ( $orders as $key => $order )
						$this->modelC->update ( array (
								"sc_order" => $order 
						), array (
								"id" => $key 
						) );
					$this->clearCacheModun(array(),array("products/widgets_show_category"),true);
					break;
			}
		}
		$pp = temp_perpage();
		$tr = $this->modelC->countrows ();
		$tp = ceil ( $tr / $pp );
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->LISTCATE = $this->modelC->listchild ( 0, '', array (
				"sc_order" => "asc" ,'id'=>'desc'
		));
		$this->LISTCATE = arraysub($this->LISTCATE,$pp*($page-1),$pp);
		$this->PAGGING = htmlpagging ( $page, $tp, mysiteurl ( "admin/products/category/[x]" ) );
		
		myview ( 'index', array (
				"CONTENT" => array (
						"products/category",
						$this 
				) ,
				'TITLE'=> lang('pro_cate')
		) );
	}
	function attr()
	{
		$this->ROW = array_shift($this->modelA->select());
		if(mypost())
		{
			$data['options'] = mypost('options');
			foreach($data['options'] as $i=>&$val)
			if(empty($val)) unset($data['options'][$i]);
			else $val = strip_tags($val);
			if(!empty($data['options'])){
				($this->ROW && $this->modelA->update($data)) or $this->modelA->insert($data);
			$this->ROW['options'] = $data['options'];
			}
		}
		myview ( 'index', array (
		"CONTENT" => array (
		"products/attr",
		$this
		),
		'TITLE'=>lang('pro_attr')
		) );
	}
	function readcomment($id=0)
	{
		$this->load->models(array('products/mproducts_site'));
		$ROW = array_shift($this->mproducts_site->select(array('id'=>intval($id))));
		$ROW or show_404();
		if($this->model->update(array('comment'=>0),array('id'=>$id)))
		redirect($ROW['link']);
		exit();
	}
	protected function filter()
	{
		
		if(isset($_SESSION['products_filter_time']) && $_SESSION['products_filter_time'] < time())
			$this->clearFilter();
		$flag = false;
		if(mypost('filter_submit')){
			$filter_form = mypost('filter');
			$_SESSION['products_filter'] = mypost('filter');
			$_SESSION['products_filter_time'] = time()+(5*60);
			$flag = true;
		}
		else if(!@empty($_SESSION['products_filter'])){
			$filter_form = $_SESSION['products_filter'];
			$flag = true;
		}
		if($flag)
		{
			intval($filter_form['pricefrom'])!=0 && $this->db->where('price >=',intval($filter_form['pricefrom']));
			intval($filter_form['priceto'])!=0 && $this->db->where('price <=',intval($filter_form['priceto']));
			intval($filter_form['pricefrom'])!=0 && $this->db->where('price >=',intval($filter_form['pricefrom']));
			intval($filter_form['priceto'])!=0 && $this->db->where('price <=',intval($filter_form['priceto']));
			intval($filter_form['price_sale_from'])!=0 && $this->db->where('price_sale >=',intval($filter_form['price_sale_from']));
			intval($filter_form['price_sale_to'])!=0 && $this->db->where('price_sale <=',intval($filter_form['price_sale_to']));
			intval($filter_form['price_sale_from'])!=0 && $this->db->where('price_sale >=',intval($filter_form['price_sale_from']));
			intval($filter_form['price_sale_to'])!=0 && $this->db->where('price_sale <=',intval($filter_form['price_sale_to']));
			$filter_form['cate'] != 'all' && $this->db->where('id_cate',intval($filter_form['cate'])) && $this->db->join('products_relate','id=id_item');
		}
	}
	protected function clearFilter()
	{
		unset($_SESSION['products_filter_time']);
		unset($_SESSION['products_filter']);
	}
	protected function startFilter()
	{
		$this->CATEGORY = arraymake($this->modelC->listchild(),'id','_txttitle');
		return myview('products/other/filter',$this,true);		
	}
	//Xoa cache cua modun khi san pham dx cap nhat
	protected function clearCacheModun($ids=array(),$path=array("products/widgets_show"),$removePath = false)
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
				} else if(in_array($tmp['path'],$path) && $removePath){
					$this->cachefile->setFilename($tmp['options']['primary_key']);
					$this->cachefile->delete();
				}	
		}	
	}
	//Xoa cache cua san pham khi sp dx cap nhat
	protected function clearCacheProducts($ids=array())
	{
		$this->cachefile->setDir($this->cachefile->getRoot()."/products");
		foreach($ids as $id)
		{
			$this->cachefile->setFile('detail_'.$id);
			$this->cachefile->delete();
		}
	}
	/*AJAX FOR CUSTOMER*/
	function ajaxDelete()
	{
		$this->input->is_ajax_request() or show_404();
		$ids = mypost('id');
		$this->model->delete($ids);
		echo json_encode(array('status'=>'success'));
	}
	/*WIDGETS - Them Sua Xoa Cap Nhat Lai function clearCacheModun*/ 
	function widgets_show($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
	{
		$this->input->is_ajax_request() or show_404();
		$this->load->languages(array('core/common_widgets'),$this->appmanager->LANGUAGE);
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
					if($val['primary_key']==$primary_key && $val['id'] == intval($id_widgets))
					{
						$val['options']['title'] = mypost('title');
						$val['options']['hidetitle'] = @intval(mypost('hidetitle'));
						$val['options']['carousel'] = @intval(mypost('carousel'));
						$val['options']['interval'] = @intval(mypost('interval'));
						$val['options']['number_slide'] = @intval(mypost('number_slide'));						
						$val['options']['sort'] = mypost('sort');
						$val['options']['show_price'] = mypost('show_price')?mypost('show_price'):0;
						$val['options']['show_price_sale'] = mypost('show_price_sale')?mypost('show_price_sale'):0;
						$val['options']['icon'] = isUrlImg(mypost('icon'))?getPath(mypost('icon')):'';
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
		$this->PRODUCTS = $this->model->select ( '', array (
				'sc_order'=>'asc','sc_utime'=>'desc','id'=>'desc'
		),$pp * ($page - 1), $pp );
		$this->PAGGING = htmlpagging ( $page, $tp, mysiteurl ( "admin/products/widgets_show/[x]/".$primary_key."/".$id_page."/".$id_widgets."/".$layout ) );
		$this->SORT = array(
		'default'=>lang('sortNone_widgets'),
		'id'=>lang('sortDesc_widgets'),
		'sc_utime'=>lang('sortUctime_widgets'),
		'price'=>lang('sortPirceDesc_widgets'),
		'asc_price'=>lang('sortPirceAsc_widgets'),
		'price_sale'=>lang('sortPirceSaleDesc_widgets'),
		'asc_price_sale'=>lang('sortPirceSaleAsc_widgets'),
		'title'=>lang('titleDesc_widgets'),
		'asc_title'=>lang('titleAsc_widgets')				
		);
		$this->appmanager->THEME = $themes;
		myview('products/widgets/widgets_show',$this);
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
		myview('products/widgets/widgets_show_category',$this);
	}
	function widgets_category($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
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
						$val['options']['carousel'] = @intval(mypost('carousel'));
						$val['options']['interval'] = @intval(mypost('interval'));
						$val['options']['number_slide'] = @intval(mypost('number_slide'));
						$val['options']['limit'] = mypost('limit');
						$val['options']['sort'] = mypost('sort');
						$val['options']['show_price'] = mypost('show_price')?mypost('show_price'):0;
						$val['options']['show_price_sale'] = mypost('show_price_sale')?mypost('show_price_sale'):0;
						$val['options']['icon'] = isUrlImg(mypost('icon'))?getPath(mypost('icon')):'';
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
			$this->MODULES = $this->modelC->listchild(0,$where,array('sc_order'=>'asc','id'=>'desc'));
		}
		$tr = $this->modelC->countrows ();
		$pp = 25;
		$tp = ceil ( $tr / $pp );
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		$this->CATEGORY = $this->modelC->listChild ( 0,'', array (
				'sc_order'=>'asc','sc_utime'=>'desc','id'=>'desc'
		),$pp * ($page - 1), $pp );
		
		$this->PAGGING = htmlpagging ( $page, $tp, mysiteurl ( "admin/products/widgets_category/[x]/".$primary_key."/".$id_page."/".$id_widgets."/".$layout ) );
		$this->SORT = array(
				'default'=>lang('sortNone_widgets'),
				'id'=>lang('sortDesc_widgets'),
				'sc_utime'=>lang('sortUctime_widgets'),
				'price'=>lang('sortPirceDesc_widgets'),
				'asc_price'=>lang('sortPirceAsc_widgets'),
				'price_sale'=>lang('sortPirceSaleDesc_widgets'),
				'asc_price_sale'=>lang('sortPirceSaleAsc_widgets'),
				'title'=>lang('titleDesc_widgets'),
				'asc_title'=>lang('titleAsc_widgets')
		);
		$this->appmanager->THEME = $themes;
		myview('products/widgets/widgets_category',$this);
	}
	function widgets_show_new($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
	{
		$this->input->is_ajax_request() or show_404();
		$this->load->languages(array('core/common_widgets'),$this->appmanager->LANGUAGE);
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
						if($val['primary_key']==$primary_key && $val['id'] == intval($id_widgets))
						{
							$val['options']['title'] = mypost('title');
							$val['options']['hidetitle'] = @intval(mypost('hidetitle'));
							$val['options']['carousel'] = @intval(mypost('carousel'));
							$val['options']['interval'] = @intval(mypost('interval'));
							$val['options']['number_slide'] = @intval(mypost('number_slide'));
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
		myview('products/widgets/widgets_show_new',$this);
	}	
}
?>