<?php

namespace cambioClave\formRegistro;

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

        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
    }
    public function formulario()
    {

        /**
         * IMPORTANTE: Este formulario está utilizando jquery.
         * Por tanto en el archivo script/ready.php y script/ready.js se declaran
         * algunas funciones js que lo complementan.
         */
        // Datos Beneficiario
        $cadenaSql = $this->miSql->getCadenaSql('consultarBeneficiario');
        $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

        $_REQUEST = array_merge($_REQUEST, $beneficiario);

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionFacturacion', $_REQUEST['id_beneficiario']);
        $facturacion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        // Datos Factura

        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
        $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

        $directorio = $this->miConfigurador->getVariableConfiguracion("host");
        $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
        $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
        $rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
        $rutaBloque .= $esteBloque['grupo'] . '/' . $esteBloque['nombre'];

        $this->_rutaBloque = $rutaBloque;
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
            $cadenaACodificar = "pagina=impresionFactura";
            $cadenaACodificar .= "&procesarAjax=true";
            $cadenaACodificar .= "&action=impresionFactura";
            $cadenaACodificar .= "&bloqueNombre=impresionFactura";
            $cadenaACodificar .= "&bloqueGrupo=facturacion";
            $cadenaACodificar .= "&funcion=ejecutarProcesos";
            $cadenaACodificar .= "&documento_intantaneo=true";
            $cadenaACodificar .= "&id_beneficiario=" . $_REQUEST['id_beneficiario'];

            // Codificar las variables
            $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
            $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

            // URL Consultar Proyectos
            $urlDocumento = $url . $cadena;

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
        $atributos['estilo'] = 'main';
        $atributos['marco'] = true;
        $tab = 1;
        // ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------

        // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
        $atributos['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->formulario($atributos);
        unset($atributos);

        echo '<div class="main">';
        {
            echo '<div class="row">';
            {

                echo '<div class="col-xs-12 col-lg-12">';
                {

                    echo '<div class="panel panel-primary">';
                    {

                        echo '<div class="panel-body">';
                        {

                            echo '<div class="row">';
                            {

                                // -------------Control texto-----------------------
                                $esteCampo = 'mostrarMensaje';
                                $atributos["tamanno"] = '';
                                $atributos["etiqueta"] = '';

                                if ($facturacion) {
                                    $mensaje = '<center>
                                            <b>Descarga de Última Factura</b><br>
                                            <a href="' . $urlDocumento . '"  target="_blank" ><img src="theme/basico/img/DecargaPDF.png"></a>
                                            </center>';
                                    $atributos["estilo"] = 'information'; // information,warning,error,validation
                                } else {
                                    $mensaje = '<center><b>Sin Factura para Descargar</a></center>';
                                    $atributos["estilo"] = 'warning'; // information,warning,error,validation
                                }
                                $atributos["mensaje"] = $mensaje;

                                $atributos["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
                                echo $this->miFormulario->campoMensaje($atributos);
                                unset($atributos);

                                echo $this->tablaDatos();
                            }
                            echo '</div>';
                        }
                        echo '</div>';

                    }
                    echo '</div>';
                }
                echo '</div>';

            }
            echo '</div>';

        }
        echo '</div>';

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
        $valorCodificado .= "&opcion=generarCertificacion";
        /**
         * SARA permite que los nombres de los campos sean dinámicos.
         * Para ello utiliza la hora en que es creado el formulario para
         * codificar el nombre de cada campo.
         */
        $valorCodificado .= "&campoSeguro=" . $_REQUEST['tiempo'];
        // Paso 4: codificar la cadena resultante
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

    public function tablaDatos()
    {

        $tabla = '<table id="example" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                           <th><center>Fecha Factura</center></th>
                                           <th><center>Periodo Facturado</center></th>
                                           <th><center>Valor Factura($)</center></th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                           <th><center>Fecha Factura</center></th>
                                           <th><center>Periodo Facturado</center></th>
                                           <th><center>Valor Factura($)</center></th>
                                        </tr>
                                    </tfoot>
                                  </table>';

        return $tabla;

    }

    public function mensaje()
    {

        switch ($_REQUEST['mensaje']) {

            case 'sucess':
                $estilo_mensaje = 'success'; // information,warning,error,validation
                $mensa = explode("\n", $_REQUEST['valor']);
                $atributos["mensaje"] = "";
                foreach ($mensa as $m) {
                    $atributos["mensaje"] .= $m . "<br>";
                }
                break;

            case 'error':
                $estilo_mensaje = 'error'; // information,warning,error,validation
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
