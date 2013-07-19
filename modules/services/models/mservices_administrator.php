<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mservices_administrator extends Administrator_model
{
	function __construct(){
		parent::__construct('services');
	}
}
?>