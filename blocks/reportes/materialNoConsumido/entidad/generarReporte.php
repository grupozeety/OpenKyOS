<?php
namespace reportes\materialNoConsumido\entidad;
include_once 'Redireccionador.php';

class FormProcessor {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;

    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;

    }

    public function procesarFormulario() {

        $_REQUEST['tiempo'] = time();

        var_dump($_REQUEST);

        var_dump(base64_decode($_REQUEST['elementos']));

        exit;

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        //Obtener Identificadores de salida de acuerdo al proyecto

        $urlObtenerIdSalida = $urlApi . "&metodo=obtenerIdentificadoresSalida";
        $urlObtenerIdSalida .= "&proyecto=" . $_REQUEST['proyecto'];
        echo $urlObtenerIdSalida;

        $context = stream_context_create(['http' => ['max_redirects' => 0, 'ignore_errors' => true]]);

        $opts = array('http' => array(
            'method' => 'GET',
            'max_redirects' => 1,
        ),
        );

        $context = stream_context_create($opts);

        $idSalida = file_get_contents($urlObtenerIdSalida, false, $context);

        var_dump($idSalida);

        exit;
/*
$ch = curl_init($urlObtenerIdSalida);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_TIMEOUT, $this->_curl_timeout);
$response = curl_exec($ch);

$this->header = curl_getinfo($ch);
$error_no = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);

var_dump($response);*/
        //$idSalida = file_get_contents($content);

        $context = array(
            'http' => array('max_redirects' => 99),
        );
        $context = stream_context_create($context);
// hand over the context to fopen()
        $fp = fopen($urlObtenerIdSalida, 'r', false, $context);

        var_dump($fp);
        exit;

        $context = stream_context_create(
            array(
                'http' => array(
                    'follow_location' => false,
                ),
            )
        );

        exit();
        //Al final se ejecuta la redirección la cual pasará el control a otra página
        $variable = 'cualquierDato';
        Redireccionador::redireccionar('opcion1', $variable);

    }

    public function resetForm() {
        foreach ($_REQUEST as $clave => $valor) {

            if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
                unset($_REQUEST[$clave]);
            }
        }
    }

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);

$resultado = $miProcesador->procesarFormulario();

?>

