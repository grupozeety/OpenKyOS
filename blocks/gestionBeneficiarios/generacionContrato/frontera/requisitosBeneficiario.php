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
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
        $miPaginaActual = $this->miConfigurador->getVariableConfiguracion("pagina");
        // Conexion a Base de Datos
        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        // Consulta información

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionBeneficiario');
        $infoBeneficiario = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $infoBeneficiario = $infoBeneficiario[0];

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionContrato');
        $infoContrato = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $infoContrato = $infoContrato[0];

        if ($infoContrato['numero_identificacion'] != NULL) {

            $_REQUEST['mensaje'] = 'insertoInformacionContrato';
        }

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionAprobacion');
        $estadoAprobacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        // Ruta Imagen
        $rutaWarning = $this->rutaURL . "/frontera/css/imagen/warning.png";
        $rutaCheck = $this->rutaURL . "/frontera/css/imagen/check.png";

        if ($estadoAprobacion != false) {
            foreach ($estadoAprobacion as $key => $values) {
                $imagenSupervisor[$estadoAprobacion[$key]['codigo_requisito']] = $estadoAprobacion[$key]['supervisor'] == 't' ? $rutaCheck : $rutaWarning;
                $imagenComisionador[$estadoAprobacion[$key]['codigo_requisito']] = $estadoAprobacion[$key]['comisionador'] == 't' ? $rutaCheck : $rutaWarning;
                $imagenAnalista[$estadoAprobacion[$key]['codigo_requisito']] = $estadoAprobacion[$key]['analista'] == 't' ? $rutaCheck : $rutaWarning;

                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=verArchivo";
                $variable .= "&mensaje=confirma";
                $variable .= "&id_beneficiario=" . $_REQUEST['id_beneficiario'];
                $variable .= "&ruta=" . $estadoAprobacion[$key]['ruta_relativa'];
                $variable .= "&archivo=" . $estadoAprobacion[$key]['id'];
                $variable .= "&tipologia=" . $estadoAprobacion[$key]['tipologia_documento'];
                $url = $this->miConfigurador->configuracion["host"] . $this->miConfigurador->configuracion["site"] . "/index.php?";
                $enlace = $this->miConfigurador->configuracion['enlace'];
                $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar($variable);
                $_REQUEST[$enlace] = $enlace . '=' . $variable;
                $redireccion[$estadoAprobacion[$key]['codigo_requisito']] = $url . $_REQUEST[$enlace];

                if ($estadoAprobacion[$key]['supervisor'] == 'f') {

                    $Validaciones_Requisitos = true;

                } elseif ($estadoAprobacion[$key]['comisionador'] == 'f') {
                    $Validaciones_Requisitos = true;
                } elseif ($estadoAprobacion[$key]['analista'] == 'f') {
                    $Validaciones_Requisitos = true;
                }
            }
        } else {
            $cadenaSql = $this->miSql->getCadenaSql('consultarParametroCodigo');
            $codigo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            foreach ($codigo as $key => $values) {
                $imagenSupervisor[$codigo[$key]['codigo']] = $rutaWarning;
                $imagenComisionador[$codigo[$key]['codigo']] = $rutaWarning;
                $imagenAnalista[$codigo[$key]['codigo']] = $rutaWarning;
            }
        }

        // Cuando Exite Registrado un borrador del contrato

        if (is_null($infoBeneficiario['id_contrato']) != true) {

            $cadenaSql = $this->miSql->getCadenaSql('consultaRequisitosVerificados');
            $infoArchivo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        }

        // Rescatar los datos de este bloque

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
                    if (is_null($infoBeneficiario['id_contrato']) != true && !isset($_REQUEST['mensaje'])) {
                        $_REQUEST['mensaje'] = 'inserto';
                        $this->mensaje();
                        unset($atributos);
                    } elseif (isset($_REQUEST['mensaje'])) {

                        $this->mensaje();
                        unset($atributos);
                    }

                    $esteCampo = 'nombre_beneficiario'; // Nombre Beneficiario
                    $atributos['id'] = $esteCampo;
                    $atributos['nombre'] = $esteCampo;
                    $atributos['tipo'] = 'text';
                    $atributos['estilo'] = 'textoElegante';
                    $atributos['columnas'] = 1;
                    $atributos['dobleLinea'] = false;
                    $atributos['tabIndex'] = $tab;
                    $atributos['texto'] = $this->lenguaje->getCadena($esteCampo) . $infoBeneficiario['nombre'] . " " . $infoBeneficiario['primer_apellido'] . " " . $infoBeneficiario['segundo_apellido'];
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

                    $esteCampo = "cedula"; // 001
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 1;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["estilo"] = "file";
                    $atributos["anchoCaja"] =15;
                    $atributos["etiqueta"] = $this->lenguaje->getCadena($esteCampo) ;
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);

                    if (isset($infoArchivo)) {
                        $indice = array_search("001", array_column($infoArchivo, 'codigo_requisito'), true);

                        if (!is_null($indice) && isset($redireccion['001'])) {

                            $cadena = "<center><a href='" . $redireccion['001'] . "' >" . $this->lenguaje->getCadena("cedula") . "</a></center>";
                        } else {

                            $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                        }
                    } else {
                        $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                    }

                    $archivo_cedula = $cadena;
                    unset($atributos);

                    $esteCampo = "cedula_cliente"; // 777
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 1;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["estilo"] = "file";
                    $atributos["anchoCaja"] =15;
                    $atributos["etiqueta"] = $this->lenguaje->getCadena($esteCampo) ;
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);

                    if (isset($infoArchivo)) {
                        $indice = array_search("777", array_column($infoArchivo, 'codigo_requisito'), true);

                        if (!is_null($indice) && isset($redireccion['777'])) {

                            $cadena = "<center><a href='" . $redireccion['777'] . "'  >" . $this->lenguaje->getCadena($esteCampo) . "</a></center>";
                        } else {

                            $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                        }
                    } else {
                        $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                    }

                    $archivo_cedula_cliente = $cadena;
                    unset($atributos);

                    $esteCampo = "acta_vip"; // 002
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 1;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] =0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["estilo"] = "file";
                    $atributos["anchoCaja"] =15;
                    $atributos["etiqueta"] = $this->lenguaje->getCadena($esteCampo);
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);

                    if (isset($infoArchivo)) {
                        $indice = array_search("002", array_column($infoArchivo, 'codigo_requisito'), true);

                        if (!is_null($indice) && isset($redireccion['002'])) {

                            $cadena = "<center><a href='" . $redireccion['002'] . "' >" . $this->lenguaje->getCadena("acta_vip") . "</a></center>";
                        } else {

                            $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                        }
                    } else {
                        $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                    }
                    
                    $archivo_acta_vip = $cadena;
                    unset($atributos);

                    $esteCampo = "certificado_servicio"; // 003
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 1;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["anchoCaja"] =15;
                  $atributos["etiqueta"] = $this->lenguaje->getCadena($esteCampo); ;
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    if (isset($infoArchivo)) {
                        $indice = array_search("003", array_column($infoArchivo, 'codigo_requisito'), true);

                        if (!is_null($indice) && isset($redireccion['003'])) {

                            $cadena = "<center><a href='" . $redireccion['003'] . "'  >" . $this->lenguaje->getCadena("certificado_servicio") . "</a></center>";
                        } else {

                            $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                        }
                    } else {
                        $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                    }
                    $archivo_certificado_servicios = $cadena;
                    unset($atributos);

                    $esteCampo = "certificado_proyecto_vip"; // 005
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 1;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["anchoCaja"] =15;
                  $atributos["etiqueta"] = $this->lenguaje->getCadena($esteCampo);;
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    if (isset($infoArchivo)) {
                        $indice = array_search("005", array_column($infoArchivo, 'codigo_requisito'), true);

                        if (!is_null($indice) && isset($redireccion['005'])) {

                            $cadena = "<center><a href='" . $redireccion['005'] . "'  >" . $this->lenguaje->getCadena("certificado_proyecto_vip") . "</a></center>";
                        } else {

                            $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                        }
                    } else {
                        $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                    }
                    $archivo_certificado_proyecto_vip = $cadena;
                    unset($atributos);

                    $esteCampo = "documento_acceso_propietario"; // 006
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 1;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["anchoCaja"] =15;
                    $atributos["etiqueta"] = $this->lenguaje->getCadena($esteCampo);;
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    if (isset($infoArchivo)) {
                        $indice = array_search("006", array_column($infoArchivo, 'codigo_requisito'), true);

                        if (!is_null($indice) && isset($redireccion['006'])) {

                            $cadena = "<center><a href='" . $redireccion['006'] . "' >" . $this->lenguaje->getCadena("documento_acceso_propietario") . "</a></center>";
                        } else {

                            $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                        }
                    } else {
                        $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                    }
                    $archivo_documento_acceso_propietario = $cadena;
                    unset($atributos);

                    $esteCampo = "documento_direccion"; // 007
                    $atributos["id"] = $esteCampo; // No cambiar este nombre
                    $atributos["nombre"] = $esteCampo;
                    $atributos["tipo"] = "file";
                    $atributos["obligatorio"] = true;
                    $atributos["etiquetaObligatorio"] = false;
                    $atributos["tabIndex"] = $tab++;
                    $atributos["columnas"] = 1;
                    $atributos["estilo"] = "textoIzquierda";
                    $atributos["anchoEtiqueta"] = 0;
                    $atributos["tamanno"] = 500000;
                    $atributos["validar"] = "required";
                    $atributos["estilo"] = "file";
                    $atributos["anchoCaja"] =15;
                    $atributos["etiqueta"] = $this->lenguaje->getCadena($esteCampo);;
                    $atributos["bootstrap"] = true;
                    // $atributos ["valor"] = $valorCodificado;
                    $atributos = array_merge($atributos);
                    if (isset($infoArchivo)) {
                        $indice = array_search("007", array_column($infoArchivo, 'codigo_requisito'), true);

                        if (!is_null($indice) && isset($redireccion['007'])) {

                            $cadena = "<center><a href='" . $redireccion['007'] . "'  >" . $this->lenguaje->getCadena("documento_direccion") . "</a></center>";
                        } else {

                            $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                        }
                    } else {
                        $cadena = "<center>" . $this->miFormulario->campoCuadroTexto($atributos) . "</center>";
                    }

                    $archivo_documento_direccion = $cadena;
                    unset($atributos);

                    switch ($infoBeneficiario['tipo_beneficiario']) {
                        case '1':
                            if ($estadoAprobacion != false) {
                                $tabla = "
                                     <table id='example' class='table table-striped table-bordered dt-responsive nowrap' cellspacing='0' width='100%'>
                                      <thead>
                                        <tr>
                                          <th>Documento</th>
                                          <th ><center><strong>Comisión</strong></center></th>
                                          <th><center><strong>Supervisor</strong></center></th>
                                          <th><center><strong>Analista</strong></center></th>
                                        </tr>
                                        </thead>

                                        <tr>
                                          <td>"     . $archivo_cedula . "</td>
                                          <td><center><IMG SRC='"     . $imagenComisionador['001'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenSupervisor['001'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenAnalista['001'] . "'width='19px'></center> </td>
                                        </tr>

                                        <tr>
                                          <td>"     . $archivo_acta_vip . "</td>
                                          <td><center><IMG SRC='"     . $imagenComisionador['002'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenSupervisor['002'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenAnalista['002'] . "'width='19px'></center> </td>
                                        </tr>

                                        <tr>
                                          <td>"     . $archivo_certificado_proyecto_vip . "</td>
                                          <td><center><IMG SRC='"     . $imagenComisionador['005'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenSupervisor['005'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenAnalista['005'] . "'width='19px'></center> </td>
                                        </tr>

                                        <tr>

                                          <td>"     . $archivo_documento_acceso_propietario . "</td>
                                          <td><center><IMG SRC='"     . $imagenComisionador['006'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenSupervisor['006'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenAnalista['006'] . "'width='19px'></center> </td>
                                        </tr>


                                        <tr>
                                          <td>"     . $archivo_documento_direccion . "</td>
                                         <td><center><IMG SRC='"     . $imagenComisionador['007'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenSupervisor['007'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenAnalista['007'] . "'width='19px'></center> </td>
                                        </tr>

                                        </table>"    ;
                            } else {
                                $tabla = "
                       <table id='example' class='table table-striped table-bordered dt-responsive nowrap' cellspacing='0' width='100%'>
                                      <thead>
                                        <tr>
                                          <td align='left'> </td>
                                        </tr>
                                        </thead>

                                        <tr>
                                          <td>"     . $archivo_cedula . "</td>
                                        </tr>

                                        <tr>
                                          <td>"     . $archivo_acta_vip . "</td>
                                        </tr>

                                        <tr>
                                          <td>"     . $archivo_certificado_proyecto_vip . "</td>
                                        </tr>

                                        <tr>

                                          <td>"     . $archivo_documento_acceso_propietario . "</td>
                                        </tr>


                                        <tr>
                                          <td>"     . $archivo_documento_direccion . "</td>
                                        </tr>

                                        </table>"    ;
                            }
                            break;

                        case '2':
                            if ($estadoAprobacion != false) {
                                $tabla = "
                          <table id='example' class='table table-striped table-bordered dt-responsive nowrap' cellspacing='0' width='100%'>
                                      <thead>
                                        <tr>
                                          <th>Documento</th>
                                          <th><center><strong>Comisión</strong></center></th>
                                          <th><center><strong>Supervisor</strong></center></th>
                                          <th><center><strong>Analista</strong></center></th>
                                        </tr>
                                        </thead>

                                        <tr>
                                          <td>"     . $archivo_cedula . "</td>
                                          <td><center><IMG SRC='"     . $imagenComisionador['001'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenSupervisor['001'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenAnalista['001'] . "'width='19px'></center> </td>
                                        </tr>

                                        <tr>
                                          <td>"     . $archivo_certificado_servicios . "</td>
                                          <td><center><IMG SRC='"     . $imagenComisionador['003'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenSupervisor['003'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenAnalista['003'] . "'width='19px'></center> </td>
                                        </tr>

                                        <tr>
                                          <td>"     . $archivo_documento_direccion . "</td>
                                          <td><center><IMG SRC='"     . $imagenComisionador['007'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenSupervisor['007'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenAnalista['007'] . "'width='19px'></center> </td>
                                        </tr>

                                        </table>"    ;
                            } else {
                                $tabla = "
                                    <table id='example' class='table table-striped table-bordered dt-responsive nowrap' cellspacing='0' width='100%'>
                                      <thead>
                                        <tr>
                                          <td> </td>
                                            </tr>
                                        </thead>
                                                                        <tr>
                                          <td>"     . $archivo_cedula . "</td>
                                        </tr>
                                                                        <tr>
                                          <td>"     . $archivo_certificado_servicios . "</td>
                                        </tr>

                                        <tr>
                                          <td>"     . $archivo_documento_direccion . "</td>
                                        </tr>
                                        </table>"    ;
                            }
                            break;

                        case '3':
                            if ($estadoAprobacion != false) {
                                $tabla = "
                                    <table id='example' class='table table-striped table-bordered dt-responsive nowrap' cellspacing='0' width='100%'>
                                      <thead>
                                        <tr>
                                          <th>Documento</th>
                                          <th><center><strong>Comisión</strong></center></th>
                                          <th><center><strong>Supervisor</strong></center></th>
                                          <th><center><strong>Analista</strong></center></th>
                                        </tr>
                                        </thead>

                                        <tr>
                                          <td>"     . $archivo_cedula_cliente . "</td>
                                          <td><center><IMG SRC='"     . $imagenComisionador['777'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenSupervisor['777'] . "'width='19px'></center> </td>
                                          <td><center><IMG SRC='"     . $imagenAnalista['777'] . "'width='19px'></center> </td>
                                        </tr>


                                     </table>"    ;
                                break;
                            } else {
                                $tabla = "
                                     <table id='example' class='table table-striped table-bordered dt-responsive nowrap' cellspacing='0' width='100%'>
                                      <thead>
                                        <tr>
                                          <th>Documento</th>
                                        </tr>
                                        </thead>

                                        <tr>
                                          <td>"     . $archivo_cedula_cliente . "</td>
                                        </tr>

                                     </table>"    ;
                            }
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
                    if (isset($Validaciones_Requisitos)) {
                        // Acordar Roles

                        if (is_null($infoBeneficiario['id_contrato']) != true && $infoContrato['numero_identificacion'] === NULL) {

                            // -----------------CONTROL: Botón ----------------------------------------------------------------
                            $esteCampo = 'botonVisualizarContrato';
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
                        } elseif ($infoContrato['numero_identificacion'] != NULL) {

                            $url = $this->miConfigurador->getVariableConfiguracion("host");
                            $url .= $this->miConfigurador->getVariableConfiguracion("site");
                            $url .= "/index.php?";

                            // ------------------Division para los botones-------------------------
                            $atributos["id"] = "botones_sin";
                            $atributos["estilo"] = "marcoBotones";
                            $atributos["estiloEnLinea"] = "display:block;";
                            echo $this->miFormulario->division("inicio", $atributos);
                            unset($atributos);

                            {

                                if ($infoContrato['numero_identificacion'] != NULL) {
                                    $valorCodificado = "action=" . $esteBloque["nombre"];
                                } else {
                                    $valorCodificado = (is_null($infoBeneficiario['id_contrato']) != true) ? "actionBloque=" . $esteBloque["nombre"] : "action=" . $esteBloque["nombre"];
                                }

                                $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                                $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
                                $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
                                $valorCodificado .= "&botonGenerarPdfNoFirmas=true";
                                $valorCodificado .= "&botonGenerarPdf=false";

                                if ($infoContrato['numero_identificacion'] != NULL) {
                                    $valorCodificado .= "&opcion=generarContratoPDF";
                                } else {
                                    $valorCodificado .= (is_null($infoBeneficiario['id_contrato']) != true) ? "&opcion=mostrarContrato" : "&opcion=cargarRequisitos";
                                }

                                $valorCodificado .= "&tipo=" . $infoBeneficiario['tipo_beneficiario'];
                                $valorCodificado .= "&id_beneficiario=" . $_REQUEST['id_beneficiario'];
                                if (is_null($infoBeneficiario['id_contrato']) != true) {
                                    $valorCodificado .= "&numero_contrato=" . $infoBeneficiario['numero_contrato'];
                                }
                            }

                            $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
                            $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valorCodificado, $enlace);

                            $urlpdfNoFirmas = $url . $cadena;

                            echo "<b><a id='link_b' href='" . $urlpdfNoFirmas . "'>Documento Contrato Sin Firmas</a></b>";

                            // -----------------CONTROL: Botón ----------------------------------------------------------------
                            $esteCampo = 'botonGenerarPdfNoFirmas';
                            $atributos["id"] = $esteCampo;
                            $atributos["tabIndex"] = $tab;
                            $atributos["tipo"] = 'boton';
                            // submit: no se coloca si se desea un tipo button genérico
                            $atributos['submit'] = true;
                            $atributos["simple"] = true;
                            $atributos["estiloMarco"] = '';
                            $atributos["estiloBoton"] = 'jqueryui';
                            $atributos["block"] = false;
                            // verificar: true para verificar el formulario antes de pasarlo al servidor.
                            $atributos["verificar"] = '';
                            $atributos["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
                            $atributos["valor"] = $this->lenguaje->getCadena($esteCampo);
                            $atributos['nombreFormulario'] = $esteBloque['nombre'];
                            $tab++;

                            // Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            //echo $this->miFormulario->campoBoton($atributos);
                            unset($atributos);

                            //echo $this->miFormulario->division("fin");
                            unset($atributos);

                            // ------------------Division para los botones-------------------------
                            $atributos["id"] = "botones_pdf";
                            $atributos["estilo"] = "marcoBotones";
                            $atributos["estiloEnLinea"] = "display:block;";
                            echo $this->miFormulario->division("inicio", $atributos);
                            unset($atributos);

                            {

                                if ($infoContrato['numero_identificacion'] != NULL) {
                                    $valorCodificado = "action=" . $esteBloque["nombre"];
                                } else {
                                    $valorCodificado = (is_null($infoBeneficiario['id_contrato']) != true) ? "actionBloque=" . $esteBloque["nombre"] : "action=" . $esteBloque["nombre"];
                                }

                                $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                                $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
                                $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
                                $valorCodificado .= "&botonGenerarPdfNoFirmas=false";
                                $valorCodificado .= "&botonGenerarPdf=true";

                                if ($infoContrato['numero_identificacion'] != NULL) {
                                    $valorCodificado .= "&opcion=generarContratoPDF";
                                } else {
                                    $valorCodificado .= (is_null($infoBeneficiario['id_contrato']) != true) ? "&opcion=mostrarContrato" : "&opcion=cargarRequisitos";
                                }

                                $valorCodificado .= "&tipo=" . $infoBeneficiario['tipo_beneficiario'];
                                $valorCodificado .= "&id_beneficiario=" . $_REQUEST['id_beneficiario'];
                                if (is_null($infoBeneficiario['id_contrato']) != true) {
                                    $valorCodificado .= "&numero_contrato=" . $infoBeneficiario['numero_contrato'];
                                }
                            }

                            $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
                            $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valorCodificado, $enlace);

                            $urlpdfFirmas = $url . $cadena;

                            echo "<b><a id='link_a' href='" . $urlpdfFirmas . "'>Documento Contrato Con Firmas</a></b>";
                            // -----------------CONTROL: Botón ----------------------------------------------------------------
                            $esteCampo = 'botonGenerarPdf';
                            $atributos["id"] = $esteCampo;
                            $atributos["tabIndex"] = $tab;
                            $atributos["tipo"] = 'boton';
                            // submit: no se coloca si se desea un tipo button genérico
                            $atributos['submit'] = true;
                            $atributos["simple"] = true;
                            $atributos["estiloMarco"] = '';
                            $atributos["estiloBoton"] = 'jqueryui';
                            $atributos["block"] = false;
                            // verificar: true para verificar el formulario antes de pasarlo al servidor.
                            $atributos["verificar"] = '';
                            $atributos["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
                            $atributos["valor"] = $this->lenguaje->getCadena($esteCampo);
                            $atributos['nombreFormulario'] = $esteBloque['nombre'];
                            $tab++;

                            // Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            //echo $this->miFormulario->campoBoton($atributos);
                            unset($atributos);

                            echo $this->miFormulario->division("fin");
                            unset($atributos);
                            // -----------------FIN CONTROL: Botón -----------------------------------------------------------
                        } else {
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
                    } else {

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

                // $valorCodificado = "action=" . $esteBloque["nombre"];

                if ($infoContrato['numero_identificacion'] != NULL) {
                    $valorCodificado = "action=" . $esteBloque["nombre"];
                } else {
                    $valorCodificado = (is_null($infoBeneficiario['id_contrato']) != true) ? "actionBloque=" . $esteBloque["nombre"] : "action=" . $esteBloque["nombre"];
                }

                $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
                $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];

                if ($infoContrato['numero_identificacion'] != NULL) {
                    $valorCodificado .= "&opcion=generarContratoPDF";
                } else {
                    $valorCodificado .= (is_null($infoBeneficiario['id_contrato']) != true) ? "&opcion=mostrarContrato" : "&opcion=cargarRequisitos";
                }

                $valorCodificado .= "&tipo=" . $infoBeneficiario['tipo_beneficiario'];
                $valorCodificado .= "&id_beneficiario=" . $_REQUEST['id_beneficiario'];
                if (is_null($infoBeneficiario['id_contrato']) != true) {
                    $valorCodificado .= "&numero_contrato=" . $infoBeneficiario['numero_contrato'];
                }

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
        // var_dump($_REQUEST);
        switch ($_REQUEST['mensaje']) {
            case 'inserto':
                $estilo_mensaje = 'success';     // information,warning,error,validation
                // $atributos["mensaje"] = 'Requisitos Correctamente Subidos<br>Se ha Habilitado la Opcion de ver Contrato';
                $atributos["mensaje"] = 'Requisitos Correctamente Subidos. <br>Proceder a Validar';

                if (isset($_REQUEST['alfresco'])) {
                    $atributos["mensaje"] .= 'Errores de Gestor Documental:' . $_REQUEST['alfresco'];
                }
                break;

            case 'noinserto':
                $estilo_mensaje = 'error';     // information,warning,error,validation
                $atributos["mensaje"] = 'Error al validar los Requisitos.<br>Verifique los Documentos de Requisitos';
                break;

            case 'insertoInformacionContrato':
                $estilo_mensaje = 'success';     // information,warning,error,validation
                $atributos["mensaje"] = 'Se ha registrado la información de contrato con exito.<br>Habilitado la Opcion de Descargar Contrato';
                break;

            case 'noInsertoInformacionContrato':
                $estilo_mensaje = 'error';     // information,warning,error,validation
                $atributos["mensaje"] = 'Error al registrar información del contrato';
                break;

            case 'verifico':
                $estilo_mensaje = 'success';     // information,warning,error,validation
                // $atributos["mensaje"] = 'Requisitos Correctamente Subidos<br>Se ha Habilitado la Opcion de ver Contrato';
                $atributos["mensaje"] = 'Documento Verificado';
                break;

            case 'noverifico':
                $estilo_mensaje = 'warning';     // information,warning,error,validation
                // $atributos["mensaje"] = 'Requisitos Correctamente Subidos<br>Se ha Habilitado la Opcion de ver Contrato';
                $atributos["mensaje"] = 'Atención, fallo en actualización.';
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
function array_column($input = null, $columnKey = null, $indexKey = null)
{
	// Using func_get_args() in order to check for proper number of
	// parameters and trigger errors exactly as the built-in array_column()
	// does in PHP 5.5.
	$argc = func_num_args();
	$params = func_get_args();
	if ($argc < 2) {
		trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
		return null;
	}
	if (!is_array($params[0])) {
		trigger_error(
				'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
				E_USER_WARNING
				);
		return null;
	}
	if (!is_int($params[1])
			&& !is_float($params[1])
			&& !is_string($params[1])
			&& $params[1] !== null
			&& !(is_object($params[1]) && method_exists($params[1], '__toString'))
			) {
				trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
				return false;
			}
			if (isset($params[2])
					&& !is_int($params[2])
					&& !is_float($params[2])
					&& !is_string($params[2])
					&& !(is_object($params[2]) && method_exists($params[2], '__toString'))
					) {
						trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
						return false;
					}
					$paramsInput = $params[0];
					$paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
					$paramsIndexKey = null;
					if (isset($params[2])) {
						if (is_float($params[2]) || is_int($params[2])) {
							$paramsIndexKey = (int) $params[2];
						} else {
							$paramsIndexKey = (string) $params[2];
						}
					}
					$resultArray = array();
					foreach ($paramsInput as $row) {
						$key = $value = null;
						$keySet = $valueSet = false;
						if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
							$keySet = true;
							$key = (string) $row[$paramsIndexKey];
						}
						if ($paramsColumnKey === null) {
							$valueSet = true;
							$value = $row;
						} elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
							$valueSet = true;
							$value = $row[$paramsColumnKey];
						}
						if ($valueSet) {
							if ($keySet) {
								$resultArray[$key] = $value;
							} else {
								$resultArray[] = $value;
							}
						}
					}
					return $resultArray;
} 


$miSeleccionador = new Registrador($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->seleccionarForm();

?>
