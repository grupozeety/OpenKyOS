<?php
namespace reportes\instalacionesGenerales\entidad;

class GenerarReporteInstalaciones {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;
    public $proyectos_general;
    public $informacion_proyecto_core;

    //_______________________________
    public $esteRecursoDB;
    public $esteRecursoOP;
    public $esteRecursoAD;
    public $info_proyectos;
    //________________________________
    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

//         $conexion = "openproject";
//         $this->esteRecursoOP = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexion = "almacendatos";
        $this->esteRecursoAD = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        /**
         * -1. Consultar Parametrización
         **/
//         $this->consultarParametrizacion();

        /**
         * 0. Estrucurar Desatelles Proyecto
         **/
        // $this->estruturarProyectos();

        /**
         * 1. Filtrar Proyectos a Reportear
         **/
        //$this->filtrarProyectos();

        /**
         * 2. Filtrar Actividades Paquetes de Trabajo
         **/
        // $this->detallarCamposPersonalizadosProyecto();

        /**
         * 3. Obtener Paquetes de Trabajo
         **/
//         $this->obtenerPaquetesTrabajo();

        /**
         * 4. Obtener Actividades Paquetes de Trabajo
         **/
//         $this->obtenerActividades();

        /**
         * 5. Filtrar Actividades Paquetes de Trabajo
         **/
        //$this->filtrarActividades();

        /**
         * XX. Consultar Informacion(Reporte)
         **/

        $this->consultarInformacion();

        /**
         * 6. Crear Documento Hoja de Calculo(Reporte)
         **/

        $this->crearHojaCalculo();

    }

    public function consultarInformacion() {

        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionReporte');
        $this->info_proyectos = $this->esteRecursoAD->ejecutarAcceso($cadenaSql, "busqueda");

    }

    public function consultarParametrizacion() {

        $cadenaSql = $this->miSql->getCadenaSql('consultarProyectosParametrizados');
        $proyectos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $this->proyectos = $proyectos;

        foreach ($this->proyectos as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarCamposParametrizados', $value['id_proyecto']);
            $campos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            $this->proyectos[$key]['campos_parametrizados'] = $campos;

        }

        $this->obtenerDetalleProyectos();

    }

    public function obtenerDetalleProyectos() {

        foreach ($this->proyectos as $key => $value) {

            $urlDetalle = $this->crearUrlDetalleProyectos($value['id_proyecto']);

            $detalle = file_get_contents($urlDetalle);

            $detalle = json_decode($detalle, true);

            $this->proyectos[$key]["info"] = $detalle;
            //echo base64_encode(json_encode($detalle)) . "<br>";
        }

//        var_dump($this->proyectos);exit;

    }

    public function estruturarProyectos() {

        $this->proyectos = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', base64_decode($_REQUEST['info_proyectos'])), true);

        foreach ($this->proyectos as $key => $value) {
            $proyectos[] = $value;
        }

        $this->proyectos = $proyectos;

        $this->proyectos_general = $this->proyectos;

    }

    public function crearUrlDetalleProyectos($var = '') {

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
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }

    public function crearHojaCalculo() {
        include_once "crearDocumentoHojaCalculo.php";

    }

    public function detallarCamposPersonalizadosProyecto() {

        foreach ($this->proyectos as $key => $value) {

            $urlPaquetes = $this->crearUrlDetalleProyecto($value['id']);

            $detalleProyecto = file_get_contents($urlPaquetes);

            $detalleProyecto = json_decode($detalleProyecto, true);

            $this->proyectos[$key]['campos_personalizados'] = $detalleProyecto['custom_fields'];

        }
    }

    public function crearUrlDetalleProyecto($var = '') {

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
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }
    public function filtrarActividades() {

        foreach ($this->proyectos as $key => $value) {

            foreach ($value['paquetesTrabajo'] as $llave => $valor) {

                if (isset($valor['actividades'])) {
                    if ($valor['type_id'] == 2) {

                        foreach ($valor['actividades'] as $llave2 => $actividad) {

                            if ($actividad['_type'] != 'Activity::Comment') {
                                unset($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][$llave2]);

                            } else {

                                $val = (strpos($actividad['comment']['raw'], 'automáticamente cambiando'));

                                if (is_numeric($val)) {

                                    unset($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][$llave2]);
                                }

                            }

                            $fecha_actividad = substr($actividad['createdAt'], 0, 10);
                            $fecha_actividad = strtotime($fecha_actividad);
                            $fecha_inicio = strtotime($_REQUEST['fecha_inicio']);
                            $fecha_final = strtotime($_REQUEST['fecha_final']);

                            if ($fecha_actividad < $fecha_inicio) {
                                unset($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][$llave2]);
                            }

                            if ($fecha_actividad > $fecha_final) {
                                unset($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][$llave2]);
                            }

                        }

                    }

                }

            }

        }

    }

    public function obtenerProyectoCore() {

        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionCore');
        $info_core = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $info_core = $info_core[0];

        $cadenaSql = $this->miSql->getCadenaSql('consultarCamposParametrizados', $info_core['id_proyecto']);
        $campos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $info_core['campos_parametrizados'] = $campos;

        $urlDetalle = $this->crearUrlDetalleProyectos($info_core['id_proyecto']);

        $detalle = file_get_contents($urlDetalle);

        $detalle = json_decode($detalle, true);

        $info_core['info'] = $detalle;

        $urlPaquetes = $this->crearUrlPaquetesTrabajo($info_core['id_proyecto']);

        $paquetesTrabajo = file_get_contents($urlPaquetes);

        $paquetesTrabajo = json_decode($paquetesTrabajo, true);

        $info_core['paquetesTrabajo'] = $paquetesTrabajo;

        $this->informacion_proyecto_core = $info_core;

    }

    public function obtenerActividades() {

        $this->obtenerProyectoCore();

        //var_dump($this->informacion_proyecto_core);exit;

        foreach ($this->informacion_proyecto_core['campos_parametrizados'] as $key => $valor) {

            if ($valor['sub_tipo'] === 'Mesa  de Ayuda') {

                $urlActividades = $this->crearUrlActividades($valor['valor_actividad']);

                $actividades = file_get_contents($urlActividades);

                $actividad = json_decode($actividades, true);
                var_dump($actividad);exit;

                foreach ($actividad as $avance) {

                    $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                }

                foreach ($valor['child_ids'] as $llave_a => $contenido) {

                    $urlActividades = $this->crearUrlActividades($contenido);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                    //unset($this->proyectos[$key]['paquetesTrabajo'][$clave]);
                }

            }

        }

        foreach ($this->proyectos as $key => $value) {

            foreach ($value['paquetesTrabajo'] as $llave => $valor) {

                //Avance y  estado instalación NOC

                if ($valor['subject'] === 'Infraestructura nodos') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }
                    foreach ($valor['child_ids'] as $llave_a => $contenido) {

                        $urlActividades = $this->crearUrlActividades($contenido);

                        $actividades = file_get_contents($urlActividades);

                        $actividad = json_decode($actividades, true);

                        foreach ($actividad as $avance) {

                            $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                        }

                        $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                        // unset($this->proyectos[$key]['paquetesTrabajo'][$clave]);

                    }

                }

                if ($valor['subject'] === 'Instalación red troncal o interconexión ISP') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }
                    foreach ($valor['child_ids'] as $llave_a => $contenido) {

                        $urlActividades = $this->crearUrlActividades($contenido);

                        $actividades = file_get_contents($urlActividades);

                        $actividad = json_decode($actividades, true);

                        foreach ($actividad as $avance) {

                            $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                        }

                        $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                        foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                            $array_ordenado_paquete_trabajo[] = $val;

                        }

                        $variable = $this->proyectos[$key]['paquetesTrabajo'][$clave];

                        if (!empty($variable['child_ids'])) {
                            $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                        }

                    }

                }

                if ($valor['description'] === 'Instalación y puesta en funcionamiento equipos cabecera') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }
                    foreach ($valor['child_ids'] as $llave_a => $contenido) {

                        $urlActividades = $this->crearUrlActividades($contenido);

                        $actividades = file_get_contents($urlActividades);

                        $actividad = json_decode($actividades, true);

                        foreach ($actividad as $avance) {

                            $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                        }

                        $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                        foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                            $array_ordenado_paquete_trabajo[] = $val;

                        }
                        $variable = $array_ordenado_paquete_trabajo[$clave];
                        if (!empty($variable['child_ids'])) {
                            $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                        }

                    }

                }

                if ($valor['subject'] === 'Estado construcción red de distribución') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    if (!empty($valor['child_ids'])) {

                        foreach ($valor['child_ids'] as $llave_a => $contenido) {

                            $urlActividades = $this->crearUrlActividades($contenido);

                            $actividades = file_get_contents($urlActividades);

                            $actividad = json_decode($actividades, true);

                            foreach ($actividad as $avance) {

                                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                            }

                            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                                $array_ordenado_paquete_trabajo[] = $val;

                            }
                            $variable = $this->proyectos[$key]['paquetesTrabajo'][$clave];
                            if (!empty($variable['child_ids'])) {
                                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                            }

                        }

                    }

                }

                if ($valor['subject'] === 'Tendido y puesta en funcionamiento fibra óptica') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    if (!empty($valor['child_ids'])) {

                        foreach ($valor['child_ids'] as $llave_a => $contenido) {

                            $urlActividades = $this->crearUrlActividades($contenido);

                            $actividades = file_get_contents($urlActividades);

                            $actividad = json_decode($actividades, true);

                            foreach ($actividad as $avance) {

                                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                            }

                            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                                $array_ordenado_paquete_trabajo[] = $val;

                            }
                            $variable = $array_ordenado_paquete_trabajo[$clave];
                            if (!empty($variable['child_ids'])) {
                                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                            }

                        }

                    }

                }

                if ($valor['description'] === 'Infraestructura nodo (Avance y estado instalación nodo EOC)') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    if (!empty($valor['child_ids'])) {

                        foreach ($valor['child_ids'] as $llave_a => $contenido) {

                            $urlActividades = $this->crearUrlActividades($contenido);

                            $actividades = file_get_contents($urlActividades);

                            $actividad = json_decode($actividades, true);

                            foreach ($actividad as $avance) {

                                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                            }

                            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                                $array_ordenado_paquete_trabajo[] = $val;

                            }
                            $variable = $array_ordenado_paquete_trabajo[$clave];

                            if (!empty($variable['child_ids'])) {
                                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                            }

                        }

                    }

                }

                if ($valor['description'] === 'Instalación y puesta en funcionamiento equipos (Avance y estado instalación nodo EOC)') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    if (!empty($valor['child_ids'])) {

                        foreach ($valor['child_ids'] as $llave_a => $contenido) {

                            $urlActividades = $this->crearUrlActividades($contenido);

                            $actividades = file_get_contents($urlActividades);

                            $actividad = json_decode($actividades, true);

                            foreach ($actividad as $avance) {

                                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                            }

                            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                                $array_ordenado_paquete_trabajo[] = $val;

                            }
                            $variable = $array_ordenado_paquete_trabajo[$clave];

                            if (!empty($variable['child_ids'])) {
                                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                            }

                        }

                    }

                }

                if ($valor['description'] === 'Infraestructura nodo (Avance y estado instalación nodo inalámbrico)') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    if (!empty($valor['child_ids'])) {

                        foreach ($valor['child_ids'] as $llave_a => $contenido) {

                            $urlActividades = $this->crearUrlActividades($contenido);

                            $actividades = file_get_contents($urlActividades);

                            $actividad = json_decode($actividades, true);

                            foreach ($actividad as $avance) {

                                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                            }

                            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                                $array_ordenado_paquete_trabajo[] = $val;

                            }
                            //$variable = $array_ordenado_paquete_trabajo[$clave];

                            $variable = $this->proyectos[$key]['paquetesTrabajo'][$clave];

                            if (!empty($variable['child_ids'])) {

                                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                            }

                        }

                    }

                }
/*
if ($valor['description'] === 'Instalación y puesta en funcionamiento equipos (Avance y estado instalación nodo inalámbrico)') {

$urlActividades = $this->crearUrlActividades($valor['id']);

$actividades = file_get_contents($urlActividades);

$actividad = json_decode($actividades, true);

foreach ($actividad as $avance) {

$this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
}

if (!empty($valor['child_ids'])) {

foreach ($valor['child_ids'] as $llave_a => $contenido) {

$urlActividades = $this->crearUrlActividades($contenido);

$actividades = file_get_contents($urlActividades);

$actividad = json_decode($actividades, true);

foreach ($actividad as $avance) {

$this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
}

$clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

$array_ordenado_paquete_trabajo[] = $val;

}
$variable = $array_ordenado_paquete_trabajo[$clave];

if (!empty($variable['child_ids'])) {
$this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

}

}

}

}*/

                if ($valor['subject'] === 'Tendido y puesta en funcionamiento red coaxial') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    if (!empty($valor['child_ids'])) {

                        foreach ($valor['child_ids'] as $llave_a => $contenido) {

                            $urlActividades = $this->crearUrlActividades($contenido);

                            $actividades = file_get_contents($urlActividades);

                            $actividad = json_decode($actividades, true);

                            foreach ($actividad as $avance) {

                                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                            }

                            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                                $array_ordenado_paquete_trabajo[] = $val;

                            }
                            $variable = $array_ordenado_paquete_trabajo[$clave];

                            if (!empty($variable['child_ids'])) {
                                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                            }

                        }

                    }

                }

            }

        }

    }

    public function obtenerHijosPaquetesTrabajo($contenido = '', $key = '', $llave = '', $variable = '') {

        foreach ($variable['child_ids'] as $llave_a => $contenido) {

            $urlActividades = $this->crearUrlActividades($contenido);

            $actividades = file_get_contents($urlActividades);

            $actividad = json_decode($actividades, true);

            foreach ($actividad as $avance) {

                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
            }

            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                $array_ordenado_paquete_trabajo[] = $val;

            }

            $variable = $array_ordenado_paquete_trabajo[$clave];

            if (!empty($variable['child_ids'])) {
                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

            }

        }

    }

    public function crearUrlActividades($var = '') {

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
        $variable .= "&metodo=detalleActividadesPaquetesTrabajo";
        $variable .= "&id_paquete_trabajo=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }

    public function obtenerPaquetesTrabajo() {

        foreach ($this->proyectos as $key => $value) {

            $urlPaquetes = $this->crearUrlPaquetesTrabajo($value['id_proyecto']);

            $paquetesTrabajo = file_get_contents($urlPaquetes);

            $paquetesTrabajo = json_decode($paquetesTrabajo, true);

            $this->proyectos[$key]['paquetesTrabajo'] = $paquetesTrabajo;

        }

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
/*
foreach ($this->proyectos as $key => $value) {

$this->proyectos[$key]['name'] = str_replace('?', ' ', utf8_decode($value['name']));

}

$cantidadProyectos = count($this->proyectos);

for ($i = 1; $i < $cantidadProyectos; $i++) {

if (isset($_REQUEST['item' . $i])) {

$ident_proyectos[] = $_REQUEST['item' . $i];

}

}

$this->obtenerDetalleProyectos();

if (isset($ident_proyectos)) {

foreach ($this->proyectos as $key => $value) {

foreach ($ident_proyectos as $valor) {

if ($value['id'] == $valor) {

$proyectos[] = $value;

$llave = array_search($value['custom_fields'][3]['value'], array_column($this->proyectos, 'name'), true);

if (!is_bool($llave)) {
$proyectos[] = $this->proyectos[$llave];
}

$llave = array_search('ins', array_column($this->proyectos, 'identifier'), true);

if (!is_bool($llave)) {
$proyectos[] = $this->proyectos[$llave];
}

}

}

}

$this->proyectos = $proyectos;

}*/

    }

}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>

