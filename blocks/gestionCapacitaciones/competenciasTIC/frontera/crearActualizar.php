<?php
namespace gestionCapacitaciones\competenciasTIC\frontera;

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
            $atributos['leyenda'] = "<b>Actualización Competencia TIC</b>";
        } else {
            $atributos['leyenda'] = "<b>Registro Competencia TIC</b>";
        }
        echo $this->miFormulario->agrupacion('inicio', $atributos);
        unset($atributos);

        $esteCampo = 'AgrupacionGeneral';
        $atributos['id'] = $esteCampo;
        $atributos['leyenda'] = "Información General";
        echo $this->miFormulario->agrupacion('inicio', $atributos);
        unset($atributos);
        {

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
            $atributos['valor'] = "NO ASIGNADO";
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
                $atributos['valor'] = '';
            }
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroTexto($atributos);
            unset($atributos);

        }

        echo $this->miFormulario->agrupacion('fin');
        unset($atributos);

        $esteCampo = 'AgrupacionGeneral';
        $atributos['id'] = $esteCampo;
        $atributos['leyenda'] = "Información Capacitado";
        echo $this->miFormulario->agrupacion('inicio', $atributos);
        unset($atributos);
        {
            $esteCampo = 'identificacion';
            $atributos['id'] = $esteCampo;
            $atributos['nombre'] = $esteCampo;
            $atributos['tipo'] = "number";
            //$atributos['minimo'] = 0.1;
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
            $atributos['placeholder'] = "Ingrese Valor Númerico";
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

            $esteCampo = 'nombre';
            $atributos['id'] = $esteCampo;
            $atributos['nombre'] = $esteCampo;
            $atributos['tipo'] = "text";
            //$atributos['minimo'] = 0.1;
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
            $atributos['placeholder'] = "Ingrese Nombre Completo";
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

            $esteCampo = 'edad';
            $atributos['id'] = $esteCampo;
            $atributos['nombre'] = $esteCampo;
            $atributos['tipo'] = "number";
            $atributos['minimo'] = 3;
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
            $atributos['placeholder'] = "Ingrese Edad";
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

            $esteCampo = 'genero';
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
            //$cadenaSql = $this->miSql->getCadenaSql('consultaMunicipio');
            $matrizItems = array(
                array(
                    'F',
                    'Femenino',
                ),
                array(
                    'M',
                    'Masculino',
                ),

            );
            $atributos['matrizItems'] = $matrizItems;
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
            unset($atributos);

            $esteCampo = 'correo';
            $atributos['id'] = $esteCampo;
            $atributos['nombre'] = $esteCampo;
            $atributos['tipo'] = "email";
            //$atributos['minimo'] = 3;
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
            $atributos['placeholder'] = "Ingrese Correo Valido";
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

            $esteCampo = 'telefono';
            $atributos['id'] = $esteCampo;
            $atributos['nombre'] = $esteCampo;
            $atributos['tipo'] = "number";
            //$atributos['minimo'] = 3;
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
            $atributos['placeholder'] = "Ingrese Teléfono de Contacto";
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

        // ------------------Division para los botones-------------------------
        $atributos["id"] = "botones";
        $atributos["estilo"] = "marcoBotones";
        $atributos["estiloEnLinea"] = "";
        echo $this->miFormulario->division("inicio", $atributos);
        unset($atributos);

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
            $valorCodificado .= "&opcion=actualizarCompetenciaParticular";
            $valorCodificado .= "&id_periodo=" . $_REQUEST['id_periodo'];
        } else {
            $valorCodificado .= "&opcion=registrarPeriodoParticular";
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
    }
}

$miSeleccionador = new Periodos($this->lenguaje, $this->miFormulario, $this->sql);
