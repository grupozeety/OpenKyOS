<?php

namespace reportes\adicionalActaPortatil\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";

class GenerarDocumentoActaAdicional
{
    public $miConfigurador;
    public $beneficiario;
    public $ruta_archivo;

    public function __construct($beneficiario, $ruta_archivo)
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->beneficiario = $beneficiario;
        $this->ruta_archivo = $ruta_archivo;

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
         * x.Estruturar Documento
         */

        $this->estruturaDocumento();

        /**
         * x.Crear Documento
         */

        $this->crearPDF();

    }

    public function retornarNombreDocumento()
    {
        return $this->ruta_archivo . "doc2.pdf";
    }

    public function crearPDF()
    {

        ob_start();
        $html2pdf = new \HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output($this->ruta_archivo . "doc2.pdf", 'F');
    }
    public function estruturaDocumento()
    {

        //var_dump($this->beneficiario);exit;
        $anexo_dir = '';

        if ($this->beneficiario['manzana'] != '0' && $this->beneficiario['manzana'] != '') {
            $anexo_dir .= " Manzana  #" . $this->beneficiario['manzana'] . " - ";
        }

        if ($this->beneficiario['bloque'] != '0' && $this->beneficiario['bloque'] != '') {
            $anexo_dir .= " Bloque #" . $this->beneficiario['bloque'] . " - ";
        }

        if ($this->beneficiario['torre'] != '0' && $this->beneficiario['torre'] != '') {
            $anexo_dir .= " Torre #" . $this->beneficiario['torre'] . " - ";
        }

        if ($this->beneficiario['casa_apartamento'] != '0' && $this->beneficiario['casa_apartamento'] != '') {
            $anexo_dir .= " Casa/Apartamento #" . $this->beneficiario['casa_apartamento'];
        }

        if ($this->beneficiario['interior'] != '0' && $this->beneficiario['interior'] != '') {
            $anexo_dir .= " Interior #" . $this->beneficiario['interior'];
        }

        if ($this->beneficiario['lote'] != '0' && $this->beneficiario['lote'] != '') {
            $anexo_dir .= " Lote #" . $this->beneficiario['lote'];
        }

        if ($this->beneficiario['piso'] != '0' && $this->beneficiario['piso'] != '') {
            $anexo_dir .= " Piso #" . $this->beneficiario['piso'];
        }

        {

            $tipo_vip = ($this->beneficiario['tipo_beneficiario'] == "1") ? "<b>X</b>" : "";
            $tipo_residencial_1 = ($this->beneficiario['tipo_beneficiario'] == "2") ? (($this->beneficiario['estrato_socioeconomico'] == "1") ? "<b>X</b>" : "") : "";
            $tipo_residencial_2 = ($this->beneficiario['tipo_beneficiario'] == "2") ? (($this->beneficiario['estrato_socioeconomico'] == "2") ? "<b>X</b>" : "") : "";
        }

        setlocale(LC_ALL, "es_CO.UTF-8");

        $contenidoPagina = "
                            <style type=\"text/css\">
                                table {

                                    font-family:Helvetica, Arial, sans-serif; /* Nicer font */

                                    border-collapse:collapse; border-spacing: 3px;
                                }
                                td, th {

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
                                p{
                                font-size:15px;

                                }
                               page{

                                  font-size:15px;

                                }
                            </style>



                        <page backtop='25mm' backbottom='10mm' backleft='10mm' backright='10mm'>
                            <page_header>
                                 <table  style='width:100%;' >
                                          <tr>
                                                <td align='center' style='width:100%;border=none;' >
                                                <img src='" . $this->rutaURL . "frontera/css/imagen/logos_contrato.png'  width='500' height='45'>
                                                </td>
                                                <tr>
                                                <td style='width:100%;border:none;text-align:center;font-size:9px;'><b>008 - ACTA DE ENTREGA DE COMPUTADOR PORTÁTIL</b></td>
                                                </tr>
                                                <tr>
                                                <td style='width:100%;border:none;text-align:center;'><br><br><b>008 - ACTA DE ENTREGA DE COMPUTADOR PORTÁTIL</b></td>
                                                </tr>

                                        </tr>
                                    </table>
                        </page_header>";

        $contenidoPagina .= "
                            <p style='text-align:justify'>
                            El suscrito beneficiario del Proyecto Conexiones Digitales II, cuyos datos se presentan a continuación:
                            </p>

                             <table  style='width:100%;border:0.1px;' >
                                  <tr>
                                    <td style='width:25%;border:0.1px;'>Contrato de Servicio</td>
                                    <td colspan='3' style='text-align:center;width:75%;border:0.1px;'><b>" . $this->beneficiario['numero_contrato'] . "</b></td>
                                  </tr>
                                  <tr>
                                    <td style='width:25%;border:0.1px;'>Beneficiario</td>
                                    <td colspan='3' style='text-align:center;width:75%;border:0.1px;'><b>" . $this->beneficiario['nombres'] . " " . $this->beneficiario['primer_apellido'] . " " . $this->beneficiario['segundo_apellido'] . "</b></td>
                                  </tr>
                                  <tr>
                                    <td style='width:25%;border:0.1px;'>Número Identificación</td>
                                    <td colspan='3' style='text-align:center;width:75%;border:0.1px;'><b>" . number_format($this->beneficiario['numero_identificacion'], 0, '', '.') . "</b></td>
                                  </tr>
                                  <tr>
                                    <td colspan='4' style='width:100%;border:0.1px;'><b>Datos de Vivienda</b></td>
                                  </tr>
                                  <tr>
                                    <td style='text-align:center;width:25%;border:0.1px;'>Tipo</td>
                                    <td style='text-align:center;width:25%;border:0.1px;'>Estrato 2 (<b>" . $tipo_residencial_2 . "</b>)</td>
                                    <td style='text-align:center;width:25%;border:0.1px;'>Estrato 1 (<b>" . $tipo_residencial_1 . "</b>)</td>
                                    <td style='text-align:center;width:25%;border:0.1px;'>VIP (<b>" . $tipo_vip . "</b>)</td>
                                  </tr>
                                  <tr>
                                    <td style='width:25%;border:0.1px;'>Dirección</td>
                                    <td colspan='3' style='text-align:center;width:75%;border:0.1px;'><b>" . $this->beneficiario['direccion_domicilio'] . $anexo_dir . "</b></td>
                                  </tr>
                                  <tr>
                                    <td style='width:25%;border:0.1px;'>Departamento</td>
                                    <td style='text-align:center;width:25%;border:0.1px;'><b>" . $this->beneficiario['nombre_departamento'] . "</b></td>
                                    <td style='width:25%;border:0.1px;'>Municipio</td>
                                    <td style='text-align:center;width:25%;border:0.1px;'><b>" . $this->beneficiario['nombre_municipio'] . "</b></td>
                                  </tr>
                                  <tr>
                                    <td style='width:25%;border:0.1px;'>Urbanización</td>
                                    <td colspan='3' style='text-align:center;width:75%;border:0.1px;'><b>" . $this->beneficiario['proyecto_urbanizacion'] . "</b></td>
                                  </tr>
                            </table>
                            <br>
                            <table  style='width:100%;' >
                                   <tr>
                                    <td align='center' style='width:100%;border=none;' ><b>MANIFIESTO QUE:</b></td>
                                   </tr>
                            </table>
                            <p style='text-align:justify'>1.El contratista entregó un computador portátil marca HP 245 G4 Notebook PC nuevo, a titulo de uso y goce hasta la terminación del contrato de aporte suscrito entre el Fondo TIC y la Corporación Politécnica. En consecuencia, el computador no puede ser vendido, arrendado,transferido, dado en prenda, servir de garantía, so pena de perder el beneficio.<br>2. Respecto al computador descrito en la hoja 2 de este documento, dejo constancia que no se me
                                cobro ningún tipo de cargo como usuario beneficiado del Proyecto de Conexiones Digitales y que el equipo fue entregado embalado, garantizando la integridad del     mismo.<br>3. Además certifico que se realizaron las siguientes pruebas de funcionalidad:</p>
                            <table align='center' style='width:50%;' >
                                   <tr>
                                    <td style='text-align:center;width:80%;border=0.1px;' >Correcto encendido/apagado</td>
                                    <td style='text-align:center;width:20%;border=0.1px;' >SI</td>
                                   </tr>
                                    <tr>
                                    <td style='text-align:center;width:80%;border=0.1px;' >Equipo funcionando y navegando</td>
                                    <td style='text-align:center;width:20%;border=0.1px;' >SI</td>
                                   </tr>
                                   <tr>
                                    <td style='text-align:center;width:80%;border=0.1px;' >Funciona el teclado, parlante y touchpad</td>
                                    <td style='text-align:center;width:20%;border=0.1px;' >SI</td>
                                   </tr>
                            </table>
                            <p style='text-align:justify'>4. La garantía del equipo es un año a partir de la fecha de entrega en la que se firma este documento.<br>5. El Contacto de Garantía es la Corporación Politécnica, y me puedo comunicar con la línea gratuita las 24 horas del día de los 7 días de la semana. ((018000 961016)).<br>6. Que con el fin de no perder la garantía del fabricante en la eventualidad de presentarse fallas, el beneficiario ni un tercero no autorizado por el fabricante, pueden manipular el equipo tratando deresolver el problema presentado.<br>7. En caso de daño, hurto, el usuario debe hacer el reporte a la mesa de ayuda con numero 018000961016, lo cual debe quedar consignado en un ticket para la gestión y seguimiento del mismo.<br>8. En caso de pérdida o hurto no habrá reposición del equipo.<br>9. Que manifiesto mi entera conformidad y satisfacción del bien que recibo en la fecha y me obligo a realizar su correcto uso, custodia y conservación, autorizando al prestador del servicio (Corporación Politécnica) para que ejerza seguimiento y control sobre el mismo.<br>10. Que a la terminación del plazo de ejecución de este contrato de comodato, tendré la opción de adquirir el bien antes descrito.</p>";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;
    }
}
