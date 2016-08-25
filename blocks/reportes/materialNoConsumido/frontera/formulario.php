<?php
namespace reportes\materialNoConsumido\frontera;
/**
 * IMPORTANTE: Este formulario está utilizando jquery.
 * Por tanto en el archivo ready.php se declaran algunas funciones js
 * que lo complementan.
 */

var_dump($_REQUEST);

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

            $esteCampo = 'Agrupacion';
            $atributos['id'] = $esteCampo;
            $atributos['leyenda'] = "Reporte Materiales No Consumidos";
            echo $this->miFormulario->agrupacion('inicio', $atributos);
            unset($atributos);

            {
                // ----------------INICIO CONTROL: Lista Proyectos---------------------------

                $esteCampo = 'proyecto';
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

                $atributos['matrizItems'] = array(0 => array(0 => "", 1 => 'Seleccione...'));

                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                unset($atributos);

                // ------------------Division para los botones-------------------------
                $atributos["id"] = "botones";
                $atributos["estilo"] = "marcoBotones";
                echo $this->miFormulario->division("inicio", $atributos);
                unset($atributos);

                // -----------------CONTROL: Botón ----------------------------------------------------------------
                $esteCampo = 'botonAceptar';
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
            }
            echo $this->miFormulario->agrupacion('fin');
            unset($atributos);
        }

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos['marco'] = true;
        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);
    }
    public function mensaje() {

        // Si existe algun tipo de error en el login aparece el siguiente mensaje
        $mensaje = $this->miConfigurador->getVariableConfiguracion('mostrarMensaje');
        $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', null);

        if ($mensaje) {
            $tipoMensaje = $this->miConfigurador->getVariableConfiguracion('tipoMensaje');
            if ($tipoMensaje == 'json') {

                $atributos['mensaje'] = $mensaje;
                $atributos['json'] = true;
            } else {
                $atributos['mensaje'] = $this->lenguaje->getCadena($mensaje);
            }
            // ------------------Division para los botones-------------------------
            $atributos['id'] = 'divMensaje';
            $atributos['estilo'] = 'marcoBotones';
            echo $this->miFormulario->division("inicio", $atributos);

            // -------------Control texto-----------------------
            $esteCampo = 'mostrarMensaje';
            $atributos["tamanno"] = '';
            $atributos["estilo"] = 'information';
            $atributos["etiqueta"] = '';
            $atributos["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
            echo $this->miFormulario->campoMensaje($atributos);
            unset($atributos);

            // ------------------Fin Division para los botones-------------------------
            echo $this->miFormulario->division("fin");
        }
    }
}

$miSeleccionador = new Registrador($this->lenguaje, $this->miFormulario);

$miSeleccionador->mensaje();

$miSeleccionador->seleccionarForm();

?>
