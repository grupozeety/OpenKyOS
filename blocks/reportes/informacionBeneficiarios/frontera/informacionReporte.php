<?php
namespace reportes\informacionBeneficiarios\frontera;

/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se declaran algunas funciones js
 * que lo complementan.
 */
class Registrador
{
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public function __construct($lenguaje, $formulario, $sql)
    {
        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->miSql = $sql;

        $conexion = "interoperacion";
        //$conexion = "produccion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
    }
    public function seleccionarForm()
    {

        // Rescatar los datos de este bloque

        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        {

            {

                $url = $this->miConfigurador->getVariableConfiguracion("host");
                $url .= $this->miConfigurador->getVariableConfiguracion("site");
                $url .= "/index.php?";

                $valorCodificado = "pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                $valorCodificado .= "&action=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
                $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
                $valorCodificado .= "&opcion=generarReporte";
                $valorCodificado .= "&tipo_resultado=3";

            }

            $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
            $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valorCodificado, $enlace);

            $urlProceso = $url . $cadena;

        }
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

            //echo "<div class='modalLoad'></div>";

            $esteCampo = 'Agrupacion';
            $atributos['id'] = $esteCampo;
            $atributos['leyenda'] = "<b>Reporte Información Beneficiarios</b>";
            echo $this->miFormulario->agrupacion('inicio', $atributos);
            unset($atributos);

            $esteCampo = 'proceso';
            $atributos['nombre'] = $esteCampo;
            $atributos['id'] = $esteCampo;
            $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos["etiquetaObligatorio"] = true;
            $atributos['tab'] = $tab++;
            $atributos['anchoEtiqueta'] = 1;
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
            $atributos['anchoCaja'] = 3;
            $atributos['miEvento'] = '';
            //$atributos['validar'] = 'required';
            $atributos['cadena_sql'] = 'required';
            //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
            //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
            //"Cédula de Ciudadanía";"1"
            //"Tarjeta de Identidad";"2"
            $matrizItems = array(
                array(
                    '1',
                    'Reporte y Consulta Accesos',
                ),
                array(
                    '2',
                    'Estado Proceso Accesos',
                ),

            );
            $atributos['matrizItems'] = $matrizItems;
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
            unset($atributos);

            echo "<br><br><br>";
            // ------------------Division para los botones-------------------------
            $atributos["id"] = "consulta_reporte";
            $atributos["estilo"] = "marcoBotones";
            $atributos["estiloEnLinea"] = "display:block;";
            echo $this->miFormulario->division("inicio", $atributos);
            unset($atributos);
            {

                {
                    $esteCampo = 'tipo_resultado';
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
                    //$atributos['validar'] = 'required';
                    $atributos['cadena_sql'] = 'required';
                    //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                    //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                    //"Cédula de Ciudadanía";"1"
                    //"Tarjeta de Identidad";"2"
                    $matrizItems = array(
                        array(
                            '1',
                            'Reporte',
                        ),
                        array(
                            '2',
                            'Reporte y Documentos Beneficiarios',
                        ),
                        array(
                            '4',
                            'Documentos Beneficiarios',
                        ),

                    );
                    $atributos['matrizItems'] = $matrizItems;
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
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
                        $atributos['seleccion'] = -1;
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
                    //$atributos['validar'] = '';
                    $cadenaSql = $this->miSql->getCadenaSql('consultarDepartamento');
                    $resultado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                    $atributos['matrizItems'] = $resultado;

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
                    //$atributos['validar'] = '';
                    $atributos['cadena_sql'] = ' ';
                    $cadenaSql = $this->miSql->getCadenaSql('consultarMunicipio');
                    $resultado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                    $matrizItems = $resultado;
                    $atributos['matrizItems'] = $matrizItems;
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                    unset($atributos);

                    $esteCampo = 'urbanizacion';
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
                    //$atributos['validar'] = '';
                    $atributos['cadena_sql'] = ' ';
                    $cadenaSql = $this->miSql->getCadenaSql('consultarUrbanizacion');
                    $resultado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                    $matrizItems = $resultado;
                    $atributos['matrizItems'] = $matrizItems;
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                    unset($atributos);

                    $esteCampo = 'estado_beneficiario';
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
                    //$atributos['validar'] = 'required';
                    $atributos['cadena_sql'] = 'required';
                    //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                    //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                    //"Cédula de Ciudadanía";"1"
                    //"Tarjeta de Identidad";"2"
                    $matrizItems = array(
                        array(
                            '1',
                            'En Revisión',
                        ),
                        array(
                            '2',
                            'Verificado por el Supervisor',
                        ),

                        array(
                            '3',
                            'Aprobado por Interventoria',
                        ),
                    );
                    $atributos['matrizItems'] = $matrizItems;
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                    unset($atributos);

                    $esteCampo = 'estado_documento';
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
                    //$atributos['validar'] = 'required';
                    $atributos['cadena_sql'] = 'required';
                    //$cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                    //$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                    //"Cédula de Ciudadanía";"1"
                    //"Tarjeta de Identidad";"2"
                    $matrizItems = array(
                        array(
                            '1',
                            'SI',
                        ),
                        array(
                            '0',
                            'NO',
                        ),
                    );
                    $atributos['matrizItems'] = $matrizItems;
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                    unset($atributos);

                    $esteCampo = "beneficiario";
                    $atributos['nombre'] = $esteCampo;
                    $atributos['id'] = $esteCampo;
                    $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos["etiquetaObligatorio"] = true;
                    $atributos['tab'] = $tab++;
                    if (isset($_REQUEST[$esteCampo])) {
                        $atributos['valor'] = $_REQUEST[$esteCampo];
                    } else {
                        $atributos['valor'] = "";
                    }
                    //$atributos['validar'] = '';
                    $atributos['filas'] = 3;
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTextAreaBootstrap($atributos);
                    unset($atributos);

                }

                // ------------------Division para los botones-------------------------
                $atributos["id"] = "botones";
                $atributos["estilo"] = "marcoBotones";
                $atributos["estiloEnLinea"] = "display:block;";
                echo $this->miFormulario->division("inicio", $atributos);
                unset($atributos);
                {
                    // -----------------CONTROL: Botón ----------------------------------------------------------------
                    $esteCampo = 'generarReporte';
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

            //echo "</div>";
            echo $this->miFormulario->division("fin");
            unset($atributos);

            // ------------------Division para los botones-------------------------
            $atributos["id"] = "consulta_proceso";
            $atributos["estilo"] = "marcoBotones";
            $atributos["estiloEnLinea"] = "display:none;";
            echo $this->miFormulario->division("inicio", $atributos);
            unset($atributos);

            {

                // ------------------Division para los botones-------------------------
                $atributos["id"] = "validacion";
                $atributos["estilo"] = "marcoBotones";
                $atributos["estiloEnLinea"] = "display:block;";
                echo $this->miFormulario->division("inicio", $atributos);
                unset($atributos);
                {

                    // ------------------Division para los botones-------------------------
                    $atributos['id'] = 'divMensaje';
                    $atributos['estilo'] = 'textoIzquierda';
                    echo $this->miFormulario->division("inicio", $atributos);
                    unset($atributos);
                    {

                        {
                            // URL base
                            $url = $this->miConfigurador->getVariableConfiguracion("host");
                            $url .= $this->miConfigurador->getVariableConfiguracion("site");
                            $url .= '/archivos/generacionMasiva/plantillas/';
                            $url .= 'Plantilla_Actas_Masivas.xls';

                        }

                        // -------------Control texto-----------------------
                        $esteCampo = 'mostrarMensaje';
                        $atributos["tamanno"] = '';
                        $atributos["etiqueta"] = '';
                        $mensaje = 'Los paquetes de accesos tienen un tiempo vigencia de 5 días desde su creación, de lo contrario se eliminarán automáticamente.';

                        $atributos["mensaje"] = $mensaje;
                        $atributos["estilo"] = 'information'; // information,warning,error,validation
                        $atributos["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
                        echo $this->miFormulario->campoMensaje($atributos);
                        unset($atributos);
                    }
                    // ------------------Fin Division para los botones-------------------------
                    echo $this->miFormulario->division("fin");
                    unset($atributos);
                }

                // ------------------Fin Division para los botones-------------------------
                echo $this->miFormulario->division("fin");
                unset($atributos);

                echo '<table id="example" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th><center>Paquete Accesos</center></th>
                                            <th><center>Descripción</center></th>
                                            <th><center>Estado</center></th>
                                            <th><center>Porcentaje(%)<br>Progreso</center></th>
                                            <th><center>Fecha Generación</center></th>
                                            <th><center>Tamaño<br>Archivo</center></th>
                                            <th><center>Archivo Descarga</center></th>
                                            <th><center>Finalizar<br>Proceso</center></th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th><center>Paquete Accesos</center></th>
                                            <th><center>Descripción</center></th>
                                            <th><center>Estado</center></th>
                                            <th><center>Porcentaje(%)<br>Progreso</center></th>
                                            <th><center>Fecha Generación</center></th>
                                            <th><center>Tamaño<br>Archivo</center></th>
                                            <th><center>Archivo Descarga</center></th>
                                            <th><center>Finalizar<br>Proceso</center></th>
                                        </tr>
                                    </tfoot>
                                  </table>';
            }

            echo "</div>";
            echo $this->miFormulario->division("fin");
            unset($atributos);

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
            $valorCodificado .= "&opcion=generarReporte";

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

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos['marco'] = true;
        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);

        if (isset($_REQUEST['mensaje'])) {
            $this->mensajeModal($tab, $esteBloque['nombre']);
        }

    }
    public function mensajeModal($tab = '', $nombreBloque = '')
    {

        switch ($_REQUEST['mensaje']) {
            case 'SinResultado':
                $mensaje = "<b>No Se Genero Ningun Resultado<br>Verifique la combinacion de Parametros</b>";
                $atributos['estiloLinea'] = 'error'; //success,error,information,warning
                break;

            case 'archivoGenerado':
                $mensaje = "Exito en la Generación del Reporte y Estructuración de Documentos de los Beneficiarios<br> Link de Archivo : <a target='_blank' href='" . $_REQUEST['archivo'] . "'  >Descargar Reporte y Documentos</a>";
                $atributos['estiloLinea'] = 'success'; //success,error,information,warning
                break;
            case 'errorGenerarArchivo':
                $mensaje = "Error en la Generación del Reporte y/o en la Etructuración de los Documentos de los Beneficiarios";
                $atributos['estiloLinea'] = 'error'; //success,error,information,warning

                break;

            case 'exitoProceso':
                $mensaje = "Exito en el Registro del Proceso para la Descarga de los Accesos.<br><b>Proceso N° " . $_REQUEST['identificacion_proceso'] . "</b><br>Verifique en estado del Proceso en la Opción \"Estado Procesos Accesos\".<br>Recuerde que el tiempo para poder descargar depende del la cantidad de Accesos (Beneficiarios)que arroje la consulta.";
                $atributos['estiloLinea'] = 'success'; //success,error,information,warning
                break;

            case 'errorProceso':
                $mensaje = "Error en el Registro del Proceso para la Descarga de los Accesos";
                $atributos['estiloLinea'] = 'error'; //success,error,information,warning

                break;

            case 'errorEliminarProceso':
                $mensaje = "Error al eliminar proceso de generación de reporte y documentos acceso.<br>Sugerencia para eliminar un proceso el estado del mismo debe estar <b>'No Iniciado' o 'Finalizado'</b><br>y tiene un tiempo límite de 5 minutos desde su registro para poderlo eliminar, si no exiten más procesos ejecutados.";
                $atributos['estiloLinea'] = 'error'; //success,error,information,warning

                break;

            case 'exitoEliminarProceso':
                $mensaje = "Exito en la eliminación proceso de generación de reporte y documentos acceso";
                $atributos['estiloLinea'] = 'success';
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

$miSeleccionador = new Registrador($this->lenguaje, $this->miFormulario, $this->sql);
$miSeleccionador->seleccionarForm();
