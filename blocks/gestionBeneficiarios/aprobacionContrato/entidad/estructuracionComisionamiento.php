<?php
namespace gestionBeneficiarios\aprobacionContrato\entidad;

class comisionamientoOP {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $esteRecursoDB;
    public $infoDocumento;
    public $prefijo;
    public $actualizarContrato;
    public $actualizarServicio;
    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

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
        $variable .= "&metodo=crearPaqueteTrabajo";

        $arreglo['proyecto'] = "1";
        $arreglo['nombre'] = "A Tarea programadas";
        $arreglo['porcentaje_avance'] = "90";
        $arreglo['descripcion'] = "Descripción Tarea";
        $arreglo['tipo'] = "2";
        $arreglo['estado'] = "1";
        $arreglo['prioridad'] = "8";
        $arreglo['paquete_trabajo_padre'] = "467";
        $arreglo['camposPersonalizados'] = array(
            "customField14" => array(
                'value' => 'No Iniciado',
                'tipo' => 'string_objects',
            ),

        );

        $variable .= "&variables=" . base64_encode(json_encode($arreglo));

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        $resultado_registro = file_get_contents($urlApi);

        $variable = json_decode($resultado_registro, true);
        var_dump($variable);exit;

        echo "estruturar Comisionamiento";
        var_dump($_REQUEST);exit;

        /**
         * 1. Actualizar Contrato
         **/

        $this->actualizarContrato();

        /**
         * 2. Actualizar Servicio
         **/

        $this->actualizarServicio();

        /**
         * 3. Redireccionar
         **/

        if ($this->actualizarContrato && $this->actualizarServicio) {
            Redireccionador::redireccionar('actualizoContrato');
        } else {
            Redireccionador::redireccionar('noActualizo');
        }
    }

    public function actualizarServicio() {

        if ($this->actualizarContrato) {
            $cadenaSql = $this->miSql->getCadenaSql('consultarEstadoInstalarAgendar');
            $id_estadoServicio = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            $id_estadoServicio = $id_estadoServicio[0];

            $cadenaSql = $this->miSql->getCadenaSql('actualizarServicio', $id_estadoServicio);
            $this->actualizarServicio = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

        }

    }

}

$miProcesador = new comisionamientoOP($this->lenguaje, $this->miSql);

?>

