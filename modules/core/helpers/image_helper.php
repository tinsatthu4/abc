<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 * Thumb()
 * A TimThumb-style function to generate image thumbnails on the fly.
 *
 * @author Darren Craig
 * @param string $image_path        	
 * @param int $width        	
 * @param int $height        	
 * @return String
 *
 */

function thumb($image_path, $width = 0, $height = 0) {
	if(isUrlImg($image_path)) $image_path = getPath($image_path);
	$CI = &get_instance ();
	$file = explode ( ".", $image_path );
	$ext = array_pop ( $file );
	$file_name = array_shift ( $file );
	$file_name = str_replace ( dirname ( $image_path ) . "/", "", $file_name );
	// get file extension
	$file = explode ( ".", $image_path );
	$ext = array_pop ( $file );
	$file_name = array_shift ( $file );
	$file_name = str_replace ( dirname ( $image_path ) . "/", "", $file_name );
	
	// Path to image thumbnail
	@mkdir(dirname ( $image_path ).'/thumb');
	$image_thumb = dirname ( $image_path ) . '/thumb/' . $file_name ."_".$width. '_' .$height. "." . $ext;
	if (! file_exists ( $image_thumb )) {
		// LOAD LIBRARY
		$CI->load->library ( 'image_lib' );
		// CONFIGURE IMAGE LIBRARY
		$config ['image_library'] = 'gd2';
		$config ['source_image'] = $image_path;
		$config ['new_image'] = $image_thumb;
		$config ['maintain_ratio'] = true;
		$config ['master_dim'] = "width";
		if ($height > $width) {
			$config ['master_dim'] = "height";
		}
		if ($width != null || $width != 0)
			$config ['width'] = intval($width);
		if ($height != null || $height != 0)
			$config ['height'] = intval($height);
		$CI->image_lib->initialize ( $config );
		$CI->image_lib->resize ();
		$CI->image_lib->clear ();
	}
	return $image_thumb;
}
function getThumb($file_path) {
	$file_path = explode ( '.', $file_path );
	$ext = array_pop ( $file_path );
	$file_path = array_shift ( $file_path );
	$path = dirname ( $file_path );
	$filename = str_replace ( $path . '/', "", $file_path );
	$CI = &get_instance ();
	$CI->load->helper ( 'file' );
	$filename = explode ( '.', $filename );
	$files = get_filenames ( $path );
	$rs = array ();
	if (is_array ( $files ))
		foreach ( $files as $file ) {
			if (preg_match ( '/^' . $filename [0] . '[0-9_]+\.' . $ext . '/', $file ))
				$rs [] = $path . "/" . $file;
		}
	return $rs;
}
function deleteImg($file_path) {
	$thumb = getThumb ( $file_path );
	@unlink ( $file_path );
	if (is_array ( $thumb ))
		foreach ( $thumb as $val ) {
			@unlink ( $val );
		}
}
function getImages($file_path) {
	$CI = &get_instance ();
	$CI->load->helper ( 'file' );
	$images = array ();
	$img = get_filenames ( $file_path );
	if (is_array ( $img ))
		foreach ( $img as $val )
			$images = array_merge ( $images, array (
					$file_path . "/" . $val 
			) );
	return $images;
}
function getPath($link_image)
{
	$link_image = str_replace(base_url(),'',$link_image);
	return $link_image;
}
function convertImages($post_images)
{
	 $post_images = isUrlImg($post_images)?getPath($post_images):"no-image";
	return $post_images;
}

