<?php

require_once 'consultar.php';

class Procesador {

    public $consultar;
    public $servidor = "localhost";
    public $usuario_db = "serviciosweb";
    public $pwd_db = "serviciosZeety";
    public $nombre_db = "sitios";
    public $conn = '';
    public $error = array();
    public $datosConexionERPNext = array(
        'host' => '<url>',
        'auth_url' => '/api/method/login',
        'api_url' => '/api/resource/',
        'auth' => array(
            'usr' => 'Administrator',
            'pwd' => '<password>',
        ),
        'curl_timeout' => 30,
        'basic_auth' => array(),
        'puerto' => '',
        'database' => '',
        'cookie_file' => '/var/www/html/workspace/cookie.txt',
    );

    public $datosConexionOpenProject = array(
        'host' => '<url>',
        'token' => '<token>',
        'api_url' => 'api/v2/',
        'type' => 'json',
        'curl_timeout' => 30,
        'puerto' => '',
        'database' => '',
    );

    public $producto = array(
        array(

            "qty" => '',
            "item_code" => '',
        ),
    );
    public $orden = array(
        "delivery_date" => '',
        "customer" => '',
        "company" => '',
    );

    public function __construct() {
        $this->consultar = new Consultar();
        $this->procesar();

    }

    public function procesar() {
        if (isset($_REQUEST['metodo']) && $_REQUEST['metodo'] == "almacenes") {
            $resultado = $this->consultar->obtenerAlmacen($this->datosConexionERPNext);
        } else if (isset($_REQUEST['metodo']) && $_REQUEST['metodo'] == "proyectos") {
            $resultado = $this->consultar->obtenerProjectos($this->datosConexionOpenProject);
        } else if (isset($_REQUEST['metodo']) && $_REQUEST['metodo'] == "actividades" && isset($_REQUEST['proyecto'])) {
            $resultado = $this->consultar->obtenerActividades($this->datosConexionOpenProject, $_REQUEST['proyecto']);
        } else if (isset($_REQUEST['metodo']) && $_REQUEST['metodo'] == "ordenTrabajo") {
            $resultado = $this->consultar->obtenerOrdenTrabajo($this->datosConexionERPNext);
        } else if (isset($_REQUEST['metodo']) && $_REQUEST['metodo'] == "obtenerMateriales") {
            $resultado = $this->consultar->obtenerMaterialesOrden($this->datosConexionERPNext, $_REQUEST['nombre']);
        } else if (isset($_REQUEST['metodo']) && $_REQUEST['metodo'] == "obtenerDetalleOrden") {
            $resultado = $this->consultar->obtenerDetalleOrden($this->datosConexionERPNext, $_REQUEST['nombre']);
        } else if (isset($_REQUEST['metodo']) && $_REQUEST['metodo'] == "obtenerProjectosSalida") {
            $resultado = $this->consultar->obtenerProjectosSalida($this->datosConexionERPNext);
        }
    }
}

$api = new Procesador();