<?php

namespace reportes\actaEntregaPortatil\frontera;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}
class Certificado {
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $ruta;
    public $rutaURL;
    public function __construct($lenguaje, $formulario, $sql) {
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
    }
    public function edicionCertificado() {
        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificado');

        $infoCertificado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];


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
        $atributos['estilo'] = 'main';
        $atributos['marco'] = true;
        $tab = 1;
        // ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------

        echo "<div class='modalLoad'></div>";

        // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
        $atributos['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->formulario($atributos);
        {

            {
            	
            	$cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificado');
            	$infoCertificado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
            	 
            	$_REQUEST = array_merge($_REQUEST, $infoCertificado);
            	 

            	echo ' <div class="main">
      			<div class="row">
        			<div class="col-xs-14 col-lg-11">
          				<div class="panel panel-primary">
            				<div class="panel-body">
								<div class="row">';
            	
                $esteCampo = 'Agrupacion';
                $atributos['id'] = $esteCampo;
                $atributos['leyenda'] = "INFORMACIÓN DE COMPUTADOR PORTÁTIL";

                echo $this->miFormulario->agrupacion('inicio', $atributos);
                unset($atributos);

                // ------------------Division para los botones-------------------------
                $atributos["id"] = "espacio_trabajo";
                $atributos["estilo"] = " ";
                $atributos["estiloEnLinea"] = "";
                echo $this->miFormulario->division("inicio", $atributos);
                unset($atributos);

                        $esteCampo = 'serial';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = true;
                        $atributos['readonly'] = false;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'id_serial';
                        $atributos["id"] = $esteCampo; // No cambiar este nombre
                        $atributos["tipo"] = "hidden";
                        $atributos['estilo'] = '';
                        $atributos["obligatorio"] = false;
                        $atributos['marco'] = true;
                        $atributos["etiqueta"] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTexto($atributos);
                        unset($atributos);

                        $esteCampo = 'marca';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'modelo';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'procesador';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'memoria_ram';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'disco_duro';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'sistema_operativo';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'perifericos';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
//                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'camara';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'audio';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'bateria';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'targeta_red_alambrica';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'targeta_red_inalambrica';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'cargador';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'pantalla';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = false;
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'web_soporte';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = true;
                        $atributos['readonly'] = false;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'http://www.hp.com/latam/co/soporte/cas/';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'telefono_soporte';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['tipo'] = "text";
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 3;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['evento'] = '';
                        $atributos['deshabilitado'] = true;
                        $atributos['readonly'] = false;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '0180005147468368 - 01800096916';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 9;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);
                    }
                    
                    echo '
					</div>
					</div>
					</div>
		            </div>
        			</div>';

            {
                /**
                 * En algunas ocasiones es útil pasar variables entre las diferentes páginas.
                 * SARA permite realizar esto a través de tres
                 * mecanismos:
                 * (a). Registrando las variables como variables de sesión. Estarán disponibles durante toda la sesión de usuario. Requiere acceso a
                 * la base de datos.
                 * (b). Incluirlas de manera codificada como campos de los formularios. Para ello se utiliza un campo especial denominado
                 * formsara, cuyo valor será una cadena codificada que contiene las variables.
                 * (c) a través de campos ocultos en los formularios. (deprecated)
                 */

                // En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:

                // Paso 1: crear el listado de variables

                $valorCodificado = "action=" . $esteBloque["nombre"];
                $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
                $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];

                /**
                 * SARA permite que los nombres de los campos sean dinámicos.
                 * Para ello utiliza la hora en que es creado el formulario para
                 * codificar el nombre de cada campo.
                 */
                $valorCodificado .= "&campoSeguro=" . $_REQUEST['tiempo'];
                // Paso 3: codificar la cadena resultante
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
            }
        }

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos['marco'] = true;
        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);
    }
    public function mensaje() {
        switch ($_REQUEST['mensaje']) {
            case 'inserto':
                $estilo_mensaje = 'success';     // information,warning,error,validation
                $atributos["mensaje"] = 'Requisitos Correctamente Validados<br>Se ha Habilitado la Opcion de Descargar Borrador del Contrato';
                break;

            case 'noinserto':
                $estilo_mensaje = 'error';     // information,warning,error,validation
                $atributos["mensaje"] = 'Error al validar los Requisitos.<br>Verifique los Documentos de Requisitos';
                break;

            default:
                // code...
                break;
        }
        // ------------------Division para los botones-------------------------
        $atributos['id'] = 'divMensaje';
        $atributos['estilo'] = 'marcoBotones';
        echo $this->miFormulario->division("inicio", $atributos);

        // -------------Control texto-----------------------
        $esteCampo = 'mostrarMensaje';
        $atributos["tamanno"] = '';
        $atributos["etiqueta"] = '';
        $atributos["estilo"] = $estilo_mensaje;
        $atributos["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
        echo $this->miFormulario->campoMensaje($atributos);
        unset($atributos);

        // ------------------Fin Division para los botones-------------------------
        echo $this->miFormulario->division("fin");
        unset($atributos);
    }
}

$miSeleccionador = new Certificado($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->edicionCertificado();

?>

       

       