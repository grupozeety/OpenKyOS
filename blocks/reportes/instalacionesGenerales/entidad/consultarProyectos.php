<?php
namespace reportes\instalacionesGenerales\entidad;

class FormProcessor {

    public $miConfigurador;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $urlApiProyectos;
    public $proyectos;

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        /**
         * 1. Construcción Url Api Proyectos
         **/
        $this->crearUrlProyectos();

        /**
         * 2. Obtener Proyectos de Api
         **/

        $this->obtenerProyectos();

        /**
         * 2. Construcción Url Api Detalle Proyectos
         **/

        $this->obtenerDetalleProyectos();

        exit;

    }

    public function obtenerDetalleProyectos() {

        foreach ($this->proyectos as $key => $value) {

        }

    }

    public function crearUrlDetalleProyectos($variable = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=proyectosDetalle";
        $variable .= "&id_proyecto=" . $variable;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }
    public function obtenerProyectos() {
        $proyectos = file_get_contents($this->urlApiProyectos);

        $this->proyectos = json_decode($proyectos, true);
    }

    public function crearUrlProyectos() {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=proyectosGeneral";

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $this->urlApiProyectos = $url . $cadena;

    }

    public function resetForm() {
        foreach ($_REQUEST as $clave => $valor) {

            if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
                unset($_REQUEST[$clave]);
            }
        }
    }

}

$miProcesador = new FormProcessor($this->sql);

?>

