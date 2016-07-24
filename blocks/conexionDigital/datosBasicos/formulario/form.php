<?php

namespace conexionDigital\datosBasicos\formulario;

if (!isset($GLOBALS["autorizado"])) {
    include("../index.php");
    exit;
}

class Formulario {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miSql;

    function __construct($lenguaje, $formulario, $sql) {

        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->miSql = $sql;
    }

    function formulario() {

        /**
         * IMPORTANTE: Este formulario está utilizando jquery.
         * Por tanto en el archivo ready.php se delaran algunas funciones js
         * que lo complementan.
         */
// Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
        /**
         * Atributos que deben ser aplicados a todos los controles de este formulario.
         * Se utiliza un arreglo
         * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
         *
         * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
         * $atributos= array_merge($atributos,$atributosGlobales);
         */
        $atributosGlobales ['campoSeguro'] = 'true';
        $_REQUEST['tiempo'] = time();


        $conexion = "conexiones";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $sql_basicos = $this->miSql->getCadenaSql('consultausuario_basicos', '');
        $sql_contrato = $this->miSql->getCadenaSql('consultausuario_contrato', '');
        $sql_contacto = $this->miSql->getCadenaSql('consultausuario_contacto', '');
        $basicos = $esteRecursoDB->ejecutarAcceso($sql_basicos, "busqueda");
        $contrato = $esteRecursoDB->ejecutarAcceso($sql_contrato, "busqueda");
        $contacto = $esteRecursoDB->ejecutarAcceso($sql_contacto, "busqueda");


// -------------------------------------------------------------------------------------------------
// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
        $esteCampo = $esteBloque ['nombre'];
        $atributos ['id'] = $esteCampo;
        $atributos ['nombre'] = $esteCampo;
        $atributos ['tipoFormulario'] = '';
        $atributos ['metodo'] = 'POST';
        $atributos ['action'] = 'index.php';
        $atributos ['titulo'] = '';
        $atributos ['estilo'] = '';
        $atributos ['marco'] = true;
        $tab = 1;
// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
        $atributos ['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->formulario($atributos);
        $esteCampo = "marcoDatosBasicos";
        $atributos ['id'] = $esteCampo;
        $atributos ["estilo"] = "jqueryui";
        $atributos ['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);
// ---------------- SECCION: Controles del Formulario -----------------------------------------------
        ?><h3 class="column-title">Datos Básicos</h3><?php
        if (isset($basicos[0])) {
            foreach ($basicos[0] as $key => $values) {
                if (!is_numeric($key)) {
                    $encabezado [$key] = (str_replace("_", " ", $key));
                }
            } foreach ($basicos[0] as $key => $values) {
                if (!is_numeric($key)) {
// ----- CONTROL texto Simple ------------------------ 
                    $esteCampo = $encabezado [$key];
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jquery';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ['etiqueta'] = ucfirst($esteCampo);
                    $atributos ['texto'] = $basicos [0] [$key];
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['columnas'] = 1;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['validar'] = ''; // 
                    //   $atributos ['titulo'] = '';
                    $atributos ['deshabilitado'] = true;
                    $atributos ['tamanno'] = 5;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 5;
                    $tab ++;
// Aplica atributos globales al control 
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTexto($atributos);
                    unset($atributos);
// ------ Fin CONTROL texto simple -------------------------- // } } }
                }
            }
        } echo $this->miFormulario->marcoAgrupacion('fin');
        $esteCampo = "marcoDatosContacto";
        $atributos ['id'] = $esteCampo;
        $atributos ["estilo"] = "jqueryui";
        $atributos ['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);
        ?><h3 class="column-title">Datos de Contacto</h3><?php
        if (isset($contacto[0])) {
            foreach ($contacto[0] as $key => $values) {
                if (!is_numeric($key)) {
                    $encabezado [$key] = (str_replace("_", " ", $key));
                }
            } foreach ($contacto[0] as $key => $values) {
                if (!is_numeric($key)) {
// ----- CONTROL texto Simple ------------------------ 
                    $esteCampo = $encabezado [$key];
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jquery';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ['etiqueta'] = ucfirst($esteCampo);
                    $atributos ['texto'] = $contacto [0] [$key];
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['columnas'] = 1;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['validar'] = ''; // 
                    //   $atributos ['titulo'] = '';
                    $atributos ['deshabilitado'] = true;
                    $atributos ['tamanno'] = 5;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 5;
                    $tab ++;
// Aplica atributos globales al control 
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTexto($atributos);
                    unset($atributos);
// ------ Fin CONTROL texto simple -------------------------- // } } }
                }
            }
        }echo $this->miFormulario->marcoAgrupacion('fin');
        $esteCampo = "marcoDatosContrato";
        $atributos ['id'] = $esteCampo;
        $atributos ["estilo"] = "jqueryui";
        $atributos ['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);
        
        ?><h3 class="column-title">Datos del Contrato</h3><?php
        if (isset($contrato[0])) {
            foreach ($contrato[0] as $key => $values) {
                if (!is_numeric($key)) {
                    $encabezado [$key] = (str_replace("_", " ", $key));
                }
            } foreach ($contrato[0] as $key => $values) {
                if (!is_numeric($key)) {
// ----- CONTROL texto Simple ------------------------ 
                    $esteCampo = $encabezado [$key];
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jquery';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ['etiqueta'] = ucfirst($esteCampo);
                    $atributos ['texto'] = $contrato [0] [$key];
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['columnas'] = 1;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['validar'] = ''; // 
                    //   $atributos ['titulo'] = '';
                    $atributos ['deshabilitado'] = true;
                    $atributos ['tamanno'] = 5;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 5;
                    $tab ++;
// Aplica atributos globales al control 
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTexto($atributos);
                    unset($atributos);
// ------ Fin CONTROL texto simple -------------------------- // } } }
                }
            }
        }
        echo $this->miFormulario->marcoAgrupacion('fin');

// ------------------- SECCION: Paso de variables ------------------------------------------------

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

        $valorCodificado = "action=" . $esteBloque ["nombre"];
        $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
        $valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
        $valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
        $valorCodificado .= "&opcion=registrarBloque";
        /**
         * SARA permite que los nombres de los campos sean dinámicos.
         * Para ello utiliza la hora en que es creado el formulario para
         * codificar el nombre de cada campo. 
         */
        $valorCodificado .= "&campoSeguro=" . $_REQUEST['tiempo'];
// Paso 2: codificar la cadena resultante
        $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar($valorCodificado);

        $atributos ["id"] = "formSaraData"; // No cambiar este nombre
        $atributos ["tipo"] = "hidden";
        $atributos ['estilo'] = '';
        $atributos ["obligatorio"] = false;
        $atributos ['marco'] = true;
        $atributos ["etiqueta"] = "";
        $atributos ["valor"] = $valorCodificado;
        echo $this->miFormulario->campoCuadroTexto($atributos);
        unset($atributos);

// ----------------FIN SECCION: Paso de variables -------------------------------------------------
// ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
// Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos ['marco'] = true;
        $atributos ['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);

        return true;
    }

    function mensaje() {

// Si existe algun tipo de error en el login aparece el siguiente mensaje
        $mensaje = $this->miConfigurador->getVariableConfiguracion('mostrarMensaje');
        $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', null);

        if ($mensaje) {

            $tipoMensaje = $this->miConfigurador->getVariableConfiguracion('tipoMensaje');

            if ($tipoMensaje == 'json') {

                $atributos ['mensaje'] = $mensaje;
                $atributos ['json'] = true;
            } else {
                $atributos ['mensaje'] = $this->lenguaje->getCadena($mensaje);
            }
// -------------Control texto-----------------------
            $esteCampo = 'divMensaje';
            $atributos ['id'] = $esteCampo;
            $atributos ["tamanno"] = '';
            $atributos ["estilo"] = 'information';
            $atributos ["etiqueta"] = '';
            $atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
            echo $this->miFormulario->campoMensaje($atributos);
            unset($atributos);
        }

        return true;
    }

}

$miFormulario = new Formulario($this->lenguaje, $this->miFormulario, $this->sql);


$miFormulario->formulario();
$miFormulario->mensaje();
?>