<?php

require_once("../Rest.inc.php");

class API extends REST {
	
	public $data = "";
	
	public function __construct() {
		parent::__construct();    // Init parent contructor
	}
	/*
	 * Public method for access api.
	 * This method dynmically call the method based on the query string
	 *
	 */
	public function processApi() {
		$func = strtolower(trim(str_replace("/", "", $_REQUEST['rquest'])));
		if ((int) method_exists($this, $func) > 0)
			$this->$func();
			else
				$this->response('', 404);    // If the method not exist with in this class, response would be "Page not found".
	}
	
	private function json($data) {
		if (is_array($data)) {
			return json_encode($data);
		}
	}
	
	function genrarUrl() {
		
		$ds = '/';
		$api = 'api';
		$host = $_REQUEST['host'];
		$recurso = $_REQUEST['recurso'];
		$elemento = $_REQUEST['elemento'];
		$campo = $_REQUEST['campo'];
		
		$url = $host . $ds . $api . $ds . $recurso . $ds . $elemento. $ds . 'fields=["' . $campo . '"]';
		
		return $url;
	}
	
	function executarUrl(){
		
		$url = genrarUrl();
		
		$ch = curl_init();
		
		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		// grab URL and pass it to the browser
		curl_exec($ch);
		
		// close cURL resource, and free up system resources
		curl_close($ch);
		
	}
	
}

?>