<?php
require_once ('registrador.php');
class ProcesadorVenta {
	var $registrador;
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
					'usr' => 'aqui',
					'pwd' => 'abc123' 
			),
			'curl_timeout' => 30,
			'basic_auth' => array (),
			'puerto' => '',
			'database' => '',
			'cookie_file' => '/var/www/html/cookie.txt' 
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
		$this->registrador = new RegistradorVenta ();
	}

	function procesarVenta($entrada) {
		
		$totalProcesadas = 0;
		$totalEmpresas = 0;
		$empresasProcesadas = array ();
		
		foreach ( $entrada as $llave => $empresa ) {
			
			
			if (isset ( $empresa ['empresa'] ) && isset ( $empresa ['producto'] ) && isset ( $empresa ['cliente'] ) && isset ( $empresa ['datosEnvio'] ) && isset ( $empresa ['orden'] )) {
				$resultado = $this->procesarEmpresa ( $empresa ['empresa'] );
				
				
				if ($resultado) {
					$resultado = $this->procesarProducto ( $empresa ['producto'] );
				}
				
				if ($resultado) {
					$resultado = $this->procesarCliente ( $empresa ['cliente'] );
				}
				
				if ($resultado) {
					$resultado = $this->procesarDatosEnvio ( $empresa ['datosEnvio'] );
				}
				
				if ($resultado) {
					$resultado = $this->procesarOrden ( $empresa ['orden'] );
				}
				
				if ($resultado) {
					
					$resultado = $this->registrador->registrarVenta ( $this->producto, $this->datosConexion, $this->orden );
					
					if (is_string ( $resultado )) {
						$this->error [] = $resultado;
					}
					
					$empresasProcesadas [] = $empresa ['empresa'];
					$totalProcesadas ++;
				}
			} else {
				$this->error [] = 'Entrada no válida';
			}
			$totalEmpresas ++;
		}
		
		
		
		if (count ( $this->error > 0 )) {
			$respuesta ['estado'] = 'ERROR';
			$respuesta ['errores'] = $this->error;
		} else {
			$respuesta ['estado'] = 'CORRECTO';
		}
		
		return $respuesta;
	}
	function procesarEmpresa($datos) {
		// Buscar los datos de conexión de la empresa
		$this->conectarDB ();
		
		$sql = "SELECT id_empresa, db, user, pass, sitepwd, nomb_empresa, port, estado ";
		$sql .= " FROM empresas  ";
		$sql .= " WHERE nomb_empresa like '%" . $datos . "%' ";
		
		$query = $this->conn->query ( $sql );
		
		if ($query) {
			$filasDB = $query->fetchAll ( PDO::FETCH_ASSOC );
			
			if (count ( $filasDB ) > 0) {
				$this->datosConexion ['puerto'] = $filasDB [0] ['port'];
				$this->datosConexion ['auth'] ['pwd'] = $filasDB [0] ['sitepwd'];
				$this->datosConexion ['auth'] ['database'] = $filasDB [0] ['db'];
				return true;
			}
		}
		
		$this->error [] = 'La empresa ' . $datos . ' no esta registrada';
		
		return false;
	}
}