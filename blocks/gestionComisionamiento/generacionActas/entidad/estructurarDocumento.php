<?php

namespace gestionComisionamiento\generacionActas\entidad;

include_once 'Redireccionador.php';

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";

class GenerarDocumento {
    public $miConfigurador;
    public $agendamientos = NULL;
    public $miSql;
    public $conexion;
    public $contenidoPagina = '';
    public $rutaURL;
    public $rutaAbsoluta;
    public $ruta_dir;
    public $ruta_dir_actas;
    public $nombre_dir_actas;
    public $nombre_archivo_zip;
    public $rutaURLArchivo;
    public $agendamiento_particular;
    public $esteRecursoDB;
    public function __construct($sql, $agendamientos) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->agendamientos = $agendamientos;
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }

        /**
         *  1. Eliminar archivos .zip no funcionales de la fecha actual
         **/

        $this->eliminarArchivosNoFuncionales();
        /**
         *  2. EstructurarDocumento de acuerdo a # Agendamienntos
         **/

        $this->estructurarActas();

    }

    public function eliminarArchivosNoFuncionales() {

        $anio = date("Y");
        $mes = date("m");
        $dia = date("d");

        $tiempo = mktime(0, 0, 0, $mes, $dia, $anio);

        $ruta_directorio = $this->rutaAbsoluta . "/entidad/directorio_actas";

        $this->eliminarContenido($ruta_directorio, $tiempo);

    }

    public function eliminarContenido($rutaAnalizar, $tiempo_actual) {
        foreach (glob($rutaAnalizar . "/*") as $archivos_carpeta) {

            $archivo = end(explode("/", $archivos_carpeta));

            $tiempo_archivo = reset(explode(".", $archivo));

            if (!is_dir($archivos_carpeta) && $tiempo_archivo < $tiempo_actual) {
                unlink($archivos_carpeta);
            }
        }

    }
    public function estructurarActas() {

        /**
         * 1. Crear Directorio Actas
         **/
        $this->rutaURLArchivo = $this->rutaURL . "/entidad/directorio_actas";
        $this->ruta_dir = $this->rutaAbsoluta . "/entidad/directorio_actas";

        $this->nombre_dir_actas = "actas" . time();
        $this->ruta_dir_actas = $this->ruta_dir . "/" . $this->nombre_dir_actas;

        mkdir($this->ruta_dir_actas, 0777, true);
        chmod($this->ruta_dir_actas, 0777);

        /**
         * 2. Generar Actas
         **/
        $this->generarActas();

        /**
         * 3. Comprimir Directorio
         **/
        $this->nombre_archivo_zip = $this->comprimir($this->ruta_dir, $this->nombre_dir_actas, $this->nombre_dir_actas);

        /**
         * 4. Eliminar Archivos No Necesarios
         **/
        $this->eliminarDirectorioContenido($this->ruta_dir_actas);

        /**
         * 4. Redireccionar
         **/

        $arreglo = array(
            "nombre_archivo" => $this->nombre_archivo_zip,
            "rutaUrl" => $this->rutaURLArchivo . "/" . $this->nombre_archivo_zip,
        );

        if (file_exists($this->ruta_dir . "/" . $this->nombre_archivo_zip)) {

            Redireccionador::redireccionar('archivoGenerado', $arreglo);
        } else {

            Redireccionador::redireccionar('archivoNoGenerado');
        }

    }

    public function eliminarDirectorioContenido($rutaAnalizar) {
        foreach (glob($rutaAnalizar . "/*") as $archivos_carpeta) {
            if (is_dir($archivos_carpeta)) {

                $valorContenido = @scandir($archivos_carpeta);

                if (count($valorContenido) == 2) {

                    rmdir($archivos_carpeta);
                } else {

                    $this->eliminarDirectorioContenido($archivos_carpeta);
                }
            } else {
                unlink($archivos_carpeta);
            }
        }
        rmdir($rutaAnalizar);
    }

    public function comprimir($rutaObjetivoContenido, $nombreComprimido, $nombreDirectorioComprimir, $rutaSalidaComprimido = '') {

        $ruta_actual = getcwd();

        chdir($rutaObjetivoContenido);

        $nombre_archivo = time() . ".zip";

        $cadena = "zip " . $rutaSalidaComprimido . $nombre_archivo . " " . $nombreDirectorioComprimir . "/*";

        $queries = exec($cadena);

        chdir($ruta_actual);

        return $nombre_archivo;

    }

    public function generarActas() {

        if (isset($this->agendamientos) && $this->agendamientos != false) {

            foreach ($this->agendamientos as $key => $value) {

                $this->agendamiento_particular = $value;
                $this->contenidoPagina = $this->estruturaDocumento();

                $this->crearPDF();
            }

        } else {
            Redireccionador::redireccionar('archivoNoGenerado');
        }

    }

    public function crearPDF() {
        ob_start();
        $html2pdf = new \HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output($this->ruta_dir_actas . '/ActaEntregaComisionamiento' . time() . '.pdf', 'F');

    }

    public function crearUrlDetalleProyectos($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . time();
        $variable .= "&metodo=proyectosDetalle";
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }

    public function crearUrlPaquetesTrabajo($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . time();
        $variable .= "&metodo=paquetesTrabajo";
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }

    public function estruturaDocumento() {

        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionBeneficiario', $this->agendamiento_particular);
        $informacion_beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $informacion_beneficiario = $informacion_beneficiario[0];

        $localizacion = explode(",", $informacion_beneficiario['geolocalizacion']);

        $localizacion[0] = trim($localizacion[0]);
        $localizacion[1] = trim($localizacion[1]);

        /**
         * Calculo Latitud GMS
         **/
        $latitud = $localizacion[0];

        $latitud_grados = reset(explode(".", $latitud));

        $latitud_minutos_dc = (((($latitud - $latitud_grados) * 60) < 0) ? (($latitud - $latitud_grados) * 60) * -1 : (($latitud - $latitud_grados) * 60));

        $latitud_minutos = reset(explode(".", $latitud_minutos_dc));

        $latitud_segundos = (($latitud_minutos_dc - $latitud_minutos) * 60 < 0) ? ($latitud_minutos_dc - $latitud_minutos) * 60 * -1 : ($latitud_minutos_dc - $latitud_minutos) * 60;

        /**
         * Calculo longitud GMS
         **/
        $longitud = $localizacion[1];

        $longitud_grados = reset(explode(".", $longitud));

        $longitud_minutos_dc = (((($longitud - $longitud_grados) * 60) < 0) ? (($longitud - $longitud_grados) * 60) * -1 : (($longitud - $longitud_grados) * 60));

        $longitud_minutos = reset(explode(".", $longitud_minutos_dc));

        $longitud_segundos = (($longitud_minutos_dc - $longitud_minutos) * 60 < 0) ? ($longitud_minutos_dc - $longitud_minutos) * 60 * -1 : ($longitud_minutos_dc - $longitud_minutos) * 60;

        /**
         * Consultar Proyecto Beneficiario
         **/

        $urlProyecto = $this->crearUrlDetalleProyectos($informacion_beneficiario['id_proyecto']);

        $proyecto = file_get_contents($urlProyecto);
        $proyecto = json_decode(($proyecto), true);

        $validacion_tipo_tecnologia = strpos($proyecto['identifier'], 'wman');

        if (is_numeric($validacion_tipo_tecnologia)) {

            $wman = "X";
            $hfc = " ";
        } else {

            $wman = " ";
            $hfc = "X";

        }

        //var_dump($informacion_beneficiario);

        //var_dump($this->agendamiento_particular);

        $urlPaquetesTrabajo = $this->crearUrlPaquetesTrabajo($informacion_beneficiario['id_proyecto']);

        $paquetesTrabajo = file_get_contents($urlPaquetesTrabajo);
        $paquetesTrabajo = json_decode(($paquetesTrabajo), true);

        //var_dump($paquetesTrabajo);exit;

        foreach ($paquetesTrabajo as $key => $value) {
            if ($value['type_id'] === 3) {
                $paqueteVelocidad = $value;

            }
        }

        $info_conexion = array(
            "direccion_ip" => "",
            "mac_wan" => "",
            "marcara_sub_red" => "",
            "gateway" => "",
            "servidor_dns" => "",
            "cus" => "",
            "tracert_navegabilidad" => "",
            "tracert_cumplimiento" => "",
            "ping_mintic" => "",
            "ping_nasa" => "",
            "ping_tiempo" => "",
            "ping_gmail" => "",
            "vl_sb_speed" => "",
            "tm_sb_speed" => "",
            "vl_bj_speed" => "",
            "tm_bj_speed" => "",
            "tm_bj_performance" => "",
            "tm_sb_performance" => "",
            "vl_bj_performance" => "",
            "vl_sb_performance" => "",

        );

if(isset($paqueteVelocidad)){
        foreach ($paqueteVelocidad as $key => $value) {

            switch ($key) {
                case 'cf_50':
                    $info_conexion['direccion_ip'] = $value;
                    break;

                case 'cf_51':
                    $info_conexion['mac_wan'] = $value;
                    break;

                case 'cf_52':
                    $info_conexion['marcara_sub_red'] = $value;
                    break;

                case 'cf_53':
                    $info_conexion['gateway'] = $value;
                    break;

                case 'cf_54':
                    $info_conexion['servidor_dns'] = $value;
                    break;

                case 'cf_55':
                    $info_conexion['cus'] = $value;
                    break;

                case 'cf_56':
                    $info_conexion['tracert_navegabilidad'] = $value;
                    break;

                case 'cf_57':
                    $info_conexion['tracert_cumplimiento'] = $value;
                    break;

                case 'cf_58':
                    $info_conexion['ping_mintic'] = $value;
                    break;

                case 'cf_59':
                    $info_conexion['ping_nasa'] = $value;
                    break;

                case 'cf_60':
                    $info_conexion['ping_tiempo'] = $value;
                    break;

                case 'cf_61':
                    $info_conexion['ping_gmail'] = $value;
                    break;

                case 'cf_62':
                    $info_conexion['vl_sb_speed'] = $value;
                    break;

                case 'cf_63':
                    $info_conexion['tm_sb_speed'] = $value;
                    break;

                case 'cf_64':
                    $info_conexion['vl_bj_speed'] = $value;
                    break;

                case 'cf_65':
                    $info_conexion['tm_bj_speed'] = $value;
                    break;

                case 'cf_67':
                    $info_conexion['tm_bj_performance'] = $value;
                    break;

                case 'cf_68':
                    $info_conexion['tm_sb_performance'] = $value;
                    break;

                case 'cf_69':
                    $info_conexion['vl_bj_performance'] = $value;
                    break;

                case 'cf_70':
                    $info_conexion['vl_sb_performance'] = $value;
                    break;

            }}
        }

//        var_dump($info_conexion);
        //      exit;
        $contenidoPagina = "    <style type=\"text/css\">
                                table {

                                    font-family:Helvetica, Arial, sans-serif; /* Nicer font */

                                    border-collapse:collapse; border-spacing: 3px;
                                }
                                td, th {
                                    border: 1px solid #CCC;
                                    height: 13px;
                                } /* Make cells a bit taller */

                                th {

                                    font-weight: bold; /* Make sure they're bold */
                                    text-align: center;
                                    font-size:10px;
                                }
                                td {

                                    text-align: left;

                                }
                            </style>



                        <page backtop='35mm' backbottom='10mm' backleft='5mm' backright='5mm' footer='page'>
                            <page_header>
                            <br>
                            <br>
                                    <table  style='width:100%;' >
                                        <tr>
                                            <td rowspan='3' style='width:33.3%;text-align=center;'><img src='" . $this->rutaURL . "frontera/css/imagenes/politecnica.png'  width='125' height='40'></td>
                                            <td rowspan='3' style='width:33.3%;text-align=center;'><b>RECIBO A SATISFACCIÓN DE LA INSTALACIÓN Y PUESTA EN SERVICIO PARA ACCESOS VIP Y ESTRATOS 1 Y 2</b></td>
                                            <td align='center' style='width:33.3%;'>CODIGO: CPN-FO-CDII-63</td>
                                        </tr>

                                        <tr>
                                             <td align='center' style='width:33.3%;'>VERSIÓN: 01</td>
                                        </tr>
                                        <tr>
                                             <td align='center' style='width:33.3%;'>FECHA: " . date('Y-m-d') . "</td>
                                        </tr>
                                    </table>

                        </page_header>";

        $contenidoPagina .= "<table  style='width:100%;border:none;' >
                                        <tr>
                                            <td align='center' style='width:12%;border:none;border-right:#CCC;'>Consecutivo</td>
                                            <td align='center' style='width:10%;'> </td>
                                            <td align='center' style='width:13%;border:none;border-right:#CCC;'>Id del Nodo</td>
                                            <td align='center' style='width:15%;'>" . $this->agendamiento_particular['codigo_nodo'] . "</td>
                                            <td align='center' style='width:20%;border:none;border-right:#CCC;'>Fecha de Comisionamiento</td>
                                            <td align='center' style='width:10%;'>DD</td>
                                            <td align='center' style='width:10%;'>MM</td>
                                            <td align='center' style='width:10%;'>AAAA</td>
                                        </tr>
                                    </table>
                                    <br>
                                    <table  style='width:100%;' >
                                        <tr>
                                            <td align='center' style='width:100%;'>1. INFORMACIÓN GENERAL</td>
                                        </tr>
                                        <tr>
                                            <td align='center' style='width:100%;'>

                                                <table style='width:100%;'>
                                                    <tr>
                                                        <td colspan='4' style='width:100%;border:none;'>1.1 Lugar de instalación del equipo:<br>       </td>
                                                    </tr>
                                                    <tr>
                                                        <td style='width:100%;border:none;'>
                                                            <br>
                                                            <table width:100%;>
                                                            <tr>
                                                                <td style='width:25%;border:none;border-right:#CCC;'>Departamento de instalación:</td>
                                                                <td align='center' style='width:25%;border:#CCC;'>" . $informacion_beneficiario['nombre_dp'] . "</td>
                                                                <td style='width:20%;border:none;border-right:#CCC;'>&nbsp;Municipio o Ciudad:</td>
                                                                <td align='center' style='width:25%;border:#CCC;'>" . $informacion_beneficiario['nombre_mn'] . "</td>
                                                                <td align='center' style='width:5%;border:none;'> </td>
                                                            </tr>
                                                            <tr>
                                                                <td style='width:25%;border:none;border-right:#CCC;'>Codigo DANE:</td>
                                                                <td align='center' style='width:25%;border:#CCC;'>" . $informacion_beneficiario['codigo_dane_mn'] . "</td>
                                                                <td style='width:20%;border:none;border-right:#CCC;'>&nbsp;Localidad o Barrio:</td>
                                                                <td align='center' style='width:25%;border:#CCC;'> </td>
                                                                <td align='center' style='width:5%;border:none;'> </td>
                                                            </tr>
                                                            <tr>
                                                                <td style='width:25%;border:none;border-right:#CCC;'>Dirección de instalación:</td>
                                                                <td align='center' style='width:25%;border:#CCC;'>" . $informacion_beneficiario['direccion'] . "</td>
                                                                <td style='width:20%;border:none;border-right:#CCC;'>&nbsp;Nombre del Proyecto:</td>
                                                                <td align='center' style='width:25%;border:#CCC;'>" . $informacion_beneficiario['proyecto'] . "</td>
                                                                <td align='center' style='width:5%;border:none;'> </td>
                                                            </tr>
                                                            </table>
                                                            <br>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan='4' style='width:100%;border:none;'>Coordenadas del lugar de instalación: </td>
                                                    </tr>

                                                    <tr>
                                                        <td style='width:100%;border:none;'>
                                                            <br>
                                                            <table width:100%;>
                                                                <tr>
                                                                    <td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Latitud: (grados)</td>
                                                                    <td align='center' style='width:12.5%;border:#CCC;'>" . $latitud_grados . "°</td>
                                                                    <td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Latitud: (minutos)</td>
                                                                    <td align='center' style='width:12.5%;border:#CCC;'>" . $latitud_minutos . "'</td>
                                                                    <td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Latitud: (segundos)</td>
                                                                    <td align='center' style='width:12.5%;border:#CCC;font-size:8px;'>" . $latitud_segundos . "''</td>
                                                                    <td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Dirección:</td>
                                                                    <td align='center' style='width:12.5%;border:#CCC;'> </td>
                                                                </tr>
                                                                <tr>
                                                                    <td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Longitud: (grados)</td>
                                                                    <td align='center' style='width:12.5%;border:#CCC;'>" . $longitud_grados . "°</td>
                                                                    <td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Longitud: (minutos)</td>
                                                                    <td align='center' style='width:12.5%;border:#CCC;'>" . $longitud_minutos . "'</td>
                                                                    <td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Longitud: (segundos)</td>
                                                                    <td align='center' style='width:12.5%;border:#CCC;font-size:8px;'>" . $longitud_segundos . "''</td>
                                                                    <td align='center' style='width:12.5%;border:none;border-right:#CCC;'>Dirección:</td>
                                                                    <td align='center' style='width:12.5%;border:#CCC;'> </td>
                                                                </tr>
                                                            </table>
                                                            <br>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align='center' style='width:100%;'>2. INFORMACIÓN TÉCNICO QUE COMISIONA</td>
                                        </tr>
                                        <tr>
                                            <td style='width:100%;'>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:20%;border:none;border-right:#CCC;'>Nombre del Técnico:</td>
                                                                        <td align='center' style='width:30%;border:#CCC;'>" . $this->agendamiento_particular['nombre_comisionador'] . "</td>
                                                                        <td style='width:20%;border:none;border-right:#CCC;'>&nbsp;Empresa Contratista:</td>
                                                                        <td align='center' style='width:25%;border:#CCC;'>POLITÉCNICA</td>
                                                                        <td align='center' style='width:5%;border:none;'> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style='width:20%;border:none;border-right:#CCC;'>Telefono de Contacto:</td>
                                                                        <td align='center' style='width:30%;border:#CCC;'> </td>
                                                                        <td style='width:20%;border:none;border-right:#CCC;'>&nbsp;E-mail:</td>
                                                                        <td align='center' style='width:25%;border:#CCC;'> </td>
                                                                        <td align='center' style='width:5%;border:none;'> </td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align='center' style='width:100%;'>3. TIPO DE TECNOLOGÍA</td>
                                        </tr>
                                        <tr>
                                            <td style='width:100%;'>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td align='right' style='width:33%;border:none;border-right:#CCC;'>HFC:</td>
                                                                        <td align='center' style='width:10%;border:#CCC;'>" . $hfc . "</td>
                                                                        <td align='right' style='width:20%;border:none;border-right:#CCC;'>&nbsp;WMAN:</td>
                                                                        <td align='center' style='width:10%;border:#CCC;'>" . $wman . "</td>
                                                                        <td align='center' style='width:10%;border:none;'> </td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align='center' style='width:100%;'>4. INFORMACIÓN DE EQUIPOS</td>
                                        </tr>
                                        <tr>
                                            <td style='width:100%;'>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:20%;border:none;'>Serial del EOC:</td>
                                                                        <td align='center' style='width:30%;border:none;border-bottom:#CCC;'> </td>
                                                                        <td style='width:20%;border:none;'>&nbsp;Serial Esclavo:</td>
                                                                        <td align='center' style='width:25%;border:none;border-bottom:#CCC;'> </td>
                                                                        <td align='center' style='width:5%;border:none;'> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style='width:20%;border:none;'>Mac del EOC:</td>
                                                                        <td align='center' style='width:30%;border:none;border-bottom:#CCC;'> </td>
                                                                        <td style='width:20%;border:none;'>&nbsp;Mac del Esclavo:</td>
                                                                        <td align='center' style='width:25%;border:none;border-bottom:#CCC;'> </td>
                                                                        <td align='center' style='width:5%;border:none;'> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style='width:20%;border:none;'>IP del EOC:</td>
                                                                        <td align='center' style='width:30%;border:none;border-bottom:#CCC;'> </td>
                                                                        <td style='width:20%;border:none;'>&nbsp;IP del Esclavo:</td>
                                                                        <td align='center' style='width:25%;border:none;border-bottom:#CCC;'> </td>
                                                                        <td align='center' style='width:5%;border:none;'> </td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align='center' style='width:100%;'>5. RELACIÓN DE EQUIPOS INSTALADOS</td>
                                        </tr>
                                        <tr>
                                            <td style='width:100%;'>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td colspan='2' align='center'style='width:20%;'>EQUIPO(PC, Antena, Router,entre otros)</td>
                                                                        <td align='center'style='width:20%;'>MAC</td>
                                                                        <td align='center'style='width:20%;'>SERIAL</td>
                                                                        <td align='center'style='width:20%;'>MARCA</td>
                                                                        <td align='center'style='width:20%;'>MODELO</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:5%;'>1</td>
                                                                        <td align='center'style='width:15%;'> </td>
                                                                        <td align='center'style='width:20%;'> </td>
                                                                        <td align='center'style='width:20%;'> </td>
                                                                        <td align='center'style='width:20%;'> </td>
                                                                        <td align='center'style='width:20%;'> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:5%;'>2</td>
                                                                        <td align='center'style='width:15%;'> </td>
                                                                        <td align='center'style='width:20%;'> </td>
                                                                        <td align='center'style='width:20%;'> </td>
                                                                        <td align='center'style='width:20%;'> </td>
                                                                        <td align='center'style='width:20%;'> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:5%;'>3</td>
                                                                        <td align='center'style='width:15%;'> </td>
                                                                        <td align='center'style='width:20%;'> </td>
                                                                        <td align='center'style='width:20%;'> </td>
                                                                        <td align='center'style='width:20%;'> </td>
                                                                        <td align='center'style='width:20%;'> </td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                            </td>
                                        </tr>
                                        <tr>
                                        <td style='width:100%;'>5.1 Reporte de Fallas(reportar las fallas si aplica durante el proceso de instalación):<br>&nbsp;<br>&nbsp;<br>Observaciones:<br>&nbsp;<br>&nbsp;<br>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align='center' style='width:100%;'>6. ENTREGA DE TERMINAL Marque con una X según corresponda:</td>
                                        </tr>
                                        <tr>
                                            <td style='width:100%;'>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:35%;border:none;'>Elementos que contiene la caja de entrega:</td>
                                                                        <td align='right' style='width:20%;border:none;border-right:#CCC;'>Computador</td>
                                                                        <td align='center' style='width:4%;'> </td>
                                                                        <td align='center' style='width:15%;border:none;border-right:#CCC;'>&nbsp;Manual de Uso</td>
                                                                        <td align='center' style='width:4%;'> </td>
                                                                        <td align='center' style='width:10%;border:none;border-right:#CCC;'>&nbsp;Cargador</td>
                                                                        <td align='center' style='width:4%;'> </td>
                                                                        <td align='center' style='width:5%;border:none;'> </td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:35%;border:none;'>Estado físico del equipo:</td>
                                                                        <td align='right' style='width:20%;border:none;border-right:#CCC;'>Sin Rayados</td>
                                                                        <td align='center' style='width:4%;'> </td>
                                                                        <td align='center' style='width:15%;border:none;border-right:#CCC;'>Sin Golpes y/o Hendiduras</td>
                                                                        <td align='center' style='width:4%;'> </td>
                                                                        <td align='center' style='width:10%;border:none;'> </td>
                                                                        <td align='center' style='width:4%;border:none;'> </td>
                                                                        <td align='center' style='width:5%;border:none;'> </td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:35%;border:none;'>Prueba de funcionalidad:</td>
                                                                        <td align='right' style='width:20%;border:none;border-right:#CCC;'>Equipo enciende correctamente</td>
                                                                        <td align='center' style='width:4%;'> </td>
                                                                        <td align='center' style='width:15%;border:none;border-right:#CCC;'>Equipo navega en Internet</td>
                                                                        <td align='center' style='width:4%;'> </td>
                                                                        <td align='center' style='width:10%;border:none;'> </td>
                                                                        <td align='center' style='width:4%;border:none;'> </td>
                                                                        <td align='center' style='width:5%;border:none;'> </td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:35%;border:none;'>En general el estado físico y funcionalidad del equipo es:</td>
                                                                        <td align='right' style='width:20%;border:none;border-right:#CCC;'>Bueno</td>
                                                                        <td align='center' style='width:4%;'> </td>
                                                                        <td align='center' style='width:15%;border:none;border-right:#CCC;'>&nbsp;Regular</td>
                                                                        <td align='center' style='width:4%;'> </td>
                                                                        <td align='center' style='width:10%;border:none;border-right:#CCC;'>&nbsp;Malo</td>
                                                                        <td align='center' style='width:4%;'> </td>
                                                                        <td align='center' style='width:5%;border:none;'> </td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align='center' style='width:100%;'>7. PRUEBAS DE CONECTIVIDAD</td>
                                        </tr>
                                        <tr>
                                            <td style='width:100%;'>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:100%;border:none;'>7.1. Información equipo del cliente (En caso de sistema operativo Android- No aplica)</td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:20%;border:none;border-right:#CCC;'>Direccionamiento IP:</td>
                                                                        <td align='center' style='width:10%;border:#CCC;font-size:8px;'>" . $info_conexion['direccion_ip'] . "</td>
                                                                        <td style='width:15%;border:none;border-right:#CCC;'>&nbsp;Dirección MAC:</td>
                                                                        <td align='center' style='width:15%;border:#CCC;font-size:8px;'>" . $info_conexion['mac_wan'] . "</td>
                                                                        <td style='width:20%;border:none;border-right:#CCC;'>&nbsp;Máscara de Subred:</td>
                                                                        <td align='center' style='width:15%;border:#CCC;font-size:8px;'>" . $info_conexion['marcara_sub_red'] . "</td>
                                                                        <td align='center' style='width:5%;border:none;'> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style='width:20%;border:none;border-right:#CCC;'>Gateway:</td>
                                                                        <td align='center' style='width:10%;border:#CCC;font-size:8px;'>" . $info_conexion['gateway'] . "</td>
                                                                        <td style='width:15%;border:none;border-right:#CCC;'>&nbsp;Servidor DNS:</td>
                                                                        <td align='center' style='width:15%;border:#CCC;font-size:8px;'>" . $info_conexion['servidor_dns'] . "</td>
                                                                        <td style='width:20%;border:none;'> </td>
                                                                        <td align='center' style='width:15%;border:none;'> </td>
                                                                        <td align='center' style='width:5%;border:none;'> </td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:100%;border:none;'>7.2. Pruebas Tracert</td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:20%;border:none;border-right:#CCC;'> </td>
                                                                        <td align='center' style='width:30%;border:#CCC;'>LINUX</td>
                                                                        <td align='center' style='width:50%;border:none;'>tracert 'nodo'&nbsp;&nbsp;|&nbsp;&nbsp;traceroute 'nodo'</td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td align='center'style='width:25%;'>REFERENCIA</td>
                                                                        <td align='center'style='width:30%;'>DESTINO</td>
                                                                        <td align='center'style='width:10%;'>Puede Navegar</td>
                                                                        <td align='center'style='width:10%;'>CUMPLE</td>
                                                                        <td align='center'style='width:25%;'>OBSERVACIONES</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:25%;'>www.gobiernoenlinea.gov.co</td>
                                                                        <td align='center'style='width:30%;'>La página de referencia pasa por el NAP Colombia antes de pasar por cualquier destino Internacional.<br>(La página de referencia debe estar conectada al NAP)</td>
                                                                        <td align='center'style='width:10%;'>SI __<br><br>NO__</td>
                                                                        <td align='center'style='width:10%;'>SI __<br><br>NO__</td>
                                                                        <td align='center'style='width:25%;'>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br></td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:100%;border:none;'>7.3. Pruebas Windows : ping (ping -n 10 -l 512)  --- Linux : buscar herramientas de Red</td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td align='center'style='width:33.33%;'>TIPO DE CONECTIVIDAD : </td>
                                                                        <td align='center'style='width:33.33%;'>VIP</td>
                                                                        <td align='center'style='width:33.33%;'>Hogares estrato 1 y 2</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:33.33%;'>Página a realizar la prueba</td>
                                                                        <td align='center'style='width:33.33%;'>TIEMPO(ms)</td>
                                                                        <td align='center'style='width:33.33%;'>TIEMPO(ms)</td>
                                                                    </tr>";

        if ($informacion_beneficiario['tipo_beneficiario'] == 1) {

            $contenidoPagina .= "                                   <tr>
                                                                        <td align='center'style='width:33.33%;'>www.mintic.gov.co</td>
                                                                        <td align='center'style='width:33.33%;'>" . $info_conexion['ping_mintic'] . "</td>
                                                                        <td align='center'style='width:33.33%;'> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:33.33%;'>www.nasa.gov</td>
                                                                        <td align='center'style='width:33.33%;'>" . $info_conexion['ping_nasa'] . "</td>
                                                                        <td align='center'style='width:33.33%;'> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:33.33%;'>www.eltiempo.com</td>
                                                                        <td align='center'style='width:33.33%;'>" . $info_conexion['ping_tiempo'] . "</td>
                                                                        <td align='center'style='width:33.33%;'> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:33.33%;'>www.gmail.com</td>
                                                                        <td align='center'style='width:33.33%;'>" . $info_conexion['ping_gmail'] . "</td>
                                                                        <td align='center'style='width:33.33%;'> </td>
                                                                    </tr>";

        } elseif ($informacion_beneficiario['tipo_beneficiario'] == 2) {

            $contenidoPagina .= "                                   <tr>
                                                                        <td align='center'style='width:33.33%;'>www.mintic.gov.co</td>
                                                                        <td align='center'style='width:33.33%;'> </td>
                                                                        <td align='center'style='width:33.33%;'>" . $info_conexion['ping_mintic'] . "</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:33.33%;'>www.nasa.gov</td>
                                                                        <td align='center'style='width:33.33%;'> </td>
                                                                        <td align='center'style='width:33.33%;'>" . $info_conexion['ping_nasa'] . "</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:33.33%;'>www.eltiempo.com</td>
                                                                        <td align='center'style='width:33.33%;'> </td>
                                                                        <td align='center'style='width:33.33%;'>" . $info_conexion['ping_tiempo'] . "</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:33.33%;'>www.gmail.com</td>
                                                                        <td align='center'style='width:33.33%;'> </td>
                                                                        <td align='center'style='width:33.33%;'>" . $info_conexion['ping_gmail'] . "</td>
                                                                    </tr>";

        }

        $contenidoPagina .= "                                   </table>
                                                                <br>
                                                                <br>
                                            </td>
                                        </tr>
                                  </table>
                                  <table>
                                        <tr>
                                            <td>
                                                            <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:100%;border:none;'>7.4. Pruebas de desempeño Velocidad (speed test)(Aplica para VIP y Hogares estrato 1 y 2).<br>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Recordar que 4048 Kbps es igual a 2Mbps.</td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td align='center'style='width:25%;'>Página a realizar la prueba(speed test)</td>
                                                                        <td align='center'style='width:15%;'>Vel mínima (Kbps)</td>
                                                                        <td align='center'style='width:20%;'>Velocidad medición</td>
                                                                        <td align='center'style='width:20%;'>TIEMPO (Seg)</td>
                                                                        <td align='center'style='width:20%;'>Velocidad (Mbps)</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td rowspan='2' align='center'style='width:25%;'>http://www.speedtest.net/</td>
                                                                        <td align='center'style='width:15%;'>4048</td>
                                                                        <td align='center'style='width:20%;'>DOWNLOAD</td>
                                                                        <td align='center'style='width:20%;'>" . $info_conexion['tm_bj_speed'] . "</td>
                                                                        <td align='center'style='width:20%;'>" . $info_conexion['vl_bj_speed'] . "</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:15%;'>1024</td>
                                                                        <td align='center'style='width:20%;'>UPLOAD</td>
                                                                        <td align='center'style='width:20%;'>" . $info_conexion['tm_sb_speed'] . "</td>
                                                                        <td align='center'style='width:20%;'>" . $info_conexion['vl_sb_speed'] . "</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td rowspan='2' align='center'style='width:25%;'>http://performance.toast.net/</td>
                                                                        <td align='center'style='width:15%;'>4048</td>
                                                                        <td align='center'style='width:20%;'>DOWNLOAD</td>
                                                                        <td align='center'style='width:20%;'>" . $info_conexion['tm_bj_performance'] . "</td>
                                                                        <td align='center'style='width:20%;'>" . $info_conexion['vl_bj_performance'] . "</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align='center'style='width:15%;'>1024</td>
                                                                        <td align='center'style='width:20%;'>UPLOAD</td>
                                                                        <td align='center'style='width:20%;'>" . $info_conexion['tm_sb_performance'] . "</td>
                                                                        <td align='center'style='width:20%;'>" . $info_conexion['vl_sb_performance'] . "</td>
                                                                    </tr>
                                                                </table>
                                                                <table width:100%;>
                                                                    <tr>
                                                                        <td style='width:100%;'>* Nota: Señor Técnico favor realizar los speed test con dos paginas diferentes, tomar registro fotografico, adicionar fotografia del equipo navegando en www.mintic.gov.co</td>
                                                                    </tr>
                                                                </table>
                                                                <br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align='center' style='width:100%;'>8. DECLARACIÓN DE NO INTERNET</td>
                                        </tr>
                                        <tr>
                                            <td align='justify' style='width:100%;'>
                                                <br>
                                                Fecha:<br>
                                                Ciudad,<br>
                                                <br>
                                                <br>
                                                Yo______________________________ identificado(a) con cedula de ciudadanía N°.___________ de_______________ en mi calidad de beneficiario(a) del Proyecto Conexiones Digitales II Redes de Acceso última milla para la masificación de accesos de banda ancha en viviendas de interés prioritario y hogares en estratos 1 y 2 - Ministerio de las Tecnologías de la Información y las Comunicaciones, por medio de la presente declaro inequívocamente que no he contratado los servicios de internet en los últimos seis (6) meses.
                                                Como constancia se firma a los _________ días del mes de ______________ del año ______ en la ciudad de ________________.<br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                ____________________________<br>
                                                Firma Beneficiario<br>
                                                Nombre:&nbsp;&nbsp;" . $informacion_beneficiario['nombre'] . " " . $informacion_beneficiario['primer_apellido'] . " " . $informacion_beneficiario['segundo_apellido'] . "<br>
                                                CC:&nbsp;&nbsp;" . $informacion_beneficiario['identificacion'] . "<br>
                                                No. Celular:<br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                            </td>
                                        </tr>
                                   </table>
                                   <br>
                                   <table>
                                        <tr>
                                            <td align='center' style='width:100%;'>9. SERVICIO AL CLIENTE</td>
                                        </tr>
                                        <tr>
                                            <td align='justify' style='width:100%;'>
                                                <br>
                                                Recuerde que cualquier inquietud sobre las funcionalidades del servicio, soporte,  los términos y condiciones, así como las peticiones, quejas o reclamos, serán atendidos en los siguientes canales:<br>
                                                Telefónicamente: línea gratuita nacional 018000961016<br>
                                                Portal Web: http://conexionesdigitales.politecnica.edu.co/.<br>
                                                Correo: soportecd2@soygenial.co.<br>
                                                <br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table width:100%;>
                                                    <tr>
                                                        <td align='center' style='width:100%;border:none;'><b>EL CLIENTE MANIFIESTA QUE ESTÁ CONFORME CON LA INSTALACIÓN REALIZADA Y QUE SE DEJA EL LUGAR EN ADECUADO ESTADO DE ORDEN Y ASEO.</b></td>
                                                    </tr>
                                                </table>
                                                <br>
                                                <table width:100%;>
                                                    <tr>
                                                        <td colspan='2' align='center'style='width:45%;'>RECIBI A SATISFACCIÓN</td>
                                                        <td align='center'style='width:10%;border:none;border-right:#CCC;'> </td>
                                                        <td colspan='2' align='center'style='width:45%;'>INFORMACIÓN TECNICO</td>
                                                    </tr>
                                                    <tr>
                                                        <td rowspan='4' align='center'style='width:23%;color:#c5c5c5;'>FIRMA DEL CLIENTE</td>
                                                        <td align='center'style='width:22%;color:#c5c5c5;'>Nombre</td>
                                                        <td align='center'style='width:10%;border:none;border-right:#CCC;'> </td>
                                                        <td rowspan='4' align='center'style='width:23%;color:#c5c5c5;'>FIRMA DEL TECNICO</td>
                                                        <td align='center'style='width:22%;color:#c5c5c5;'>Nombre</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'style='width:22%;color:#c5c5c5;'>Numero de Identificación</td>
                                                        <td align='center'style='width:10%;border:none;border-right:#CCC;'> </td>
                                                        <td align='center'style='width:22%;color:#c5c5c5;'>Numero de Identificación</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'style='width:22%;color:#c5c5c5;'>Cargo</td>
                                                        <td align='center'style='width:10%;border:none;border-right:#CCC;'> </td>
                                                        <td align='center'style='width:22%;color:#c5c5c5;'>Cargo</td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'style='width:22%;color:#c5c5c5;'>Número celular</td>
                                                        <td align='center'style='width:10%;border:none;border-right:#CCC;'> </td>
                                                        <td align='center'style='width:22%;color:#c5c5c5;'>Número celular</td>
                                                    </tr>
                                                </table>
                                                <table>
                                                    <tr>
                                                        <td align='justify'style='width:100%;'>
                                                        <br>
                                                        Con la firma del presente documento EL CLIENTE reconoce y acepta que todo lo manifestado en el es cierto y que en tal sentido está satisfecho con la información brindada por la Corporación Politécnica Nacional de Colombia.<br><br>
                                                        EL CLIENTE se compromete a informar oportunamente a la Corporación Politécnica Nacional de Colombia. sobre cualquier daño, pérdida o afectación de los equipos antes mencionados.<br><br>
                                                        El CLIENTE acepta y reconoce que a la fecha a consultado o ha sido informado por la Corporación Politécnica Nacional de Colombia sobre las condiciones mínimas requeridas para los equipos necesarios para hacer uso de los servicios contratados.<br><br>
                                                        En caso de que el CLIENTE desee efectuar la devolución de equipos instalados por la Corporación Politécnica Nacional de Colombia para la prestación del servicio, podrá comunicarse a la línea gratuita de atención nacional.<br><br>
                                                        El CLIENTE debe tener en cuenta que existen riesgos sobre la seguridad de la red y de los servicios contratados los cuales son los siguientes:
                                                        <br><br>a. Riesgos relacionados con fraudes electrónicos, Riesgos relacionados con la información, Riesgos relacionados con las actividades económicas, Riesgos relacionados con el funcionamiento del Internet y Riesgos relacionados con hábitos adictivos.<br>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align='center'style='width:100%;'>DOCUMENTO CONTROLADO</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                   </table>


                                  ";

        $contenidoPagina .= " </page> ";

        return $contenidoPagina;
    }
}
$miDocumento = new GenerarDocumento($this->miSql, $this->agendamientos);

?>
