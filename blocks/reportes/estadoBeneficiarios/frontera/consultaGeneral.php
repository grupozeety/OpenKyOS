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
    public $miSql;
    public function __construct($lenguaje, $formulario, $sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->miSql = $sql;
    }
    public function seleccionarForm() {

        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $conexion = "produccion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

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

                        $esteCampo = 'tipo_datos';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 1;
                        $atributos['evento'] = '';
                        $atributos['seleccion'] = '1';
                        $atributos['deshabilitado'] = false;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 3;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = 'required';
                        $atributos['cadena_sql'] = 'required';
                        $matrizItems = array(
                            array(
                                '1',
                                'Porcentaje',
                            ),
                            array(
                                '2',
                                'Númerico',
                            ),
                        );
                        $atributos['matrizItems'] = $matrizItems;
                        // Aplica atributos globales al control

                        echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'metas';
                        $atributos['nombre'] = $esteCampo;
                        $atributos['id'] = $esteCampo;
                        $atributos['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                        $atributos["etiquetaObligatorio"] = true;
                        $atributos['tab'] = $tab++;
                        $atributos['anchoEtiqueta'] = 1;
                        $atributos['evento'] = '';
                        $atributos['seleccion'] = '0';
                        $atributos['deshabilitado'] = false;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['estilo'] = "bootstrap";
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 3;
                        $atributos['miEvento'] = '';
                        $atributos['validar'] = 'required';
                        $atributos['cadena_sql'] = 'required';
                        $cadenaSql = $this->miSql->getCadenaSql('consultarMetas');
                        $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                        $arreglo = array(
                            array(
                                '0',
                                'Todas las Metas',
                            ),
                        );
                        $atributos['matrizItems'] = array_merge($resultado, $arreglo);
                        // Aplica atributos globales al control

                        echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                        unset($atributos);

                        // ------------------Division para los botones-------------------------
                        $atributos["id"] = "porcentaje";
                        $atributos["estilo"] = " ";
                        $atributos["estiloEnLinea"] = "display:block;";
                        echo $this->miFormulario->division("inicio", $atributos);
                        unset($atributos);
                        {
                            echo '<table id="example_porcentaje" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th><center>Municipio<center></th>
                                            <th><center>Proyecto<center></th>
                                            <th><center>Beneficiarios<br>Meta<center></th>
                                            <th><center>Beneficiarios<br>Sistema<center></th>
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
                                            <th><center>Municipio<center></th>
                                            <th><center>Proyecto<center></th>
                                            <th><center>Beneficiarios<br>Meta<center></th>
                                            <th><center>Beneficiarios<br>Sistema<center></th>
                                            <th><center>Preventas(%)<center></th>
                                            <th><center>Ventas(%)<center></th>
                                            <th><center>Asignación de<br>Portatiles(%)<center></th>
                                            <th><center>Asignación de<br>Equipos de Acceso(%)<center></th>
                                            <th><center>Activación(%)<center></th>
                                            <th><center>Revisión(%)<center></th>
                                            <th><center>Aprobación(%)<center></th>
                                        </tr>
                                    </tfoot>
                                  </table>';
                        }
                        //echo "</div>";
                        echo $this->miFormulario->division("fin");
                        unset($atributos);

                        // ------------------Division para los botones-------------------------
                        $atributos["id"] = "numerico";
                        $atributos["estilo"] = " ";
                        $atributos["estiloEnLinea"] = "display:none;";
                        echo $this->miFormulario->division("inicio", $atributos);
                        unset($atributos);
                        {

                            echo '<table id="example_numerico" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th><center>Municipio<center></th>
                                            <th><center>Proyecto<center></th>
                                            <th><center>Beneficiarios<br>Meta<center></th>
                                            <th><center>Beneficiarios<br>Sistema<center></th>
                                            <th><center>Contratos<center></th>
                                            <th><center>Asignación de<br>Portatiles<center></th>
                                            <th><center>Asignación de<br>Equipos de Acceso<center></th>
                                            <th><center>Activación<center></th>
                                            <th><center>Revisión<center></th>
                                            <th><center>Aprobación<center></th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th><center>Municipio<center></th>
                                            <th><center>Proyecto<center></th>
                                            <th><center>Beneficiarios<br>Meta<center></th>
                                            <th><center>Beneficiarios<br>Sistema<center></th>
                                            <th><center>Contratos<center></th>
                                            <th><center>Asignación de<br>Portatiles<center></th>
                                            <th><center>Asignación de<br>Equipos de Acceso<center></th>
                                            <th><center>Activación<center></th>
                                            <th><center>Revisión<center></th>
                                            <th><center>Aprobación<center></th>
                                        </tr>
                                    </tfoot>
                                  </table>';
                        }
                        //echo "</div>";
                        echo $this->miFormulario->division("fin");
                        unset($atributos);
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

}

$miSeleccionador = new Registrador($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->seleccionarForm();

?>


