<?php

namespace reportes\cartelActa\entidad;

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
    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->lenguaje = $lenguaje;
        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        // Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        $this->rutaURLArchivo = $this->rutaURL . '/archivos/actas_entrega_portatil/';

        $this->rutaAbsolutaArchivo = $this->rutaAbsoluta . '/archivos/actas_entrega_portatil/';

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }

        $this->rutaURL_Bloque = $this->rutaURL;
        $this->rutaAbsoluta_Bloque = $this->rutaAbsoluta;

        $this->obtenerInformacionBeneficiario();

        foreach ($this->beneficiario as $key => $value) {

            $this->asosicarCodigoDocumento($value);

            $this->estruturaDocumentoCartel($value);
            $this->crearPDFCartel();

            unset($this->contenidoPagina);
            $this->contenidoPagina = NULL;

            unset($value);
            $value = NULL;

            unset($archivo_datos);
            $archivo_datos = NULL;

        }

    }

    public function obtenerInformacionBeneficiario() {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificador');
        $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $this->beneficiario = $beneficiario;

    }

    public function crearPDFCartel() {

        ob_start();
        $html2pdf = new \HTML2PDF('L', 'LETTER', 'es', true, 'UTF-8', array(
            2,
            2,
            2,
            10,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output($this->nombreCartel, 'D');
    }

    public function asosicarCodigoDocumento($beneficiario) {

        unset($this->prefijo);
        $this->prefijo = NULL;

        unset($this->nombreCartel);
        $this->nombreCartel = NULL;

        unset($this->nombreDocumento);
        $this->nombreDocumento = NULL;

        $this->prefijo = substr(md5(uniqid(time())), 0, 6);

        $this->nombreCartel = $beneficiario['numero_identificacion_contrato'] . "_Cartel_" . $this->prefijo . '.pdf';

    }

    public function estruturaDocumentoCartel($beneficiario) {

        {
            $anexo_dir = '';

            if ($beneficiario['manzana'] != '0' && $beneficiario['manzana'] != '') {
                $anexo_dir .= " Manzana  #" . $beneficiario['manzana'];
            }

            if ($beneficiario['bloque'] != '0' && $beneficiario['bloque'] != '') {
                $anexo_dir .= " Bloque #" . $beneficiario['bloque'];
            }

            if ($beneficiario['torre'] != '0' && $beneficiario['torre'] != '') {
                $anexo_dir .= " Torre #" . $beneficiario['torre'];
            }

            if ($beneficiario['casa_apartamento'] != '0' && $beneficiario['casa_apartamento'] != '') {
                $anexo_dir .= " Casa/Apartamento #" . $beneficiario['casa_apartamento'];
            }

            if ($beneficiario['interior'] != '0' && $beneficiario['interior'] != '') {
                $anexo_dir .= " Interior #" . $beneficiario['interior'];
            }

            if ($beneficiario['lote'] != '0' && $beneficiario['lote'] != '') {
                $anexo_dir .= " Lote #" . $beneficiario['lote'];
            }

            if ($beneficiario['piso'] != '0' && $beneficiario['piso'] != '') {
                $anexo_dir .= " Piso #" . $beneficiario['piso'];
            }
        }

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



                        <page backtop='25mm' backbottom='10mm' backleft='10mm' backright='10mm'>

                       ";
//var_dump($beneficiario);exit;
        $contenidoPagina .= "
                        <table>
                               <tr>
                                    <td style='width:100%;border:none;font-size:30px;'>
                                                <br>
                                                <b>CODIGO DANE Y ESTRATO: </b>" . $beneficiario['codigo_municipio'] . " - VIP" . "<br><br>
                                                <b>MUNICIPIO:</b>  " . $beneficiario['nombre_municipio'] . "<br><br>
                                                <b>SUBPROYECTO: </b>" . $beneficiario['nombre_urbanizacion'] . "<br><br>
                                                <b>BENEFICIARIO: </b>" . $beneficiario['nombre_contrato'] . " " . $beneficiario['primer_apellido_contrato'] . " " . $beneficiario['segundo_apellido_contrato'] . "<br><br>
                                                <b>DIRECCIÓN: </b>" . $beneficiario['direccion_domicilio'] . "  " . $anexo_dir . "<br><br>
                                                <br>
                                                <br>


                                    </td>
                                </tr>
                            </table>



                            <table>
                               <tr>
                                    <td style='width:100%;text-align:center;border:none;font-size:30px;'><b>CONEXIONES DIGITALES II</b>
                                    <br>CONTRATO DE APORTE 681/2015<</td>
                                </tr>
                            </table>


                            <page_footer>
                            <table  style='width:100%;' >
                                        <tr>
                                                <td align='center' style='width:100%;border=none;' >
                                                <img src='" . $this->rutaURL_Bloque . "frontera/css/imagen/logos_contrato.png'  width='950' height='90'>
                                                </td>
                                        </tr>
                                    </table>
                            </page_footer>
                            ";

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;

        unset($beneficiario);
        $beneficiario = NULL;

        unset($contenidoPagina);
        $contenidoPagina = NULL;

    }

}
$miDocumento = new GenerarDocumento($this->lenguaje, $this->sql);

?>
