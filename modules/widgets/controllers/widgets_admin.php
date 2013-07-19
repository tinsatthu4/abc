<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class widgets_admin extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->languages(array(
			'widgets/widgets_admin',
			'core/common',		
		),$this->appmanager->LANGUAGE);
	}
	function index()
	{
		myview('index',array('CONTENT'=>array('widgets/index',$this)));
	}

	function widgets_support_online($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
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
					if($val['primary_key']==$primary_key && $val['id'] == intval($id_widgets) ){					
						$val['options']['title'] = mypost('title');
						$val['options']['hidetitle'] = @intval(mypost('hidetitle'));				
						$val['options']['nick'] = @is_array(mypost('nick'))?array_values(mypost('nick')):array();
						$val['options']['name'] = @is_array(mypost('name'))?array_values(mypost('name')):array();
						$val['options']['server'] = @is_array(mypost('server'))?array_values(mypost('server')):array();
						$options = $val['options'];
					}
					$data[$layout] = $ROW;
					if($this->mmodun_admin->update($data,array('id_page'=>intval($id_page))))
						$this->OPTIONS = $options;
					break;
			}
			$this->cachefile->config($this->OPTIONS['primary_key'],$this->cachefile->getDir().'/widgets');
			$this->cachefile->delete();
		}
		$this->SERVER = array(
		"yahoo"=>lang('yahoo'),
		"skype"=>lang("skype"),		
		);	
		$this->appmanager->THEME = $themes;
		myview('widgets/widgets/widgets_support_online',$this);
		
	}
	function widgets_html($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
	{
		$this->input->is_ajax_request() or show_404();
		$this->load->models(array('modun/mmodun_admin'));
		$this->load->helpers(array('core/ckeditor'));
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
						if($val['primary_key']==$primary_key && $val['id'] == intval($id_widgets) ){
						$val['options']['title'] = @mypost('title');
						$val['options']['content'] = @mypost('content');
						$val['options']['hidetitle'] = @intval(mypost('hidetitle'));
						$options = $val['options'];
					}
					$data[$layout] = $ROW;
					if($this->mmodun_admin->update($data,array('id_page'=>intval($id_page))))
						$this->OPTIONS = $options;
					break;
			}
			$this->cachefile->config($this->OPTIONS['primary_key'],$this->cachefile->getDir().'/widgets');
			$this->cachefile->delete();
		}
		$this->appmanager->THEME = $themes;
		myview('widgets/widgets/widgets_html',$this);
	
	}
	function widgets_slideshow($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
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
				$ruleslist = array(
					array('interval','','intval'),
					array('title_image[]','','strip_tags|trim'),
					array('caption[]','','strip_tags|trim'),
					array('link[]','','prep_url'),
					array('images[]','','getPath'),
				);
				myvalid(mypost(), $ruleslist);
					foreach($ROW as &$val)					
						if($val['primary_key']==$primary_key && $val['id'] == intval($id_widgets)){
						$val['options']['title'] = mypost('title');
						$val['options']['hidetitle'] = @intval(mypost('hidetitle'));
						$val['options']['interval'] = @mypost('interval');
						$post_images = is_array(mypost('images'))?array_values(mypost('images')):array(); 
						$val['options']['images'] = array_values($post_images);
						$val['options']['title_image'] = is_array(mypost('title_image'))?array_values(mypost('title_image')):array();
						$val['options']['caption'] = is_array(mypost('caption'))?array_values(mypost('caption')):array();
						$val['options']['link'] = is_array(mypost('link'))?array_values(mypost('link')):array();
						$options = $val['options'];						
					}
					$data[$layout] = $ROW;
					if($this->mmodun_admin->update($data,array('id_page'=>intval($id_page))))
						$this->OPTIONS = $options;
					break;
			}
			$this->cachefile->config($this->OPTIONS['primary_key'],$this->cachefile->getDir().'/widgets');
			$this->cachefile->delete();
		}
		$this->appmanager->THEME = $themes;
		myview('widgets/widgets/widgets_slideshow',$this);
	}
	function widgets_ads($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
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
						$ruleslist = array(
								array('title_image[]','','strip_tags|trim'),
								array('caption[]','','strip_tags|trim'),
								array('link[]','','prep_url'),
								array('images[]','','getPath'),
						);
						myvalid(mypost(), $ruleslist);	
						$val['options']['images'] = is_array(mypost('images'))?array_values(mypost('images')):array();;
						$val['options']['title_image'] = is_array(mypost('title_image'))?array_values(mypost('title_image')):array();
						$val['options']['link'] = is_array(mypost('link'))?array_values(mypost('link')):array();
						$options = $val['options'];
					}
					$data[$layout] = $ROW;
					if($this->mmodun_admin->update($data,array('id_page'=>intval($id_page))))
						$this->OPTIONS = $options;
					break;
			}
			$this->cachefile->config($this->OPTIONS['primary_key'],$this->cachefile->getDir().'/widgets');
			$this->cachefile->delete();
		}
		$this->appmanager->THEME = $themes;
		myview('widgets/widgets/widgets_ads',$this);		
	}
	function widgets_facebook_like($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
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
					$rules = array(
					array('title','','trim|strip_tags'),
					array('url','','prep_url|trim'),
					);
					foreach($ROW as &$val)
						if($val['primary_key']==$primary_key && $val['id'] == intval($id_widgets)){
						$val['options']['title'] = mypost('title');
						$val['options']['hidetitle'] = @intval(mypost('hidetitle'));
						$val['options']['url'] = mypost('url');
						$val['options']['showfaces'] = @intval(mypost('showfaces'));
						$val['options']['showborder'] = @intval(mypost('showborder'));
						$val['options']['showheader'] = @intval(mypost('showheader'));
						$options = $val['options'];
					}
					$data[$layout] = $ROW;
					if($this->mmodun_admin->update($data,array('id_page'=>intval($id_page))))
						$this->OPTIONS = $options;
					break;
			}
			$this->cachefile->config($this->OPTIONS['primary_key'],$this->cachefile->getDir().'/widgets');
			$this->cachefile->delete();
		}
		$this->appmanager->THEME = $themes;
		myview('widgets/widgets/widgets_facebook_like',$this);
	}
	function widgets_google_maps($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
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
						$content = mypost('content');
						$content = str_ireplace(array("<script>","</scirpt>","<?php","<?=","</script>","?>"),"",$content);
						$content = preg_replace("/\swidth\=(\"|\')([0-9]+)(\"|\')\s/",' style="width:100%" ', $content);
							
						$val['options']['content'] = $content;
												
						$options = $val['options'];
					}
					$data[$layout] = $ROW;
					if($this->mmodun_admin->update($data,array('id_page'=>intval($id_page))))
						$this->OPTIONS = $options;
					break;
			}
			$this->cachefile->config($this->OPTIONS['primary_key'],$this->cachefile->getDir().'/widgets');
			$this->cachefile->delete();
		}
		$this->appmanager->THEME = $themes;
		myview('widgets/widgets/widgets_google_maps',$this);
	}
	function widgets_facebook_comment($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
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
						if($val['primary_key']==$primary_key && $val['id'] == @intval($id_widgets)){
						$val['options']['title'] = mypost('title');
						$val['options']['hidetitle'] = @intval(mypost('hidetitle'));
						$val['options']['numberpost'] = @intval(mypost('numberpost'));
						$options = $val['options'];
					}
					$data[$layout] = $ROW;
					if($this->mmodun_admin->update($data,array('id_page'=>intval($id_page))))
						$this->OPTIONS = $options;
					break;
			}
			$this->cachefile->config($this->OPTIONS['primary_key'],$this->cachefile->getDir().'/widgets');
			$this->cachefile->delete();
		}
		$this->appmanager->THEME = $themes;
		myview('widgets/widgets/widgets_facebook_comment',$this);
	}
	function widgets_text($page=1,$primary_key='',$id_page='',$id_widgets='',$layout='none',$themes='site/common')
	{
		$this->input->is_ajax_request() or show_404();
		$this->load->models(array('modun/mmodun_admin'));
		$this->load->helpers(array('core/ckeditor'));
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
						if($val['primary_key']==$primary_key && $val['id'] == intval($id_widgets) ){
						$val['options']['title'] = @mypost('title');
						$val['options']['content'] = @mypost('content');
						$val['options']['hidetitle'] = @intval(mypost('hidetitle'));
						$options = $val['options'];
					}
					$data[$layout] = $ROW;
					if($this->mmodun_admin->update($data,array('id_page'=>intval($id_page))))
						$this->OPTIONS = $options;
					break;
			}
			$this->cachefile->config($this->OPTIONS['primary_key'],$this->cachefile->getDir().'/widgets');
			$this->cachefile->delete();
		}
		$this->appmanager->THEME = $themes;
		myview('widgets/widgets/widgets_text',$this);
	}	

}
?>