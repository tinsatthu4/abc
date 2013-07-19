<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	
	// myredirect
	// mybaseurl
	// mymodurl
	// mysiteurl
/**
 * get config key from table config_customer.
 */
if (! function_exists ( 'getMyconfig' )) {
	function getMyconfig($key,$table="config_customer") {
		$CI = &get_instance();
		$CI->db->where(array(
				'customer_id'=>$CI->appmanager->CUSTOMER_ID,
				'key'=>$key,
				));
		return $CI->db->get($table)->row_array();
		
	}
}
/**
 * Save config key value.Delete if exists and insert new.
 */
if (! function_exists ( 'saveMyconfig' )) {
	function saveMyconfig($key, $val,$table="config_customer") {
		$CI = &get_instance();
		$CI->db->delete($table,array(
				'key'=>$key,
				'customer_id'=>$CI->appmanager->CUSTOMER_ID
				));
		return $CI->db->insert($table,array(
				'key'=>$key,
				'customer_id'=>$CI->appmanager->CUSTOMER_ID,
				'value'=>$val
				));
	}
}
if (! function_exists ( 'myredirect' )) {
	function myredirect($uri = '', $prefix = '') {
		return redirect ( CI::$APP->config->site_url ( $uri, $prefix ) );
	}
}
if (! function_exists ( 'mybaseurl' )) {
	function mybaseurl($uri = '') {
		return base_url () . trim ( $uri, '/' );
	}
}
if (! function_exists ( 'mysiteurl' )) {
	function mysiteurl($uri = '', $prefix = '') {
		return CI::$APP->config->site_url ( $uri, $prefix );
	}
}
if (! function_exists ( 'mycustomerurl' )) {
	function mycustomerurl($uri='',$prefix='') {
		if(@CI::$APP->appmanager->CUSTOMER_URL == 'domain')
		return mysiteurl($uri,$prefix);
		return mysiteurl(@CI::$APP->appmanager->CUSTOMER_URL.$uri,$prefix);
	}
}
if (! function_exists ( 'myhomeurl' )) {
	function myhomeurl($uri='',$prefix='') {
		return mysiteurl('home/'.$uri,$prefix);
	}
}
//Kiem tra domain hop le
if(!function_exists('myIsDomain'))
{
	function myIsDomain($domain){
		$domain = strtolower($domain);
		$pattern  = '/^(http:\/\/|https:\/\/)?(www\.)?\w{3,}\.{1}[a-z]{2,5}(\.{1}[a-z]{2,3})?\/?$/';
		return preg_match($pattern, $domain,$matches);
	}
}
//Lay domain duoi dang domain.com,loai http://,www.Su dung ham nay khi domain hop le
if(!function_exists('myGetDomain'))
{
	function myGetDomain($domain){
		$domain = strtolower($domain);
		$pattern = '/^(http:\/\/|https:\/\/)?(www\.)?([a-z0-9_]+\.{1}[a-z]{2,3}(\.{1}[a-z]{2,3})?)\/?$/';
		preg_match($pattern,strtolower($domain),$matches);
		return @$matches[3];
	}	
}

if (! function_exists ( 'mylocale' )) {
	function mylocale($key) {
		CI::$APP->load->library ( 'core/locale' );
		return CI::$APP->locale->item ( $key );
	}
}


if (! function_exists ( 'mymod' )) {
	function mymod($uri = '') {
		return CI::$APP->config->config ['mod_path'] . ltrim ( $uri, '/' );
	}
}
if (! function_exists ( 'mymodurl' )) {
	function mymodurl($uri = '') {
		return base_url ( CI::$APP->config->config ['mod_path'] . ltrim ( $uri, '/' ) );
	}
}

if (! function_exists ( 'mythemeurl' )) {
	function mythemeurl($uri = '') {
		return base_url ( CI::$APP->config->config ['themes_path'] . trim ( CI::$APP->appmanager->THEME, '/' ) . '/' . ltrim ( $uri, '/' ) );
	}
}

if (! function_exists ( 'customerthemeurl' )) {
	function customerthemeurl($uri = '') {
		return base_url ( CI::$APP->config->config ['themes_path'] . trim ( CI::$APP->appmanager->THEME, '/' ) . '/' . ltrim ( $uri, '/' ) );
	}
}

if (! function_exists ( 'myvalid' )) {
	function myvalid($data, $ruleslist, $error = array(), $module = '') {
		CI::$APP->load->library ( 'form_validation' );
		CI::$APP->load->library ( 'core/validation' );
		// CI::$APP->load->language('core/form_validation',CI::$APP->appmanager->LANGUAGE);
		if (is_object ( $module ))
			CI::$APP->validation->CI = & $module;
		foreach ( $ruleslist as $rule )
			CI::$APP->validation->set_rules ( $rule [0], $rule [1], $rule [2] );
		CI::$APP->load->languages ( array (
				'core/validate' 
		), CI::$APP->appmanager->LANGUAGE );
		/* Set message */
		CI::$APP->validation->set_message ( 'required', lang ( 'required' . @$module ) );
		CI::$APP->validation->set_message ( 'isset', lang ( 'isset' . @$module ) );
		CI::$APP->validation->set_message ( 'valid_email', lang ( 'valid_email' . @$module ) );
		CI::$APP->validation->set_message ( 'valid_emails', lang ( 'valid_emails' . @$module ) );
		CI::$APP->validation->set_message ( 'valid_url', lang ( 'valid_url' . @$module ) );
		CI::$APP->validation->set_message ( 'valid_ip', lang ( 'valid_ip' . @$module ) );
		CI::$APP->validation->set_message ( 'min_length', lang ( 'min_length' . @$module ) );
		CI::$APP->validation->set_message ( 'max_length', lang ( 'max_length' . @$module ) );
		CI::$APP->validation->set_message ( 'exact_length', lang ( 'exact_length' . @$module ) );
		CI::$APP->validation->set_message ( 'alpha', lang ( 'alpha' . @$module ) );
		CI::$APP->validation->set_message ( 'alpha_numeric', lang ( 'alpha_numeric' . @$module ) );
		CI::$APP->validation->set_message ( 'alpha_dash', lang ( 'alpha_dash' . @$module ) );
		CI::$APP->validation->set_message ( 'numeric', lang ( 'numeric' . @$module ) );
		CI::$APP->validation->set_message ( 'is_numeric', lang ( 'is_numeric' . @$module ) );
		CI::$APP->validation->set_message ( 'integer', lang ( 'integer' . @$module ) );
		CI::$APP->validation->set_message ( 'regex_match', lang ( 'regex_match' . @$module ) );
		CI::$APP->validation->set_message ( 'matches', lang ( 'matches' . @$module ) );
		CI::$APP->validation->set_message ( 'is_natural', lang ( 'is_natural' . @$module ) );
		CI::$APP->validation->set_message ( 'is_natural_no_zero', lang ( 'is_natural_no_zero' . @$module ) );
		CI::$APP->validation->set_message ( 'decimal', lang ( 'decimal' . @$module ) );
		CI::$APP->validation->set_message ( 'less_than', lang ( 'less_than' . @$module ) );
		CI::$APP->validation->set_message ( 'greater_than', lang ( 'greater_than' . @$module ) );
		CI::$APP->validation->run ( '', $data );
		$error = CI::$APP->validation->result_array ( $error );
		CI::$APP->validation->clear ();
		
		return $error;
	}
}

if (! function_exists ( 'myupload' )) {
	function myupload($file, $rule) {
		
		$upload_path = $rule ['upload_path'];
		is_dir ( $rule ['upload_path'] ) or mkdir ( $rule ['upload_path'], 0777, true );
		CI::$APP->load->library ( 'upload' );
		CI::$APP->upload->initialize ( $rule );
		if (! CI::$APP->upload->do_upload ( $file ))
			return array ();
		return CI::$APP->upload->data ();
	}
}

if (! function_exists ( 'mybugview' )) {
	function mybugview($ob) {
		echo '<pre>';
		print_r ( is_object ( $ob ) ? get_object_vars ( $ob ) : $ob );
		echo ('</pre>');
	}
}

if (! function_exists ( 'mybug' )) {
	function mybug($ob) {
		echo '<pre>';
		print_r ( is_object ( $ob ) ? get_object_vars ( $ob ) : $ob );
		die ( '</pre>' );
	}
}

if (! function_exists ( 'mypost' )) {
	function mypost($key = '', $default = '') {
		$CI = & get_instance ();
		$val = empty ( $key ) ? $CI->input->post () : $CI->input->post ( $key );
		return empty ( $val ) ? $default : $val;
	}
}

if (! function_exists ( 'myview' )) {
	function myview($view, $vars = array(), $return = FALSE) {
		return CI::$APP->load->view ( $view, $vars, $return );
	}
}
if (! function_exists ( 'mylanguages' )) {
	function mylanguages($key, $val = '') {
		$CI = & get_instance ();
		$row = $CI->mlanguages->get ( $key );
		return ($row == false) ? $val : $row;
	}
}

if (! function_exists ( 'mypagging' )) {
	function mypagging($pp, $totalrows, $page) {
		$totalpage = ceil ( $totalrows / $pp );
		$page = (($page < 1) ? 1 : ($page > $totalpage ? (($totalpage < 1) ? 1 : $totalpage) : $page));
		return array (
				'pp' => $pp,
				'tr' => $totalrows,
				'tp' => $totalpage,
				'pa' => $page 
		);
	}
}

if (! function_exists ( 'myemail' )) {
	function myemail($from_email, $from_name, $to, $subject, $message, $data = array()) {
		CI::$APP->load->library ( 'email' );
		CI::$APP->email->initialize ( array (
				'protocol' => 'smtp',
				'smtp_host' => 'mail.nuocsuoitinhkhiet.com',
				'smtp_user' => 'smartwebvn@nuocsuoitinhkhiet.com',
				'smtp_pass' => '@123456',
				'mailtype' => 'html' 
		) );
		CI::$APP->email->from ( empty ( $from_email ) ? 'administrator@smartwebvn.com' : $from_email, $from_name );
		CI::$APP->email->to ( $to );
		
		CI::$APP->email->subject ( stringparse ( $data, $subject ) );
		CI::$APP->email->message ( stringparse ( $data, $message ) );
		if (! CI::$APP->email->send ()) {
			CI::$APP->email->clear ();
			return false;
		}
		CI::$APP->email->clear ();
		return true;
	}
}
if(!function_exists('phpmailergmail'))
{
	function phpmailergmail($to, $subject, $body, $admin='') {
		require_once('phpmailer/class.phpmailer.php');
		//Khoi tao doi tuong
		$mail = new PHPMailer();

		/*=====================================
		 * THIET LAP THONG TIN GUI MAIL
		*=====================================*/

		$mail->IsSMTP(); // Gọi đến class xử lý SMTP
		$mail->Host       = "smtp.gmail.com"; // tên SMTP server
		//$mail->SMTPDebug  = 2; // enables SMTP debug information (for testing)
		// 1 = errors and messages
		// 2 = messages only
		$mail->SMTPAuth   = true;        // Sử dụng đăng nhập vào account
		$mail->SMTPSecure = "ssl";
		$mail->Host       = "smtp.gmail.com";         // Thiết lập thông tin của SMPT
		$mail->Port       = 465;                    // Thiết lập cổng gửi email của máy
		$mail->Username   = "no.reply.thanksyou@gmail.com";         // SMTP account username
		$mail->Password   = "8935006800347";                // SMTP account password

		//Thiet lap thong tin nguoi gui va email nguoi gui
		$mail->SetFrom('no.reply.thanksyou@gmail.com',mysiteurl());

		//Thiet lap thong tin nguoi nhan
		if(!empty($admin))
		$mail->AddAddress($admin, mysiteurl());//goi cho admin
		$mail->AddAddress($to, mysiteurl());// goi cho kh

		//Thiet lap email nhan email hoi dap
		//Neu nguoi dung nhan nut reply
		//$mail->AddReplyTo("demo@zend.vn","Pham Vu Khanh");

		/*=====================================
		 * THIET LAP NOI DUNG EMAIL
		*=====================================*/

		//Thiet Lap Tieu De
		$mail->Subject    = $subject;

		//Thiet Lap Font Chu
		$mail->CharSet = "utf-8";

		//Thiết lập nội dung chính của email
		$mail->WordWrap = 50;
		$mail->IsHTML('text/html');
		$mail->Body = $body;

		if(!$mail->Send()) {
			return false;
		} else {
			return true;
		}
	}
}
if (! function_exists ( 'mycss' )) {
	function mycss($file) {
		echo "<link rel='stylesheet' type='text/css' href='" . $file . "' />  ";
	}
}
if (! function_exists ( 'myscript' )) {
	function myscript($filejs) {
		echo "<script type='text/javascript' src='" . $filejs . "'></script>";
	}
}

if (! function_exists ( 'myencode' ) && ! function_exists ( 'mydecode' )) {
	function myencode($str) {
		CI::$APP->load->libraries ( array (
				'encrypt' 
		) );
		CI::$APP->encrypt->set_cipher ( MCRYPT_BLOWFISH );
		$list = array (
				'/\//' => ':121:',
				'/\+/' => ':122:',
				'/\-/' => ':123:',
				'/\=/' => ':124:' 
		);
		return preg_replace ( array_keys ( $list ), array_values ( $list ), CI::$APP->encrypt->encode ( $str ) );
	}
	function mydecode($str) {
		CI::$APP->load->libraries ( array (
				'encrypt' 
		) );
		CI::$APP->encrypt->set_cipher ( MCRYPT_BLOWFISH );
		$list = array (
				'/\:121\:/' => '/',
				'/\:122\:/' => '+',
				'/\:123\:/' => '-',
				'/\:124\:/' => '=' 
		);
		return CI::$APP->encrypt->decode ( preg_replace ( array_keys ( $list ), array_values ( $list ), $str ) );
	}
}

if (! function_exists ( 'myvalue' )) {
	function myvalue() {
		$values = func_get_args ();
		if (is_array ( $values ))
			foreach ( $values as $value )
				if (! empty ( $value ))
					return $value;
	}
}