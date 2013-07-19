<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class pages_administrator extends Administrator_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->models ( array (
				'pages/mpage_administrator',
		) );
		$this->load->languages ( array (
				'core/common',
				'pages/pages',
				'common' 
		), $this->appmanager->LANGUAGE );
		$this->model = $this->mpage_administrator;
		$this->config->load ( 'form_validate' );
		$this->RU = $this->config;
		$this->MYRULES = array (
				'title' => array (
						'title',
						'lang:pages_pagestitle',
						'trim|required|min_length[5]|max_length[24]' 
				),
				'file' => array (
						'upload_path' => $this->config->config ['mod_path'] . 'pages/upload/',
						'upload_dir' => 'pages/upload/',
						'allowed_types' => 'jpg|png|gif|jpeg',
						'max_size' => 0,
						'note' => @sprintf ( lang ( '_upload_rules' ), lang ( 'news_upload' ), 'png, gif, jpg', '< 10.00 MB' ) 
				) 
		);	
	}
	function index($page = 1) {
		/* Begin action */
		if (! empty ( $_POST )) {
			$ids = mypost ( 'id' );
			switch (mypost ( 'action' )) {
				case 'add' :
					myredirect ( 'administrator/pages/add' );
					break;
				case 'edit' :
					myredirect ( 'administrator/pages/edit/' . $ids [0] );
					break;
				case 'delete' :
					// kiem tra quyen module
					$this->model->check_group ( 'pages_administrator::delete' );
					$this->db->where_in ( 'id', $ids );
					$this->model->delete ();
					break;
				case 'inactive' :
					$this->model->check_group ( 'pages_administrator::edit' );
					$this->db->where_in ( 'id', $ids );
					$this->model->update ( array (
							'sc_status' => 0 
					) );
					break;
				case 'active' :
					$this->model->check_group ( 'pages_administrator::edit' );
					$this->db->where_in ( 'id', $ids );
					$this->model->update ( array (
							'sc_status' => 1 
					) );
					break;
				case 'order' :
					$this->model->check_group ( 'pages_administrator::edit' );
					$this->model->orderlist ( mypost ( 'sc_order' ) );
					break;
				case 'reorder' :
					$this->model->check_group ( 'Modules_administrator::edit' );
					$this->model->ordersort ( array (
							'sc_ctime' => 'asc' 
					) );
					break;
			}
		} else {
			// kiem tra quyen module
			$this->model->check_group ( __METHOD__ );
			// ket thuc kiem tra quyen module
		}
		/* End action */
		/* Begin pagging */
		$pp 		= 20;
		$tr 		= $this->model->countrows();
		$tp 		= ceil($tr/$pp);
		$page 	= (($page<1)?1:($page>$tp?(($tp<1)?1:$tp):$page));
		
		$this->MYPAGGING 	= htmlpagging($page,$tp,mysiteurl('administrator/pages/index/[x]'));
		$this->MYROWS = arraypagging($this->model->listchild ( 0,'', array (
				'sc_order' => 'desc' 
		), $pp * ($page - 1), $pp ), $pp, $page );
		/* End pagging */
		$this->count_rows	=	$tr;

		myview ( 'index', array (
				'CONTENT' => array (
						'pages/index',
						$this 
				) 
		) );
	}
	function add() {
		$tr 		= $this->model->countrows();
		$this->count_rows	=	$tr;
		$this->MYROWS = $this->model->listchild ( 0,'', array (
				'sc_order' => 'desc' 
		));
		
		$this->model->check_group ( __METHOD__ );
		
		if (! empty ( $_POST )) {
		
			switch (mypost ( 'action' )) {
				case 'back' :
					myredirect ( 'administrator/pages' );
					break;
				case 'save_new' :
				case 'save_back' :
				case 'save' :
					// Valid input
					$this->MYERROR = myvalid ( mypost (), array (
							$this->MYRULES ['title'] 
					) );
					
					/* Begin upload */
					$file = '';
					if (! empty ( $_FILES ['file'] ['name'] )) {
						$upload = myupload ( 'file', $this->MYRULES ['file'] );
						if (! empty ( $upload ))
							$file = $this->MYRULES ['file'] ['upload_dir'] . @$upload ['file_name'];
						else
							$this->MYERROR [] = $this->MYRULES ['file'] ['note'];
					}
					
					/* End upload */
					
					if (empty ( $this->MYERROR ))
						if ($this->model->insert ( array (
								'title' => mypost ( 'title' ),								
								'pid'	=> @mypost('pid'),
								'method'=>@mypost('method'),
								
								'sc_ctime' => time (),
								'sc_utime' => time (),
								'sc_status' => 1,
								'sc_order' => $this->model->ordermax () + 1 
						) )) {
							! (mypost ( 'action' ) == 'save_new') or myredirect ( 'administrator/pages/add' );
							! (mypost ( 'action' ) == 'save_back') or myredirect ( 'administrator/pages' );
							myredirect ( 'administrator/pages/edit/' . $this->model->insert_id () );
						}
						// Delete file uploaded
					empty ( $file ) or @unlink ( $file );
					break;
			}
		}
		$this->action = __function__;
		myview ( 'index', array (
				'CONTENT' => array (
						'pages/add',
						$this 
				) 
		) );
	}
	function edit($id = 0) {
		$tr 		= $this->model->countrows();
		$this->count_rows	=	$tr;
		$this->MYROWS = $this->model->listchild ( 0,'', array (
			'sc_order' => 'desc' 
		));

		$this->MYROWS = $this->model->listchild ( 0,'', array (
				'sc_order' => 'desc' 
		));
		$this->model->check_group ( __METHOD__ );
		$this->action = __function__;
		$this->MYROW = @array_shift ( $this->model->select ( array (
				'id' => intval ( $id ) 
		) ) );
		! empty ( $this->MYROW ) or show_404 ();
		if (! empty ( $_POST )) {
			// mybugview($_POST); die();
			switch (mypost ( 'action' )) {
				case 'back' :
					myredirect ( 'administrator/pages' );
					break;
				case 'save_new' :
				case 'save_back' :
				case 'save' :
					// Valid input
					$this->MYERROR = myvalid ( mypost (), array (
							$this->MYRULES ['title'] 
					) );
				
					
					$data = array (
							'title' 	=> 	mypost ( 'title' ),
							'pid'		=> 	@mypost('pid'),
							'method'	=>	@mypost('method'),
							'sc_utime' 	=> 	time () 
					);
					
					if (empty ( $this->MYERROR )) {
						if ($this->model->update ( $data, array (
								'id' => $this->MYROW['id'] 
						) )) {
							! (mypost ( 'action' ) == 'save_new') or myredirect ( 'administrator/pages/add' );
							! (mypost ( 'action' ) == 'save_back') or myredirect ( 'administrator/pages' );
							myredirect ( 'administrator/pages/edit/' . $this->MYROW ['id'] );
						}
					} else {
						mybugview ( $this->MYERROR );
					}
					break;
			}
		}
		myview ( 'index', array (
				'CONTENT' => array (
						'pages/add',
						$this 
				) 
		) );
	}
}
