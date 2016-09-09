<?php
namespace gestionBeneficiarios\generacionContrato\entidad;

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

    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {

            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }

        /**
         *  1. Optener Clausulas
         **/

        $this->obtenerClausulas();

        /**
         *  2. Información de Beneficiario
         **/

        $this->estruturaDocumento();

        /**
         *  3. Estruturar Documento
         **/

        $this->estruturaDocumento();

        /**
         *  4. Crear PDF
         **/

        $this->crearPDF();

    }

    public function obtenerClausulas() {
        $cadenaSql = $this->miSql->getCadenaSql('consultarNumeralesContrato');
        $numerales = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        foreach ($numerales as $key => $value) {

            $cadenaSql = $this->miSql->getCadenaSql('consultarClausulas', $value['id_parametro']);
            $clausulas = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
            $numerales[$key]['clausulas'] = $clausulas;

        }

        $this->clausulas = $numerales;

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
        $html2pdf->Output('FormatoMaterialNoConsumido' . date('Y-m-d') . '.pdf', 'D');

    }
    public function estruturaDocumento() {

        $contenidoPagina = "
							<style type=\"text/css\">
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



						<page backtop='35mm' backbottom='30mm' backleft='10mm' backright='10mm' footer='page'>
							<page_header>
							<br>
							<br>
    								<table  style='width:100%;' >
							            <tr>
							            	   <td align='center' style='width:30%;border=none;' >
                    							<img src='" . $this->rutaURL . "frontera/css/imagen/vivedigital.png'  width='125' height='40'>
                								</td>
							                   <td align='center' style='width:70%;border=none;' >
							                    <font size='40px'><b>CONTRATO DE PRESTACIÓN DE SERVICIOS DE COMUNICACIONES</b></font>

							                </td>
							            </tr>
							        </table>

						</page_header>";

        $contenidoPagina .= "


        			<table style='width:100%;'>
        				<tr>
        					<td style='width:35%;text-align=center;border:none'> </td>
        					<td style='width:30%;text-align=center;border:none'>
		        					<table style='width:100%;'>
										<tr>
	        								<td style='width:100%;border:none;text-align:center'><b>COMPRAVENTA DE SERVICIOS</b></td>
	        							</tr>
		        					</table>
        					</td>
        					<td style='width:5%;text-align=center;border:none'> </td>
        					<td style='width:30%;text-align=center;border:none'></td>
						</tr>
					</table>
					<br>
        			<table style='width:100%;'>
        				<tr>
        					<td style='width:35%;text-align=center;border:none'> </td>
        					<td style='width:30%;text-align=center;border:none'>
		        					<table style='width:100%;'>
										<tr>
	        								<td style='width:25%;text-align=center;'>Fecha</td>
	        								<td style='width:25%;text-align=center;'>DD</td>
	        								<td style='width:25%;text-align=center;'>MM</td>
	        								<td style='width:25%;text-align=center;'>AAAA</td>
										</tr>
		        					</table>
        					</td>
        					<td style='width:5%;text-align=center;border:none'> </td>
        					<td style='width:30%;text-align=center;border:none'>
        						<table style='width:100%;'>
										<tr>
	        								<td style='width:50%;text-align=center;'>N° Contrato</td>
	        								<td style='width:50%;text-align=center;'> </td>
										</tr>
		        					</table>
		        			 </td>
						</tr>
					</table>
					<br>
					<br>
					<br>

			        <table style='width:100%;'>
				        <tr>
				        	<td rowspan='8' style='width:15%;text-align=center;'><b>DATOS ABONADO SUSCRIPTOR</b></td>
					        <td style='width:15%;text-align=center;'><b>Nombres</b></td>
					        <td style='width:10%;text-align=center;'> </td>
					        <td style='width:10%;text-align=center;'><b>Primer Apellido</b></td>
					        <td style='width:10%;text-align=center;'> </td>
					        <td style='width:5%;text-align=center;'><b>Segundo Apellido</b></td>
					        <td colspan='2' style='width:10%;text-align=center;'> </td>
				        </tr>
				        <tr>
					        <td style='width:15%;text-align=center;'><b>Tipo Documento</b></td>
					        <td style='width:5%;text-align=center;'>CC</td>
					        <td style='width:5%;text-align=center;'>TI</td>
					        <td style='width:10%;text-align=center;'><b>Número</b></td>
					  		<td style='width:15%;text-align=center;'> </td>
					        <td style='width:15%;text-align=center;'><b>Lugar/Fecha Expedición</b></td>
					        <td style='width:10%;text-align=center;'> </td>
				        </tr>
				         <tr>
					        <td style='width:15%;text-align=center;'><b>Dirección Domicilio</b></td>
					        <td colspan='6' style='width:70%;text-align=center;'> </td>
				        </tr>
				        <tr>
					        <td style='width:15%;text-align=center;'><b>Dirección Instalación</b></td>
					        <td colspan='6' style='width:70%;text-align=center;'> </td>
				        </tr>
				          <tr>
					        <td style='width:15%;text-align=center;'><b>Departamento</b></td>
					        <td colspan='1'style='width:10%;text-align=center;'> </td>
					        <td style='width:10%;text-align=center;'><b>Municipio</b></td>
					        <td colspan='1' style='width:10%;text-align=center;'> </td>
					       <td colspan='1'style='width:5%;text-align=center;'><b>Urbanización</b></td>
					        <td colspan='2'style='width:20%;text-align=center;'> </td>
				        </tr>
				        <tr>
					        <td style='width:15%;text-align=center;'><b>Estrato</b></td>
					        <td style='width:5%;text-align=center;'>VIP</td>
					        <td style='width:5%;text-align=center;'>1 Residencial</td>
					        <td style='width:5%;text-align=center;'>1 Residencial</td>
					       <td colspan='1'style='width:5%;text-align=center;'>Barrio</td>
					        <td colspan='2'style='width:10%;text-align=center;'> </td>
					    </tr>
				         <tr>
					        <td style='width:15%;text-align=center;'><b>Telefono</b></td>
					         <td colspan='1' style='width:10%;text-align=center;'> </td>
					        <td style='width:10%;text-align=center;'><b>Celular</b></td>
					        <td style='width:10%;text-align=center;'> </td>
					         <td colspan='1' style='width:5%;text-align=center;'><b>Correo Electrónico</b></td>
					        <td colspan='2'style='width:10%;text-align=center;'></td>
				        </tr>
     					<tr>
					        <td style='width:15%;text-align=center;'><b>Cuenta Suscriptor</b></td>
					        <td colspan='6' style='width:70%;text-align=center;'> </td>
				        </tr>

			        </table>
			        <br>
			        <table style='width:100%;'>
				        <tr>
				        	<td rowspan='2' style='width:15%;text-align=center;'><b>DATOS SERVICIO</b></td>
					        <td style='width:15%;text-align=center;'><b>Velocidad Internet</b></td>
					        <td style='width:30%;text-align=right;'>MB</td>
					        <td style='width:20%;text-align=center;'><b>Vigencia Servicio</b></td>
					        <td style='width:20%;text-align=center;'>  </td>
					    </tr>
					    <tr>
				            <td style='width:15%;text-align=center;'><b>Valor Mensual Servicio Básico con IVA</b></td>
					        <td colspan='3' style='width:70%;text-align=left;'><b>$</b></td>
					    </tr>
					 </table>
					 <br>
					  <table style='width:100%;'>
				        <tr>
				        	<td rowspan='4' style='width:15%;text-align=center;'><b>EQUIPO ENTREGADO</b></td>
					        <td style='width:15%;text-align=center;'><b>Marca</b></td>
					        <td  colspan='3' style='width:70%;text-align=right;'> </td>
					    </tr>
					    <tr>
				        	<td style='width:15%;text-align=center;'><b>Modelo</b></td>
				        	<td style='width:27.5%;text-align=center;'> </td>
				        	<td style='width:15%;text-align=center;'><b>Serial</b></td>
				        	<td style='width:27.5%;text-align=center;'> </td>
					    </tr>
					    <tr>
					    	<td style='width:15%;text-align=center;'><b>Tecnología</b></td>
					        <td  colspan='3' style='width:70%;text-align=right;'> </td>
					    </tr>
					    <tr>
					    	<td style='width:15%;text-align=center;'><b>Estado</b></td>
					        <td  colspan='3' style='width:70%;text-align=right;'> </td>
					    </tr>
					   </table>
				     <br>";
/*
$contenidoPagina .= "<P style='text-align:justify'>Entre las partes antes descritas, se ha celebrado el presente <b>CONTRATO DE PRESTACIÓN DE SERVICIOS DE COMUNICACIONES</b>, el cual se regirá por lo dispuesto en la ley 1341 de 2009, en la Resolución 3066 de 2011 expedida por la Comisión de Regulación de Comunicaciones, y en las normas que la adicionen, modifiquen o deroguen; y en especial, por las siguientes cláusulas:</P>";

foreach ($this->clausulas as $key => $value) {

$contenidoPagina .= "<br><b>" . $value['descripcion'] . "</b><br>";

foreach ($value['clausulas'] as $key => $contenido) {
$contenidoPagina .= "<P style='text-align:justify'><b>CLÁUSULA " . trim($contenido['orden_general']) . ".</b>" . $contenido['contenido'] . "</P><br>";
}

}
 */
        $contenidoPagina .= "<page_footer>
	        				<P>
	        						<b>COMO CONSTANCIA DE ACEPTACIÓN SUSCRIBE EL PRESENTE CONTRATO EL USUARIO:
		        						<br>
		        						<br>
		        						FIRMA :  _________________________________
		        						<br>
		        						<br>
		        						NOMBRE : _________________________________
		        						<br>
		        						<br>
		        						C.C :    _________________________________
	        						</b>
	        					 </P>
	        			     </page_footer>";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;
    }
}
$miDocumento = new GenerarDocumento($this->sql);

?>