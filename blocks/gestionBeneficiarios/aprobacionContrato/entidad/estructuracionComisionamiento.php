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
    public $proyecto;
    public $nombreHogar;
    public $Info_Beneficiario_Contrato;
    public $contrato;
    public $idActividadHogar;
    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        /**
         * 1. Consultar Proyecto Particular Beneficiario
         **/

        $this->consultarProyectoParticular();

        /**
         * 2. Clasificar Proyecto
         **/
        $this->clasificarProyecto();

    }

    public function clasificarProyecto() {

        /**
         * Paquetes de Trabajo Proyecto
         **/
        {
            $urlPaquetes = $this->crearUrlPaquetesTrabajo($this->proyecto['id']);

            $paquetesTrabajo = file_get_contents($urlPaquetes);

            $paquetesTrabajo = json_decode($paquetesTrabajo, true);

            $this->proyecto['paquetesTrabajo'] = $paquetesTrabajo;
        }

        $cadenaSql = $this->miSql->getCadenaSql('consultarContratoEspecifico');
        $contrato = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $this->contrato = $contrato[0];
        /**
         * Nombre Hogar
         **/

        $this->nombreHogar = $this->contrato['id_beneficiario'];

        /**
         * Clasificación Proyecto
         **/

        //$validacion = strpos($this->proyecto['identifier'], 'wman');

        $this->estruturarComisionamiento();

    }

    public function estruturarComisionamiento() {

        /**
         * Paquetes Comisionamiento
         **/

        {

            //Crear Hogar
            $variableHogar = $this->crearPaqueteTrabajo($this->Info_Beneficiario_Contrato[0]['nomenclatura'], $this->idActividadHogar, 2, "Comisionamiento para Beneficiario con Identificación: " . $this->contrato['identificacion_beneficiario']);

            /**
             *  Registro de Orden de  Trabajo en beneficiario
             **/

            $cadenaSql = $this->miSql->getCadenaSql("registrarOrdenTrabajo", array('identificador_beneficiario' => $this->Info_Beneficiario_Contrato[0]['identificador_beneficiario'], "id_orden" => $this->obtenerIdentificadorPaqueteTrabajo($variableHogar)));

            $registro_orden_trabajo = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

            $this->crearPaqueteTrabajo('Verificación de la viabilidad social y comercial', $this->obtenerIdentificadorPaqueteTrabajo($variableHogar));

            $this->crearPaqueteTrabajo('Instalación, Configuración en NOC, Entrega de CPE y Portatil', $this->obtenerIdentificadorPaqueteTrabajo($variableHogar));

            $this->crearPaqueteTrabajo('Digitalización y validación de información del Comisionamiento', $this->obtenerIdentificadorPaqueteTrabajo($variableHogar));

            $this->crearPaqueteTrabajo('Acta de recibo a Satisfacción del Servicio', $this->obtenerIdentificadorPaqueteTrabajo($variableHogar));

            $this->crearPaqueteTrabajo('Acta de recibo a satisfacción por los beneficiarios de los Computadores', $this->obtenerIdentificadorPaqueteTrabajo($variableHogar));

        }

    }

    public function obtenerIdentificadorPaqueteTrabajo($paquete_Trabajo = '') {

        $String = $paquete_Trabajo['self']['href'];

        $array = explode("/", $String);

        $resultado = end($array);

        return $resultado;

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
    public function consultarProyectoParticular() {

        $cadenaSql = $this->miSql->getCadenaSql('consultarContratoEspecifico');

        $Info_Beneficiario_Contrato = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $this->Info_Beneficiario_Contrato = $Info_Beneficiario_Contrato;

        $UrlProyecto = $this->crearUrlDetalleProyectos($Info_Beneficiario_Contrato[0]['id_proyecto']);

        $proyecto = file_get_contents($UrlProyecto);

        $this->proyecto = json_decode($proyecto, true);

        $cadenaSql = $this->miSql->getCadenaSql('ConsultarParametrizacionProyecto', $Info_Beneficiario_Contrato[0]['id_proyecto']);

        $idActividadHogar = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $this->idActividadHogar = $idActividadHogar[0]['valor_actividad'];

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
    public function crearPaqueteTrabajo($nombre_paquete = '', $id_paquete_padre = '', $tipo = 2, $descripcion = '') {

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

        $arreglo['proyecto'] = $this->proyecto['id'];
        $arreglo['nombre'] = $nombre_paquete;
        $arreglo['porcentaje_avance'] = "0";

        if ($descripcion != '') {
            $arreglo['descripcion'] = $descripcion;
        } else {
            $arreglo['descripcion'] = $nombre_paquete;
        }

        $arreglo['tipo'] = $tipo;
        $arreglo['estado'] = "1";
        $arreglo['prioridad'] = "8";
        $arreglo['paquete_trabajo_padre'] = $id_paquete_padre;
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

        return $variable;

    }

}

$miProcesador = new comisionamientoOP($this->lenguaje, $this->miSql);

?>

