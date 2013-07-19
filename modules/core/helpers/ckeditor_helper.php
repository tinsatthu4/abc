<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
function display_ckeditor_widgets_html($name='content')
{
	$config = "{";
	$config .= "codemirror_theme:'rubyblue',";
	$config .= "extraPlugins:'codemirror',";
	$config .= "startupMode:'source',";
	$config .= "toolbar:[";
// 	$config .= "['Source']";
	$config	.= "],";
	$config .= "}";
	$s = '';
	if(!defined('TI_CKEDITOR_LOAD')){
		define('TI_CKEDITOR_LOAD', true);
		$s .= '<script src="'.mythemeurl('media/addon/ckeditor/ckeditor.js').'"></script>';
	}
	$s .= '<script type="text/javascript">';
	$s .=	"CKEDITOR.replace('".$name."',".$config.");";
	$s .= '</script>';
	return $s;
}
function display_ckeditor_widgets_text($name='content')
{
	$config = "{";
	$config .= "removeDialogTabs:'link:upload;image:Upload',";
	$config .= "toolbar:[";
	$config .= "{ name: 'paragraph',  items: [ 'Bold', 'Italic', 'Underline','-','JustifyLeft', 'JustifyCenter', 'JustifyRight' ] },";
	$config .= "{ name: 'styles', items: [  'Format', 'Font', 'FontSize' ] },";
	$config .= "{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },";
	$config .= "{ name: 'clipboard', items: ['Cut', 'Copy', 'Paste']},";
	$config	.= "],";
	$config .= "}";
	$s = '';
	if(!defined('TI_CKEDITOR_LOAD')){
		define('TI_CKEDITOR_LOAD', true);
		$s .= '<script src="'.mythemeurl('media/addon/ckeditor/ckeditor.js').'"></script>';
	}
	$s .= '<script type="text/javascript">';
	$s .=	"CKEDITOR.replace('".$name."',".$config.");";
	$s .= '</script>';
	return $s;
}
function display_ckeditor_administrator($name='content')
{
	$config = "{";
	$config .= "codemirror_theme:'rubyblue',";
	$config .= "extraPlugins:'codemirror',";
	$config .= "removeDialogTabs:'link:upload;image:Upload',";
	$config .= "filebrowserBrowseUrl:'".mybaseurl('elfinder/elfinder_administrator.php')."',";
	$config .= "filebrowserImageBrowseUrl:'".mybaseurl('elfinder/elfinder_administrator.php')."',";
	$config .= "filebrowserUploadUrl:'".mybaseurl('elfinder/elfinder_administrator.php')."',";
	$config .= "filebrowserImageUploadUrl:'".mybaseurl('elfinder/elfinder_administrator.php')."',";
	$config .= "}";
	$s = '';
	if(!defined('TI_CKEDITOR_LOAD')){
		define('TI_CKEDITOR_LOAD', true);
		$s .= '<script src="'.mythemeurl('media/addon/ckeditor/ckeditor.js').'"></script>';
	}
	$s .= '<script type="text/javascript">';
	$s .=	"CKEDITOR.replace('".$name."',".$config.");";
	$s .= '</script>';
	return $s;
}
function display_ckeditor_admin($name='content')
{
	$config = "{";
	$config .=	"filebrowserBrowseUrl:'".mybaseurl('elfinder/elfinder_admin.php')."',";
	$config .=	"filebrowserImageBrowseUrl:'".mybaseurl('elfinder/elfinder_admin.php')."',";
	$config .=	"filebrowserUploadUrl:'".mybaseurl('elfinder/elfinder_admin.php')."',";
	$config .=	"filebrowserImageUploadUrl:'".mybaseurl('elfinder/elfinder_admin.php')."',";
	$config .= "removeDialogTabs:'link:upload;image:Upload',";
	$config .= "toolbar:[";
	$config .= "{ name: 'document', items: [ 'Source'] },";
	$config .= "{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },";
	$config .= "{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },";
	$config .= "'/',";
	$config .= "{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },";
	$config .= "{ name: 'styles', items: [  'Format', 'Font', 'FontSize' ] },";
	$config .= "{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },";
	$config .= "{ name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar'] },";
	$config .= "{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },";
	$config .= "'/',";
	$config .= "{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },";
	$config	.= "],";
	$config .= "}";
	$s = '';
	if(!defined('TI_CKEDITOR_LOAD')){
		define('TI_CKEDITOR_LOAD', true);
		$s .= '<script src="'.mythemeurl('media/addon/ckeditor/ckeditor.js').'"></script>';
	}
	$s .= '<script type="text/javascript">';
	$s .=	"CKEDITOR.replace('".$name."',".$config.");";
	$s .= '</script>';
	return $s;
}
?>