<?php

namespace reportes\reporteAgendamientos\funcion;

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
    public function __construct($sql, $elementos) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->elementos = $elementos;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {

            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }

        /**
         *  1. Estruturar Documento
         **/

        $this->estruturaDocumento();

        /**
         *  2. Crear PDF
         **/

        $this->crearPDF();

    }

    public function crearPDF() {

        ob_start();
        $html2pdf = new \HTML2PDF('L', 'LETTER', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output('FormatoPorcentajeMaterialConsumido' . date('Y-m-d') . '.pdf', 'D');

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
								col{
								width=50%;

								}
								th {

							        font-weight: bold; /* Make sure they're bold */
							        text-align: center;
							        font-size:10px;
							    }
							    td {

							        text-align: left;
									font-size:9px;
							    }
							</style>



						<page backtop='35mm' backbottom='30mm' backleft='5mm' backright='5mm' footer='page'>
							<page_header>
    								<table align='left' style='width:100%;' >
							            <tr>
							            	   <td align='center' style='width:50%;border=none;' >
                    							<img src='" . $this->rutaURL . "css/imagenes/politecnica.png'  width='250' height='80'>
                								</td>
							                   <td align='center' style='width:50%;border=none;' >
							                    <font size='40px'><b>Comisionamientos Agendados</b></font>
							                     <br>
							                     <font size='4px'>" . date("Y-m-d") . "</font>
							                </td>
							            </tr>
							        </table>

						</page_header>";

        $contenidoPagina .= "
	 							<table style='width:100%;'>
								<tr>
									<td style='width:4%;text-align=center;'>N°</td>
									<td style='width:9%;text-align=center;'>Agendamiento </td>
									<td style='width:10%;text-align=center;'>Urbanización </td>
	        						<td style='width:7%;text-align=center;'>Manzana </td>
	        						<td style='width:7%;text-align=center;'>Torre </td>
	        						<td style='width:7%;text-align=center;'>Bloque </td>
	        						<td style='width:7%;text-align=center;'>Casa Apartamento </td>
	        						<td style='width:10%;text-align=center;'>Tipo de Agendamiento </td>
	        						<td style='width:10%;text-align=center;'>Comisionador </td>
        							<td style='width:11%;text-align=center;'>Documento Beneficiario</td>
	        						<td style='width:10%;text-align=center;'>Nombre Beneficiario </td>
	        						<td style='width:8%;text-align=center;'>Fecha </td>
								</tr>";

        $i = 1;

        foreach ($this->elementos as $valor) {

            $contenidoPagina .= "
							            <tr>
							            <td style='width:4%;text-align=center;'>" . $i . "</td>
							            <td style='width:9%;text-align=center;'>" . $valor['id_agendamiento'] . "</td>
							            <td style='width:10%;text-align=center;'>" . $valor['urbanizacion'] . "</td>
							            <td style='width:7%;text-align=center;'>" . $valor['manzana'] . "</td>
							            <td style='width:7%;text-align=center;'>" . $valor['torre'] . "</td>
							            <td style='width:7%;text-align=center;'>" . $valor['bloque'] . "</td>
							            <td style='width:7%;text-align=center;'>" . $valor['apartamento'] . "</td>
							            <td style='width:10%;text-align=center;'>" . $valor['tipo_agendamiento'] . "</td>
							            <td style='width:10%;text-align=center;'>" . $valor['comisionador'] . "</td>
							            <td style='width:11%;text-align=center;'>" . $valor['identificacion_beneficiario'] . "</td>
							            <td style='width:10%;text-align=center;'>" . $valor['nombre'] . " ". $valor['primer_apellido'] . " " . $valor['segundo_apellido'] . "</td>
							            <td style='width:8%;text-align=center;'>" . $valor['fecha'] . "</td>
							            </tr>";

            $i++;

        }

        $contenidoPagina .= "</table>";

        $contenidoPagina .= "";


        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;
    }
}
$miDocumento = new GenerarDocumento($this->miSql, $this->elementos_reporte);

?>