<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class widgets_administrator extends Administrator_Controller
{
	private $model;
	function __construct()
	{
		parent::__construct();
		
		$this->load->models(array('widgets/mwidgets_administrator'));
		$this->load->languages(array('core/common','widgets/widgets','common'),$this->appmanager->LANGUAGE);
		// Validation
		$this->config->load('form_validate');
		$this->RU = $this->config;
		$this->model = &$this->mwidgets_administrator;

		$this->MYRULES 	= 	array(
			'title'				=>	array('title','lang:title',
				'trim|required|min_length[5]|max_length[24]'),
			'key'				=>	array('key','lang:key',
				'trim|required|min_length[3]|max_length[24]'),
		);
	}
	function index($page = 1 )
	{
		/* Begin action */
		if(!empty($_POST))
		{
			$ids = mypost('id');
			switch(mypost('action'))
			{
				case 'add'		:	myredirect('administrator/widgets/add'); break;
				case 'edit'		:	myredirect('administrator/widgets/edit/'.$ids[0]); break;
				case 'delete'	:
				// kiem tra quyen module
				$this->model->check_group('widgets_administrator::delete');
				$this->db->where_in('id',$ids); $this->model->delete(); break;
				case 'inactive'	:	
				$this->model->check_group('widgets_administrator::edit');
				$this->db->where_in('id',$ids); $this->model->update(array('sc_status'=>0)); break;
				case 'active'	: 	
				$this->model->check_group('widgets_administrator::edit');
				$this->db->where_in('id',$ids); $this->model->update(array('sc_status'=>1)); break;
				case 'order'	: 	
				$this->model->check_group('widgets_administrator::edit');
				$this->model->orderlist(mypost('sc_order')); break;
				case 'reorder'	: 	
				$this->model->check_group('widgets_administrator::edit');
				$this->model->ordersort(array('sc_ctime'=>'asc')); break;
			}
		}else{
			// kiem tra quyen module
			$this->model->check_group(__METHOD__);
			//  ket thuc kiem tra quyen module
		}
		/* End action */
		/* Begin pagging */
		$pp = 20;
		$tr = $this->model->countrows ();
		$tp = ceil ( $tr / $pp );
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		
		$this->MYPAGGING 	= htmlpagging($page,$tp,mysiteurl('administrator/widgets/index/[x]'));
		$this->MYROWS = arraypagging ( $this->model->select ( '', array (
				'sc_order' => 'desc' 
		), $pp * ($page - 1), $pp ), $pp, $page );
		/* End pagging */
		$this->count_rows	=	$tr;
		
		/* End pagging */
		myview('index',array('CONTENT'=>array('widgets/index',$this)));
	}
	function add()
	{
		$this->model->check_group(__METHOD__);
		if(!empty($_POST))
		{
			switch(mypost('action'))
			{
				case 'back'				: myredirect('administrator/widgets'); break;
				case 'save_new'			:
				case 'save_back'		:
				case 'save'				:
					$path = explode('/', mypost('path'));
					
					$this->MYERROR = myvalid(mypost(),array($this->MYRULES['title']));
					

					if(empty($this->MYERROR))
					if($this->model->insert(array(
						'title'						=>	mypost('title'),
						'youtube'					=> mypost('youtube'),
						'path'						=>	mypost('path'),
						'datatext'					=>	mypost('datatext'),
						'file'						=>	isUrlImg(mypost('file'))?getPath(mypost('file')):'no-images',
						'options'					=>	@json_encode(array()),
						'sc_cuid'					=>	@$this->appmanager->USER['username'],
						'sc_uuid'					=>	@$this->appmanager->USER['username'],
						'sc_ctime'					=>	time(),
						'sc_utime'					=>	time(),
						'sc_status'					=>	1,
						'sc_order'					=>	$this->model->ordermax()+1
					)))
					{
						$ENDROW = @array_shift($this->model->select());
						$ENDROW = @array_shift($this->model->select('',array('id'=>'desc')));
						$data = array();
												
						!(mypost('action')=='save_new')  or myredirect('administrator/widgets/add');
						!(mypost('action')=='save_back') or myredirect('administrator/widgets');
						myredirect('administrator/widgets/edit/'.$this->model->insert_id());
					}
					//Delete file uploaded
					empty($file) or @unlink($file);
				break;
			}
		}
		$this->action	=	__function__;
		myview('index',array('CONTENT'=>array('widgets/add',$this)));
	}
	function edit($id=0){

		$this->model->check_group(__METHOD__);
		$this->action	=	__function__;
		$this->MYROW = @array_shift($this->model->select(array('id'=>intval($id))));

		!empty($this->MYROW) or show_404();
		if(!empty($_POST))
		{	
			// read xml 
			$path = explode('/', mypost('path'));
			$this->MYERROR = myvalid(mypost(),array($this->MYRULES['title']));
			switch(mypost('action'))
			{
				case 'back'			: myredirect('administrator/widgets'); break;
				case 'save_new'		:
				case 'save_back'	:
				case 'save'			:
					//Valid input
					$this->MYERROR = myvalid(mypost(),array($this->MYRULES['title']));

					if(empty($this->MYERROR))
					if($this->model->update(array(
						'title'						=>	mypost('title'),
						'youtube'					=> mypost('youtube'),
						'path'						=>	mypost('path'),
						'datatext'					=>	mypost('datatext'),
						'file'						=>	isUrlImg(mypost('file'))?getPath(mypost('file')):'no-images',
						'options'					=>	json_encode(array()),
						'sc_uuid'					=>	@$this->appmanager->USER['id'],
						'sc_utime'					=>	time()
					),array(
						'id'						=>	$this->MYROW['id']
					)))
					{
						!(mypost('action')=='save_new')  or myredirect('administrator/widgets/add');
						!(mypost('action')=='save_back') or myredirect('administrator/widgets');
						myredirect('administrator/widgets/edit/'.$this->MYROW['id']);
					}else
					{
						die();
					}
					//Delete uploaded file
				break;
			}
			$this->MYROW = arraymerkey($this->MYROW,mypost());
		}

		myview('index',array('CONTENT'=>array('widgets/add',$this)));
	}
	function detail( $id = null){
		$this->model->check_group(__METHOD__);
		$this->MYROW = @array_shift($this->model->select(array('id'=>intval($id)))); 
		myview('index',array('CONTENT'=>array('widgets/detail',$this)));
	}
	function cate($page = 1){
		/* Begin action */
		if(!empty($_POST))
		{
			$ids = mypost('id');
			switch(mypost('action'))
			{
				case 'add'		:	myredirect('administrator/widgets/addcate'); break;
				case 'edit'		:	myredirect('administrator/widgets/editcate/'.$ids[0]); break;
				case 'delete'	: 	
				// kiem tra quyen module
				$this->mwidgets_cate->check_group('widgets_administrator::delete');
				$this->db->where_in('id',$ids); $this->model->delete(); break;
				case 'inactive'	:	
				$this->mwidgets_cate->check_group('widgets_administrator::edit');
				$this->db->where_in('id',$ids); $this->model->update(array('sc_status'=>0)); break;
				case 'active'	: 	
				$this->mwidgets_cate->check_group('widgets_administrator::edit');
				$this->db->where_in('id',$ids); $this->model->update(array('sc_status'=>1)); break;
				case 'order'	: 	
				$this->mwidgets_cate->check_group('widgets_administrator::edit');
				$this->mwidgets_cate->orderlist(mypost('sc_order')); break;
				case 'reorder'	: 	
				$this->mwidgets_cate->check_group('widgets_administrator::edit');
				$this->mwidgets_cate->ordersort(array('sc_ctime'=>'asc')); break;
			}
		}else{
			// kiem tra quyen module
			$this->mwidgets_cate->check_group(__METHOD__);
			//  ket thuc kiem tra quyen module
		}
		/* End action */

		$pp = 20;
		$tr = $this->mwidgets_cate->countrows();
		$tp = ceil ( $tr / $pp );
		$page = (($page < 1) ? 1 : ($page > $tp ? (($tp < 1) ? 1 : $tp) : $page));
		
		$this->MYPAGGING 	= htmlpagging($page,$tp,mysiteurl('administrator/widgets/cate/[x]'));
		$this->MYROWS = arraypagging ( $this->mwidgets_cate->select ( '', array (
				'sc_order' => 'desc' 
		), $pp * ($page - 1), $pp ), $pp, $page );
		/* End pagging */

		$this->count_rows	=	$tr;
		/* End pagging */
		myview('index',array('CONTENT'=>array('widgets/cate',$this)));
	}
	function addcate()
	{
		$this->model->check_group(__METHOD__);
		if(!empty($_POST))
		{
			switch(mypost('action'))
			{
				case 'back'				: myredirect('administrator/widgets'); break;
				case 'save_new'			:
				case 'save_back'		:
				case 'save'				:
					//Valid input
					$this->MYERROR = myvalid(mypost(),array($this->MYRULES['title']));
					/* Begin upload */

					if(empty($this->MYERROR))
					if($this->mwidgets_cate->insert(array(
						'title'						=>	mypost('title'),
						'key'						=>	mypost('key'),
						'datatext'					=>	mypost('datatext'),	

						'file'						=>	mypost('file'),

						'sc_cuid'					=>	@$this->appmanager->USER['username'],
						'sc_uuid'					=>	@$this->appmanager->USER['username'],
						'sc_ctime'					=>	time(),
						'sc_utime'					=>	time(),
						'sc_status'					=>	1,
						'sc_order'					=>	$this->mwidgets_cate->ordermax()+1
					)))
					{
						!(mypost('action')=='save_new')  or myredirect('administrator/widgets/addcate');
						!(mypost('action')=='save_back') or myredirect('administrator/widgets');
						myredirect('administrator/widgets/editcate/'.$this->mwidgets_cate->insert_id());
					}

				break;
			}
		}
		$this->action	=	__function__;
		myview('index',array('CONTENT'=>array('widgets/addcate',$this)));
	}
	function editcate($id= null){

		$this->model->check_group(__METHOD__);
		$this->action	=	__function__;
		$this->MYROW = @array_shift($this->mwidgets_cate->select(array('id'=>intval($id))));
		!empty($this->MYROW) or show_404();
		if(!empty($_POST))
		{
			switch(mypost('action'))
			{
				case 'back'				: myredirect('administrator/widgets/cate'); break;
				case 'save_new'		:
				case 'save_back'	:
				case 'save'				:
					//Valid input
					$this->MYERROR = myvalid(mypost(),array($this->MYRULES['title']));
					
					if(empty($this->MYERROR))
					if($this->mwidgets_cate->update(array(
						'title'						=>	mypost('title'),
						'key'						=>	mypost('key'),
						'datatext'					=>	mypost('datatext'),

						'file'						=>	mypost('file'),

						'sc_uuid'					=>	@$this->appmanager->USER['id'],
						'sc_utime'					=>	time()
					),array(
						'id'						=>	$this->MYROW['id']
					)))
					{
						//Delete old file
						empty($_FILES['file']['name'])	 or @unlink($this->MYROW['file']);
						!(mypost('action')=='save_new')  or myredirect('administrator/widgets/addcate');
						!(mypost('action')=='save_back') or myredirect('administrator/widgets/cate');
						myredirect('administrator/widgets/editcate/'.$this->MYROW['id']);
					}
				break;
			}
			$this->MYROW = arraymerkey($this->MYROW,mypost());
		}
		$this->MYPIDS = arraymake(arraymer(
			array(array('id'=>0,'_txttitle'=>lang('_pid_root')))
		),'id','_txttitle');
		myview('index',array('CONTENT'=>array('widgets/addcate',$this)));
	}
	function readXmlwidget(){
		$widgets	= 	'';
		$map = directory_map('./modules',3);
		$this->ROWS= array();
		foreach ($map as $modulesname => $value) {
			if(isset($map[$modulesname]['widgets'])){
				foreach ($map[$modulesname]['widgets'] as $key => $name) {
					$path = 'modules/'.$modulesname.'/widgets/'.$name.'/info.xml';
				}
			}
			$data = xmlToArray($path);
			echo json_encode($data['config']);
		}
	}
	function allwidgetcat($pid = null, $page = 1){
		/* Begin action */
		if(!empty($_POST))
		{
			$ids = mypost('id');
			switch(mypost('action'))
			{
				case 'add'		:	myredirect('administrator/widgets/add'); break;
				case 'edit'		:	myredirect('administrator/widgets/edit/'.$ids[0]); break;
				case 'delete'	:
				// kiem tra quyen module
				$this->model->check_group('widgets_administrator::delete');
				$this->db->where_in('id',$ids); $this->model->delete(); break;
				case 'inactive'	:	
				$this->model->check_group('widgets_administrator::edit');
				$this->db->where_in('id',$ids); $this->model->update(array('sc_status'=>0)); break;
				case 'active'	: 	
				$this->model->check_group('widgets_administrator::edit');
				$this->db->where_in('id',$ids); $this->model->update(array('sc_status'=>1)); break;
				case 'order'	: 	
				$this->model->check_group('widgets_administrator::edit');
				$this->model->orderlist(mypost('sc_order')); break;
				case 'reorder'	: 	
				$this->model->check_group('widgets_administrator::edit');
				$this->model->ordersort(array('sc_ctime'=>'asc')); break;
			}
		}else{
			// kiem tra quyen module
			$this->model->check_group(__METHOD__);
			//  ket thuc kiem tra quyen module
		}
		/* End action */
		$this->db->where('id_cate',$pid);
		$this->db->join('widgets', 'id_widget = id');
		$this->MYROWS = $this->db->get('widgets_cate_relate')->result_array();
		
		$this->count_rows	=	count($this->MYROWS);

		/* End pagging */
		myview('index',array('CONTENT'=>array('widgets/index',$this)));
	}
	function getRealIpAddr() 
	{    
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet    
	    {      
	         $ip=$_SERVER['HTTP_CLIENT_IP'];    
	    }     
	    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy   
	    {    
	         $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];  
	    }   
	    else   
	    {      
	         $ip=$_SERVER['REMOTE_ADDR'];    
	    }   
	    echo $ip; 
	}
	function checkfile(){
		if(file_exists('modules/widgets/widgets/footer/footer.php'))
			echo "true";
		else echo 'false';
	}
}