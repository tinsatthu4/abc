<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class comments_home extends Home_controller
{
	function __construct(){
		parent::__construct();
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
					"customer_id"=>0,
			);
			$this->db->insert('comments',$data);
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