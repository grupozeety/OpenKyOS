<?php

namespace registroBeneficiario\formulario\datosBasicos;
if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se declaran algunas funciones js
 * que lo complementan.
 */
class Registrador {
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public function __construct($lenguaje, $formulario) {
        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;
    }
    public function seleccionarForm() {

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
        $atributos['tipoFormulario'] = '';
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
        {

            {

                $esteCampo = 'AgrupacionBeneficiario';
                $atributos['id'] = $esteCampo;
                $atributos['leyenda'] = "Consulta de Beneficiario";
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

                    $esteCampo = 'id';
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

                }

                echo $this->miFormulario->agrupacion('fin');
                unset($atributos);

                $esteCampo = 'AgrupacionDireccion';
                $atributos['id'] = $esteCampo;
                $atributos['leyenda'] = "Direccion de Beneficiario";
                echo $this->miFormulario->agrupacion('inicio', $atributos);
                unset($atributos);

                {

                    $esteCampo = 'direccion';
                    $atributos['nombre'] = $esteCampo;
                    $atributos['tipo'] = "text";
                    $atributos['id'] = $esteCampo;
                    $atributos['etiqueta'] = 'Dirección:';
                    $atributos["etiquetaObligatorio"] = true;
                    $atributos['tab'] = $tab++;
                    $atributos['anchoEtiqueta'] = 1;
                    $atributos['estilo'] = "bootstrap";
                    $atributos['evento'] = '';
                    $atributos['deshabilitado'] = false;
                    $atributos['readonly'] = false;
                    $atributos['columnas'] = 1;
                    $atributos['tamanno'] = 1;
                    $atributos['placeholder'] = "Dirección";
                    $atributos['valor'] = "";
                    $atributos['ajax_function'] = "";
                    $atributos['ajax_control'] = $esteCampo;
                    $atributos['limitar'] = false;
                    $atributos['anchoCaja'] = 11;
                    $atributos['miEvento'] = '';
                    $atributos['validar'] = 'required';
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                    unset($atributos);

                    {

                        echo '<div class="panel-group" id="accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Via Principal</a>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body">';

                        // ------------------Division para los botones-------------------------
                        $atributos["id"] = "marco_via_pr";
                        $atributos["estilo"] = "";
                        //  $atributos["estiloEnLinea"] = "display:block;height:100px;overflow-y:auto;overflow-x:hidden;";
                        echo $this->miFormulario->division("inicio", $atributos);
                        unset($atributos);

                        {
                            $esteCampo = 'tipo_via_ng';
                            $atributos['nombre'] = $esteCampo;
                            $atributos['id'] = $esteCampo;
                            $atributos['etiqueta'] = 'Tipo de Via:';
                            $atributos["etiquetaObligatorio"] = true;
                            $atributos['tab'] = $tab++;
                            $atributos['anchoEtiqueta'] = 1;
                            $atributos['evento'] = '';

                            if (isset($_REQUEST[$esteCampo])) {
                                $atributos['seleccion'] = $_REQUEST[$esteCampo];
                            } else {
                                $atributos['seleccion'] = '-1';
                            }
                            $atributos['deshabilitado'] = false;
                            $atributos['columnas'] = 1;
                            $atributos['tamanno'] = 1;
                            $atributos['ajax_function'] = "";
                            $atributos['ajax_control'] = $esteCampo;
                            $atributos['estilo'] = "bootstrap";
                            $atributos['limitar'] = false;
                            $atributos['anchoCaja'] = 11;
                            $atributos['miEvento'] = '';
                            //$atributos['validar'] = '';
                            $atributos['cadena_sql'] = '';
                            //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                            //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                            //"Cédula de Ciudadanía";"1"
                            //"Tarjeta de Identidad";"2"
                            $matrizItems = array(
                                array(
                                    'Calle',
                                    'Calle',
                                ),
                                array(
                                    'Carrera',
                                    'Carrera',
                                ),

                                array(
                                    'Tranversal',
                                    'Tranversal',
                                ),

                                array(
                                    'Diagonal',
                                    'Diagonal',
                                ),
                            );
                            $atributos['matrizItems'] = $matrizItems;
                            // Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                            unset($atributos);

                            $esteCampo = 'numero_pr';
                            $atributos['nombre'] = $esteCampo;
                            $atributos['tipo'] = "text";
                            $atributos['id'] = $esteCampo;
                            $atributos['etiqueta'] = 'Número Tipo Via:';
                            $atributos["etiquetaObligatorio"] = true;
                            $atributos['tab'] = $tab++;
                            $atributos['anchoEtiqueta'] = 1;
                            $atributos['estilo'] = "bootstrap";
                            $atributos['evento'] = '';
                            $atributos['deshabilitado'] = false;
                            $atributos['readonly'] = false;
                            $atributos['columnas'] = 1;
                            $atributos['tamanno'] = 1;
                            $atributos['placeholder'] = "Ingrese Número Tipo de Via";
                            $atributos['valor'] = "";
                            $atributos['ajax_function'] = "";
                            $atributos['ajax_control'] = $esteCampo;
                            $atributos['limitar'] = false;
                            $atributos['anchoCaja'] = 11;
                            $atributos['miEvento'] = '';
                            //$atributos['validar'] = '';
                            // Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                            unset($atributos);

                            $esteCampo = 'bis_pr';
                            $atributos['nombre'] = $esteCampo;
                            $atributos['id'] = $esteCampo;
                            $atributos['etiqueta'] = 'Bis:';
                            $atributos["etiquetaObligatorio"] = true;
                            $atributos['tab'] = $tab++;
                            $atributos['anchoEtiqueta'] = 1;
                            $atributos['evento'] = '';

                            if (isset($_REQUEST[$esteCampo])) {
                                $atributos['seleccion'] = $_REQUEST[$esteCampo];
                            } else {
                                $atributos['seleccion'] = '-1';
                            }
                            $atributos['deshabilitado'] = false;
                            $atributos['columnas'] = 1;
                            $atributos['tamanno'] = 1;
                            $atributos['ajax_function'] = "";
                            $atributos['ajax_control'] = $esteCampo;
                            $atributos['estilo'] = "bootstrap";
                            $atributos['limitar'] = false;
                            $atributos['anchoCaja'] = 11;
                            $atributos['miEvento'] = '';
                            //$atributos['validar'] = '';
                            $atributos['cadena_sql'] = '';
                            //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                            //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                            //"Cédula de Ciudadanía";"1"
                            //"Tarjeta de Identidad";"2"
                            $matrizItems = array(

                                array(
                                    'Bis',
                                    'Bis',
                                ),
                            );
                            $atributos['matrizItems'] = $matrizItems;
                            // Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                            unset($atributos);

                            $esteCampo = 'letra_pr';
                            $atributos['nombre'] = $esteCampo;
                            $atributos['id'] = $esteCampo;
                            $atributos['etiqueta'] = 'Letra Via';
                            $atributos["etiquetaObligatorio"] = true;
                            $atributos['tab'] = $tab++;
                            $atributos['anchoEtiqueta'] = 1;
                            $atributos['evento'] = '';

                            if (isset($_REQUEST[$esteCampo])) {
                                $atributos['seleccion'] = $_REQUEST[$esteCampo];
                            } else {
                                $atributos['seleccion'] = '';
                            }
                            $atributos['deshabilitado'] = false;
                            $atributos['columnas'] = 1;
                            $atributos['tamanno'] = 1;
                            $atributos['ajax_function'] = "";
                            $atributos['ajax_control'] = $esteCampo;
                            $atributos['estilo'] = "bootstrap";
                            $atributos['limitar'] = false;
                            $atributos['anchoCaja'] = 11;
                            $atributos['miEvento'] = '';
                            // $atributos['validar'] = '';
                            $atributos['cadena_sql'] = '';
                            unset($matrizItems);
                            for ($i = 65; $i <= 90; $i++) {
                                $letra = chr($i);

                                $matrizItems[] = array($letra, $letra);

                            }

                            $atributos['matrizItems'] = $matrizItems;
                            // Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                            unset($atributos);

                            $esteCampo = 'cuadrante_pr';
                            $atributos['nombre'] = $esteCampo;
                            $atributos['id'] = $esteCampo;
                            $atributos['etiqueta'] = 'Cuadrante:';
                            $atributos["etiquetaObligatorio"] = true;
                            $atributos['tab'] = $tab++;
                            $atributos['anchoEtiqueta'] = 1;
                            $atributos['evento'] = '';

                            if (isset($_REQUEST[$esteCampo])) {
                                $atributos['seleccion'] = $_REQUEST[$esteCampo];
                            } else {
                                $atributos['seleccion'] = '-1';
                            }
                            $atributos['deshabilitado'] = false;
                            $atributos['columnas'] = 1;
                            $atributos['tamanno'] = 1;
                            $atributos['ajax_function'] = "";
                            $atributos['ajax_control'] = $esteCampo;
                            $atributos['estilo'] = "bootstrap";
                            $atributos['limitar'] = false;
                            $atributos['anchoCaja'] = 11;
                            $atributos['miEvento'] = '';
                            // $atributos['validar'] = '';
                            $atributos['cadena_sql'] = '';
                            //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                            //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                            //"Cédula de Ciudadanía";"1"
                            //"Tarjeta de Identidad";"2"
                            $matrizItems = array(

                                array(
                                    'Norte',
                                    'Norte',
                                ),

                                array(
                                    'Sur',
                                    'Sur',
                                ),

                                array(
                                    'Este',
                                    'Este',
                                ),

                                array(
                                    'Oeste',
                                    'Oeste',
                                ),
                            );
                            $atributos['matrizItems'] = $matrizItems;
                            // Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                            unset($atributos);

                        }

                        // ------------------Fin Division para los botones-------------------------
                        echo $this->miFormulario->division("fin");
                        unset($atributos);

                        echo '</div>
                    </div>
                </div>

                        <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Via Generadora</a>
                        </h4>
                    </div>
                    <div id="collapse2" class="panel-collapse collapse">
                        <div class="panel-body">
              ';

                    }

                    {

                        // ------------------Division para los botones-------------------------
                        $atributos["id"] = "marco_via_vg";
                        $atributos["estilo"] = "";
                        //$atributos["estiloEnLinea"] = "display:block;height:100px;overflow-y:auto;overflow-x:hidden;";
                        echo $this->miFormulario->division("inicio", $atributos);
                        unset($atributos);

                        {
                            $esteCampo = 'numero_vg';
                            $atributos['nombre'] = $esteCampo;
                            $atributos['tipo'] = "text";
                            $atributos['id'] = $esteCampo;
                            $atributos['etiqueta'] = 'Número Via:';
                            $atributos["etiquetaObligatorio"] = true;
                            $atributos['tab'] = $tab++;
                            $atributos['anchoEtiqueta'] = 1;
                            $atributos['estilo'] = "bootstrap";
                            $atributos['evento'] = '';
                            $atributos['deshabilitado'] = false;
                            $atributos['readonly'] = false;
                            $atributos['columnas'] = 1;
                            $atributos['tamanno'] = 1;
                            $atributos['placeholder'] = "Ingrese Número Via";
                            $atributos['valor'] = "";
                            $atributos['ajax_function'] = "";
                            $atributos['ajax_control'] = $esteCampo;
                            $atributos['limitar'] = false;
                            $atributos['anchoCaja'] = 11;
                            $atributos['miEvento'] = '';
                            // $atributos['validar'] = '';
                            // Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                            unset($atributos);

                            $esteCampo = 'letra_vg';
                            $atributos['nombre'] = $esteCampo;
                            $atributos['id'] = $esteCampo;
                            $atributos['etiqueta'] = 'Letra Via';
                            $atributos["etiquetaObligatorio"] = true;
                            $atributos['tab'] = $tab++;
                            $atributos['anchoEtiqueta'] = 1;
                            $atributos['evento'] = '';

                            if (isset($_REQUEST[$esteCampo])) {
                                $atributos['seleccion'] = $_REQUEST[$esteCampo];
                            } else {
                                $atributos['seleccion'] = '';
                            }
                            $atributos['deshabilitado'] = false;
                            $atributos['columnas'] = 1;
                            $atributos['tamanno'] = 1;
                            $atributos['ajax_function'] = "";
                            $atributos['ajax_control'] = $esteCampo;
                            $atributos['estilo'] = "bootstrap";
                            $atributos['limitar'] = false;
                            $atributos['anchoCaja'] = 11;
                            $atributos['miEvento'] = '';
                            //  $atributos['validar'] = '';
                            $atributos['cadena_sql'] = '';
                            unset($matrizItems);
                            for ($i = 65; $i <= 90; $i++) {
                                $letra = chr($i);

                                $matrizItems[] = array($letra, $letra);

                            }

                            $atributos['matrizItems'] = $matrizItems;
                            // Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                            unset($atributos);

                            $esteCampo = 'placa_vg';
                            $atributos['nombre'] = $esteCampo;
                            $atributos['tipo'] = "text";
                            $atributos['id'] = $esteCampo;
                            $atributos['etiqueta'] = 'Número Placa:';
                            $atributos["etiquetaObligatorio"] = true;
                            $atributos['tab'] = $tab++;
                            $atributos['anchoEtiqueta'] = 1;
                            $atributos['estilo'] = "bootstrap";
                            $atributos['evento'] = '';
                            $atributos['deshabilitado'] = false;
                            $atributos['readonly'] = false;
                            $atributos['columnas'] = 1;
                            $atributos['tamanno'] = 1;
                            $atributos['placeholder'] = "Ingrese Número Placa";
                            $atributos['valor'] = "";
                            $atributos['ajax_function'] = "";
                            $atributos['ajax_control'] = $esteCampo;
                            $atributos['limitar'] = false;
                            $atributos['anchoCaja'] = 11;
                            $atributos['miEvento'] = '';
                            //$atributos['validar'] = '';
                            // Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                            unset($atributos);

                            $esteCampo = 'cuadrante_vg';
                            $atributos['nombre'] = $esteCampo;
                            $atributos['id'] = $esteCampo;
                            $atributos['etiqueta'] = 'Cuadrante:';
                            $atributos["etiquetaObligatorio"] = true;
                            $atributos['tab'] = $tab++;
                            $atributos['anchoEtiqueta'] = 1;
                            $atributos['evento'] = '';

                            if (isset($_REQUEST[$esteCampo])) {
                                $atributos['seleccion'] = $_REQUEST[$esteCampo];
                            } else {
                                $atributos['seleccion'] = '-1';
                            }
                            $atributos['deshabilitado'] = false;
                            $atributos['columnas'] = 1;
                            $atributos['tamanno'] = 1;
                            $atributos['ajax_function'] = "";
                            $atributos['ajax_control'] = $esteCampo;
                            $atributos['estilo'] = "bootstrap";
                            $atributos['limitar'] = false;
                            $atributos['anchoCaja'] = 11;
                            $atributos['miEvento'] = '';
                            //$atributos['validar'] = '';
                            $atributos['cadena_sql'] = '';
                            //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                            //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                            //"Cédula de Ciudadanía";"1"
                            //"Tarjeta de Identidad";"2"
                            $matrizItems = array(

                                array(
                                    'Norte',
                                    'Norte',
                                ),

                                array(
                                    'Sur',
                                    'Sur',
                                ),

                                array(
                                    'Este',
                                    'Este',
                                ),

                                array(
                                    'Oeste',
                                    'Oeste',
                                ),
                            );
                            $atributos['matrizItems'] = $matrizItems;
                            // Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                            unset($atributos);

                        }

                        // ------------------Fin Division para los botones-------------------------
                        echo $this->miFormulario->division("fin");
                        unset($atributos);

                        echo "</div>
                    </div>
                </div>";

                    }

                }
                echo $this->miFormulario->agrupacion('fin');
                unset($atributos);

                // ------------------Division para los botones-------------------------
                $atributos["id"] = "botones";
                $atributos["estilo"] = "marcoBotones";
                $atributos["estiloEnLinea"] = "display:block;";
                echo $this->miFormulario->division("inicio", $atributos);
                unset($atributos);
                {
                    // -----------------CONTROL: Botón ----------------------------------------------------------------
                    $esteCampo = 'botonConsultar';
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
                }
                // ------------------Fin Division para los botones-------------------------
                echo $this->miFormulario->division("fin");
                unset($atributos);
            }

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

                $valorCodificado = "actionBloque=registroBeneficiario";
                $valorCodificado .= "&pagina=registroBeneficiario";
                $valorCodificado .= "&bloque=registroBeneficiario";
                $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];

                /**
                 * SARA permite que los nombres de los campos sean dinámicos.
                 * Para ello utiliza la hora en que es creado el formulario para
                 * codificar el nombre de cada campo.
                 */
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

            }
        }

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos['marco'] = true;
        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);
    }
    public function mensaje() {

        if (isset($_REQUEST['mensaje'])) {
            switch ($_REQUEST['mensaje']) {

                case 'errorBeneficiario':
                    $estilo_mensaje = 'error';     //information,warning,error,validation
                    $atributos["mensaje"] = 'Error no exite Beneficiario';
                    break;

                default:
                    # code...
                    break;
            }
            // ------------------Division para los botones-------------------------
            $atributos['id'] = 'divMensaje';
            $atributos['estilo'] = ' ';
            // echo $this->miFormulario->division("inicio", $atributos);

            // -------------Control texto-----------------------
            $esteCampo = 'mostrarMensaje';
            $atributos["tamanno"] = '';
            $atributos["estilo"] = $estilo_mensaje;
            $atributos["estiloEnLinea"] = "text-align: center;";
            $atributos["etiqueta"] = '';
            $atributos["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
            echo $this->miFormulario->campoMensaje($atributos);
            unset($atributos);

            // ------------------Fin Division para los botones-------------------------
            echo $this->miFormulario->division("fin");
            unset($atributos);
        }
    }
}

$miSeleccionador = new Registrador($this->lenguaje, $this->miFormulario);

$miSeleccionador->mensaje();

$miSeleccionador->seleccionarForm();

?>