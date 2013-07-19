<?php 
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Mpage_adminis extends Level_Model
{
	function __construct()
	{
		parent::__construct('pages');
	}

	function select($where='',$order='',$of=0,$pp=0)
	{
		$this->db->join('products_category_info', 'id = refid');
		$this->db->where(array('language'=>CI::$APP->appmanager->LANGUAGE));
		
		$rows = parent::select($where,$order,$of,$pp);
		foreach($rows as &$row)
		{
			$row['_link'] 					= mysiteurl('products/'.stringseo($row['title']).'.c'.$row['id']);
			$row['_link_paging'] 		= mysiteurl('products/'.stringseo($row['title']).'.c'.$row['id'].'.p[x]');
		}
		return $rows;
	}	

	function insert($data)
	{			
		$data['options'] = json_encode(is_array(@$data['options'])?$data['options']:array());
		if(parent::insert(arraygetlist($data,$this->db->list_fields('products_category'))))
		{
			foreach(CI::$APP->appmanager->LANGUAGES as $code=>$name)
			{
				$data['refid'] = $this->insert_id();
				$data['language'] = $code;
				$this->db->insert('products_category_info',arraygetlist($data,$this->db->list_fields('products_category_info')));
			}
			return true;
		}
		return false;
	}

	function update($data,$where='')
	{				
		if(array_key_exists('options',$data))
		$data['options'] = json_encode(is_array(@$data['options'])?$data['options']:array());
		
		$rows = $this->select($where);

		$main_data = arraygetlist($data,$this->db->list_fields('products_category'));
		$sub_data = arraygetlist($data,$this->db->list_fields('products_category_info'));
		
		$this->db->where_in('id',arrayget($rows,'id'));
		empty($main_data) or parent::update($main_data);		
	
		$this->db->where_in('refid',arrayget($rows,'id'));
		empty($sub_data) or $this->db->update('products_category_info',$sub_data,array('language'=>CI::$APP->appmanager->LANGUAGE));
		
		return true;
	}
	
	function update1($data,$where='')
	{				
		if(array_key_exists('options',$data))
		$data['options'] = json_encode(is_array(@$data['options'])?$data['options']:array());
		
		$rows = $this->select($where);

		$main_data = arraygetlist($data,$this->db->list_fields('products_category'));
		$sub_data = arraygetlist($data,$this->db->list_fields('products_category_info'));
		
		$this->db->where_in('id',arrayget($rows,'id'));
		empty($main_data) or parent::update($main_data);
		
		
		//$this->db->where_in('refid',$refid );
		empty($sub_data) or $this->db->update('products_category_info',$sub_data,array('language'=>CI::$APP->appmanager->LANGUAGE));
		
		return true;
	}
	
	function delete($where='')
	{			
		$rows = $this->select($where);
		foreach($rows as $row)
		if(parent::delete(array('id'=>$row['id'])))
		{
			$this->db->delete('products_category_info', array('refid'=>$row['id'])); 
			@unlink($this->config->config['mod_path'].$row['file']);
		}
		return true;
	}
}
?>