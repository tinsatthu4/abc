<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class comments_site extends Site_Controller
{
	function __construct(){
		parent::__construct();
		$this->load->languages(array(
				'core/common',
				'comments/comments_admin'
		),$this->appmanager->LANGUAGE);
		$this->load->models(array('comments/mcomments_admin'));
		$this->model = $this->mcomments_admin;
	}
	function addcomment()
	{
		$this->input->is_ajax_request() or show_404();
		if(mypost())
		{
			$data = array(
					"id" =>  mypost('commentID'),
					"href" => mypost('href'),
					"sc_ctime"=>time(),
			);
			$this->model->insert($data);
		}
	}
	function removecomment()
	{
		$this->input->is_ajax_request() or show_404();
		if(mypost())
			$this->model->delete(array("id"=>mypost('commentID')));
	}
}
?>