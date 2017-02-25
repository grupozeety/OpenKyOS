<?php

namespace facturacion\impresionFactura\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

require $ruta . "/plugin/html2pdf/html2pdf.class.php";

class GenerarDocumento
{
    public $miConfigurador;
    public $miSql;
    public $conexion;
    public $contenidoPagina;
    public $rutaURL;
    public $esteRecursoDB;
    public $beneficiario;
    public $rutaAbsoluta;
    public $rutaXML;
    public $estrutura;
    public $contenido;
    public function __construct($lenguaje, $sql)
    {

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

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }

        $this->rutaXML = $this->rutaAbsoluta . 'entidad/PlantillaXML/Facturacion25012017.xml';

        /**
         * Cargar Estructura XML
         **/

        $this->cargarEstructuraXML();

        /**
         * Parametrizacioón Posición
         **/

        $this->parametrizacionPosicion();

        /**
         * Parametrizacioón Posición
         **/

        $this->estruturaDocumento();

        /**
         * Parametrizacioón Posición
         **/

        $this->crearPDF();

    }

    public function cargarEstructuraXML()
    {

        ini_set('xdebug.var_display_max_depth', 5);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 1024);

        $this->estruturaXML = simplexml_load_file($this->rutaXML);

        //            var_dump($nodo->titulo->attributes());

        $estrutura = json_encode($this->estruturaXML);

        $this->estructura = json_decode($estrutura, true);

    }

    public function parametrizacionPosicion()
    {

        /**
         * Configuracion Pagina Documento

        _____________Columna 1    Columna 2
        Seccion 1 |            |            |
        _____________________________________
        Seccion 2 |            |            |
        _____________________________________
        Seccion 3 |            |            |
        _____________________________________
        Seccion 4 |            |            |
        _____________________________________

        1020px * 100%
         **/

        $this->contenido = "<table  style='width:100%; border: 0.1px; background-color: #f0f5ff' >";

        // Determina la utilización de colspan
        $this->determinacionTipoColumna();

        $numero_secciones = count($this->estruturaXML);

        switch ($numero_secciones) {
            case 1:
                $height = '1015px';
                break;

            case 2:
                $height = '505px';
                break;

            case 3:
                $height = '335px';
                break;

            case 4:
                $height = '250px';
                break;

            default:
                echo "Error Numero Secciones";
                exit;
                break;

        }

        foreach ($this->estruturaXML as $key => $seccion) {

            $this->contenido .= "<tr>";

            $numero_columnas = count($seccion->columna);

            switch ($numero_columnas) {
                case 1:
                    $width = '100%';
                    break;

                case 2:
                    $width = '45%';
                    break;

                default:
                    echo "Error Numero columnas";
                    exit;
                    break;
            }

            foreach ($seccion as $key => $columna) {

                if (isset($this->colspan) && $width == '100%') {

                    $this->contenido .= "<td colspan='2' style='width:" . $width . ";height:" . $height . ";border:none;font-size:100%'  nowrap >";

                    // Permite generar el Contenido a unos Tipos de Parametros
                    $this->caracterizacionContenido($columna);

                    $this->contenido .= "</td>";

                } else {

                    $this->contenido .= "<td style='width:" . $width . ";height:" . $height . ";border:none;'  nowrap >";
                    $this->caracterizacionContenido($columna);

                    $this->contenido .= "</td>";
                }

            }

            $this->contenido .= "</tr>";

        }

        $this->contenido .= "</table>";

    }

    public function caracterizacionContenido($objetoDatos)
    {

        foreach ($objetoDatos as $key => $value) {
            $this->atributos = $value->attributes();
            $value = str_replace("%%", "<br>", $value);

            switch ($key) {
                case 'titulo':

                    $this->contenido .= "<div style='" . $this->atributos . "'><b>" . strtoupper($value) . "</b></div>";
                    break;

                case 'texto':
                    $this->contenido .= "<div style='" . $this->atributos . "'>" . $value . "</div>";
                    break;

                case 'imagen':
                    $this->contenido .= "<div style='text-align:" . $this->atributos['alineacionImagen'];
                    $this->contenido .= "'><img src='" . $value . "' " . $this->atributos['dimensionesImagen'] . "  ></div>";
                    break;

                case 'variable':
                    //Ejecuta los procesos para obtener contenido de la variable
                    $this->ejecutarContenidoVariable($value);
                    break;

            }
            $this->contenido .= "<br>";
        }

    }

    public function ejecutarContenidoVariable($variable)
    {

        switch ($variable) {
            case 'FechaActual':
                $this->contenido .= "<div style='" . $this->atributos . "'>" . date('Y-m-d') . "</div>";
                break;

            case 'InformacionPago':
                $this->contenido .= "<div style='" . $this->atributos . "'>INFORMACION DE PAGO</div>";
                break;

            case 'HistoricoConsumo':
                $this->contenido .= "<div style='" . $this->atributos . "'>Grafico Historico</div>";
                break;

            case 'InformacionPagoResumido':

                $this->contenido .= "<div style='" . $this->atributos . "'>";

                $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionFacturacion', 'CE114');
                $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
                //var_dump($beneficiario);exit;

                $this->contenido .= "<table style='border-collapse:collapse;border:1px;width:100%;' nowrap >
                            <tr>
                                <td colspan='2' style='height:13px;text-align:center;border:0.1px;background-color:#97b5f4;'><br><b>INFORMACIÓN PAGO RESUMIDO</b><br><br></td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;width:50%'><b>Fecha de Venta: </b></td>
                                <td style='height:13px;text-align:right;border:0.1px;width:50%'>"     . $beneficiario['fecha_venta'] . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;width:50%;'><b>Fecha Factura: </b></td>
                                <td style='height:13px;text-align:right;border:0.1px;width:50%;'>"     . $beneficiario['fecha_factura'] . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;width:50%;'><b>Periodo: </b></td>
                                <td style='height:13px;text-align:right;border:0.1px;width:50%;'>"     . $beneficiario['id_ciclo'] . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;width:50%;'><b>Contrato-Ref.Pago: </b></td>
                                <td style='height:13px;text-align:right;border:0.1px;width:50%;'>"     . $beneficiario['numero_contrato'] . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;width:50%;'><b>Fecha Pago Oportuno: </b></td>
                                <td style='height:13px;text-align:right;border:0.1px;width:50%;'></td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;width:50%;background-color:#eb9e9e;'><br><b>VALOR TOTAL A PAGAR:</b><br></td>
                                <td style='height:13px;text-align:right;border:0.1px;width:50%;'><br><b>$ "     . number_format($beneficiario['total_factura'], 2) . "</b><br><br></td>
                            </tr>
                        </table>"    ;

                $this->contenido .= "</div>";
                break;

            case 'Conceptos':
                $this->contenido .= "<div style='" . $this->atributos . "'>";

                $cadenaSql = $this->miSql->getCadenaSql('consultaValoresConceptos', 'CE114');

                $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                //var_dump($beneficiario);exit;

                $table = "<table style='border-collapse:collapse;border:0.1px;width:100%;' >
                            <tr>
                                <td colspan='4' style='height:13px;text-align:center;border:0.1px;background-color:#97b5f4;'><br><b>CONCEPTOS FACTURACIÓN</b><br><br></td>
                            </tr>"    ;

                $table .= "<tr>
                                  <td style='height:13px;text-align:center;border:0.1px;width:5%;'><br><b>N°</b><br></td>
                                  <td style='height:13px;text-align:center;border:0.1px;width:25%;'><br><b>Periodo Facturado</b><br></td>
                                  <td style='height:13px;text-align:center;border:0.1px;width:50%;'><br><b>Concepto</b><br></td>
                                  <td style='height:13px;text-align:center;border:0.1px;width:20%;'><br><b>Valor</b><br></td>
                               </tr>"    ;
                $i = 1;
                foreach ($beneficiario as $key => $value) {
                    $table .= "<tr>
                                  <td style='height:13px;text-align:center;border:0.1px;width:5%;'><br><b>"     . $i . ".</b><br></td>
                                  <td style='height:13px;text-align:center;border:0.1px;width:25%;'><br><b>"     . $value['inicio_periodo'] . "  /  " . $value['fin_periodo'] . "</b><br></td>
                                  <td style='height:13px;text-align:left;border:0.1px;width:50%;'><br><b>"     . $value['concepto'] . "</b><br></td>
                                  <td style='height:13px;text-align:left;border:0.1px;width:20%;'><br><b>$ "     . number_format($value['valor_concepto'], 2) . "</b><br></td>
                               </tr>"    ;

                    $i++;
                }

                $table .= "</table>";

                $this->contenido .= $table;
                $this->contenido .= "</div>";

                break;

            case 'InformacionBeneficiario':
                $this->contenido .= "<div style='" . $this->atributos . "'>";

                $cadenaSql = $this->miSql->getCadenaSql('consultarBeneficiario', 'CE114');
                $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                $table = "<table style='border-collapse:collapse;border:1px;width:100%;' nowrap >
                            <tr>
                                <td style='height:13px;text-align:center;border:0.1px;background-color:#97b5f4;'><br><b>DATOS ABONADO SUSCRIPTOR</b><br><br></td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;width:100%'><b>Indentificación Beneficiario: </b>"     . $beneficiario['numero_identificacion'] . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;'><b>Nombre Beneficiario: </b>"     . $beneficiario['nombre_beneficiario'] . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;'><b>Dirección Inmueble: </b>"     . $beneficiario['direccion_beneficiario'] . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;'><b>Departamento - Municipio: </b>"     . $beneficiario['departamento'] . " - " . $beneficiario['municipio'] . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;'><b>Estrato: </b>"     . $beneficiario['estrato'] . "</td>
                            </tr>
                        </table>"    ;

                $this->contenido .= $table;
                $this->contenido .= "</div>";
                break;

            case 'InformacionFacturacion':
                $this->contenido .= "<div style='" . $this->atributos . "'>";

                $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionFacturacion', 'CE114');
                $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
                $this->contenido .= "<table style='border-collapse:collapse;border:1px;width:100%;' nowrap >
                            <tr>
                                <td colspan='2' style='height:13px;text-align:center;border:0.1px;background-color:#97b5f4;'><br><b>INFORMACIÓN PAGO RESUMIDO</b><br><br></td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;width:50%'><b>Fecha de Venta: </b></td>
                                <td style='height:13px;text-align:right;border:0.1px;width:50%'>"     . $beneficiario['fecha_venta'] . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;width:50%;'><b>Fecha Factura: </b></td>
                                <td style='height:13px;text-align:right;border:0.1px;width:50%;'>"     . $beneficiario['fecha_factura'] . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;width:50%;'><b>Periodo: </b></td>
                                <td style='height:13px;text-align:right;border:0.1px;width:50%;'>"     . $beneficiario['id_ciclo'] . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;width:50%;'><b>Contrato-Ref.Pago: </b></td>
                                <td style='height:13px;text-align:right;border:0.1px;width:50%;'>"     . $beneficiario['numero_contrato'] . "</td>
                            </tr>
                            <tr>
                                <td style='height:13px;text-align:left;border:0.1px;width:50%;'><b>Fecha Pago Oportuno: </b></td>
                                <td style='height:13px;text-align:right;border:0.1px;width:50%;'></td>
                            </tr>
                          </table>"    ;

                $this->contenido .= "</div>";
                break;

            case 'CodigoBarras':

                $this->contenido .= "<div style='text-align:" . $this->atributos['alineacionCodigoBarras'];

                $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionFacturacion', 'CE114');

                $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                $fecha = str_replace('-', '', $beneficiario['fecha_factura']);

                $valorCodigo = $fecha . $beneficiario['departamento'] . $beneficiario['municipio'] . $beneficiario['id_beneficiario'];

                $valorCodigo = ereg_replace("[a-zA-Z]", "", $valorCodigo);

                $this->contenido .= "'><barcode type='CODABAR' value='" . $valorCodigo . "' style='" . $this->atributos['dimensionesCodigoBarras'] . "'></barcode></div>";
                break;

        }

    }

    public function determinacionTipoColumna()
    {

        foreach ($this->estruturaXML as $key => $seccion) {

            $numero_columnas = count($seccion);

            switch ($numero_columnas) {
                case 1:
                    $columna_1 = true;
                    break;

                case 2:
                    $columna_2 = true;
                    break;

                default:
                    echo "Error Numero columnas";
                    exit;
                    break;
            }

        }

        if (isset($columna_1) && isset($columna_2)) {

            $this->colspan = true;

        }

    }
    //----------------------------------------------------------------------

    public function obtenerInformacionBeneficiario()
    {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificador');
        $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $this->beneficiario = $beneficiario;

    }

    public function crearPDF()
    {

        //exit;

        ob_start();
        $html2pdf = new \HTML2PDF(
            'P', 'LETTER', 'es', true, 'UTF-8', array(
                1,
                1,
                1,
                1,
            )
        );
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output("Factura.pdf", 'D');
    }

    public function estruturaDocumento()
    {

        $contenidoPagina = "<style type=\"text/css\">
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

                            </style>";

        $contenidoPagina = "<page backtop='2mm' backbottom='2mm' backleft='2mm' backright='2mm'>";

        $contenidoPagina .= $this->contenido;

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;

    }

}
$miDocumento = new GenerarDocumento($this->lenguaje, $this->sql);
