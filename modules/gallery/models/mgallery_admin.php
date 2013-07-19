<?php
class mgallery_admin extends CustomerRelation_Model {
	protected $tableRelate = "gallery_relate";
	protected $table = "gallery";
	function __construct() {
		parent::__construct ( $this->table, $this->tableRelate );
	}
}
?>