<?php
class mnews_admin extends CustomerRelation_Model {
	protected $table = 'news';
	protected $tableRelation = 'news_relate';
	function __construct() {
		parent::__construct ( $this->table, $this->tableRelation );
	}
}

?>