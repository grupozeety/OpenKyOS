<?php

require_once ('consultar.php');

class Procesador {
	
	var $consultar;
	var $servidor = "localhost";
	var $usuario_db = "serviciosweb";
	var $pwd_db = "serviciosZeety";
	var $nombre_db = "sitios";
	var $conn = '';
	var $error = array ();
	var $datosConexionERPNext = array (
			'host' => 'http://52.90.42.196',
			'auth_url' => '/api/method/login',
			'api_url' => '/api/resource/',
			'auth' => array (
					'usr' => 'Administrator',
					'pwd' => 'abc123' 
			),
			'curl_timeout' => 30,
			'basic_auth' => array (),
			'puerto' => '',
			'database' => '',
			'cookie_file' => '/var/www/html/workspace/cookie.txt' 
	);
	
	var $datosConexionOpenProject = array (
			'host' => 'http://54.197.17.207:3000/',
			'token' => '515907a8d1990c75daacf6d36aff7e94482f13fc',
			'api_url' => 'api/v2/',
			'type' => 'json',
			'curl_timeout' => 30,
			'puerto' => '',
			'database' => '',
	);
	
	var $producto = array (
			array (
					
					"qty" => '',
					"item_code" => '' 
			) 
	);
	var $orden = array (
			"delivery_date" => '',
			"customer" => '',
			"company" => '' 
	);
	
	function __construct() {
		$this->consultar = new Consultar ();
		$this->procesar();
		
	}

	function procesar() {
		if(isset($_REQUEST['metodo']) && $_REQUEST['metodo']=="almacenes"){
			$resultado = $this->consultar->obtenerAlmacen( $this->datosConexionERPNext );
		}else if(isset($_REQUEST['metodo']) && $_REQUEST['metodo']=="proyectos"){
			$resultado = $this->consultar->obtenerProjectos($this->datosConexionOpenProject);
		}else if(isset($_REQUEST['metodo']) && $_REQUEST['metodo']=="actividades"){
			$resultado = $this->consultar->obtenerActividades($this->datosConexionOpenProject);
		}
	}
}

$api = new Procesador();