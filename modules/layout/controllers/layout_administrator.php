<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class layout_administrator extends Administrator_Controller {

	function __construct() {
		parent::__construct ();
		$this->load->helpers (array(
				'core/ckeditor'));
		$this->load->models ( array (
				'layout/mlayout_administrator',
				'pages/mpage_administrator',
				'widgets/mwidgets_administrator',
				'widgets/mwidgets_admin',
				'modun/mmodun_default',
				'layout/mlayout_category_administrator'
		));
		$this->load->languages ( array (
				'core/common',
				'layout/layout',
				'pages/pages_admin',
				'common'
		), $this->appmanager->LANGUAGE );
		$this->modelC = $this->mlayout_category_administrator;
		$this->modelM = $this->mmodun_default;
		$this->modelW = $this->mwidgets_administrator;
		$this->model = $this->mlayout_administrator;
		$this->config->load ( 'form_validate' );
		$this->RU = $this->config;
		$this->MYRULES = array (
				'title' => array (
						'title',
						'lang:layout_layouttitle',
						'trim|required' 
				),
				'title' => array (
						'key',
						'lang:layout_key',
						'trim|required'
				),
				
				
		);	
	}
	function category($page = 1)
	{		
		if(mypost())
		{
			$ids = mypost('id');
			switch(mypost('action'))
			{
				case 'doadd':
					myredirect('administrator/layout/categoryAdd');break;
				case 'doedit':
					myredirect('administrator/layout/categoryEdit/'.@intval($ids[0]));
					break;
				case 'active':
					$this->db->where_in('id',$ids);
					$this->modelC->update(array('sc_status'=>1));
				break;
				case 'inactive':
					$this->db->where_in('id',$ids);
					$this->modelC->update(array('sc_status'=>0));
					break;
				case 'delete':
					$this->db->where_in('id',$ids);
					$this->modelC->delete();
					$this->db->where_in('pid',$ids);
					$this->model->update(array('pid'=>0));
					break;
			}
		}
		$tr = $this->modelC->countrows();
		$pp = 25;
		$tp = ceil($tr/$pp);
		$page 	= (($page<1)?1:($page>$tp?(($tp<1)?1:$tp):$page));
		$this->MYPAGGING 	= htmlpagging($page,$tp,mysiteurl('administrator/layout/category/[x]'));
		$this->MYROWS = $this->modelC->listchild(0,'',array('id'=>'desc'),$pp*($page-1),$pp);
		myview('index',array('CONTENT'=>array('layout/category',$this),
		'TITLE'=>lang('layout_category')
		));	
	}
	function categoryAdd()
	{
		$rule = array('title','lang:cate_title','trim|required|min_length[3]|max_lenght[100]');
		if(mypost())
		switch(mypost('action'))
		{
			case 'save':
			case 'save_back':
			case 'save_new':
			$this->MYERROR = myvalid(mypost(),array($rule));
			if($this->ER) break;
			$data = $this->modelC->makedata(mypost());
			$data['options']['images'] = getPath($data['options']['images']);
			$data['sc_ctime'] = $data['sc_utime'] = time();
			if(!$this->modelC->insert($data)) {$this->ER = lang('err_data'); break; }
			$id = $this->modelC->insert_id();
			(mypost('action')=='save_back') && myredirect('administrator/layout/category') && exit();
			(mypost('action')=='save') && myredirect('administrator/layout/categoryEdit/'.$id) && exit();	
			break;
		}
		$this->PARENTCATEGORY = arraymerkey(array(0=>lang('pid_0')),arraymake($this->modelC->listchild(0),'id','_txttitle'));
		$this->action = 'add';
		myview('index',array('CONTENT'=>array('layout/addcategory',$this),
		'TITLE'=>lang('layout_add_category')
		));
	}
	function categoryEdit($id=0)
	{
		$rule = array('title','lang:cate_title','trim|required|min_length[3]|max_lenght[100]');
		$this->MYROW = array_shift($this->modelC->select(array('id'=>intval($id))));
		$this->MYROW or show_404();
		switch(mypost('action'))
		{
			case 'save_back':
			case 'save_new':
			case 'save':
			$this->MYERROR = myvalid(mypost(),$rule);
			if(@$this->MYERROR) break;
			$data = $this->modelC->makedata(mypost());
			@$data['options']['images'] = getPath(@$data['options']['images']);
			if($this->modelC->update($data,array('id'=>$id)))
			{
				(mypost('action') == 'save_back') && myredirect('administrator/layout/category') && exit();
				(mypost('action') == 'save_new') && myredirect('administrator/layout/categoryAdd') && exit();
				$this->MYROW = array_replace_recursive($this->MYROW,$data);
			}
			break;
		}
		$this->PARENTCATEGORY = arraymerkey(array(0=>lang('pid_0')),arraymake($this->modelC->listchild(0),'id','_txttitle'));
		$this->action = 'edit';
		myview('index',array('CONTENT'=>array('layout/addcategory',$this),
		'TITLE'=>lang('layout_add_category')
		));
	}
	function index($page = 1) {
		/* Begin action */
		if (! empty ( $_POST )) {
			$ids = mypost ( 'id' );
			switch (mypost ( 'action' )) {
				case 'editlayout':
					myredirect('administrator/layout/editLayout/'.intval($ids[0]));
					break;
				case 'add' :
					myredirect ( 'administrator/layout/add' );
					break;
				case 'edit' :
					myredirect ( 'administrator/layout/edit/' . $ids [0] );
					break;
				case 'delete' :
					$this->db->where_in ( 'id', $ids );
					$this->model->delete ();
					break;
				case 'activehot' :
					$this->db->where_in ( 'id', $ids );
					$this->model->update ( array (
							'hot' => 1
					) );
					break;
				case 'inactivehot' :
					$this->db->where_in ( 'id', $ids );
					$this->model->update ( array (
							'hot' => 0
					) );
					break;
				case 'inactive' :
					$this->db->where_in ( 'id', $ids );
					$this->model->update ( array (
							'sc_status' => 0 
					) );
					break;
				case 'active' :
					$this->db->where_in ( 'id', $ids );
					$this->model->update ( array (
							'sc_status' => 1 
					) );
					break;
				case 'order' :
					foreach(mypost('sc_order') as $key=>$val)
					$this->model->update(array('sc_order'=>$val),array('id'=>$key));
					break;
			}
		} 
		/* End action */
		/* Begin pagging */
		$pp 		= 20;
		$tr 		= $this->model->countrows();
		$tp 		= ceil($tr/$pp);
		$page 	= (($page<1)?1:($page>$tp?(($tp<1)?1:$tp):$page));
		
		$this->MYPAGGING 	= htmlpagging($page,$tp,mysiteurl('administrator/layout/index/[x]'));
		$this->MYROWS = arraypagging ( $this->model->select( '', array (
				'sc_order' => 'asc', 'id'=>'desc'
		), $pp * ($page - 1), $pp ), $pp, $page );
		/* End pagging */
		$this->count_rows	=	$tr;
		myview ( 'index', array (
				'CONTENT' => array (
						'layout/index',
						$this 
				) 
		) );
	}
	function add() {				
		if (! empty ( $_POST )) {
			switch (mypost ( 'action' )) {
				case 'back' :
					myredirect ( 'administrator/layout' );
					break;
				case 'save_new' :
				case 'save_back' :
				case 'save' :
					// Valid input
					$this->MYERROR = myvalid ( mypost (), array (
							$this->MYRULES ['title'] 
					) );
					$options = array(
							'modules'=>@is_array(mypost('modules'))?mypost('modules'):array()
					);
					$options = array_merge($options,@is_array(mypost('options'))?mypost('options'):array());
					$images = array();
					foreach(mypost('images') as $val)
						if(isUrlImg($val)) $images[] = getPath($val);
					if (empty ( $this->MYERROR ))
						if ($this->model->insert ( array (
							'title'    => mypost ('title'),
							'sql_file'=>@mypost('sql_file')?getPath(mypost('sql_file')):'',
							'youtube'=>mypost('youtube'),
							'demo'=>mypost('demo'),
							'hot'=>@intval(mypost('hot')),
							'datatext' => mypost ('datatext'),
							'key' 	   => mypost ('key'),
							'images' => json_encode($images),
							'options' => json_encode($options),
							'sc_cuid' => @$this->appmanager->layout ['username'],
							'sc_uuid' => @$this->appmanager->layout ['username'],
							'sc_ctime' => time (),
							'sc_utime' => time (),
							'sc_status' => 1,
							'sc_order' => $this->model->ordermax () + 1 
						) )) {												

							$id = $this->model->insert_id();
							$id_cate = is_array(mypost('id_cate'))?mypost('id_cate'):array();
							foreach ($id_cate as $val)
								$this->model->_insert('layout_relate',array(
										"id_item"=>$id,
										"id_cate"=>$val,
								));
							// add page 
							$pages = @is_array(mypost('pages'))?mypost('pages'):array();
							foreach (@$pages as $page) {
								$data = array(
									'id_page'=>$page,
									'id_layout'=>$id
								);
								$this->db->insert('pages_layout',$data);
							}

							// Add widgets
							$widgets = @is_array(mypost('widgets'))?mypost('widgets'):array();
							foreach ($widgets as $widget) {
								$data = array(
									'id_widget'=>$widget,
									'id_layout'=>$id,
								);
								$this->db->insert('widgets_layout',$data);
							}

							$modun_default = array(
								'id_layout'=>$id,
								'header'=>'[]',
								'footer'=>'[]',
								'left'=>'[]',
								'right'=>'[]',
								'content_top'=>'[]',
								'content_bottom'=>'[]'
							);
							$this->db->insert('modun_default',$modun_default);
							! (mypost ( 'action' ) == 'save_new') or myredirect ( 'administrator/layout/add' );
							! (mypost ( 'action' ) == 'save_back') or myredirect ( 'administrator/layout' );
							myredirect ( 'administrator/layout/edit/'.$id);
						}
					break;
			}
		}
		$this->action = __function__;
		$this->MYPAGES 	= $this->mpage_administrator->listchild(0,'', array (
				'sc_order' => 'desc' 
		));	
		$this->MYWIDGET = $this->modelW->select('',array('sc_order'=>'asc'));
		$this->MODULES = $this->getModules();
		$this->CATEGORY = arraymakes($this->modelC->listchild(),'id',array('title','_pos'));

		myview ('index', array(
				'CONTENT' => array(
						'layout/add',
						$this 
				) 
		));
	}
	function edit($id = 0) {	
		$id = intval($id);
		$this->id= $id;
		$this->action = __function__;
		$this->MYROW = @array_shift ( $this->model->select ( array (
				'id' => intval ( $id ) 
		) ) );
		! empty ( $this->MYROW ) or show_404 ();
		$this->MYROW['options'] = json_decode($this->MYROW['options'],true);
		$this->MYROW['images'] = json_decode($this->MYROW['images'],true);
		if (! empty ( $_POST )) {
			$options = array(
					'modules'=>@is_array(mypost('modules'))?mypost('modules'):array(),
			);
			$options = array_merge($options,@is_array(mypost('options'))?mypost('options'):array());
			switch (mypost ( 'action' )) {
				case 'back' :
					myredirect ( 'administrator/layout' );
					break;
				case 'save_new' :
				case 'save_back' :
				case 'save' :
					// Valid input
					$this->MYERROR = myvalid ( mypost (), array (
							$this->MYRULES ['title'] 
					) );
					$images = array();
					foreach(mypost('images') as $val)
						if(isUrlImg($val)) $images[] = getPath($val);
					$data = array (
							'title' => mypost ( 'title' ),
							'key' => mypost('key'),
							'hot'=>@intval(mypost('hot')),
							'sql_file'=>@mypost('sql_file')?getPath(mypost('sql_file')):'',
							'youtube'=>mypost('youtube'),
							'demo'=>mypost('demo'),
							'datatext' => mypost ( 'datatext' ),
							'options'=> json_encode($options),
							'images' => json_encode($images),
							'sc_uuid' => @$this->appmanager->layout ['id'],
							'sc_utime' => time () 
					);
					if (empty($this->MYERROR)){
						if ($this->model->update($data,array(
								'id' => $this->MYROW ['id'] 
						))) {
							//update relation
							$id_cate = is_array(mypost('id_cate'))?mypost('id_cate'):array();
							$this->model->_delete('layout_relate',array('id_item'=>$this->MYROW ['id']));
							foreach ($id_cate as $val)
							$this->model->_insert('layout_relate',array(
									"id_item"=>$this->MYROW ['id'],
									"id_cate"=>$val,
							));
							//clear cache
							$this->cachefile->config('detail_'.$id,'cache_common/layout');
							$this->cachefile->delete();
							
							// Update page					
							$pages = @is_array(mypost('pages'))?mypost('pages'):array();
							$this->db->delete('pages_layout', array('id_layout' => $id));
							foreach($pages as $page)
								$this->db->insert('pages_layout',array("id_page"=>$page,"id_layout"=>$id));
							
							// update widget
							$widgets = @is_array(mypost('widgets'))?mypost('widgets'):array();
							$this->db->delete('widgets_layout', array('id_layout' => $id)); 
							foreach($widgets as $widget)
								$this->db->insert('widgets_layout',array("id_widget"=>$widget,"id_layout"=>$id));
							
							! (mypost ( 'action' ) == 'save_new') or myredirect ( 'administrator/layout/add' );
							! (mypost ( 'action' ) == 'save_back') or myredirect ( 'administrator/layout' );
							myredirect ( 'administrator/layout/edit/' . intval($id) );
						}
					}
					break;
			}
		}
		
		$this->MYPAGES 	= $this->mpage_administrator->listchild(0,'', array ('sc_order' => 'desc' ));		
		$this->MYWIDGET = $this->modelW->select('',array('sc_order'=>'asc'));
		$this->MODULES = $this->getModules();
		//  select page 
		$this->MYROW['pages'] = array_values(arraymake($this->db->get_where("pages_layout",array("id_layout"=>$id))->result_array(),"id_page", "id_page"));
       	// select widget
		$this->MYROW['widgets'] = array_values(arraymake($this->modelW->selectWidgetsOfLayout($id),"id","id"));      	

		$this->CATEGORY = arraymakes($this->modelC->listchild(),'id',array('title','_pos'));
	
		myview ( 'index', array (
				'CONTENT' => array (
						'layout/add',
						$this 
				) 
		) );
	}
	function detail($id = null) {
		$this->model->check_group ( __METHOD__ );
		$this->MYROW = @array_shift ( $this->model->select ( array (
				'id' => intval ( $id ) 
		) ) );
		myview ( 'index', array (
				'CONTENT' => array (
						'layout/detail',
						$this 
				) 
		) );
	}
	function editLayout($id = 0) {
// 		myview ("pages",array(
// 		"CONTENT" => array(
// 		"layout/editLayout",$this
// 		)
// 		));
	}

	protected function getModules()
	{
		return array("contacts","files","gallery","news","orders","pages_customer","products");
	}
}
