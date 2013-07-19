<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Validation extends CI_Form_validation {
	
	/**
	 * Run the Validator
	 *
	 * This function does all the work.
	 *
	 * @access public
	 * @return bool
	 */
	
	/**
	 * Recaptcha
	 *
	 * @access public
	 * @param
	 *        	string
	 * @return bool
	 */
	function __construct($rules = array()) {
		parent::__construct ( $rules = array () );
		// $CI =& get_instance();
		// $CI->load->library('Recaptcha');
	}
	/*
	 * function recaptcha($str) { die('Recaptcha'.$str); }
	 */
	function run($group = '', $data = '') {
		// Do we even have any data to process? Mm?
		if (count ( $data ) == 0) {
			return FALSE;
		}
		
		// Does the _field_data array containing the validation rules exist?
		// If not, we look to see if they were assigned via a config file
		if (count ( $this->_field_data ) == 0) {
			// No validation rules? We're done...
			if (count ( $this->_config_rules ) == 0) {
				return FALSE;
			}
			
			// Is there a validation rule for the particular URI being accessed?
			$uri = ($group == '') ? trim ( $this->CI->uri->ruri_string (), '/' ) : $group;
			
			if ($uri != '' and isset ( $this->_config_rules [$uri] )) {
				$this->set_rules ( $this->_config_rules [$uri] );
			} else {
				$this->set_rules ( $this->_config_rules );
			}
			
			// We're we able to set the rules correctly?
			if (count ( $this->_field_data ) == 0) {
				log_message ( 'debug', "Unable to find validation rules" );
				return FALSE;
			}
		}
		
		// Load the language file containing error messages
		$this->CI->lang->load ( 'form_validation' );
		
		// Cycle through the rules for each field, match the
		// corresponding $data item and test for errors
		foreach ( $this->_field_data as $field => $row ) {
			// Fetch the data from the corresponding $data array and cache it in
			// the _field_data array.
			// Depending on whether the field name is an array or a string will
			// determine where we get it from.
			
			if ($row ['is_array'] == TRUE) {
				$this->_field_data [$field] ['postdata'] = $this->_reduce_array ( $data, $row ['keys'] );
			} else {
				if (isset ( $data [$field] ) and $data [$field] != "") {
					$this->_field_data [$field] ['postdata'] = $data [$field];
				}
			}
			
			$this->_execute ( $row, explode ( '|', $row ['rules'] ), $this->_field_data [$field] ['postdata'] );
		}
		
		// Did we end up with any errors?
		$total_errors = count ( $this->_error_array );
		
		if ($total_errors > 0) {
			$this->_safe_form_data = TRUE;
		}
		
		// Now we need to re-set the POST data with the new, processed data
		$this->_reset_post_array ();
		
		// No errors, validation passes!
		if ($total_errors == 0) {
			return TRUE;
		}
		
		// Validation fails
		return FALSE;
	}
	// --------------------------------------------------------------------
	function result_array($error = array()) {
		if (empty ( $this->_field_data ))
			return $error;
		
		foreach ( $this->_field_data as $k => $v )
			if (! empty ( $v ['error'] ))
				$error [$k] = $v ['error'];
		
		$this->clear ();
		return $error;
	}
	function clear() {
		$this->_error_array = array ();
		$this->_field_data = array ();
	}
}