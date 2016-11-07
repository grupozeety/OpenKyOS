<?php

namespace gestionBeneficiarios\generacionContrato\frontera;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}
/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se declaran algunas funciones js
 * que lo complementan.
 */
class GestionarContrato {
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
    public function formulario() {

        if (isset($_REQUEST['mensaje'])) {

            $this->mensajeModal();

        }

        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
        $miPaginaActual = $this->miConfigurador->getVariableConfiguracion("pagina");
        // Conexion a Base de Datos
        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        // Consulta información

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionBeneficiario');
        $infoBeneficiario = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $infoBeneficiario = $infoBeneficiario[0];

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionContrato');
        $infoContrato = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $infoContrato = $infoContrato[0];
        //var_dump($infoContrato);
        if ($infoContrato['numero_identificacion'] != NULL) {

            $_REQUEST['mensaje'] = 'insertoInformacionContrato';
        }

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionAprobacion');
        $estadoAprobacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        // Ruta Imagen
        $rutaWarning = $this->rutaURL . "/frontera/css/imagen/warning.png";
        $rutaCheck = $this->rutaURL . "/frontera/css/imagen/check.png";

        // Cuando Exite Registrado un borrador del contrato

        if (is_null($infoBeneficiario['id_contrato']) != true) {

            $cadenaSql = $this->miSql->getCadenaSql('consultaRequisitosVerificados');
            $infoArchivo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        }

        $arreglo = array(
            'perfil_beneficiario' => $infoBeneficiario['tipo_beneficiario'],
            'id_beneficiario' => $infoBeneficiario['id_beneficiario'],

        );
        $cadenaSql = $this->miSql->getCadenaSql('consultarValidacionRequisitos', $arreglo);
        $requisitos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        if ($requisitos) {
            foreach ($requisitos as $key => $value) {

                if ($value['obligatoriedad'] == '1' && is_null($value['nombre_documento'])) {
                    $requisitosFaltantesObligatorios = true;

                }

            }
        }

        // Rescatar los datos de este bloque

        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------

        {
            $atributosGlobales['campoSeguro'] = 'true';
        }

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

        {
            {
                $esteCampo = 'Agrupacion';
                $atributos['id'] = $esteCampo;
                $atributos['leyenda'] = "Requisitos Tipo de Beneficiario: " . $infoBeneficiario['descripcion_tipo'];
                echo $this->miFormulario->agrupacion('inicio', $atributos);
                unset($atributos);

                {
                    $esteCampo = 'nombre_beneficiario'; // Nombre Beneficiario
                    $atributos['id'] = $esteCampo;
                    $atributos['nombre'] = $esteCampo;
                    $atributos['tipo'] = 'text';
                    $atributos['estilo'] = 'textoElegante';
                    $atributos['columnas'] = 1;
                    $atributos['dobleLinea'] = false;
                    $atributos['tabIndex'] = $tab;
                    $atributos['texto'] = $this->lenguaje->getCadena($esteCampo) . $infoBeneficiario['nombre'] . " " . $infoBeneficiario['primer_apellido'] . " " . $infoBeneficiario['segundo_apellido'];
                    $tab++;
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTexto($atributos);
                    unset($atributos);

                    $esteCampo = 'identificacion_beneficiario'; // Identificacion Beneficiario
                    $atributos['id'] = $esteCampo;
                    $atributos['nombre'] = $esteCampo;
                    $atributos['tipo'] = 'text';
                    $atributos['estilo'] = 'textoElegante';
                    $atributos['columnas'] = 1;
                    $atributos['dobleLinea'] = false;
                    $atributos['tabIndex'] = $tab;
                    $atributos['texto'] = $this->lenguaje->getCadena($esteCampo) . $infoBeneficiario['identificacion'];
                    $tab++;
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTexto($atributos);
                    unset($atributos);

                    if (isset($requisitosFaltantesObligatorios) && $requisitosFaltantesObligatorios && $infoContrato != NULL) {

                        $_REQUEST['mensaje'] = 'requisitosFaltantes';
                        $this->mensaje();

                    } elseif (is_null($infoContrato)) {

                        $_REQUEST['mensaje'] = 'minimoRequisitos';
                        $this->mensaje();

                    } else {

                        $_REQUEST['mensaje'] = 'requisitosCompletos';
                        $this->mensaje();

                    }

                    // ------------------Division para los botones-------------------------
                    $atributos["id"] = "botones";
                    $atributos["estilo"] = "marcoBotones";
                    $atributos["estiloEnLinea"] = "display:block;";
                    echo $this->miFormulario->division("inicio", $atributos);
                    unset($atributos);

                    // Acordar Roles

                    if (is_null($infoBeneficiario['id_contrato']) != true && $infoContrato != NULL && $infoContrato['numero_identificacion'] === NULL) {

                        // -----------------CONTROL: Botón ----------------------------------------------------------------
                        $esteCampo = 'botonVisualizarContrato';
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
                    } elseif ($infoContrato['numero_identificacion'] != NULL) {

                        $url = $this->miConfigurador->getVariableConfiguracion("host");
                        $url .= $this->miConfigurador->getVariableConfiguracion("site");
                        $url .= "/index.php?";

                        // ------------------Division para los botones-------------------------
                        $atributos["id"] = "botones_sin";
                        $atributos["estilo"] = "marcoBotones";
                        $atributos["estiloEnLinea"] = "display:block;";
                        echo $this->miFormulario->division("inicio", $atributos);
                        unset($atributos);

                        {

                            if ($infoContrato['numero_identificacion'] != NULL) {
                                $valorCodificado = "action=" . $esteBloque["nombre"];
                            } else {
                                $valorCodificado = (is_null($infoBeneficiario['id_contrato']) != true) ? "actionBloque=" . $esteBloque["nombre"] : "action=" . $esteBloque["nombre"];
                            }

                            $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                            $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
                            $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
                            $valorCodificado .= "&botonGenerarPdfNoFirmas=true";
                            $valorCodificado .= "&botonGenerarPdf=false";
                            $valorCodificado .= "&tipo_beneficiario=" . $infoBeneficiario['tipo_beneficiario'];

                            if ($infoContrato['numero_identificacion'] != NULL) {
                                $valorCodificado .= "&opcion=generarContratoPDF";
                            } else {
                                $valorCodificado .= (is_null($infoBeneficiario['id_contrato']) != true) ? "&opcion=mostrarContrato" : "&opcion=cargarRequisitos";
                            }

                            $valorCodificado .= "&tipo=" . $infoBeneficiario['tipo_beneficiario'];
                            $valorCodificado .= "&id_beneficiario=" . $_REQUEST['id_beneficiario'];
                            if (is_null($infoBeneficiario['id_contrato']) != true) {
                                $valorCodificado .= "&numero_contrato=" . $infoBeneficiario['numero_contrato'];
                            }
                        }

                        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
                        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valorCodificado, $enlace);

                        $urlpdfNoFirmas = $url . $cadena;

                        echo "<b><a id='link_b' href='" . $urlpdfNoFirmas . "'>Documento Contrato Sin Firmas</a></b>";

                        // -----------------CONTROL: Botón ----------------------------------------------------------------
                        $esteCampo = 'botonGenerarPdfNoFirmas';
                        $atributos["id"] = $esteCampo;
                        $atributos["tabIndex"] = $tab;
                        $atributos["tipo"] = 'boton';
                        // submit: no se coloca si se desea un tipo button genérico
                        $atributos['submit'] = true;
                        $atributos["simple"] = true;
                        $atributos["estiloMarco"] = '';
                        $atributos["estiloBoton"] = 'jqueryui';
                        $atributos["block"] = false;
                        // verificar: true para verificar el formulario antes de pasarlo al servidor.
                        $atributos["verificar"] = '';
                        $atributos["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
                        $atributos["valor"] = $this->lenguaje->getCadena($esteCampo);
                        $atributos['nombreFormulario'] = $esteBloque['nombre'];
                        $tab++;

                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        //echo $this->miFormulario->campoBoton($atributos);
                        unset($atributos);

                        //echo $this->miFormulario->division("fin");
                        unset($atributos);

                        // ------------------Division para los botones-------------------------
                        $atributos["id"] = "botones_pdf";
                        $atributos["estilo"] = "marcoBotones";
                        $atributos["estiloEnLinea"] = "display:block;";
                        echo $this->miFormulario->division("inicio", $atributos);
                        unset($atributos);

                        {

                            if ($infoContrato['numero_identificacion'] != NULL) {
                                $valorCodificado = "action=" . $esteBloque["nombre"];
                            } else {
                                $valorCodificado = (is_null($infoBeneficiario['id_contrato']) != true) ? "actionBloque=" . $esteBloque["nombre"] : "action=" . $esteBloque["nombre"];
                            }

                            $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                            $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
                            $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
                            $valorCodificado .= "&botonGenerarPdfNoFirmas=false";
                            $valorCodificado .= "&botonGenerarPdf=true";
                            $valorCodificado .= "&tipo_beneficiario=" . $infoBeneficiario['tipo_beneficiario'];

                            if ($infoContrato['numero_identificacion'] != NULL) {
                                $valorCodificado .= "&opcion=generarContratoPDF";
                            } else {
                                $valorCodificado .= (is_null($infoBeneficiario['id_contrato']) != true) ? "&opcion=mostrarContrato" : "&opcion=cargarRequisitos";
                            }

                            $valorCodificado .= "&tipo=" . $infoBeneficiario['tipo_beneficiario'];
                            $valorCodificado .= "&id_beneficiario=" . $_REQUEST['id_beneficiario'];
                            if (is_null($infoBeneficiario['id_contrato']) != true) {
                                $valorCodificado .= "&numero_contrato=" . $infoBeneficiario['numero_contrato'];
                            }
                        }

                        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
                        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valorCodificado, $enlace);

                        $urlpdfFirmas = $url . $cadena;

                        echo "<b><a id='link_a' target='_blank' href='" . $infoContrato['ruta_documento_contrato'] . "'>Documento Contrato Con Firmas</a></b>";
                        // -----------------CONTROL: Botón ----------------------------------------------------------------
                        $esteCampo = 'botonGenerarPdf';
                        $atributos["id"] = $esteCampo;
                        $atributos["tabIndex"] = $tab;
                        $atributos["tipo"] = 'boton';
                        // submit: no se coloca si se desea un tipo button genérico
                        $atributos['submit'] = true;
                        $atributos["simple"] = true;
                        $atributos["estiloMarco"] = '';
                        $atributos["estiloBoton"] = 'jqueryui';
                        $atributos["block"] = false;
                        // verificar: true para verificar el formulario antes de pasarlo al servidor.
                        $atributos["verificar"] = '';
                        $atributos["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
                        $atributos["valor"] = $this->lenguaje->getCadena($esteCampo);
                        $atributos['nombreFormulario'] = $esteBloque['nombre'];
                        $tab++;

                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        //echo $this->miFormulario->campoBoton($atributos);
                        unset($atributos);

                        echo $this->miFormulario->division("fin");
                        unset($atributos);
                        // -----------------FIN CONTROL: Botón -----------------------------------------------------------
                    }

                    // ------------------Fin Division para los botones-------------------------
                    echo $this->miFormulario->division("fin");
                    unset($atributos);
                }
                echo $this->miFormulario->agrupacion('fin');
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

                // $valorCodificado = "action=" . $esteBloque["nombre"];

                if ($infoContrato['numero_identificacion'] != NULL) {
                    $valorCodificado = "action=" . $esteBloque["nombre"];
                } else {
                    $valorCodificado = (is_null($infoBeneficiario['id_contrato']) != true) ? "actionBloque=" . $esteBloque["nombre"] : "action=" . $esteBloque["nombre"];
                }

                $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
                $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];

                if ($infoContrato['numero_identificacion'] != NULL) {
                    $valorCodificado .= "&opcion=generarContratoPDF";
                } else {
                    $valorCodificado .= (is_null($infoBeneficiario['id_contrato']) != true) ? "&opcion=mostrarContrato" : "&opcion=cargarRequisitos";
                }

                $valorCodificado .= "&tipo=" . $infoBeneficiario['tipo_beneficiario'];
                $valorCodificado .= "&id_beneficiario=" . $_REQUEST['id_beneficiario'];
                if (is_null($infoBeneficiario['id_contrato']) != true) {
                    $valorCodificado .= "&numero_contrato=" . $infoBeneficiario['numero_contrato'];
                }

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

        switch ($_REQUEST['mensaje']) {

            case 'requisitosFaltantes':
                $estilo_mensaje = 'warning';     // information,warning,error,validation
                $atributos["mensaje"] = '<b>Aún hay documentos por cargar<br>¿Esta Seguro de Generar el Contrato?</b>';
                break;

            case 'requisitosCompletos':
                $estilo_mensaje = 'success';     // information,warning,error,validation
                $atributos["mensaje"] = '<b>Todos los documentos están cargados</b>';
                break;

            case 'minimoRequisitos':
                $estilo_mensaje = 'error';     // information,warning,error,validation
                $atributos["mensaje"] = '<b>Oh No!!!! <br>Cargue mínimo el documento de identidad para generar contrato<b>';
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

    public function mensajeModal() {

        switch ($_REQUEST['mensaje']) {

            case 'insertoInformacionContrato':
                $mensaje = "Exito en el registro información del contrato";
                $atributos['estiloLinea'] = 'success';     //success,error,information,warning
                break;
            case 'errorGenerarArchivo':
                $mensaje = "Error en el registro de información del Contrato";
                $atributos['estiloLinea'] = 'error';     //success,error,information,warning

                break;

        }

        // ----------------INICIO CONTROL: Ventana Modal Beneficiario Eliminado---------------------------------

        $atributos['tipoEtiqueta'] = 'inicio';
        $atributos['titulo'] = 'Mensaje';
        $atributos['id'] = 'mensaje';
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

$miSeleccionador = new GestionarContrato($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->formulario();

?>
