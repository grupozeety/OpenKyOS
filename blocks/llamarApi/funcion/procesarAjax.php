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

        if (isset($_REQUEST['metodo'])) {
            switch ($_REQUEST['metodo']) {

                case 'almacenes':
                    $resultado = $this->consultar->obtenerAlmacen($this->datosConexionERPNext);
                    break;

                case 'proyectos':
                    $resultado = $this->consultar->obtenerProjectos($this->datosConexionOpenProject);
                    break;
                case 'actividades':
                    $resultado = $this->consultar->obtenerActividades($this->datosConexionOpenProject, $_REQUEST['proyecto']);
                    break;
                case 'ordenTrabajo':
                    $resultado = $this->consultar->obtenerOrdenTrabajo($this->datosConexionERPNext);
                    break;

                case 'obtenerMateriales':
                    $resultado = $this->consultar->obtenerMaterialesOrden($this->datosConexionERPNext, $_REQUEST['nombre']);
                    break;

                case 'obtenerDetalleOrden':
                    $resultado = $this->consultar->obtenerDetalleOrden($this->datosConexionERPNext, $_REQUEST['nombre']);
                    break;
                case 'obtenerProjectosSalida':
                    $resultado = $this->consultar->obtenerProjectosSalida($this->datosConexionERPNext);
                    break;

                case 'obtenerIdentificadoresSalida':
                    $resultado = $this->consultar->obtenerIdentificadoresSalida($this->datosConexionERPNext, $_REQUEST['proyecto']);
                    break;

            }
        }

    }

}

$api = new Procesador();