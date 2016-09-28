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

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->proyectos = json_decode(base64_decode($_REQUEST['info_proyectos']), true);

        foreach ($this->proyectos as $key => $value) {
            $proyectos[] = $value;
        }

        $this->proyectos = $proyectos;

        $this->proyectos_general = $this->proyectos;

        $_REQUEST['tiempo'] = time();

        /**
         * 1. Filtrar Proyectos a Reportear
         **/
        $this->filtrarProyectos();

        /**
         * 2. Obtener Paquetes de Trabajo
         **/
        $this->obtenerPaquetesTrabajo();

        /**
         * 4. Obtener Actividades Paquetes de Trabajo
         **/
        $this->obtenerActividades();

        /**
         * 5. Filtrar Actividades Paquetes de Trabajo
         **/
        $this->filtrarActividades();

        /**
         * 6. Crear Documento Hoja de Calculo(Reporte)
         **/

        var_dump($this->proyectos[2]['paquetesTrabajo']);exit;
        var_dump($this->proyectos);exit;
        $this->crearHojaCalculo();

    }

    public function crearHojaCalculo() {
        include_once "crearDocumentoHojaCalculo.php";

    }
    public function filtrarActividades() {

        foreach ($this->proyectos as $key => $value) {

            foreach ($value['paquetesTrabajo'] as $llave => $valor) {

                if ($valor['type_id'] == 2) {

                    foreach ($valor['actividades'] as $llave2 => $actividad) {

                        if ($actividad['_type'] != 'Activity::Comment') {
                            unset($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][$llave2]);

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

                    //var_dump($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades']);exit();
                }

            }

        }

    }
    public function obtenerActividades() {

        foreach ($this->proyectos as $key => $value) {

            foreach ($value['paquetesTrabajo'] as $llave => $valor) {

                //Avance y  estado instalación NOC

                if ($valor['subject'] === 'Mesa de ayuda') {

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

                        unset($this->proyectos[$key]['paquetesTrabajo'][$clave]);
                    }

                }

                if ($valor['subject'] === 'Centro de gestión') {

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

                        unset($this->proyectos[$key]['paquetesTrabajo'][$clave]);
                    }

                }

                if ($valor['subject'] === 'Otros equipos o sistemas en el NOC') {

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

                        unset($this->proyectos[$key]['paquetesTrabajo'][$clave]);

                    }

                }

                if ($valor['subject'] === 'Infraestructura Nodos') {

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

                        unset($this->proyectos[$key]['paquetesTrabajo'][$clave]);

                    }

                }

                if ($valor['subject'] === 'Instalación red troncal o interconexión ISP') {

                    var_dump($valor);
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

                        if ($clave) {

                        }

                        var_dump($clave);

                        foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                            $array_ordenado_paquete_trabajo[] = $val;

                        }
                        $variable = $array_ordenado_paquete_trabajo[$clave];

                        var_dump($variable);

                        exit();

                    }

                }

                /*
            $urlActividades = $this->crearUrlActividades($valor['id']);

            $actividades = file_get_contents($urlActividades);

            $actividad = json_decode($actividades, true);

            $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'] = $actividad;
             */

            }

        }

        var_dump($this->proyectos[1]['paquetesTrabajo']);exit;

        //var_dump($this->proyectos[0]['paquetesTrabajo'][2]['actividades']);exit;

    }

    public function obtenerHijosPaquetesTrabajo($var = '') {

        $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

        if ($clave) {

        }

        var_dump($clave);

        foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

            $array_ordenado_paquete_trabajo[] = $val;

        }
        $variable = $array_ordenado_paquete_trabajo[$clave];

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

            $urlPaquetes = $this->crearUrlPaquetesTrabajo($value['id']);

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

        foreach ($this->proyectos as $key => $value) {

            $this->proyectos[$key]['name'] = str_replace('?', ' ', utf8_decode($value['name']));

        }

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

        }

    }

}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>

