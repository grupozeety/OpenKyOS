<?php
namespace gestionBeneficiarios\aprobacionContrato\frontera;
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
    public function seleccionarForm() {

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        //Consulta información

        $cadenaSql = $this->miSql->getCadenaSql('consultarContratoEspecifico');

        $infoContrato = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $infoContrato = $infoContrato[0];

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
                $atributos['leyenda'] = "<b>Generación Contrato</b>";
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
                    $atributos['texto'] = $this->lenguaje->getCadena($esteCampo) . $_REQUEST['nombre_beneficiario'];
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
                    $atributos['texto'] = $this->lenguaje->getCadena($esteCampo) . $_REQUEST['identificacion_beneficiario'];
                    $tab++;
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTexto($atributos);
                    unset($atributos);

                    $esteCampo = 'numero_contrato'; // Identificacion Beneficiario
                    $atributos['id'] = $esteCampo;
                    $atributos['nombre'] = $esteCampo;
                    $atributos['tipo'] = 'text';
                    $atributos['estilo'] = 'textoElegante';
                    $atributos['columnas'] = 1;
                    $atributos['dobleLinea'] = false;
                    $atributos['tabIndex'] = $tab;
                    $atributos['texto'] = $this->lenguaje->getCadena($esteCampo) . $_REQUEST['numero_contrato'];
                    $tab++;
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTexto($atributos);
                    unset($atributos);

                    

                    $esteCampo = 'tipo_tecnologia';
                    $atributos['nombre'] = $esteCampo;
                    $atributos['id'] = $esteCampo;
                    $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos["etiquetaObligatorio"] = true;
                    $atributos['tab'] = $tab++;
                    $atributos['anchoEtiqueta'] = 2;
                    $atributos['evento'] = '';
                    $atributos['seleccion'] = -1;
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
                    $cadenaSql = $this->miSql->getCadenaSql('consultarTipoTecnologia');
                    $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                    $atributos['matrizItems'] = $resultado;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                    unset($atributos);

                    $esteCampo = 'valor_tarificacion';
                    $atributos['nombre'] = $esteCampo;
                    $atributos['id'] = $esteCampo;
                    $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos['tipo'] = "number";
                    {
                        $atributos['decimal'] = true;
                    }
                    $atributos["etiquetaObligatorio"] = true;
                    $atributos['tab'] = $tab++;
                    $atributos['anchoEtiqueta'] = 2;
                    $atributos['evento'] = '';
                    $atributos['deshabilitado'] = false;
                    $atributos['columnas'] = 1;
                    $atributos['readonly'] = false;
                    $atributos['tamanno'] = 1;
                    $atributos['ajax_function'] = "";
                    $atributos['ajax_control'] = $esteCampo;
                    $atributos['estilo'] = "bootstrap";
                    $atributos['limitar'] = false;
                    $atributos['anchoCaja'] = 10;
                    $atributos['minimo'] = 0;
                    $atributos['miEvento'] = '';
                    $atributos['validar'] = 'required';
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                    unset($atributos);

                    $esteCampo = 'medio_pago';
                    $atributos['nombre'] = $esteCampo;
                    $atributos['id'] = $esteCampo;
                    $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos["etiquetaObligatorio"] = true;
                    $atributos['tab'] = $tab++;
                    $atributos['anchoEtiqueta'] = 2;
                    $atributos['evento'] = '';
                    $atributos['seleccion'] = -1;
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
                    $cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                    $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                    $atributos['matrizItems'] = $resultado;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                    unset($atributos);

                    $esteCampo = "900_1"; //001
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 5;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] =2;
                    $atributos["anchoCaja"] = 5;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["estilo"] = "file";
                    $atributos["etiqueta"] = $this->lenguaje->getCadena($esteCampo);
                    $atributos["bootstrap"] = true;
                    $tab++;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);
                }
                {

                    // ------------------Division para los botones-------------------------
                    $atributos["id"] = "botones";
                    $atributos["estilo"] = "marcoBotones";
                    $atributos["estiloEnLinea"] = "display:block;";
                    echo $this->miFormulario->division("inicio", $atributos);
                    unset($atributos);
                    {

                        // -----------------CONTROL: Botón ----------------------------------------------------------------
                        $esteCampo = 'botonAprobarContrato';
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
                $valorCodificado .= "&opcion=aprobacionContrato";
                $valorCodificado .= "&id_contrato=" . $_REQUEST['id_contrato'];
                $valorCodificado .= "&numero_contrato=" . $_REQUEST['numero_contrato'];
                $valorCodificado .= "&nombre_beneficiario=" . $_REQUEST['nombre_beneficiario'];
                $valorCodificado .= "&identificacion_beneficiario=" . $_REQUEST['identificacion_beneficiario'];
                $valorCodificado .= "&id_beneficiario=" . $_REQUEST['id_beneficiario'];
 
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

            if (isset($_REQUEST['mensaje'])) {
                $this->mensaje($tab, $esteBloque['nombre']);
            }
        }

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos['marco'] = true;
        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);
    }
    public function mensaje($tab = '', $nombreBloque = '') {

        switch ($_REQUEST['mensaje']) {
            case 'noinserto':
                $mensaje = "Error en la Aprobación de Contrato.<br>Verifique la Información";
                $atributos['estiloLinea'] = 'error';     //success,error,information,warning
                break;

            case 'errorArchivo':
                $mensaje = "Archivo Contrato no Valido.<br>Verifique la Archivo de Contrato";
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

$miSeleccionador = new Registrador($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->seleccionarForm();

?>
