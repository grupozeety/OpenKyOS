<?php
namespace reportes\instalacionesGenerales\frontera;
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

            $esteCampo = 'Agrupacion';
            $atributos['id'] = $esteCampo;
            $atributos['leyenda'] = "<b>Reporte Instalaciones Generales</b>";
            echo $this->miFormulario->agrupacion('inicio', $atributos);
            unset($atributos);

            $esteCampo = 'fecha_inicio';
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
            $atributos['placeholder'] = "Seleccione Fecha Inicio";
            $atributos['valor'] = "";
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['validar'] = 'required';
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            $fecha_inicio = $this->miFormulario->campoCuadroTextoBootstrap($atributos);
            unset($atributos);

            $esteCampo = 'fecha_final';
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
            $atributos['placeholder'] = "Seleccione Fecha Final";
            $atributos['valor'] = "";
            $atributos['ajax_function'] = "";
            $atributos['ajax_control'] = $esteCampo;
            $atributos['limitar'] = false;
            $atributos['anchoCaja'] = 10;
            $atributos['miEvento'] = '';
            $atributos['validar'] = 'required';
            // Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            $fecha_final = $this->miFormulario->campoCuadroTextoBootstrap($atributos);
            unset($atributos);

            {

                echo "<table id='contenido' class='table  table-hover'>
                           <tr>
                              <td>" . $fecha_inicio . "</td>
                              <td>" . $fecha_final . "</td>
                           </tr>
                          </table>";
            }
            {
                echo '<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th><center>N#</center></th>
                            <th><center>Nombre Proyecto</center></th>
                            <th><center>opcion</center></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th><center>N#</center></th>
                            <th><center>Nombre Proyecto</center></th>
                            <th><center>opcion</center></th>
                        </tr>
                    </tfoot>
                  </table>';
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

            echo $this->miFormulario->agrupacion('fin');
            unset($atributos);

        }

        if (isset($_REQUEST['mensaje'])) {
            $this->mensaje($tab, $esteBloque['nombre']);
        }

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos['marco'] = true;
        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);
    }
    public function mensaje($tab = '', $nombreBloque = '') {

        switch ($_REQUEST['mensaje']) {
            case 'inserto':
                $mensaje = "Exito en la Aprobación de Contrato.<br>Número de Contrato : " . $_REQUEST['numero_contrato'];
                $atributos['estiloLinea'] = 'success';     //success,error,information,warning
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

$miSeleccionador = new Registrador($this->lenguaje, $this->miFormulario);

$miSeleccionador->seleccionarForm();

?>
