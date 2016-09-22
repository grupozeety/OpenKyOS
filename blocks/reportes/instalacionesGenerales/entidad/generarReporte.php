<?php
namespace reportes\instalacionesGenerales\entidad;

class GenerarReporteInstalaciones {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->proyectos = json_decode(base64_decode($_REQUEST['info_proyectos']), true);

        $_REQUEST['tiempo'] = time();

        /**
         * 1. Filtrar Proyectos a Reportear
         **/
        $this->filtrarProyectos();

        /**
         * 2. Obtener Paquetes de Trabajo
         **/
        $this->obtenerPaquetesTrabajo();

        exit;

    }

    public function obtenerPaquetesTrabajo() {

        foreach ($this->proyectos as $key => $value) {

            $urlPaquetes = $this->crearUrlPaquetesTrabajo($value['id']);

            $paquetesTrabajo = file_get_contents($urlPaquetes);

            $paquetesTrabajo = json_decode($paquetesTrabajo, true);

            $this->proyectos[$key]['paquetesTrabajo'] = $paquetesTrabajo;

        }
        var_dump($this->proyectos);

    }

    public function crearUrlPaquetesTrabajo($var = '') {

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
        $variable .= "&metodo=paquetesTrabajo";
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }
    public function filtrarProyectos() {

        $cantidadProyectos = count($this->proyectos);

        for ($i = 1; $i < $cantidadProyectos; $i++) {

            if (isset($_REQUEST['item' . $i])) {

                $ident_proyectos[] = $_REQUEST['item' . $i];

            }

        }

        if (isset($ident_proyectos)) {

            foreach ($this->proyectos as $key => $value) {

                foreach ($ident_proyectos as $valor) {

                    if ($value['id'] == $valor) {

                        $proyectos[] = $value;

                    }

                }

            }

            $this->proyectos = $proyectos;

        }

    }
    public function procesarFormulario() {

        //Aquí va la lógica de procesamiento

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

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>

