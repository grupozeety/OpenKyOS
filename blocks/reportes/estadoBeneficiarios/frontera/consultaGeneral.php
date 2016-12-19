<?php
namespace reportes\estadoBeneficiarios\frontera;
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

            /**
             * Código Formulario
             */

            $esteCampo = 'Agrupacion';
            $atributos['id'] = $esteCampo;
            $atributos['leyenda'] = "Consulta de Proyectos en Relación con los Beneficiarios";
            echo $this->miFormulario->agrupacion('inicio', $atributos);
            unset($atributos);
            {

                // ------------------Division para los botones-------------------------
                $atributos['id'] = 'divMensaje';
                $atributos['estilo'] = 'marcoBotones';
                echo $this->miFormulario->division("inicio", $atributos);
                unset($atributos);
                {

                    {

                        echo '<table id="example" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th><center>Departamento<center></th>
                                            <th><center>Municipio<center></th>
                                            <th><center>Beneficiarios<center></th>
                                            <th><center>Preventas(%)<center></th>
                                            <th><center>Ventas(%)<center></th>
                                            <th><center>Asignación de<br>Portatiles(%)<center></th>
                                            <th><center>Asignación de<br>Equipos de Acceso(%)<center></th>
                                            <th><center>Activación(%)<center></th>
                                            <th><center>Revisión(%)<center></th>
                                            <th><center>Aprobación(%)<center></th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th><center>Departamento<center></th>
                                            <th><center>Municipio<center></th>
                                            <th><center>Beneficiarios<center></th>
                                            <th><center>Preventas(%)<center></th>
                                            <th><center>Ventas(%)<center></th>
                                            <th><center>Asignación de<br>Portatiles(%)<center></th>
                                            <th><center>Asignación de<br>Equipos de Acceso(%)<center></th>
                                            <th><center>Activación(%)<center></th>
                                            <th><center>Revisión(%)<center></th>
                                            <th><center>Aprobación(%)<center></th>
                                        </tr>
                                    </tfoot>

                                     <tbody>
							            <tr>
							                <td>CORDOBA</td>
							                <td>MONTERIA</td>
							                <td>EL RECUERDO</td>
							                <td>10</td>
							                <td>40</td>
							                <td>50</td>
							                <td>60</td>
							                <td>70</td>
							                <td>80</td>
							                <td>100</td>
							            </tr>
							            <tr>
							                <td>CORDOBA</td>
							                <td>CERETE</td>
							                <td>ALTOS DE LAS ACACIAS</td>
							                <td>100</td>
							                <td>30</td>
							                <td>50</td>
							                <td>80</td>
							                <td>70</td>
							                <td>10</td>
							                <td>90</td>
							            </tr>
							         </tbody>
                                  </table>';
                    }

                }
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


