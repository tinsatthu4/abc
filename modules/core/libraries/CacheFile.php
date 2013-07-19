<?php 
/**
 * @author TINSATTHU4
 * @nick_yahoo tinsatthu4@yahoo.com
 * How to using? Contact with me now
 * version 1.0
 */
class CacheFile 
{
	protected $root_dir = 'cache';
	protected $config = array(
			"directory"=>"cache",
			"filename"=>"cache",
	);
	function __construct()
	{
		
	}
	
	function config($filename,$directory)
	{
		$this->config['directory'] = $directory;
		$this->config['filename'] = $filename;	
	}
	function get()
	{
		if(!file_exists($this->getPath())) return array();
		if(FALSE === ($cachedata = read_file($this->getPath())))
			return array();	
		return unserialize($cachedata);
	}
	
	function create($data)
	{
		if($this->checkCache()) $this->delete();
		if(!is_dir($this->config['directory']))
			@mkdir($this->config['directory'],DIR_WRITE_MODE,true);
		@write_file($this->getPath(), serialize($data));
	}
	function delete()
	{
		if($this->checkCache())
		return @unlink($this->getPath());
		return false;
	}	
	function getPath() {
		return $this->config['directory']."/".$this->config['filename'];
	}
	function getDirectory(){return $this->config['directory'];}
	function getDir(){return $this->getDirectory();}
	function getFilename(){return $this->config['filename'];}
	function getFile(){return $this->getFilename();}
	function getConfig(){return $this->config;}
	function getRoot() {return $this->root_dir;}
	function setDirectory($directory)
	{
		$this->config['directory'] = $directory; return true;
	}
	function setDir($directory){return $this->setDirectory($directory);}
	function setFilename($filename)
	{
		$this->config['filename'] = $filename;
		return true;
	}
	function setFile($filename){return $this->setFilename($filename);}
	function setRoot($root) {return $this->root_dir = $root;}
	function appendDir($directory)
	{
		$this->config['directory'] = $this->config['directory']."/".$directory;
		return true;
	}
	function appendRootdir($directory)
	{
		$this->config['directory'] = $this->root_dir."/".$directory;
		return true;
	}
	function checkCache($filename="",$directory="")
	{
		if(!empty($filename)) $this->config['filename'] = $filename;
		if(!empty($directory)) $this->config['directory'] = $directory;
		return file_exists($this->getPath());
	}
	
}

?>