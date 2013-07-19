<?php 
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
class Contacts_site extends Site_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->models(array(
				'pages/mpage_site',
				'contacts/mcontacts_site',
				'modun/mmodun_site',
				'contacts/mattr'
				));
		$this->load->languages(array(
				'contacts/contacts_site'
				),$this->appmanager->LANGUAGE);
		$this->model = $this->mcontacts_site;
		$this->modelM = $this->mmodun_site;
		$this->modelP = $this->mpage_site;
	}
	function index($page = 0)
	{
		/*
		 * Lay widget cho pages
		*/
		$this->modelP->checkPage($this->appmanager->ID_LAYOUT,__METHOD__)
		or show_404();
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or $WIDGETS = $this->modelM->create(__METHOD__);
		
		/*CODE MODULES HERE*/
		$WIDGETS = array_shift($this->modelM->select(__METHOD__));
		$WIDGETS or show_404();
		$ruleslist = array(
			array('captcha','lang:captcha','required|trim'),
			array('options[email]','lang:email','required|valid_email'),
			array('options[name]','lang:name','required|trim|strip_tags'),
			array('options[content]','lang:content','required|trim|strip_tags'),
		);
		$this->ER = array();
		if(mypost())
		{
			if(mypost('captcha') == @$_SESSION['captcha'])
			{
				$this->ER = myvalid(mypost(), $ruleslist);
				
				if(!$this->ER){
				$data = array(
					'sc_ctime'=>time(),
					'sc_utime'=>time(),
					'options'=>mypost('options'),
				);
				if($this->model->insert($data))
					$this->SU = lang('success');
				else 
					$this->ER[] =lang('error_insert');
				} else $this->OPTIONS = mypost('options');
			} 
			else
			{ 
				$this->ER[] = lang('error_captcha');
				$this->OPTIONS = mypost('options');
			}
		}
		$this->CAPTCHA = mymod("core/helpers/captcha/captcha.php");
		$this->BREADCRUMBS = array(
			array(
					"title"=>lang('br_home'),
					"link"=>mysiteurl()
			),
			array(
					"title"=>lang("contact_index"),
					"link"=>"javascript:void()",
			)
		);
		myview("index",array("CONTENT"=>array("contacts/index",$this),
		'TITLE'=>lang('title'),
		'METHOD'=>'contacts_site::index',
		'WIDGETS'=>$WIDGETS,
		));
	}
}
?>