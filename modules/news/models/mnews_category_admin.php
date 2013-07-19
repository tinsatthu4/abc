<?php
class mnews_category_admin extends CustomerLeverRelation_Model {
	protected $table = 'news_cate';
	protected $tableRelation = 'news_relate';
	function __construct() {
		parent::__construct ( $this->table, $this->tableRelation );
	}
}
?>