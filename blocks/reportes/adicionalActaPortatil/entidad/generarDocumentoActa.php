<?php

namespace reportes\adicionalActaPortatil\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";

class GenerarDocumentoActa
{
    private $ruta_imagen;
    private $ruta_documento;
    public function __construct($ruta_imagen = '', $ruta_documento = '')
    {

        $this->ruta_imagen = $ruta_imagen;

        $this->ruta_documento = $ruta_documento;
        /**
         * x.Crear Estrutura Documento
         **/

        $this->estruturarDocumento();

        /**
         * x.Crear Documento PDF
         **/

        $this->crearPDF();
    }

    public function retornarNombreDocumento()
    {
        return "doc1.pdf";
    }

    public function crearPDF()
    {
        ob_start();
        $html2pdf = new \HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            2,
        ));

        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output($this->ruta_documento . "doc1.pdf", 'F');
    }

    public function estruturarDocumento()
    {

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
                                    font-size:30px;
                                }
                                td {

                                    text-align: left;

                                }
                            </style>



                        <page backtop='10mm' backbottom='10mm' backleft='10mm' backright='10mm'>

                       ";

        $contenidoPagina .= "<table  style='width:100%;' >
                                        <tr>
                                                <td align='center' style='width:100%;border=none;' >
                                                <img src='" . $this->ruta_imagen . "'  width='725' height='950'>
                                                </td>
                                        </tr>
                                    </table>";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;

    }

}
