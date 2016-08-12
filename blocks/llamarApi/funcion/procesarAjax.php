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
	var $datosConexion = array (
			'host' => 'http://52.90.66.82',
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
			$resultado = $this->consultar->obtenerAlmacen( $this->datosConexion );
		}else if(isset($_REQUEST['metodo']) && $_REQUEST['metodo']=="almacenes"){
			$resultado = $this->consultar->obtenerAlmacen( $this->datosConexion );
		}
	}
}

$api = new Procesador();