<?php
namespace reportes\actaEntregaServicios\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";

class GenerarDocumento {
    public $miConfigurador;
    public $elementos;
    public $miSql;
    public $conexion;
    public $contenidoPagina;
    public $rutaURL;
    public $esteRecursoDB;
    public $clausulas;
    public $beneficiario;
    public $esteRecursoOP;
    public $rutaAbsoluta;
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexion = "openproject";
        $this->esteRecursoOP = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

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
         *  1. Estruturar Documento
         **/

        $this->estruturaDocumento();

        /**
         *  2. Crear PDF
         **/

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
        $this->rutaURL .= '/archivos/actas_entrega_servicios/';
        $this->rutaAbsoluta .= '/archivos/actas_entrega_servicios/';
        $this->asosicarCodigoDocumento();

        $this->crearPDF();

        $arreglo = array(
            'nombre_contrato' => $this->nombreDocumento,
            'ruta_contrato' => $this->rutaURL . $this->nombreDocumento);

        $cadenaSql = $this->miSql->getCadenaSql('registrarDocumentoCertificado', $arreglo);

        $this->registro_certificado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

        $arreglo = array(
            'id_beneficiario' => $_REQUEST['id_beneficiario'],
            'tipologia' => "555",
            'nombre_documento' => $this->nombreDocumento,
            'ruta_relativa' => $this->rutaURL . $this->nombreDocumento,
        );

        //$cadenaSql = $this->miSql->getCadenaSql('registrarRequisito', $arreglo);
        //$this->registroRequisito = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
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
        $html2pdf->Output($this->rutaAbsoluta . $this->nombreDocumento, 'F');

    }

    public function asosicarCodigoDocumento() {

        $this->prefijo = substr(md5(uniqid(time())), 0, 6);
        $cadenaSql = $this->miSql->getCadenaSql('consultarParametro', '900');
        $id_parametro = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
        $tipo_documento = $id_parametro['id_parametro'];
        $descripcion_documento = $id_parametro['id_parametro'] . '_' . $id_parametro['descripcion'];
        $nombre_archivo = "AES";
        $this->nombreDocumento = $_REQUEST['id_beneficiario'] . "_" . $nombre_archivo . "_" . $this->prefijo . '.pdf';
    }

    public function estruturaDocumento() {
/*
$cadenaSql = $this->miSql->getCadenaSql('consultaNombreProyecto', $this->beneficiario['urbanizacion']);
$urbanizacion = $this->esteRecursoOP->ejecutarAcceso($cadenaSql, "busqueda");
$urbanizacion = $urbanizacion[0];
 */
        $archivo_datos = '';
        foreach ($_FILES as $key => $archivo) {

            if ($archivo['error'] == 0) {

                $this->prefijo = substr(md5(uniqid(time())), 0, 6);
                /*
                 * obtenemos los datos del Fichero
                 */
                $tamano = $archivo['size'];
                $tipo = $archivo['type'];
                $nombre_archivo = str_replace(" ", "", $archivo['name']);
                /*
                 * guardamos el fichero en el Directorio
                 */
                $ruta_absoluta = $this->rutaAbsoluta . "/entidad/firmas/" . $this->prefijo . "_" . $nombre_archivo;

                $ruta_relativa = $this->rutaURL . "/entidad/firmas/" . $this->prefijo . "_" . $nombre_archivo;

                $archivo['rutaDirectorio'] = $ruta_absoluta;

                if (!copy($archivo['tmp_name'], $ruta_absoluta)) {

                }

                $archivo_datos = array(
                    'ruta_archivo' => $ruta_relativa,
                    'nombre_archivo' => $archivo['name'],
                    'campo' => $key,
                );
            }

        }

        {
        		
        	{
        		$firmaBeneficiario = base64_decode ( $_REQUEST ['firmaBeneficiario'] );
        		$firmaBeneficiario = str_replace ( "image/svg+xml,", '', $firmaBeneficiario );
        		$firmaBeneficiario = str_replace ( '<?xml version="1.0" encoding="UTF-8" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">', '', $firmaBeneficiario );
        		$firmaBeneficiario = str_replace ( "svg", 'draw', $firmaBeneficiario );
        	}
        		
        	{
        
        		$firmacontratista = base64_decode ( $_REQUEST ['firmaInstalador'] );
        		$firmacontratista = str_replace ( "image/svg+xml,", '', $firmacontratista );
        		$firmacontratista = str_replace ( '<?xml version="1.0" encoding="UTF-8" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">', '', $firmacontratista );
        		$firmacontratista = str_replace ( "svg", 'draw', $firmacontratista );
        	}
        		
        	$firmaBeneficiario = str_replace ( "height", 'height="30" pasos2', $firmaBeneficiario );
        	$firmaBeneficiario = str_replace ( "width", 'width="80" pasos1', $firmaBeneficiario );
        	$firmacontratista = str_replace ( "height", 'height="30" pasos2', $firmacontratista );
        	$firmacontratista = str_replace ( "width", 'width="80" pasos1', $firmacontratista );
        		
        	$cadena = $_SERVER ['HTTP_USER_AGENT'];
        	$resultado = stristr ( $cadena, "Android" );
        		
        	if ($resultado) {
        		$firmacontratista = str_replace ( "<path", '<g viewBox="0 0 50 50" transform="scale(0.2,0.2)"><path', $firmacontratista );
        		$firmacontratista = str_replace ( "/>", ' /></g>', $firmacontratista );
        		$firmaBeneficiario = str_replace ( "<path", '<g viewBox="0 0 50 50" transform="scale(0.2,0.2)"><path', $firmaBeneficiario );
        		$firmaBeneficiario = str_replace ( "/>", ' /></g>', $firmaBeneficiario );
        	} else {
        		$firmacontratista = str_replace ( "<path", '<g viewBox="0 0 50 50" transform="scale(0.08,0.08)"><path', $firmacontratista );
        		$firmacontratista = str_replace ( "/>", ' /></g>', $firmacontratista );
        		$firmaBeneficiario = str_replace ( "<path", '<g viewBox="0 0 50 50" transform="scale(0.08,0.08)"><path', $firmaBeneficiario );
        		$firmaBeneficiario = str_replace ( "/>", ' /></g>', $firmaBeneficiario );
        	}
        }
        
        ini_set ( 'xdebug.var_display_max_depth', 20000 );
        ini_set ( 'xdebug.var_display_max_children', 20000 );
        ini_set ( 'xdebug.var_display_max_data', 20000 );
        
        $firma_beneficiario = $firmaBeneficiario;
        
        $firma_contratista = $firmacontratista;
        
        $fecha = explode("-",$_REQUEST['fecha_instalacion']);
		
		$dia = $fecha[0];
		$mes = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
		$mes = $mes[$fecha[1]];
		$anno = $fecha[2];
		
		$vip = "";
		$est1 = "";
		$est2 = "";
		
		switch ($_REQUEST['tipo_beneficiario']) {
		
			case '1':
				$vip = "<u> X </u>";
				$est1 = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
				$est2 = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
				break;
		
			case '2':
		
				if ($_REQUEST['estrato'] == '1') {
					$vip = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
					$est1 = "<u> X </u>";
					$est2 = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
				} elseif ($_REQUEST['estrato'] == '2') {
					$vip = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
					$est1 = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
					$est2 = "<u> X </u>";
				}
		
				break;
		
			case '3':
				if ($_REQUEST['estrato'] == '1') {
					$vip = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
					$est1 = "<u> X </u>";
					$est2 = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
				} elseif ($_REQUEST['estrato'] == '2') {
					$vip = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
					$est1 = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
					$est2 = "<u> X </u>";
				}
		
				break;
		
		}

// 		if($_REQUEST['tipo_beneficiario'] == 1){
// 			$vip = "<u> X </u>";
// 			$est1 = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
// 			$est2 = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
// 		}else if($_REQUEST['tipo_beneficiario'] == 2){
// 			$vip = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
// 			$est1 = "<u> X </u>";
// 			$est2 = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
// 		}else if($_REQUEST['tipo_beneficiario'] == 3){
// 			$vip = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
// 			$est1 = "<u>&nbsp;&nbsp;&nbsp;&nbsp;</u>";
// 			$est2 = "<u> X </u>";
// 		}
		
		$cc = "";
		$ce = "";
		
		if($_REQUEST['tipo_documento'] == 1){
			$cc = "X";
		}else if($_REQUEST['tipo_documento'] == 2){
			$ce = "X";
		}
		
		
		$localizacion = explode(",", $_REQUEST['geolocalizacion']);
		
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
		
		switch ($_REQUEST['tipo_beneficiario']) {
		
			case '1':
				$valor_tarificacion = '6500';
				break;
		
			case '2':
		
				$valor_tarificacion = '0';
		
				if ($_REQUEST['estrato'] == '1') {
					$valor_tarificacion = '12600';
				} elseif ($_REQUEST['estrato'] == '2') {
					$valor_tarificacion = '17600';
				}
		
				break;
		
			case '3':
				$valor_tarificacion = $_REQUEST['valor_tarificacion'];
				break;
		
		}
		
		setlocale ( LC_ALL, "es_CO.UTF-8" );
		$contenidoPagina = "
                            <style type=\"text/css\">
                                table {

                                    font-family:Helvetica, Arial, sans-serif; /* Nicer font */

                                    border-collapse:collapse; border-spacing: 3px;
                                }
                                td, th {
                                    border: 1px solid #000000;
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



                        <page backtop='35mm' backbottom='30mm' backleft='10mm' backright='10mm' footer='page'>
                            <page_header>
                             <br>
                            <br>
                                    <table  style='width:100%;' >
                                        <tr>
                                            <td rowspan='3' style='width:33.3%;text-align=center;'><img src='" . $this->rutaURL . "frontera/css/imagen/politecnica.png'  width='125' height='40'></td>
                                            <td rowspan='3' style='width:33.3%;text-align=center;'><b>ACTA DE ENTREGA DE SERVICIO DE BANDA ANCHA AL USUARIO</b></td>
                                            <td align='center' style='width:33.3%;'>CODIGO: CPN-FO-CDII-60</td>
                                        </tr>

                                        <tr>
                                             <td align='center' style='width:33.3%;'>VERSIÓN: 01</td>
                                        </tr>
                                        <tr>
                                             <td align='center' style='width:33.3%;'>FECHA: 2016-07-06</td>
                                        </tr>
                                    </table>

                        </page_header>
                        
                        <page_footer>
							<table  style='width:100%;' >
								<tr>
									<td align='center' style='width:100%;border=none;' >
										<img src='" . $this->rutaURL . "frontera/css/imagen/logos_contrato.png'  width='500' height='35'>
									</td>
								</tr>
							</table>
   					 	</page_footer>";
		
		$contenidoPagina .= "
        			<h4 align='center'> ACTA DE ENTREGA DE SERVICIO DE BANDA ANCHA AL USUARIO </h4> 
                    <b>PRODUCTO	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;" . $_REQUEST['producto'] . "<br><br>
        			<b>CLIENTE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> &nbsp;&nbsp;" . $_REQUEST['nombres'] . "&nbsp;" . $_REQUEST['primer_apellido'] . "&nbsp;" . $_REQUEST['segundo_apellido'] . "<br><br>
        			<b>N° CEDULA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> &nbsp;&nbsp;".  $_REQUEST['numero_identificacion'] . "<br><br>
        			<b>FECHA INSTALACIÓN &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['fecha_instalacion'] . "<br><br>
        			<b>TIPO DE VIVIENDA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;&nbsp;".  "Estrato 1:&nbsp;" . $est1 . "&nbsp;" . "Estrato 2:&nbsp;" . $est2 . "&nbsp;" . "VIP:&nbsp;" . $vip . "<br><br>
        			<b>DATOS DEL SERVICIO</b><br><br>
        			<b>DIRECCIÓN DEL PREDIO &nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['direccion'] . "<br><br>
	        		<b>DEPARTAMENTO	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['departamento'] . "<br><br>
	        		<b>MUNICIPIO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['municipio'] . "<br><br>
	        		<b>NOMBRE DEL PROYECTO &nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['urbanizacion'] . "<br><br>
	        		<b>CODIGO DANE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['codigo_dane'] . "<br><br>
	        		<b>LATITUD &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $latitud_grados . "°&nbsp;" . $latitud_minutos . "'&nbsp;" . $latitud_segundos . "''" . "<br><br>
	        		<b>LONGITUD &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $longitud_grados . "°&nbsp;" . $longitud_minutos . "'&nbsp;" . $longitud_segundos . "''" . "<br><br>
	        		<b>CONTACTO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['contacto'] . "<br><br>
	        		<b>TELÉFONO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['telefono'] . "<br><br>
	        		<b>E-MAIL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['correo'] . "<br><br>
	        		<b>TIPO DE TECNOLOGÍA &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b>&nbsp;&nbsp;".  $_REQUEST['tipo_tecnologia'] . "<br><br><br>
	        		<b>DETALLE DE LOS EQUIPOS INSTALADOS</b><br><br>
        		
                    <br>

        		 	<table width:100%;>
                        <tr>
	                        <td align='center'style='width:14%;'><b>EQUIPO</b></td>
							<td align='center'style='width:16%;'><b>No. ACTIVO FIJO</b></td>
							<td align='center'style='width:14%;'><b>MAC</b></td>
	                        <td align='center'style='width:14%;'><b>SERIAL</b></td>
	                        <td align='center'style='width:14%;'><b>MARCA</b></td>
	                        <td align='center'style='width:14%;'><b>CANT</b></td>
					 		<td align='center'style='width:14%;'><b>IP</b></td>
                       	</tr>
                        <tr>
                        	<td align='center'style='width:14%;'>ESCLAVO</td>
                        	<td align='center'style='width:16%;'>".  $_REQUEST['numero_act_esc'] . " </td>
                            <td align='center'style='width:14%;'>".  $_REQUEST['mac_esc'] . " </td>
                            <td align='center'style='width:14%;'>".  $_REQUEST['serial_esc'] . " </td>
                            <td align='center'style='width:14%;'>".  $_REQUEST['marca_esc'] . " </td>
				 			<td align='center'style='width:14%;'>".  $_REQUEST['cant_esc'] . " </td>
							<td align='center'style='width:14%;'>".  $_REQUEST['ip_esc'] . " </td>
                        </tr>
						<tr>
                        	<td align='center'style='width:14%;'>COMPUTADOR</td>
                        	<td align='center'style='width:16%;'>".  $_REQUEST['numero_act_comp'] . " </td>
                            <td align='center'style='width:14%;'>".  $_REQUEST['mac_comp'] . " </td>
                            <td align='center'style='width:14%;'>".  $_REQUEST['serial_comp'] . " </td>
                            <td align='center'style='width:14%;'>".  $_REQUEST['marca_comp'] . " </td>
				 			<td align='center'style='width:14%;'>".  $_REQUEST['cant_comp'] . " </td>
							<td align='center'style='width:14%;'>".  $_REQUEST['ip_comp'] . " </td>
                        </tr>
                    </table>
					<br>
					<b>PRUEBAS</b>
					<table width:100%;>
                        <tr>
							<td align='rigth'style='width:20%;'><b></b></td>
							<td align='center'style='width:15%;'><b>Hora de Prueba</b></td>
	                        <td align='center'style='width:20%;'><b>Resultado</b></td>
	                        <td align='center'style='width:20%;'><b>Unidad</b></td>
							<td align='center'style='width:25%;'><b>Observaciones</b></td>
                       	</tr>
                        <tr>
                        	<td align='rigth'style='width:20%;'><b>Velocidad de Subida</b></td>
                        	<td align='center'style='width:15%;'>".  $_REQUEST['hora_prueba_vs'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['resultado_vs'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_vs'] . "</td>
                            <td align='center'style='width:25%;'>".  $_REQUEST['observaciones_vs'] . " </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Velocidad de Bajada</b></td>
                        	<td align='center'style='width:15%;'>".  $_REQUEST['hora_prueba_vb'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['resultado_vb'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_vb'] . " </td>
                            <td align='center'style='width:25%;'>".  $_REQUEST['observaciones_vb'] . " </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Ping 1</b></td>
                        	<td align='center'style='width:15%;'>".  $_REQUEST['hora_prueba_p1'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['resultado_p1'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_p1'] . " </td>
                            <td align='center'style='width:25%;'>".  $_REQUEST['observaciones_p1'] . " </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Ping 2</b></td>
                        	<td align='center'style='width:15%;'>".  $_REQUEST['hora_prueba_p2'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['resultado_p2'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_p2'] . "</td>
                            <td align='center'style='width:25%;'>".  $_REQUEST['observaciones_p2'] . " </td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Ping 3</b></td>
                        	<td align='center'style='width:15%;'>".  $_REQUEST['hora_prueba_p3'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['resultado_p3'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_p3'] . " </td>
                            <td align='center'style='width:25%;'>".  $_REQUEST['observaciones_p3'] . "</td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Traceroute</b></td>
                        	<td align='center'style='width:15%;'>".  $_REQUEST['hora_prueba_tr1'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['resultado_tr1'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_tr1'] . "</td>
                            <td align='center'style='width:25%;'>".  $_REQUEST['observaciones_tr1'] . "</td>
                        </tr>
						<tr>
                        	<td align='rigth'style='width:20%;'><b>Traceroute</b></td>
                        	<td align='center'style='width:15%;'>".  $_REQUEST['hora_prueba_tr2'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['resultado_tr2'] . " </td>
                            <td align='center'style='width:20%;'>".  $_REQUEST['unidad_tr2'] . "</td>
                            <td align='center'style='width:25%;'>".  $_REQUEST['observaciones_tr2'] . "</td>
                        </tr>
                    </table>
					<br>
					<b>OBRAS CIVILES</b>
					<table width:100%;>
                        <tr>
							<td align='justify' style='padding: 5px 5px 5px 5px;width:80%;'>Si aplica, el beneficiario certifica, que las obras fueron realizadas en el proceso de instalación por parte del contratista y fueron culminadas satisfactoriamente, sin afectar la infraestructura y la estética del lugar, cumpliendo con las observaciones realizadas durante la instalación.</td>
							<td style='padding: 5px 5px 5px 5px;width:10%;text-align:left;vertical-align:top;'><b>SI</b></td>
	                        <td style='padding: 5px 5px 5px 5px;width:10%;text-align:left;vertical-align:top;'><b>NO</b></td>
                       	</tr>
					</table>
					<br>
					<table width:100%;>
                        <tr>
							<td align='justify' style='padding: 5px 5px 5px 5px;width:100%;'>Yo&nbsp;" . $_REQUEST['nombres'] . "&nbsp;" . $_REQUEST['primer_apellido'] . "&nbsp;" . $_REQUEST['segundo_apellido'] .  "&nbsp;" . "identificado con cédula de ciudadanía número" .  "&nbsp;" . $_REQUEST['numero_identificacion'] .  ", como beneficiario del proyecto “Conexiones  Digitales II” – Proyecto Conexiones Digitales redes de acceso última milla para la masificación de accesos de banda ancha en viviendas de interés prioritario, hogares en estratos 1 y 2, – Ministerio de las Tecnologías de la Información y las Comunicaciones, declaro que conozco claramente las condiciones de prestación del servicio de acceso a Internet en banda ancha que adquirí; que la tarifa mensual a pagar por dicho servicio es &nbsp;$" . $valor_tarificacion . "&nbsp; pesos y que esta condición aplica por un periodo de 15 meses. Igualmente manifiesto que este predio pertenece al estrato &nbsp;" . $_REQUEST['estrato'] . "&nbsp; y no he contado con el servicio de internet en el mismos en los últimos seis (6) meses. 
								Asimismo me comprometo a informar oportunamente a la Corporación Politécnica Nacional de Colombia. sobre cualquier daño, pérdida o afectación de los equipos antes mencionados.
								Acepta y reconozco que a la fecha he consultado o he sido informado por la Corporación Politécnica Nacional de Colombia sobre las condiciones mínimas requeridas para los equipos necesarios para hacer uso de los servicios contratados. 
								Como constancia de recibo a satisfacción, se firma a los&nbsp;" . $dia . "&nbsp;días del mes de &nbsp;" . $mes . "&nbsp; de 2016 en la ciudad de &nbsp;" . $_REQUEST['ciudad_firma'] . "&nbsp;" . ".
							</td>
                       	</tr>
					</table>
					<br>
					<br>
					<table width:100%;>
                        <tr>
							<td align='justify' style='padding: 5px 5px 5px 5px;width:100%;'>Recuerde que cualquier inquietud sobre las funcionalidades del servicio, soporte,  los términos y condiciones, así como las peticiones, quejas o reclamos, serán atendidos en los siguientes canales:
								<br><br>Línea gratuita nacional 018000961016
								<br>Portal Web: http://conexionesdigitales.politecnica.edu.co/.
								<br>Correo: soportecd2@soygenial.co.
								<br><br>En caso de que desee efectuar la devolución de equipos instalados por la Corporación Politécnica Nacional de Colombia para la prestación del servicio, podrá comunicarse a la línea gratuita de atención nacional.
								<br><br>Debe tener en cuenta que existen riesgos sobre la seguridad de la red y de los servicios contratados
								los cuales incluyen: a. Riesgos relacionados con fraudes electrónicos, Riesgos relacionados con la información, Riesgos relacionados con las actividades económicas, Riesgos relacionados con el funcionamiento del Internet y Riesgos relacionados con hábitos adictivos. 
							</td>
                       	</tr>
					</table>
					<br>
					<table width:100%;>
                        <tr>
							<td colspan='2' align='rigth' style='width:50%;'>Recibí a Satisfacción</td>
							<td colspan='2' align='rigth' style='width:50%;'>Responsable de Instalación</td>
                       	</tr>
						<tr>
							<td rowspan='3' align='center' style='width:25%;'>$firmacontratista</td>
							<td align='rigth' style='width:25%;'>" . $_REQUEST['contacto'] . "</td>
							<td rowspan='3' align='center' style='width:25%;'>$firmaBeneficiario</td>
							<td align='rigth' style='width:25%;'>" . $_REQUEST['nombre_ins'] . "</td>
						</tr>
						<tr>
							<td align='rigth' style='width:25%;'>" . $_REQUEST['numero_identificacion_cont'] . "</td>
							<td align='rigth' style='width:25%;'>" . $_REQUEST['identificacion_ins'] . "</td>
						</tr>
						<tr>
							<td align='rigth' style='width:25%;'>" . $_REQUEST['celular'] . "</td>
							<td align='rigth' style='width:25%;'>" . $_REQUEST['celular_ins'] . "</td>
						</tr>
					</table>";
		
		if ($_REQUEST['soporte'] != '') {
		
			$contenidoPagina .= "<br> <div style='page-break-after:always; clear:both'></div>
                                         <P style='text-align:center'><b>Soporte</b></P><br><br>";
			$contenidoPagina .= "<table style='text-align:center;width:100%;border:none'>
                                            <tr>
                                                <td style='text-align:center;border:none;width:100%'>
                                                    <img src='" . $_REQUEST['soporte'] . "'  width='500' height='500'>
                                                </td>
                                            </tr>
                                        </table>
                                     ";
		}
		
		$contenidoPagina .= "</page>";
		
		$this->contenidoPagina = $contenidoPagina;
    }
}
$miDocumento = new GenerarDocumento($this->miSql);

?>
