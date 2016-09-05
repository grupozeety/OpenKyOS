<?php

namespace reportes\materialNoConsumido\entidad;

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
							                    <font size='40px'><b>Relación de Materiales No Consumidos para Devolución a Bodega</b></font>
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
								<td style='width:10%;text-align=center;'>Identificador Material</td>
								<td style='width:35%;text-align=center;'>Descripción Material</td>
								<td style='width:10%;text-align=center;'>Cantidad de Material No Consumido</td>
								<td style='width:10%;text-align=center;'>Cantidad de Material Entregado </td>
								<td style='width:5%;text-align=center;'>Número Orden Trabajo</td>
								<td style='width:25%;text-align=center;'>Descripción Orden Trabajo</td>
								</tr>";

        $i = 1;

        foreach ($this->elementos as $valor) {

            $contenidoPagina .= "
							            <tr>
							            <td style='width:5%;text-align=center;'>" . $i . "</td>
							            <td style='width:10%;text-align=center;'>" . $valor['name'] . "</td>
							            <td style='width:35%;text-align=center;'>" . $valor['description'] . "</td>
							            <td style='width:10%;text-align=center;'>" . $valor['qty'] . "</td>
							            <td style='width:10%;text-align=center;'></td>
							            <td style='width:5%;text-align=center;'>" . $valor['numero_orden'] . "</td>
							            <td style='width:25%;text-align=center;'>" . $valor['descripcion_orden'] . "</td>
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