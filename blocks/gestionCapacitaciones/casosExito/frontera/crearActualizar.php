<?php
namespace gestionCapacitaciones\casosExito\frontera;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}
/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se declaran algunas funciones js
 * que lo complementan.
 */
class Periodos
{
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $ruta;
    public $rutaURL;
    public function __construct($lenguaje, $formulario, $sql)
    {

        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->miSql = $sql;

        $esteBloque = $this->miConfigurador->configuracion['esteBloque'];

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        if (!isset($esteBloque["grupo"]) || $esteBloque["grupo"] == "") {
            $ruta .= "/blocks/" . $esteBloque["nombre"] . "/";
            $this->rutaURL .= "/blocks/" . $esteBloque["nombre"] . "/";
        } else {
            $this->ruta .= "/blocks/" . $esteBloque["grupo"] . "/" . $esteBloque["nombre"] . "/";
            $this->rutaURL .= "/blocks/" . $esteBloque["grupo"] . "/" . $esteBloque["nombre"] . "/";
        }

        $this->gestionPeriodos();
    }
    public function gestionPeriodos()
    {

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        //Conexion a Base de Datos OtunWs
        $conexion = "otunWs";
        $esteRecursoDBOtunWS = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        if (isset($_REQUEST['opcion']) && $_REQUEST['opcion'] == 'actualizarCompetencia') {
            $cadenaSql = $this->miSql->getCadenaSql('consultarPeriodoParticular');
            $Periodo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
            if ($Periodo && !is_null($Periodo)) {
                $arrayName = array(
                    'unidad' => $Periodo['tipo_unidad'],
                    'valor' => $Periodo['valor'],
                );
                $_REQUEST = array_merge($_REQUEST, $arrayName);
            }
        }

        if (isset($_REQUEST['idActividad']) && $_REQUEST['idActividad'] != '') {

            $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionCapacitacion', $_REQUEST['idActividad']);

            $capacitacion = $esteRecursoDBOtunWS->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if ($capacitacion) {

                $arreglo_capacitacion = array(
                    'actividad' => $capacitacion['actividad'],
                    'identificadorActividad' => $capacitacion['id_actividad'],
                    'fechaCapacitacion' => $capacitacion['fecha_capacitacion'],
                    'horas' => $capacitacion['horas_capacitacion'],
                    'servicio' => $capacitacion['servicio_capacitacion'],
                    'detalleServicio' => $capacitacion['detalle_servicio'],

                );

                $_REQUEST = array_merge($_REQUEST, $arreglo_capacitacion);

            }

        }

        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------

        $atributosGlobales['campoSeguro'] = 'true';

        $_REQUEST['tiempo'] = time();
        // -------------------------------------------------------------------------------------------------

        // ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
        $esteCampo = $esteBloque['nombre'];
        $atributos['id'] = $esteCampo;
        $atributos['nombre'] = $esteCampo;
        // Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
        $atributos['tipoFormulario'] = 'multipart/form-data';
        // Si no se coloca, entonces toma el valor predeterminado 'POST'
        $atributos['metodo'] = 'POST';
        // Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
        $atributos['action'] = 'index.php';
        $atributos['titulo'] = $this->lenguaje->getCadena($esteCampo);
        // Si no se coloca, entonces toma el valor predeterminado.
        $atributos['estilo'] = '';
        $atributos['marco'] = true;
        $tab = 1;
        // ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------

        // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
        $atributos['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->formulario($atributos);

        $esteCampo = 'Agrupacion';
        $atributos['id'] = $esteCampo;

        if (isset($_REQUEST['opcion']) && $_REQUEST['opcion'] == 'actualizarCompetencia') {
            $atributos['leyenda'] = "<b>Actualización Casos de Éxito</b>";
        } else {
            $atributos['leyenda'] = "<b>Registro Casos de Éxito</b>";
        }
        echo $this->miFormulario->agrupacion('inicio', $atributos);
        unset($atributos);

        $esteCampo = 'AgrupacionGeneral';
        $atributos['id'] = $esteCampo;
        $atributos['leyenda'] = "Información con Respecto al Caso de Éxito";
        echo $this->miFormulario->agrupacion('inicio', $atributos);
        unset($atributos);
        {

            $esteCampo = 'titulo';
            $atributos['id'] = $esteCampo;
            $atributos['nombre'] = $esteCampo;
            $atributos['tipo'] = "text";
            $atributos['minimo'] = 0;
            $atributos['decimal'] = false;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            $atributos['anchoEtiqueta'] = 2;
            $atributos['estilo'] = "bootstrap";
            $atributos['evento'] = '';
            $atributos['deshabilitado'] = false;
            $atributos['readonly'] = false;
            $atributos['columnas'] = 1;
            $atributos['tamanno'] = 1;
            $atributos['placeholder'] = "Ingrese titulo del caso de éxito";
            if (isset($_REQUEST[$esteCampo])) {
                $atributos['valor'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['valor'] = "";
            }
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['validar'] = 'required';
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
            unset($atributos);

            $esteCampo = 'etiqueta';
            $atributos['id'] = $esteCampo;
            $atributos['nombre'] = $esteCampo;
            $atributos['tipo'] = "text";
            $atributos['minimo'] = 0;
            $atributos['decimal'] = false;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            $atributos['anchoEtiqueta'] = 2;
            $atributos['estilo'] = "bootstrap";
            $atributos['evento'] = '';
            $atributos['deshabilitado'] = false;
            $atributos['readonly'] = false;
            $atributos['columnas'] = 1;
            $atributos['tamanno'] = 1;
            $atributos['placeholder'] = "Ingrese etiqueta hashtag (#)";
            if (isset($_REQUEST[$esteCampo])) {
                $atributos['valor'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['valor'] = "";
            }
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['validar'] = 'required';
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
            unset($atributos);

            $esteCampo = "resumen";
            $atributos['nombre'] = $esteCampo;
            $atributos['id'] = $esteCampo;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            if (isset($_REQUEST[$esteCampo])) {
                $atributos['valor'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['valor'] = '';
            }
            $atributos['validar'] = 'required';
            $atributos['placeholder'] = "Ingrese resumen del caso";
            $atributos['filas'] = 2;
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoTextAreaBootstrap($atributos);
            unset($atributos);

            $esteCampo = "testimonio";
            $atributos['nombre'] = $esteCampo;
            $atributos['id'] = $esteCampo;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            if (isset($_REQUEST[$esteCampo])) {
                $atributos['valor'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['valor'] = '';
            }
            $atributos['validar'] = 'required';
            $atributos['placeholder'] = "Ingrese testimonio del caso";
            $atributos['filas'] = 2;
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoTextAreaBootstrap($atributos);
            unset($atributos);

            $esteCampo = "contexto";
            $atributos['nombre'] = $esteCampo;
            $atributos['id'] = $esteCampo;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            if (isset($_REQUEST[$esteCampo])) {
                $atributos['valor'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['valor'] = '';
            }
            $atributos['validar'] = 'required';
            $atributos['placeholder'] = "Ingrese contexto del caso";
            $atributos['filas'] = 2;
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoTextAreaBootstrap($atributos);
            unset($atributos);

            $esteCampo = 'imagen1';
            $atributos['id'] = $esteCampo;
            $atributos['nombre'] = $esteCampo;
            $atributos['tipo'] = "url";
            $atributos['minimo'] = 0;
            $atributos['decimal'] = false;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            $atributos['anchoEtiqueta'] = 2;
            $atributos['estilo'] = "bootstrap";
            $atributos['evento'] = '';
            $atributos['deshabilitado'] = false;
            $atributos['readonly'] = false;
            $atributos['columnas'] = 1;
            $atributos['tamanno'] = 1;
            $atributos['placeholder'] = "Path absoluto de donde se encuentra publicada la imagen 1";
            if (isset($_REQUEST[$esteCampo])) {
                $atributos['valor'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['valor'] = "";
            }
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['validar'] = 'required';
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
            unset($atributos);

            $esteCampo = 'imagen2';
            $atributos['id'] = $esteCampo;
            $atributos['nombre'] = $esteCampo;
            $atributos['tipo'] = "url";
            $atributos['minimo'] = 0;
            $atributos['decimal'] = false;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            $atributos['anchoEtiqueta'] = 2;
            $atributos['estilo'] = "bootstrap";
            $atributos['evento'] = '';
            $atributos['deshabilitado'] = false;
            $atributos['readonly'] = false;
            $atributos['columnas'] = 1;
            $atributos['tamanno'] = 1;
            $atributos['placeholder'] = "Path absoluto de donde se encuentra publicada la imagen 2";
            if (isset($_REQUEST[$esteCampo])) {
                $atributos['valor'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['valor'] = "";
            }
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['validar'] = 'required';
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
            unset($atributos);

            $esteCampo = 'imagen3';
            $atributos['id'] = $esteCampo;
            $atributos['nombre'] = $esteCampo;
            $atributos['tipo'] = "url";
            $atributos['minimo'] = 0;
            $atributos['decimal'] = false;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            $atributos['anchoEtiqueta'] = 2;
            $atributos['estilo'] = "bootstrap";
            $atributos['evento'] = '';
            $atributos['deshabilitado'] = false;
            $atributos['readonly'] = false;
            $atributos['columnas'] = 1;
            $atributos['tamanno'] = 1;
            $atributos['placeholder'] = "Path absoluto de donde se encuentra publicada la imagen 3";
            if (isset($_REQUEST[$esteCampo])) {
                $atributos['valor'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['valor'] = "";
            }
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['validar'] = 'required';
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
            unset($atributos);

            $esteCampo = "codigo";
            $atributos['nombre'] = $esteCampo;
            $atributos['id'] = $esteCampo;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            if (isset($_REQUEST[$esteCampo])) {
                $atributos['valor'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['valor'] = '';
            }
            $atributos['validar'] = 'required';
            $atributos['placeholder'] = "Ingrese código enbebido multimedia";
            $atributos['filas'] = 2;
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoTextAreaBootstrap($atributos);
            unset($atributos);

            $esteCampo = 'categoriaAprendizaje';
            $atributos['nombre'] = $esteCampo;
            $atributos['id'] = $esteCampo;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            $atributos['anchoEtiqueta'] = 2;
            $atributos['evento'] = '';

            if (isset($_REQUEST[$esteCampo])) {
                $atributos['seleccion'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['seleccion'] = '1';
            }
            $atributos['deshabilitado'] = false;
            $atributos['columnas'] = 1;
            $atributos['tamanno'] = 1;
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['estilo'] = "bootstrap";
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['validar'] = 'required';
            $atributos['cadena_sql'] = 'required';
            $cadenaSql = $this->miSql->getCadenaSql('consultarCategoriaAprendizaje');
            $matrizItems = $esteRecursoDBOtunWS->ejecutarAcceso($cadenaSql, "busqueda");
            $atributos['matrizItems'] = $matrizItems;
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
            unset($atributos);

            $esteCampo = 'categoriaApropiacion';
            $atributos['nombre'] = $esteCampo;
            $atributos['id'] = $esteCampo;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            $atributos['anchoEtiqueta'] = 2;
            $atributos['evento'] = '';

            if (isset($_REQUEST[$esteCampo])) {
                $atributos['seleccion'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['seleccion'] = '1';
            }
            $atributos['deshabilitado'] = false;
            $atributos['columnas'] = 1;
            $atributos['tamanno'] = 1;
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['estilo'] = "bootstrap";
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['validar'] = 'required';
            $atributos['cadena_sql'] = 'required';
            $cadenaSql = $this->miSql->getCadenaSql('consultarCategoriaApropiacion');
            $matrizItems = $esteRecursoDBOtunWS->ejecutarAcceso($cadenaSql, "busqueda");
            $atributos['matrizItems'] = $matrizItems;
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
            unset($atributo);

            $esteCampo = 'relacionPlan';
            $atributos['nombre'] = $esteCampo;
            $atributos['id'] = $esteCampo;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            $atributos['anchoEtiqueta'] = 2;
            $atributos['evento'] = '';

            if (isset($_REQUEST[$esteCampo])) {
                $atributos['seleccion'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['seleccion'] = '-1';
            }
            $atributos['deshabilitado'] = false;
            $atributos['columnas'] = 1;
            $atributos['tamanno'] = 4;
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['estilo'] = "bootstrap";
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['miEvento'] = '';
            $atributos['multiple'] = true;
            $atributos['cadena_sql'] = 'required';
            $cadenaSql = $this->miSql->getCadenaSql('consultarRelacionPlan');
            $matrizItems = $esteRecursoDBOtunWS->ejecutarAcceso($cadenaSql, "busqueda");
            $atributos['matrizItems'] = $matrizItems;
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
            unset($atributo);

        }

        echo $this->miFormulario->agrupacion('fin');
        unset($atributos);

        $esteCampo = 'AgrupacionGeneral';
        $atributos['id'] = $esteCampo;
        $atributos['leyenda'] = "Información Beneficiario";
        echo $this->miFormulario->agrupacion('inicio', $atributos);
        unset($atributos);
        {

            // ----------------INICIO CONTROL: Lista Proyectos---------------------------

            $esteCampo = 'beneficiario';
            $atributos['nombre'] = $esteCampo;
            $atributos['tipo'] = "text";
            $atributos['id'] = $esteCampo;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            $atributos['anchoEtiqueta'] = 2;
            $atributos['estilo'] = "bootstrap";
            $atributos['evento'] = '';
            $atributos['deshabilitado'] = false;
            $atributos['readonly'] = false;
            $atributos['columnas'] = 1;
            $atributos['tamanno'] = 1;
            $atributos['placeholder'] = "Ingrese Mínimo 3 Caracteres de Busqueda";
            $atributos['valor'] = "";
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['validar'] = 'required';
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
            unset($atributos);

            $esteCampo = 'id_beneficiario';
            $atributos["id"] = $esteCampo; // No cambiar este nombre
            $atributos["tipo"] = "hidden";
            $atributos['estilo'] = '';
            $atributos["obligatorio"] = false;
            $atributos['marco'] = true;
            $atributos["etiqueta"] = "";
            if (isset($_REQUEST[$esteCampo])) {
                $atributos['valor'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['valor'] = 'NO ASIGNADO';
            }
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroTexto($atributos);
            unset($atributos);

            $esteCampo = 'departamento';
            $atributos['nombre'] = $esteCampo;
            $atributos['id'] = $esteCampo;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            $atributos['anchoEtiqueta'] = 2;
            $atributos['evento'] = '';

            if (isset($_REQUEST[$esteCampo])) {
                $atributos['seleccion'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['seleccion'] = '1';
            }
            $atributos['deshabilitado'] = false;
            $atributos['columnas'] = 1;
            $atributos['tamanno'] = 1;
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['estilo'] = "bootstrap";
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['validar'] = 'required';
            $atributos['cadena_sql'] = 'required';
            $cadenaSql = $this->miSql->getCadenaSql('consultaDepartamento');
            $matrizItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
            $atributos['matrizItems'] = $matrizItems;
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
            unset($atributos);

            $esteCampo = 'municipio';
            $atributos['nombre'] = $esteCampo;
            $atributos['id'] = $esteCampo;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            $atributos['anchoEtiqueta'] = 2;
            $atributos['evento'] = '';

            if (isset($_REQUEST[$esteCampo])) {
                $atributos['seleccion'] = $_REQUEST[$esteCampo];
            } else {
                $atributos['seleccion'] = '1';
            }
            $atributos['deshabilitado'] = false;
            $atributos['columnas'] = 1;
            $atributos['tamanno'] = 1;
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['estilo'] = "bootstrap";
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['validar'] = 'required';
            $atributos['cadena_sql'] = 'required';
            $cadenaSql = $this->miSql->getCadenaSql('consultaMunicipio');
            $matrizItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
            $atributos['matrizItems'] = $matrizItems;
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
            unset($atributos);

        }

        echo $this->miFormulario->agrupacion('fin');
        unset($atributos);

        $esteCampo = 'AgrupacionGeneral';
        $atributos['id'] = $esteCampo;
        $atributos['leyenda'] = "Información Coordinador y Administrador";
        echo $this->miFormulario->agrupacion('inicio', $atributos);
        unset($atributos);
        {

            $esteCampo = 'AgrupacionGeneral';
            $atributos['id'] = $esteCampo;
            $atributos['leyenda'] = "Información Administrador";
            echo $this->miFormulario->agrupacion('inicio', $atributos);
            unset($atributos);
            {

                $esteCampo = 'cedulaAdmin';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "number";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese cedula administrador/gestor";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'nombreAdmin';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "text";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese nombre administrador/gestor";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'telefonoAdmin';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "number";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese teléfono administrador/gestor";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'celularAdmin';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "number";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese celular administrador/gestor";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'emailAdmin';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "email";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese email administrador/gestor";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'perfilAdmin';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "url";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Url perfil de facebook administrador/gestor";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'emailPerfilAdmin';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "email";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese email perfil facebook administrador/gestor";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

            }
            echo $this->miFormulario->agrupacion('fin');
            unset($atributos);

            $esteCampo = 'AgrupacionGeneral';
            $atributos['id'] = $esteCampo;
            $atributos['leyenda'] = "Información Coordinador";
            echo $this->miFormulario->agrupacion('inicio', $atributos);
            unset($atributos);
            {
                $esteCampo = 'cedulaCoord';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "number";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese cedula coordinador";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'nombreCoord';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "text";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese nombre coordinador";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'telefonoCoord';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "number";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese teléfono coordinador";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'celularCoord';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "number";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese celular coordinador";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'emailCoord';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "email";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese email coordinador";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'perfilCoord';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "url";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Url perfil de facebook coordinador";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

                $esteCampo = 'emailPerfilCoord';
                $atributos['id'] = $esteCampo;
                $atributos['nombre'] = $esteCampo;
                $atributos['tipo'] = "email";
                $atributos['minimo'] = 0;
                $atributos['decimal'] = false;
                $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                $atributos["etiquetaObligatorio"] = true;
                $atributos['tab'] = $tab++;
                $atributos['anchoEtiqueta'] = 2;
                $atributos['estilo'] = "bootstrap";
                $atributos['evento'] = '';
                $atributos['deshabilitado'] = false;
                $atributos['readonly'] = false;
                $atributos['columnas'] = 1;
                $atributos['tamanno'] = 1;
                $atributos['placeholder'] = "Ingrese email perfil facebook coordinador";
                if (isset($_REQUEST[$esteCampo])) {
                    $atributos['valor'] = $_REQUEST[$esteCampo];
                } else {
                    $atributos['valor'] = "";
                }
                $atributos['ajax_function'] = "";
                $atributos['ajax_control'] = $esteCampo;
                $atributos['limitar'] = false;
                $atributos['anchoCaja'] = 10;
                $atributos['miEvento'] = '';
                $atributos['validar'] = 'required';
                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                unset($atributos);

            }
            echo $this->miFormulario->agrupacion('fin');
            unset($atributos);

        }
        echo $this->miFormulario->agrupacion('fin');
        unset($atributos);

        // ------------------Division para los botones-------------------------
        $atributos["id"] = "botones";
        $atributos["estilo"] = "marcoBotones";
        $atributos["estiloEnLinea"] = "";
        echo $this->miFormulario->division("inicio", $atributos);
        unset($atributos);

        // -----------------CONTROL: Botón ----------------------------------------------------------------
        $esteCampo = 'botonGuardar'
        ;
        $atributos["id"] = $esteCampo;
        $atributos["tabIndex"] = $tab;
        $atributos["tipo"] = 'boton';
        // submit: no se coloca si se desea un tipo button genérico
        $atributos['submit'] = true;
        $atributos["simple"] = true;
        $atributos["estiloMarco"] = '';
        $atributos["estiloBoton"] = 'default';
        $atributos["block"] = false;
        // verificar: true para verificar el formulario antes de pasarlo al servidor.
        $atributos["verificar"] = '';
        $atributos["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
        $atributos["valor"] = $this->lenguaje->getCadena($esteCampo);
        $atributos['nombreFormulario'] = $esteBloque['nombre'];
        $tab++;

        // Aplica atributos globales al control
        $atributos = array_merge($atributos, $atributosGlobales);
        echo $this->miFormulario->campoBotonBootstrapHtml($atributos);
        unset($atributos);
        // -----------------FIN CONTROL: Botón -----------------------------------------------------------

        // ------------------Fin Division para los botones-------------------------
        echo $this->miFormulario->division("fin");
        unset($atributos);

        echo $this->miFormulario->agrupacion('fin');
        unset($atributos);
        /**
         * En algunas ocasiones es útil pasar variables entre las diferentes páginas.
         * SARA permite realizar esto a través de tres
         * mecanismos:
         * (a). Registrando las variables como variables de sesión. Estarán disponibles durante toda la sesión de usuario. Requiere acceso a
         * la base de datos.
         * (b). Incluirlas de manera codificada como campos de los formularios. Para ello se utiliza un campo especial denominado
         * formsara, cuyo valor será una cadena codificada que contiene las variables.
         * (c) a través de campos ocultos en los formularios. (deprecated)
         **/

        // En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:
        // Paso 1: crear el listado de variables

        $valorCodificado = "action=" . $esteBloque["nombre"];
        $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
        $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
        $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
        if (isset($_REQUEST['opcion']) && $_REQUEST['opcion'] == 'actualizarCompetencia') {
            $valorCodificado .= "&opcion=actualizarCasoExito";
            $valorCodificado .= "&id_caso=" . $_REQUEST['id_caso'];
        } else {
            $valorCodificado .= "&opcion=registrarCasoExito";
        }

        /**
         * SARA permite que los nombres de los campos sean dinámicos.
         * Para ello utiliza la hora en que es creado el formulario para
         * codificar el nombre de cada campo.
         **/
        $valorCodificado .= "&campoSeguro=" . $_REQUEST['tiempo'];
        // Paso 2: codificar la cadena resultante
        $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar($valorCodificado);

        $atributos["id"] = "formSaraData"; // No cambiar este nombre
        $atributos["tipo"] = "hidden";
        $atributos['estilo'] = '';
        $atributos["obligatorio"] = false;
        $atributos['marco'] = true;
        $atributos["etiqueta"] = "";
        $atributos["valor"] = $valorCodificado;
        echo $this->miFormulario->campoCuadroTexto($atributos);
        unset($atributos);
        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos['marco'] = true;
        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);

        if (isset($_REQUEST['mensaje'])) {
            $this->mensajeModal();
        }
    }

    public function mensajeModal()
    {

        switch ($_REQUEST['mensaje']) {
            case 'exitoRegistro':
                $mensaje = "Exito<br><b>Registro Caso de Éxito</b>";
                $atributos['estiloLinea'] = 'success'; //success,error,information,warning
                break;

            case 'errorRegistro':
                $mensaje = "Error<br><b>Registro Caso de Éxito</b>";
                $atributos['estiloLinea'] = 'error'; //success,error,information,warning
                break;

            case 'errorValidacionBeneficiario':
                $mensaje = "Error<br><b>Beneficiario no valido</b>";
                $atributos['estiloLinea'] = 'error'; //success,error,information,warning
                break;

            //-------------------------------------------------
            case 'exitoActualizacion':
                $mensaje = "Exito<br>Periodo Actualizado";
                $atributos['estiloLinea'] = 'success'; //success,error,information,warning
                break;

            case 'errorActualizacion':
                $mensaje = "Error<br>Actualización del Periodo";
                $atributos['estiloLinea'] = 'error'; //success,error,information,warning
                break;

            case 'exitoEliminar':
                $mensaje = "Exito<br>Periodo Eliminado";
                $atributos['estiloLinea'] = 'success'; //success,error,information,warning
                break;

            case 'errorEliminar':
                $mensaje = "Error<br>Eliminar Periodo";
                $atributos['estiloLinea'] = 'error'; //success,error,information,warning
                break;
        }

        // ----------------INICIO CONTROL: Ventana Modal Beneficiario Eliminado---------------------------------

        $atributos['tipoEtiqueta'] = 'inicio';
        $atributos['titulo'] = 'Mensaje';
        $atributos['id'] = 'mensajeModal';
        echo $this->miFormulario->modal($atributos);
        unset($atributos);

        // ----------------INICIO CONTROL: Mapa--------------------------------------------------------
        echo '<div style="text-align:center;">';

        echo '<p><h5>' . $mensaje . '</h5></p>';

        echo '</div>';

        // ----------------FIN CONTROL: Mapa--------------------------------------------------------

        echo '<div style="text-align:center;">';

        echo '</div>';

        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->modal($atributos);
        unset($atributos);
    }

}

$miSeleccionador = new Periodos($this->lenguaje, $this->miFormulario, $this->sql);
