<?php

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class ModManager {
	private $tmpdir = 'tmp/';
	private $allowed_types = 'zip';
	private $max_size = 10000;
	private $overwrite = true;
	
	function __construct($items = array()) {
		if (is_array ( $items ))
			foreach ( $items as $k => $v )
				$this->{$k} = $v;
		
		CI::$APP->load->helpers ( array (
				'download',
				'url',
				'file',
				'directory',
				'string',
				'core/dir' 
		) );
		
		CI::$APP->load->libraries ( array (
				'zip',
				'core/Unzip',
				'upload' 
		) );
		
		log_message ( 'debug', 'ModManager Class Initialized' );
	}
	function getList() {
		$rows = array ();
		$modules = @directory_map ( CI::$APP->config->item ( 'mod_path' ), 1 );
		if (is_array ( $modules ))
			foreach ( $modules as $module )
				if (is_file ( CI::$APP->config->item ( 'mod_path' ) . $module . '/config/info.php' ))
					if (is_dir ( CI::$APP->config->item ( 'mod_path' ) . $module )) {
						$info = CI::$APP->config->load ( $module . '/info' );
						$rows [$module] = @$info [$module];
					}
		return $rows;
	}
	function install($fileupload) {
		$tmpdir = $this->tmpdir . random_string ( 'alnum', 8 ) . '/';
		mkdir ( $tmpdir, 0777, true );
		
		CI::$APP->upload->initialize ( array (
				'upload_path' => $tmpdir,
				'allowed_types' => $this->allowed_types,
				'max_size' => $this->max_size,
				'overwrite' => $this->overwrite 
		) );
		
		if (! CI::$APP->upload->do_upload ( $fileupload ))
			return false;
		
		$upload = CI::$APP->upload->data ();
		
		CI::$APP->unzip->extract ( $upload ['full_path'], $tmpdir );
		CI::$APP->unzip->close ();
		unlink ( $upload ['full_path'] );
		
		$modpath = CI::$APP->config->item ( 'mod_path' ) . $upload ['raw_name'] . '/';
		$tmppath = $tmpdir . $upload ['raw_name'] . '/';
		
		if (is_file ( $tmppath . 'setup/install.php' )) {
			$rollback = require ($tmppath . 'setup/install.php');
			$rollback = (@$rollback == false) ? true : false;
		} else
			$rollback = false;
		
		$rollback or dircopy ( $tmppath, $modpath );
		chmod ( $tmpdir, 0777 );
		delete_files ( $tmpdir, TRUE );
		rmdir ( $tmpdir );
		
		return $rollback ? false : true;
	}
	function uninstall($module) {
		$moddir = CI::$APP->config->item ( 'mod_path' ) . $module . '/';
		if (! is_dir ( $moddir ))
			return false;
		@require ($moddir . 'setup/uninstall.php');
		chmod ( $moddir, 0777 );
		delete_files ( $moddir, true );
		rmdir ( $moddir );
		return true;
	}
	function download($module) {
		if (! is_dir ( CI::$APP->config->item ( 'mod_path' ) . $module . '/' ))
			return false;
		
		CI::$APP->zip->clear_data ();
		CI::$APP->zip->read_dir ( CI::$APP->config->item ( 'mod_path' ) . $module . '/', FALSE );
		CI::$APP->zip->download ( $module . '.zip' );
	}
}