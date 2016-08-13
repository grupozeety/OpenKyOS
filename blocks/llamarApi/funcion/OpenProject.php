<?php

class OpenProject{
	
	private $_curl_timeout = 30;
	private $_limit_page_length = 20;
	public $errorno = 0;
	public $error;
	public $body;
	public $header;
	
	function search(){
		
		try {
			
			$url = 'http://54.197.17.207:3000/api/v2/projects.json?key=515907a8d1990c75daacf6d36aff7e94482f13fc';
			
			$ch = curl_init($url);
			
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			
			curl_setopt ( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt ( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
			curl_setopt ( $ch, CURLOPT_TIMEOUT, $this->_curl_timeout );
			$response = curl_exec ( $ch );
			
			$this->header = curl_getinfo ( $ch );
			$error_no = curl_errno ( $ch );
			$error = curl_error ( $ch );
			curl_close ( $ch );
			if ($error_no) {
				$this->error_no = $error_no;
			}
			if ($error) {
				$this->error = $error;
			}
			$this->body = @json_decode ( $response, true );
			
			if (JSON_ERROR_NONE != json_last_error ()) {
				$this->body = $response;
			}
			
			return $this;
		
		} catch(Exception $e) {
		
			trigger_error(sprintf(
					'Curl failed with error #%d: %s',
					$e->getCode(), $e->getMessage()),
					E_USER_ERROR);
		
		}
	}
}

?>