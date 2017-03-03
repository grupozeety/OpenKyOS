<?php
namespace cabecera\formulario\form;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit;
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
            // ------------------Division para los botones-------------------------
            $atributos["id"] = "botones";
            $atributos["estilo"] = "marcoBotones";
            echo $this->miFormulario->division("inicio", $atributos);
            unset($atributos);

            // -----------------CONTROL: Botón ----------------------------------------------------------------
            $esteCampo = 'botonRegistro';
            $atributos["id"] = $esteCampo;
            $atributos["tabIndex"] = $tab;
            $atributos["tipo"] = 'boton';
            // submit: no se coloca si se desea un tipo button genérico
            $atributos['submit'] = true;
            $atributos["simple"] = true;
            $atributos["estiloMarco"] = '';
            $atributos["estiloBoton"] = 'info';
            $atributos["block"] = false;
            // verificar: true para verificar el formulario antes de pasarlo al servidor.
            $atributos["verificar"] = '';
            $atributos["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
            $atributos["valor"] = $this->lenguaje->getCadena($esteCampo);
            $atributos['nombreFormulario'] = $esteBloque['nombre'];
            $tab++;

            // Aplica atributos globales al control
            $atributos = array_merge($atributos);
            echo $this->miFormulario->campoBotonBootstrapHtml($atributos);
            unset($atributos);

            echo "<br><br>";

            // ------------------Fin Division para los botones-------------------------
            echo $this->miFormulario->division("fin");

            echo '
                <table id="example" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Código de Cabecera</th>
                            <th>Descripción</th>
                            <th>Departamento</th>
                            <th>Municipio</th>
                            <th>Urbanización</th>
                            <th>Actualización</th>
                        </tr>
                    </thead>
                </table>
            ';

            // ------------------- SECCION: Paso de variables ------------------------------------------------

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
             */

            // En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:

            // Paso 1: crear el listado de variables

            $valorCodificado = "actionBloque=" . $esteBloque["nombre"];
            $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
            $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
            $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
            $valorCodificado .= "&opcion=agregar";
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

            case 'errorActualizarRegistrar':
                $mensaje = "Error en el Registro y/o Actualización de la Cabecera";
                $atributos['estiloLinea'] = 'error'; //success,error,information,warning

                break;

            case 'exitoActualizarRegistrar':
                $mensaje = "Exito en el Registro y/o Actualización de la Cabecera";
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

$miFormulario = new Formulario($this->lenguaje, $this->miFormulario, $this->sql);
$miFormulario->formulario();
