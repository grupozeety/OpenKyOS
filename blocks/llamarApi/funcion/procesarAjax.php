<?php
require_once 'consultar.php';
class Procesador {
	public $consultar;
	public $servidor = "localhost";
	public $usuario_db = "serviciosweb";
	public $pwd_db = "serviciosZeety";
	public $nombre_db = "sitios";
	public $conn = '';
	public $error = array ();
	public $datosConexionERPNext = array (
			'host' => 'http://52.90.113.106:8080',
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
	public $datosConexionOpenProject = array (
			'host' => 'http://172.16.146.157:3000/',
			'token' => '8225f991ac98b96d27270d083c8a65e83ca4d7e4',
			'api_url_v2' => 'api/v2/',
			'api_url_v3' => 'api/v3/',
			'type' => 'json',
			'curl_timeout' => 30,
			'puerto' => '',
			'database' => '' 
	);
	public $producto = array (
			array (
					
					"qty" => '',
					"item_code" => '' 
			) 
	);
	public $orden = array (
			"delivery_date" => '',
			"customer" => '',
			"company" => '' 
	);
	public function __construct() {
		$this->consultar = new Consultar ();
		$this->procesar ();
	}
	public function procesar() {
		if (isset ( $_REQUEST ['metodo'] )) {
			switch ($_REQUEST ['metodo']) {
				
				case 'almacenes' :
					$resultado = $this->consultar->obtenerAlmacen ( $this->datosConexionERPNext );
					break;
				
				case 'proyectos' :
					$resultado = $this->consultar->obtenerProjectos ( $this->datosConexionOpenProject );
					break;
				case 'urbanizaciones' :
					$resultado = $this->consultar->obtenerUrbanizaciones ( $this->datosConexionOpenProject );
					break;
				case 'actividades' :
					$resultado = $this->consultar->obtenerActividades ( $this->datosConexionOpenProject, $_REQUEST ['proyecto'] );
					break;
				case 'ordenTrabajo' :
					$resultado = $this->consultar->obtenerOrdenTrabajo ( $this->datosConexionERPNext, $_REQUEST ['nombre'] );
					break;
				
				case 'obtenerMateriales' :
					$resultado = $this->consultar->obtenerMaterialesOrden ( $this->datosConexionERPNext, $_REQUEST ['nombre'] );
					break;
				
				case 'obtenerDetalleOrden' :
					$resultado = $this->consultar->obtenerDetalleOrden ( $this->datosConexionERPNext, $_REQUEST ['nombre'] );
					break;
				case 'obtenerProjectosSalida' :
					$resultado = $this->consultar->obtenerProjectosSalida ( $this->datosConexionERPNext );
					break;
				
				case 'obtenerIdentificadoresSalida' :
					$resultado = $this->consultar->obtenerIdentificadoresSalida ( $this->datosConexionERPNext, $_REQUEST ['proyecto'] );
					break;
				
				case 'obtenerProyecto' :
					$resultado = $this->consultar->obtenerProyectoErp ( $this->datosConexionERPNext );
					break;
				
				case 'ordenTrabajoModificada' :
					$resultado = $this->consultar->obtenerOrdenTrabajoModificada ( $this->datosConexionERPNext, $_REQUEST ['nombre'] );
					break;
				
				case 'obtenerMaterialesModificado' :
					$resultado = $this->consultar->obtenerMaterialesOrdenModificado ( $this->datosConexionERPNext, $_REQUEST ['nombre'] );
					break;
				
				case 'proyectosGeneral' :
					$resultado = $this->consultar->obtenerProjectosGeneral ( $this->datosConexionOpenProject );
					break;
				
				case 'proyectosDetalle' :
					$resultado = $this->consultar->obtenerDetalleProjecto ( $this->datosConexionOpenProject, $_REQUEST ['id_proyecto'] );
					break;
				
				case 'paquetesTrabajo' :
					$resultado = $this->consultar->obtenerPaquetesTrabajo ( $this->datosConexionOpenProject, $_REQUEST ['id_proyecto'] );
					break;
				
				case 'detalleActividadesPaquetesTrabajo' :
					$resultado = $this->consultar->obtenerActividadesPaquetesTrabajo ( $this->datosConexionOpenProject, $_REQUEST ['id_paquete_trabajo'] );
					break;
				
				case 'crearPaqueteTrabajo' :
					$variables = json_decode ( base64_decode ( $_REQUEST ['variables'] ), true );
					$resultado = $this->consultar->crearPaqueteTrabajo ( $this->datosConexionOpenProject, $variables );
					break;
				
				case 'crearCarpeta' :
					$variables = json_decode ( base64_decode ( $_REQUEST ['variables'] ), true );
					$resultado = $this->consultar->crearCarpeta ( $this->datosConexionAlfresco, $variables );
					break;
				
				case 'crearCliente' :
					$resultado = $this->consultar->crearCliente ( $this->datosConexionERPNext, $_REQUEST ['variables'] );
					break;
				
				case 'crearSolicitud' :
					$resultado = $this->consultar->crearMaterialRequest ( $this->datosConexionERPNext, $_REQUEST ['variables'] );
					break;
				
				case 'consultarOrdenTrabajo' :
					$resultado = $this->consultar->consultarOrdenTrabajo ( $this->datosConexionERPNext, $_REQUEST ['variables'] );
					break;
				
				case 'consultarKit' :
					$resultado = $this->consultar->consultarKit ( $this->datosConexionERPNext );
					break;
			}
		}
	}
}

$api = new Procesador ();
