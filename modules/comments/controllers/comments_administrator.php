<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class comments_administrator extends Administrator_Controller
{
	function __construct(){
		parent::__construct();
		$this->load->models(array('comments/mcomments_administrator'));
		$this->model = $this->mcomments_administrator;
	}
	function index()
	{
		$this->ROWS = $this->model->select(array('customer_id'=>0));
		myview("index",array("CONTENT"=>array("comments/index"),
		"TITLE"=>lang("comments_modules")
		));
	}
	function view($commentID = '')
	{
		$ROW = array_shift($this->model->select(array("id"=>$commentID,"customer_id"=>0)));
		$ROW or show_404();
		$this->model->update(array("sc_status"=>0),array("id"=>$commentID,"customer_id"=>0));
		redirect($ROW['href']);
	}
}
?>