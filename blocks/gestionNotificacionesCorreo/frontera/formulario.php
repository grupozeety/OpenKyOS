<?php

namespace gestionNotificacionesCorreo\formRegistro;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}
class Formulario
{
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public function __construct($lenguaje, $formulario, $sql)
    {
        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->miSql = $sql;
    }
    public function formulario()
    {

        /**
         * IMPORTANTE: Este formulario está utilizando jquery.
         * Por tanto en el archivo script/ready.php y script/ready.js se declaran
         * algunas funciones js que lo complementan.
         */
        $conexion = "estructura";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
        /**
         * Atributos que deben ser aplicados a todos los controles de este formulario.
         * Se utiliza un arreglo independiente debido a que los atributos individuales se reinician cada vez que se
         * declara un campo.
         *
         * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
         * $atributos= array_merge($atributos,$atributosGlobales);
         */

        $atributosGlobales['campoSeguro'] = 'true';

        if (!isset($_REQUEST['tiempo'])) {
            $_REQUEST['tiempo'] = time();
        }

        {

            // URL base
            $url = $this->miConfigurador->getVariableConfiguracion("host");
            $url .= $this->miConfigurador->getVariableConfiguracion("site");
            $url .= "/index.php?";

            // Variables para Con
            $cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
            $cadenaACodificar .= "&procesarAjax=true";
            $cadenaACodificar .= "&action=index.php";
            $cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
            $cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
            $cadenaACodificar .= "&funcion=ejecutarNotificaciones";
            $cadenaACodificar .= "&notificacion=estadoProyectos";

            // Codificar las variables
            $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
            $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

            // URL Consultar Proyectos
            $urlEjecutarProceso = $url . $cadena;

            echo $urlEjecutarProceso;

        }

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
        echo $this->miFormulario->formularioBootstrap($atributos);
        unset($atributos);

        $esteCampo = 'ficheros';
        $atributos['id'] = $esteCampo;
        $atributos['leyenda'] = "Gestión Notificaciones Correo";
        echo $this->miFormulario->agrupacion('inicio', $atributos);
        unset($atributos);

        if (isset($_REQUEST['mensaje'])) {
            $this->mensaje();
        }

        // ----------------INICIO CONTROL: Campo Texto Cedulas a Generar Acta--------------------------------------------------------

        echo '
                        <table id="example" class="display table-container" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Nombre</th>
                                    <th>Roles</th>
                                    <th>Correo</th>
                                </tr>
                            </thead>
                    </table>
                    ';

        // ------------------- SECCION: Paso de variables ------------------------------------------------

        echo $this->miFormulario->agrupacion('fin');
        unset($atributos);

        // ------------------Division para los botones-------------------------
        $atributos["id"] = "botones";
        $atributos["estilo"] = "marcoBotones";
        echo $this->miFormulario->division("inicio", $atributos);
        unset($atributos);

        // -----------------CONTROL: Botón ----------------------------------------------------------------
        $esteCampo = 'botonCrear';
        $atributos["id"] = $esteCampo;
        $atributos["tabIndex"] = $tab;
        $atributos["tipo"] = 'boton';
        // submit: no se coloca si se desea un tipo button genérico
        $atributos['submit'] = true;
        $atributos["simple"] = true;
        $atributos["estiloMarco"] = '';
        $atributos["estiloBoton"] = 'primary';
        $atributos["block"] = false;
        $atributos['deshabilitado'] = false;
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
        $valorCodificado = "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
        $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
        $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
        $valorCodificado .= "&opcion=crearUsuario";
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

        // ----------------FIN SECCION: Paso de variables -------------------------------------------------

        // ---------------- FIN SECCION: Controles del Formulario -------------------------------------------

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.

        $atributos['marco'] = true;
        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);
    }
    public function mensaje()
    {

        switch ($_REQUEST['mensaje']) {

            case 'sucess':
                $estilo_mensaje = 'success';     // information,warning,error,validation
                $mensa = explode("\n", $_REQUEST['valor']);
                $atributos["mensaje"] = "";
                foreach ($mensa as $m) {
                    $atributos["mensaje"] .= $m . "<br>";
                }
                break;

            case 'error':
                $estilo_mensaje = 'error';     // information,warning,error,validation
                $mensa = explode("\n", $_REQUEST['valor']);
                $atributos["mensaje"] = "";
                foreach ($mensa as $m) {
                    $atributos["mensaje"] .= $m . "<br>";
                }

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

$miFormulario = new Formulario($this->lenguaje, $this->miFormulario, $this->sql);

$miFormulario->formulario();
