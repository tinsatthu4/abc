<?php

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class AppManager {
	function __construct($items = array()) {
		if (is_array ( $items ))
			foreach ( $items as $k => $v )
				$this->{$k} = $v;
		
		log_message ( 'debug', 'AppManager Class Initialized' );
	}
}