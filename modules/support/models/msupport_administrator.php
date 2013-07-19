<?php 
class msupport_administrator extends Administrator_model
{
	protected $table = 'support';
	function __construct(){
		parent::__construct($this->table);

	}	
}
?>