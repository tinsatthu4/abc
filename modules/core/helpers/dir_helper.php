<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

if (! function_exists ( 'dircopy' )) {
	function dircopy($srcdir, $dstdir) {
		// preparing the paths
		$srcdir = rtrim ( $srcdir, '/' );
		$dstdir = rtrim ( $dstdir, '/' );
		// creating the destenation directory
		if (! is_dir ( $dstdir ))
			mkdir ( $dstdir );
			
			// Mapping the directory
		$dir_map = directory_map ( $srcdir );
		
		foreach ( $dir_map as $object_key => $object_value ) {
			if (is_numeric ( $object_key ))
				copy ( $srcdir . '/' . $object_value, $dstdir . '/' . $object_value ); // This
				                                                           // is a File not
				                                                           // a directory
			else
				dircopy ( $srcdir . '/' . $object_key, $dstdir . '/' . $object_key ); // this is
				                                                          // a dirctory
		}
	}
}
if (! function_exists ( 'deleteDir' )) {
	function deleteDir($path_dir = '') {
		$CI = &get_instance ();
		$CI->load->helper ( 'file' );
		if (is_dir ( $path_dir )) {
			@delete_files ( $path_dir, true );
			@rmdir ( $path_dir );
		}
	}
} 