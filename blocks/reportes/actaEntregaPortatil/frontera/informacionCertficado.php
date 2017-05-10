<?php

namespace reportes\actaEntregaPortatil\frontera;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}
class Certificado
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
    }
    public function edicionCertificado()
    {
        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['id_beneficiario'] = $_REQUEST['id'];
        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificado');

        $infoCertificado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

        if ($infoCertificado && $_REQUEST['opcion'] != 'edicionActa') {

            $variable = 'pagina=actaEntregaPortatil';
            $variable .= '&opcion=resultadoActa';
            $variable .= '&mensaje=insertoInformacionCertificado';
            $variable .= '&id_beneficiario=' . $_REQUEST['id_beneficiario'];
            $url = $this->miConfigurador->configuracion["host"] . $this->miConfigurador->configuracion["site"] . "/index.php?";
            $enlace = $this->miConfigurador->configuracion['enlace'];
            $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar($variable);
            $_REQUEST[$enlace] = $enlace . '=' . $variable;
            $redireccion = $url . $_REQUEST[$enlace];
            echo "<script>location.replace('" . $redireccion . "')</script>";

            exit();
        }
        // Consulta información

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionBeneficiario');
        $infoBeneficiario = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

        {

            $arreglo = array(
                'nombres' => $infoBeneficiario['nombre_contrato'],
                'primer_apellido' => $infoBeneficiario['primer_apellido_contrato'],
                'segundo_apellido' => $infoBeneficiario['segundo_apellido_contrato'],
                'tipo_documento' => $infoBeneficiario['tipo_documento_contrato'],
                'numero_identificacion' => $infoBeneficiario['numero_identificacion_contrato'],
                'departamento' => $infoBeneficiario['nombre_departamento'],
                'municipio' => $infoBeneficiario['nombre_municipio'],
                'urbanizacion' => $infoBeneficiario['nombre_urbanizacion'],
                'id_urbanizacion' => $infoBeneficiario['id_proyecto'],
                'tipo_beneficiario' => $infoBeneficiario['tipo_beneficiario'],
                'telefono' => $infoBeneficiario['telefono'],
                'celular' => $infoBeneficiario['celular_contrato'],
                'correo' => $infoBeneficiario['correo'],
                'codigo_municipio' => $infoBeneficiario['codigo_municipio'],
                'codigo_departamento' => $infoBeneficiario['codigo_departamento'],
                'fecha_entrega' => $infoCertificado['fecha_entrega'],
                'estrato_socioeconomico' => $infoBeneficiario['estrato_socioeconomico'],
            );

            $_REQUEST = array_merge($_REQUEST, $arreglo);

            $anexo_dir = ' ';

            if ($infoBeneficiario['manzana_contrato'] != '0' && $infoBeneficiario['manzana_contrato'] != '') {
                $anexo_dir .= " Manzana  #" . $infoBeneficiario['manzana_contrato'] . " - ";
            }

            if ($infoBeneficiario['bloque_contrato'] != '0' && $infoBeneficiario['bloque_contrato'] != '') {
                $anexo_dir .= " Bloque #" . $infoBeneficiario['bloque_contrato'] . " - ";
            }

            if ($infoBeneficiario['torre_contrato'] != '0' && $infoBeneficiario['torre_contrato'] != '') {
                $anexo_dir .= " Torre #" . $infoBeneficiario['torre_contrato'] . " - ";
            }

            if ($infoBeneficiario['casa_apto_contrato'] != '0' && $infoBeneficiario['casa_apto_contrato'] != '') {
                $anexo_dir .= " Casa/Apartamento #" . $infoBeneficiario['casa_apto_contrato'];
            }

            if ($infoBeneficiario['interior_contrato'] != '0' && $infoBeneficiario['interior_contrato'] != '') {
                $anexo_dir .= " Interior #" . $infoBeneficiario['interior_contrato'];
            }

            if ($infoBeneficiario['lote_contrato'] != '0' && $infoBeneficiario['lote_contrato'] != '') {
                $anexo_dir .= " Lote #" . $infoBeneficiario['lote_contrato'];
            }

            if ($infoBeneficiario['piso_contrato'] != '0' && $infoBeneficiario['piso_contrato'] != '') {
                $anexo_dir .= " Piso #" . $infoBeneficiario['piso_contrato'];
            }

            if (!is_null($infoBeneficiario['barrio']) && $infoBeneficiario['barrio'] != '') {
                $anexo_dir .= " Barrio " . $infoBeneficiario['barrio'];
            }

            $direccion_general = $infoBeneficiario['direccion_domicilio'] . $anexo_dir;

        }

        if ($_REQUEST['opcion'] == 'edicionActa') {
            $mensaje_titulo = '(Edición)';

            $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificacion', $_REQUEST['id_beneficiario']);
            $serial_pc = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (!is_null($serial_pc['serial']) && $serial_pc['serial'] != '') {

                $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionEquipoSerial', $serial_pc['serial']);
                $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($resultado) {
                    $_REQUEST = array_merge($_REQUEST, $resultado[0]);
                }
            }

            echo '<div class="alert alert-danger text-center">
                            <strong>Información!</strong> Para editar los datos básicos del beneficiario, lo debe realizar desde el módulo de contratos.
                          </div>';
        } else {
            $mensaje_titulo = '';

            $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificacion', $_REQUEST['id_beneficiario']);
            $serial_pc = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

            if (!is_null($serial_pc['serial']) && $serial_pc['serial'] != '') {

                $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionEquipoSerial', $serial_pc['serial']);
                $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($resultado) {
                    $_REQUEST = array_merge($_REQUEST, $resultado[0]);
                }
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

        //echo "<div class='modalLoad'></div>";

        // ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
        $atributos['tipoEtiqueta'] = 'inicio';
        echo $this->miFormulario->formulario($atributos);
        {

            {

                $esteCampo = 'Agrupacion';
                $atributos['id'] = $esteCampo;
                $atributos['leyenda'] = "<b>ACTA ENTREGA DE COMPUTADOR PORTÁTIL" . $mensaje_titulo . "</b><br>Número Contrato # " . $infoBeneficiario['numero_contrato'];

                echo $this->miFormulario->agrupacion('inicio', $atributos);
                unset($atributos);

                // ------------------Division para los botones-------------------------
                $atributos["id"] = "espacio_trabajo";
                $atributos["estilo"] = " ";
                $atributos["estiloEnLinea"] = "";
                echo $this->miFormulario->division("inicio", $atributos);
                unset($atributos);
                {

                    echo '<div class="panel-group" id="accordion">

                       <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Datos Básicos</a>
                                </h4>
                            </div>
                            <div id="collapse1" class="panel-collapse collapse">
                                <div class="panel-body">';

                    {

                        $esteCampo = 'nombres';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "Ingrese Nombres";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
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

                        $esteCampo = 'primer_apellido';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "Ingrese Primer Apellido";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
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

                        $esteCampo = 'segundo_apellido';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "Ingrese Segundo Apellido";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
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

                        $esteCampo = 'tipo_documento';
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
                        $atributos['deshabilitado'] = true;
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
                        // $cadenaSql = $this->miSql->getCadenaSql('consultarMedioPago');
                        // $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                        $matrizItems = array(
                            array(
                                '1',
                                'Cédula de Ciudadanía',
                            ),
                            array(
                                '2',
                                'Cédula de Extranjería',
                            ),
                        );
                        $atributos['matrizItems'] = $matrizItems;
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroListaBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'numero_identificacion';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "Ingrese Número Identificacion";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
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

                        $esteCampo = 'celular';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
//                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        // ----------------INICIO CONTROL: Fecha de Agendamiento--------------------------------------------------------

                        $esteCampo = 'fecha_entrega';
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
                        $atributos['placeholder'] = "Seleccione la Fecha de Instalación";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
                        //$atributos['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);
                        // ----------------FIN CONTROL: Fecha de Agendamiento-----------------------
                        // ----------------FIN CONTROL: Lista Tipo de Beneficiario--------------------------------------------------------

                        // ----------------INICIO CONTROL: Lista Urbanización--------------------------------------------------------

                        $esteCampo = 'urbanizacion';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";

                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
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

                        // ----------------INICIO CONTROL: Campo Texto Departamento--------------------------------------------------------
                        $esteCampo = 'departamento';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        $atributos['valor'] = "";
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
                        //$atributos['validar'] = 'required';
                        // Aplica atributos globales al control

                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }

                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);
                        // ----------------FIN CONTROL: Campo Texto Departamento--------------------------------------------------------

                        // ----------------INICIO CONTROL: Campo Texto Municipio--------------------------------------------------------
                        $esteCampo = 'municipio';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        $atributos['valor'] = "";
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
                        //$atributos['validar'] = 'required';
                        // Aplica atributos globales al control

                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }

                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);
                        // ----------------FIN CONTROL: Campo Texto Municipio--------------------------------------------------------

                    }
                    echo '</div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Información del Computador</a>
                        </h4>
                    </div>
                    <div id="collapse2" class="panel-collapse collapse">
                        <div class="panel-body">';
                    {

                        $esteCampo = 'serial';
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
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
                        //$atributos['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'id_serial';
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

                        $esteCampo = 'marca';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'Hewlett Packard';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'modelo';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'HP 245 G4 Notebook PC';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'procesador';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'AMD A8-7410 4 cores 2.2 GHz';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'memoria_ram';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'DDR3 4096 MB';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'disco_duro';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '500 GB';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'sistema_operativo';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'Ubuntu';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'perifericos';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '';
                        }
                        $atributos['ajax_function'] = "";
                        $atributos['ajax_control'] = $esteCampo;
                        $atributos['limitar'] = false;
                        $atributos['anchoCaja'] = 10;
                        $atributos['miEvento'] = '';
//                                         $atributos ['validar'] = 'required';
                        // Aplica atributos globales al control
                        $atributos = array_merge($atributos, $atributosGlobales);
//                        echo $this->miFormulario->campoCuadroTextoBootstrap($atributos);
                        unset($atributos);

                        $esteCampo = 'camara';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'Integrada 720 px HD';
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

                        $esteCampo = 'audio';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'Integrado Estéreo';
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

                        $esteCampo = 'bateria';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '41610 mWh';
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

                        $esteCampo = 'targeta_red_alambrica';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'Integrada';
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

                        $esteCampo = 'targeta_red_inalambrica';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'Integrada';
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

                        $esteCampo = 'cargador';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'Smart AC 100 v a 120 v';
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

                        $esteCampo = 'pantalla';
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
                        $atributos['readonly'] = true;
                        $atributos['columnas'] = 1;
                        $atributos['tamanno'] = 1;
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'HD SVA anti-brillo LED 14"';
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

                        $esteCampo = 'web_soporte';
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
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = 'http://www.hp.com/latam/co/soporte/cas/';
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

                        $esteCampo = 'telefono_soporte';
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
                        $atributos['placeholder'] = "";
                        if (isset($_REQUEST[$esteCampo])) {
                            $atributos['valor'] = $_REQUEST[$esteCampo];
                        } else {
                            $atributos['valor'] = '0180005147468368 - 018000961016';
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
                    echo '</div>
                    </div>
                </div>';

                    $esteCampo = 'Agrupacion';
                    $atributos['id'] = $esteCampo;
                    $atributos['leyenda'] = "Firmas Interesados";
                    // echo $this->miFormulario->agrupacion('inicio', $atributos);
                    unset($atributos);
                    {

                        $esteCampo = 'Agrupacion';
                        $atributos['id'] = $esteCampo;
                        $atributos['leyenda'] = "Firmas Beneficiario ";
                        echo $this->miFormulario->agrupacion('inicio', $atributos);
                        unset($atributos);
                        {
                            echo "<div id='mensaje_firma_bn' style='display:none;'><center><b>Firma Guardada<b></center></div>";
                            echo "<div id='firma_digital_beneficiario'  style='border-style:double;'></div>";
                            echo "<br>";
                            echo "<input type='button' style='float:left' class='btn btn-default' id='guardarBn' value='Guardar'> <input type='button' id='limpiarBn' style='float:right' class='btn btn-default' value='Limpiar'>";

                            $esteCampo = 'firmaBeneficiario';
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

                        $esteCampo = 'Agrupacion';
                        $atributos['id'] = $esteCampo;
                        $atributos['leyenda'] = "Firmas Representante Operador ";
                        echo $this->miFormulario->agrupacion('inicio', $atributos);
                        unset($atributos);
                        {
                            echo "<div id='mensaje_firma_ins' style='display:none;'><center><b>Firma Guardada<b></center></div>";
                            echo "<div id='firma_digital_instalador'  style='border-style:double;'></div>";
                            echo "<br>";
                            echo "<input type='button' style='float:left' class='btn btn-default' id='guardarIns' value='Guardar'> <input type='button' id='limpiarIns' style='float:right' class='btn btn-default' value='Limpiar'>";

                            $esteCampo = 'firmaInstalador';
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

                    }

                    // ------------------Division para los botones-------------------------
                    $atributos["id"] = "botones";
                    $atributos["estilo"] = "marcoBotones";
                    $atributos["estiloEnLinea"] = "display:block;";
                    echo $this->miFormulario->division("inicio", $atributos);
                    unset($atributos);
                    {

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
                    }
                    // ------------------Fin Division para los botones-------------------------
                    echo $this->miFormulario->division("fin");
                    unset($atributos);
                }
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
                // $valorCodificado .= "&opcion=generarCertificacion";
                $valorCodificado .= "&opcion=guardarInformacion";
                $valorCodificado .= "&id_beneficiario=" . $_REQUEST['id_beneficiario'];
                $valorCodificado .= "&tipo_beneficiario=" . $infoBeneficiario['tipo_beneficiario'];
                $valorCodificado .= "&numero_contrato=" . $infoBeneficiario['numero_contrato'];
                $valorCodificado .= "&direccion=" . $direccion_general;

                $valorCodificado .= "&tipo_beneficiario_contrato=" . $_REQUEST['tipo_beneficiario'];

                $valorCodificado .= "&estrato_socioeconomico_contrato=" . $_REQUEST['estrato_socioeconomico'];

                $valorCodificado .= "&codigo_departamento=" . $_REQUEST['codigo_departamento'];

                $valorCodificado .= "&codigo_municipio=" . $_REQUEST['codigo_municipio'];

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
        }

        // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
        // Se debe declarar el mismo atributo de marco con que se inició el formulario.
        $atributos['marco'] = true;
        $atributos['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);
    }
    public function mensaje()
    {
        switch ($_REQUEST['mensaje']) {
            case 'inserto':
                $estilo_mensaje = 'success'; // information,warning,error,validation
                $atributos["mensaje"] = 'Requisitos Correctamente Validados<br>Se ha Habilitado la Opcion de Descargar Borrador del Contrato';
                break;

            case 'noinserto':
                $estilo_mensaje = 'error'; // information,warning,error,validation
                $atributos["mensaje"] = 'Error al validar los Requisitos.<br>Verifique los Documentos de Requisitos';
                break;

            default:
                // code...
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

$miSeleccionador = new Certificado($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->edicionCertificado();
