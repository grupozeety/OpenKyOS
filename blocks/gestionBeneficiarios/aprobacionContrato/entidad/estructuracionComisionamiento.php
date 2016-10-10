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
        exit;

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

        /**
         * Nombre Hogar
         **/

        $this->nombreHogar = "Hogar" . time();

        /**
         * Clasificación Proyecto
         **/

        $validacion = strpos($this->proyecto['identifier'], 'wman');

        if (is_numeric($validacion)) {

            $this->estruturarWMAN();

        } else {

            $this->estruturarHFC();

        }

    }

    public function estruturarWMAN() {

        /**
         * Identificar Paquete Trabajo Padre
         **/

        foreach ($this->proyecto['paquetesTrabajo'] as $key => $value) {

            //Infraestructura red de acceso

            if ($value['subject'] === 'Instalación de hogares') {

                $paqueteInstalacionHogares = $value;

            }

            //Comisionamiento

            if ($value['subject'] === 'Comisionamiento') {

                $paqueteComisionamiento = $value;

            }

        }

        /**
         * Paquetes Infraestructura Red de acceso
         **/

        {

            //Crear Hogar
            $variableHogar = $this->crearPaqueteTrabajo($this->nombreHogar, $paqueteInstalacionHogares['id']);

            //Acometida a hogares
            $variableAcHg = $this->crearPaqueteTrabajo("Acometida a hogares", $this->obtenerIdentificadorPaqueteTrabajo($variableHogar));

            //Instalación, configuración, entrega CPE y portátil
            $variableInsCPE = $this->crearPaqueteTrabajo("Instalación, configuración, entrega CPE y portátil", $this->obtenerIdentificadorPaqueteTrabajo($variableHogar));

            //Documentación de aceptación y acta de entrega
            $variableDocAcep = $this->crearPaqueteTrabajo("Documentación de aceptación y acta de entrega", $this->obtenerIdentificadorPaqueteTrabajo($variableHogar));

            {

                //Acta de recibo a satisfacción del servicio
                $variableActRecSer = $this->crearPaqueteTrabajo("Acta de recibo a satisfacción del servicio", $this->obtenerIdentificadorPaqueteTrabajo($variableDocAcep));

                //Acta de recibo a satisfacción por los beneficiarios de los computadores
                $variableActRecSat = $this->crearPaqueteTrabajo("Acta de recibo a satisfacción por los beneficiarios de los computadores", $this->obtenerIdentificadorPaqueteTrabajo($variableDocAcep));

                //Formato de inventario de equipos instalados
                $variableForInvEqui = $this->crearPaqueteTrabajo("Formato de inventario de equipos instalados", $this->obtenerIdentificadorPaqueteTrabajo($variableDocAcep));
            }

            //Documentación interventoría
            $variableDocInter = $this->crearPaqueteTrabajo("Documentación interventoría", $this->obtenerIdentificadorPaqueteTrabajo($variableHogar));

            {

                //Revisión pruebas interventoría
                $variableRevPrInter = $this->crearPaqueteTrabajo("Documentación interventoría", $this->obtenerIdentificadorPaqueteTrabajo($variableDocInter));

                {

                    //Protocolos de prueba de aceptación equipos activos
                    $variablePrtPrEquAct = $this->crearPaqueteTrabajo("Protocolos de prueba de aceptación equipos activos", $this->obtenerIdentificadorPaqueteTrabajo($variableRevPrInter));

                }

                //Entrega de documentación
                $variableEntrDocm = $this->crearPaqueteTrabajo("Entrega de documentación", $this->obtenerIdentificadorPaqueteTrabajo($variableDocInter));

                {

                    //Formato de protocolos de prueba de aceptación equipos activos
                    $variableFormProcEquiAct = $this->crearPaqueteTrabajo("Formato de protocolos de prueba de aceptación equipos activos", $this->obtenerIdentificadorPaqueteTrabajo($variableEntrDocm));

                    //Formato de inventario de equipos instalados
                    $variableFormInvEquIns = $this->crearPaqueteTrabajo("Formato de inventario de equipos instalados", $this->obtenerIdentificadorPaqueteTrabajo($variableEntrDocm));

                    //Acta de entrega final
                    $variableActFinal = $this->crearPaqueteTrabajo("Acta de entrega final", $this->obtenerIdentificadorPaqueteTrabajo($variableEntrDocm));
                }

            }

        }

        /**
         * Paquetes Comisionamiento
         **/

        {

            //Crear Hogar
            $variableHogar = $this->crearPaqueteTrabajo($this->nombreHogar, $paqueteComisionamiento['id']);

            //Verificación de la viabilidad social y comercial

            $variableVerViaSocComr = $this->crearPaqueteTrabajo('Verificación de la viabilidad social y comercial', $this->obtenerIdentificadorPaqueteTrabajo($variableHogar));

            {

                //Ajuste de verificación detallada hogares VIP
                $variableVerDtllVip = $this->crearPaqueteTrabajo('Ajuste de verificación detallada hogares VIP', $this->obtenerIdentificadorPaqueteTrabajo($variableVerViaSocComr));

                //Socialización del proyecto a beneficiarios
                $variableSociProBenf = $this->crearPaqueteTrabajo('Socialización del proyecto a beneficiarios', $this->obtenerIdentificadorPaqueteTrabajo($variableVerViaSocComr));

                //Validación y certificación de idoneidad social y comercial
                $variableValCertIdo = $this->crearPaqueteTrabajo('Validación y certificación de idoneidad social y comercial', $this->obtenerIdentificadorPaqueteTrabajo($variableVerViaSocComr));

                //Aceptación de las condiciones del servicio y firma de documentos pre-contractuales y contrato
                $variableAcptConContr = $this->crearPaqueteTrabajo('Aceptación de las condiciones del servicio y firma de documentos pre-contractuales y contrato', $this->obtenerIdentificadorPaqueteTrabajo($variableVerViaSocComr));
            }

        }

        var_dump($paqueteInstalacionHogares);
        var_dump($paqueteComisionamiento);exit;

    }

    public function estruturarHFC() {

        /**
         * Identificar Paquete Trabajo Padre
         **/

        foreach ($this->proyecto['paquetesTrabajo'] as $key => $value) {

            if ($value['subject'] === 'Comisionamiento') {

                $paqueteComisionamiento = $value;

            }

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

        $UrlProyecto = $this->crearUrlDetalleProyectos($Info_Beneficiario_Contrato[0]['id_proyecto']);

        $proyecto = file_get_contents($UrlProyecto);

        $this->proyecto = json_decode($proyecto, true);

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
    public function crearPaqueteTrabajo($nombre_paquete = '', $id_paquete_padre = '') {

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
        $arreglo['descripcion'] = $nombre_paquete;
        $arreglo['tipo'] = "2";
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

