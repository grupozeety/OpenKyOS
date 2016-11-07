<?php
namespace reportes\certificadoNoInternet\frontera;

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

        $conexion = "openproject";
        $esteRecursoOP = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['id_beneficiario'] = $_REQUEST['id'];
        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificado');
        $infoCertificado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

        if ($infoCertificado) {

            $variable = 'pagina=certificadoNoInternet';
            $variable .= '&opcion=resultadoCertificado';
            $variable .= '&mensaje=insertoInformacionCertificado';
            $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
            $url = $this->miConfigurador->configuracion["host"] . $this->miConfigurador->configuracion["site"] . "/index.php?";
            $enlace = $this->miConfigurador->configuracion['enlace'];
            $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar($variable);
            $_REQUEST[$enlace] = $enlace . '=' . $variable;
            $redireccion = $url . $_REQUEST[$enlace];

            echo "<script>location.replace('" . $redireccion . "')</script>";

            exit();

        }
        //Consulta información

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionBeneficiario');
        $infoBeneficiario = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $infoBeneficiario = $infoBeneficiario[0];
        //var_dump($infoBeneficiario);

        {

            $arreglo = array(
                'nombres' => $infoBeneficiario['nombre'],
                'primer_apellido' => $infoBeneficiario['primer_apellido'],
                'segundo_apellido' => $infoBeneficiario['segundo_apellido'],
                'tipo_documento' => $infoBeneficiario['tipo_documento'],
                'numero_identificacion' => $infoBeneficiario['identificacion'],
                'direccion_domicilio' => $infoBeneficiario['direccion'],
                'departamento' => $infoBeneficiario['departamento'],
                'municipio' => $infoBeneficiario['municipio'],
                'urbanizacion' => $infoBeneficiario['id_proyecto'],
                'estrato' => $infoBeneficiario['tipo_beneficiario'],
                'telefono' => $infoBeneficiario['telefono'],
                'celular' => $infoBeneficiario['celular'],
                'correo' => $infoBeneficiario['correo'],
                // 'clausulas' => '',

            );

            $_REQUEST = array_merge($_REQUEST, $arreglo);

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
        {

            {

                $esteCampo = 'Agrupacion';
                $atributos['id'] = $esteCampo;
                $atributos['leyenda'] = "<b>Certificado de No Internet</b>";

                echo $this->miFormulario->agrupacion('inicio', $atributos);
                unset($atributos);

                // ------------------Division para los botones-------------------------
                $atributos["id"] = "espacio_trabajo";
                $atributos["estilo"] = " ";
                $atributos["estiloEnLinea"] = "";
                echo $this->miFormulario->division("inicio", $atributos);
                unset($atributos);
                {

                    $esteCampo = 'Agrupacion';
                    $atributos['id'] = $esteCampo;
                    $atributos['leyenda'] = "Información Certificado";
                    echo $this->miFormulario->agrupacion('inicio', $atributos);
                    unset($atributos);
                    {

                        $esteCampo = 'nombres';
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
                        $atributos['placeholder'] = "Ingrese Nombres";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
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

                        $esteCampo = 'primer_apellido';
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
                        $atributos['placeholder'] = "Ingrese Primer Apellido";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
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

                        $esteCampo = 'segundo_apellido';
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
                        $atributos['placeholder'] = "Ingrese Segundo Apellido";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
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

                        $esteCampo = 'tipo_documento';
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
                        $atributos['tamanno'] = 1;
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = 'required';
                        $atributos['cadena_sql'] = 'required';
                        //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                        //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                        //"Cédula de Ciudadanía";"1"
                        //"Tarjeta de Identidad";"2"
                        $matrizItems = array(
                            array(
                                '1',
                                'Cédula de Ciudadanía',
                            ),
                            array(
                                '2',
                                'Tarjeta de Identidad',
                            ),
                        );
                        $atributos['matrizItems'] = $matrizItems;
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        //echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'numero_identificacion';
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
                        $atributos['placeholder'] = "Ingrese Número Identificacion";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
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

                        $esteCampo = 'celular';
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
                        $atributos['placeholder'] = "Ingrese Número Celular";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = ' ';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'ciudad';
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
                        $atributos['placeholder'] = "Ingrese Ciudad de Expedición de la Identificación";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
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

                        $esteCampo = 'ciudad_firma';
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
                        $atributos['placeholder'] = "Ingrese Ciudad de Expedición de la Identificación";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
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
                // ------------------Fin Division para los botones-------------------------
                echo $this->miFormulario->division("fin");
                unset($atributos);
                {

                    $esteCampo = 'Agrupacion';
                    $atributos['id'] = $esteCampo;
                    $atributos['leyenda'] = "Firmas Interesados";
                    // echo $this->miFormulario->agrupacion('inicio', $atributos);
                    unset($atributos);
                    {

                        $esteCampo = "firma_beneficiario";
                        $atributos["id"] = $esteCampo;
                        $atributos["nombre"] = $esteCampo;
                        $atributos["tipo"] = "file";
                        $atributos["obligatorio"] = true;
                        $atributos["etiquetaObligatorio"] = false;
                        $atributos["tabIndex"] = $tab++;
                        $atributos["columnas"] = 2;
                        $atributos["estilo"] = "textoIzquierda";
                        $atributos["anchoEtiqueta"] = 0;
                        $atributos["tamanno"] = 500000;
                        $atributos["validar"] = " ";
                        $atributos["estilo"] = "file";
                        $atributos["anchoCaja"] = "0";
                        $atributos["etiqueta"] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["bootstrap"] = true;
                        $tab++;
                        // $atributos ["valor"] = $valorCodificado;
                        $atributos = array_merge($atributos);
                        echo $this->miFormulario->campoCuadroTexto($atributos);
                        unset($atributos);

                    }

                    // echo $this->miFormulario->agrupacion('fin');
                    unset($atributos);
                    // ------------------Division para los botones-------------------------
                    $atributos["id"] = "botones";
                    $atributos["estilo"] = "marcoBotones";
                    $atributos["estiloEnLinea"] = "display:block;";
                    echo $this->miFormulario->division("inicio", $atributos);
                    unset($atributos);
                    {

                        // -----------------CONTROL: Botón ----------------------------------------------------------------
                        $esteCampo = 'botonGuardar';
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

                $valorCodificado = "action=" . $esteBloque["nombre"];
                $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
                $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
//                $valorCodificado .= "&opcion=generarCertificacion";
                $valorCodificado .= "&opcion=guardarInformacion";
                $valorCodificado .= "&id_beneficiario=" . $_REQUEST['id'];

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
            case 'inserto':
                $estilo_mensaje = 'success';     //information,warning,error,validation
                $atributos["mensaje"] = 'Requisitos Correctamente Validados<br>Se ha Habilitado la Opcion de Descargar Borrador del Contrato';
                break;

            case 'noinserto':
                $estilo_mensaje = 'error';     //information,warning,error,validation
                $atributos["mensaje"] = 'Error al validar los Requisitos.<br>Verifique los Documentos de Requisitos';
                break;

            default:
                # code...
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
