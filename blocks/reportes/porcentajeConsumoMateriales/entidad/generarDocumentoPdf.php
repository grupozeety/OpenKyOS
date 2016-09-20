<?php

namespace reportes\porcentajeConsumoMateriales\entidad;

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

							    }
							</style>



						<page backtop='35mm' backbottom='30mm' backleft='5mm' backright='5mm' footer='page'>
							<page_header>
    								<table align='left' style='width:100%;' >
							            <tr>
							            	   <td align='center' style='width:50%;border=none;' >
                    							<img src='" . $this->rutaURL . "frontera/css/imagen/politecnica.png'  width='250' height='80'>
                								</td>
							                   <td align='center' style='width:50%;border=none;' >
							                    <font size='40px'><b>Porcentaje De Consumo De Materiales</b></font>
							                     <br>
							                    <font size='50px'><b>Proyecto : " . $_REQUEST['proyecto'] . "</b></font>
							                     <br>
							                     <font size='4px'>" . date("Y-m-d") . "</font>
							                </td>
							            </tr>
							        </table>

						</page_header>";

        $contenidoPagina .= "
	 							<table style='width:100%;'>
								<tr>
								<td style='width:5%;text-align=center;'>N°</td>
								<td style='width:30%;text-align=center;'>Proyecto</td>
								<td style='width:15%;text-align=center;'>Número Orden Trabajo</td>
								<td style='width:35%;text-align=center;'>Descripción Orden Trabajo</td>
								<td style='width:15%;text-align=center;'>Consumo De Materiales </td>
								</tr>";

        $i = 1;

        foreach ($this->elementos as $valor) {

            $contenidoPagina .= "
							            <tr>
							            <td style='width:5%;text-align=center;'>" . $i . "</td>
							            <td style='width:30%;text-align=center;'>" . $valor['proyecto'] . "</td>
							            <td style='width:15%;text-align=center;'>" . $valor['orden_trabajo'] . "</td>
							            <td style='width:35%;text-align=center;'>" . $valor['descripcion'] . "</td>
							            <td style='width:15%;text-align=center;'>" . round($valor['porcentaje_consumo'], 4) . " %"."</td>
							            </tr>";

            $i++;

        }

        $contenidoPagina .= "</table>";

        $contenidoPagina .= "

			<page_footer  backbottom='10mm'>
						<br>
						<br>
						<br>
						<br>

						<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>
						<tr>
						<td style='width:33.3%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>_____________________</td>
						<td style='width:33.3%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>_____________________</td>
						<td style='width:33.3%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>_____________________</td>
						</tr>
						<tr>
						<td style='width:33.3%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>Firma Solicitante</td>
						<td style='width:33.3%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>Firma Líder Técnico</td>
						<td style='width:33.3%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>Firma Almacenista</td>
						</tr>
						</table>
			</page_footer>
					";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;
    }
}
$miDocumento = new GenerarDocumento($this->miSql, $this->elementos_reporte);

?>