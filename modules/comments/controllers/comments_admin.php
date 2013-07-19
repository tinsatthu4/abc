<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class comments_admin extends Admin_Controller
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
	function index()
	{
		$this->ROWS = $this->model->select();
		myview("index",array("CONTENT"=>array("comments/index"),
		"TITLE"=>lang("comments_modules")
		));
	}
	function view($commentID = '')
	{
		$ROW = array_shift($this->model->select(array("id"=>$commentID)));
		$ROW or show_404();
		$this->model->update(array("sc_status"=>0),array("id"=>$commentID));
		redirect($ROW['href']);
	}
}
?>