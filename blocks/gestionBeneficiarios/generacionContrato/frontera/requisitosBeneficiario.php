<?php
namespace gestionBeneficiarios\generacionContrato\frontera;
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

        //Ruta Imagen
        $rutaWarning = $this->rutaURL . "/frontera/css/imagen/warning.png";
        $rutaCheck = $this->rutaURL . "/frontera/css/imagen/check.png";
        $imagen = $rutaWarning;

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        //Consulta información

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionBeneficiario');
        $infoBeneficiario = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $infoBeneficiario = $infoBeneficiario[0];

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
                $atributos['leyenda'] = "Requisitos Tipo de Beneficiario: " . $infoBeneficiario['descripcion_tipo'];
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
                    $atributos['texto'] = $this->lenguaje->getCadena($esteCampo) . $infoBeneficiario['nombre'];
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
                    $atributos['texto'] = $this->lenguaje->getCadena($esteCampo) . $infoBeneficiario['identificacion'];
                    $tab++;
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTexto($atributos);
                    unset($atributos);

                    $esteCampo = "cedula"; //001
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 2;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["estilo"] = "file";
                    $atributos["etiqueta"] = "";
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    $archivo_cedula = $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = "cedula_cliente"; //777
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 2;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["estilo"] = "file";
                    $atributos["etiqueta"] = "";
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    $archivo_cedula_cliente = $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = "acta_vip"; //002
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 2;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["estilo"] = "file";
                    $atributos["etiqueta"] = "";
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    $archivo_acta_vip = $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = "certificado_servicio"; //003
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 2;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["etiqueta"] = "";
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    $archivo_certificado_servicios = $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = "certificado_proyecto_vip"; //005
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 2;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["etiqueta"] = "";
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    $archivo_certificado_proyecto_vip = $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = "documento_acceso_propietario"; //006
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 2;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["etiqueta"] = "";
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    $archivo_documento_acceso_propietario = $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = "documento_direccion"; //007
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 2;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["estilo"] = "file";
                    $atributos["etiqueta"] = "";
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    $archivo_documento_direccion = $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    switch ($infoBeneficiario['tipo']) {
                        case '1':

                            $tabla = "
                                     <table id='contenido' class='table  table-hover'>
                                      <thead>
                                        <tr>
                                          <td> </td>
                                          <td> </td>
                                          <td><center><strong>Verificación</strong></center></td>
                                        </tr>
                                        </thead>

                                        <tr>
                                          <td>"     . $this->lenguaje->getCadena("cedula") . "</td>
                                          <td><center>"     . $archivo_cedula . "</center></td>
                                          <td><center><IMG SRC='"     . $imagen . "'width='19px'></center> </td>
                                        </tr>

                                        <tr>
                                          <td>"     . $this->lenguaje->getCadena("acta_vip") . "</td>
                                          <td><center>"     . $archivo_acta_vip . "</center></td>
                                          <td><center><IMG SRC='"     . $imagen . "'width='19px'></center> </td>
                                        </tr>

                                        <tr>
                                          <td>"     . $this->lenguaje->getCadena("certificado_proyecto_vip") . "</td>
                                          <td><center>"     . $archivo_certificado_proyecto_vip . "</center></td>
                                          <td><center><IMG SRC='"     . $imagen . "'width='19px'></center> </td>
                                        </tr>

                                        <tr>
                                          <td>"     . $this->lenguaje->getCadena("documento_acceso_propietario") . "</td>
                                          <td><center>"     . $archivo_documento_acceso_propietario . "</center></td>
                                          <td><center><IMG SRC='"     . $imagen . "'width='19px'></center> </td>
                                        </tr>


                                        <tr>
                                          <td>"     . $this->lenguaje->getCadena("documento_direccion") . "</td>
                                          <td><center>"     . $archivo_documento_direccion . "</center></td>
                                          <td><center><IMG SRC='"     . $imagen . "'width='19px'></center> </td>
                                        </tr>

                                        </table>"    ;
                            break;

                        case '2':

                            $tabla = "
                                     <table id='contenido' class='table  table-hover'>
                                      <thead>
                                        <tr>
                                          <td> </td>
                                          <td> </td>
                                          <td><center><strong>Verificación</strong></center></td>
                                        </tr>
                                        </thead>

                                        <tr>
                                          <td>"     . $this->lenguaje->getCadena("cedula") . "</td>
                                          <td><center>"     . $archivo_cedula . "</center></td>
                                          <td><center><IMG SRC='"     . $imagen . "'width='19px'></center> </td>
                                        </tr>

                                        <tr>
                                          <td>"     . $this->lenguaje->getCadena("certificado_servicio") . "</td>
                                          <td><center>"     . $archivo_certificado_servicios . "</center></td>
                                          <td><center><IMG SRC='"     . $imagen . "'width='19px'></center> </td>
                                        </tr>

                                        <tr>
                                          <td>"     . $this->lenguaje->getCadena("documento_direccion") . "</td>
                                          <td><center>"     . $archivo_documento_direccion . "</center></td>
                                          <td><center><IMG SRC='"     . $imagen . "'width='19px'></center> </td>
                                        </tr>

                                        </table>"    ;

                            break;

                        case '4':
                            $tabla = "
                                     <table id='contenido' class='table  table-hover'>
                                      <thead>
                                        <tr>
                                          <td> </td>
                                          <td> </td>
                                          <td><center><strong>Verificación</strong></center></td>
                                        </tr>
                                        </thead>

                                        <tr>
                                          <td>"     . $this->lenguaje->getCadena("cedula_cliente") . "</td>
                                          <td><center>"     . $archivo_cedula_cliente . "</center></td>
                                          <td><center><IMG SRC='"     . $imagen . "'width='19px'></center> </td>
                                        </tr>

                                     </table>"    ;
                            break;

                    }
                    echo $tabla;

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
                        $esteCampo = 'botonCargarRequisitos';
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
                $valorCodificado .= "&opcion=cargarRequisitos";
                $valorCodificado .= "&tipo=" . $infoBeneficiario['tipo'];
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

$miSeleccionador = new Registrador($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->mensaje();

$miSeleccionador->seleccionarForm();

?>
