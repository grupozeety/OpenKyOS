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

        $this->contenido = "<table  style='width:100%; border: none; background-color: #f0f5ff' >";

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
                $width = '50%';
                break;

            default:
                echo "Error Numero columnas";
                exit;
                    break;
            }

            foreach ($seccion as $key => $columna) {

                if (isset($this->colspan) && $width == '100%') {

                    $this->contenido .= "<td colspan='2' style='width:" . $width . ";height:" . $height . ";border:0.1px;font-size:100%'  nowrap >";

                    // Permite generar el Contenido a unos Tipos de Parametros
                    $this->caracterizacionContenido($columna);

                    $this->contenido .= "</td>";

                } else {

                    $this->contenido .= "<td style='width:" . $width . ";height:" . $height . ";border:0.1px;font-size:80%'  nowrap >";
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
            $this->atributos=$value->attributes();
            $value= str_replace("%%", "<br>", $value);

            switch ($key) {
            case 'titulo':

                $this->contenido .= "<div style='".$this->atributos."'><b>" . strtoupper($value) . "</b></div>";
                break;

            case 'texto':
                $this->contenido .= "<div style='".$this->atributos."'>" . $value . "</div>";
                break;

            case 'codigoBarras':
                $this->contenido .= "<div style='text-align:".$this->atributos['alineacionCodigoBarras'];
                $this->contenido .="'><barcode type='CODABAR' value='" . $value . "' style='".$this->atributos['dimensionesCodigoBarras']."'></barcode></div>";
                break;

            case 'imagen':
                $this->contenido .= "<div style='text-align:".$this->atributos['alineacionImagen'];
                $this->contenido .= "'><img src='" . $value . "' ".$this->atributos['dimensionesImagen']."  ></div>";
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
            $this->contenido .= "<div style='".$this->atributos."'>" . date('Y-m-d') . "</div>";
            break;

        case 'InformacionPago':
            $this->contenido .= "<div style='".$this->atributos."'>INFORMACION DE PAGO</div>";
            break;


        case 'HistoricoConsumo':
            $this->contenido .= "<div style='".$this->atributos."'>HISTORICO CONSUMO<BR>(GRAFICA)</div>";
            break;

        case 'InformacionPagoResumido':
            $this->contenido .= "<div style='".$this->atributos."'>INFORMACION DE PAGO RESUMIDO</div>";
            break;

        case 'Conceptos':
            $this->contenido .= "<div style='".$this->atributos."'>CONCEPTOS</div>";
            break;

        case 'InformacionBeneficiario':
            $this->contenido .= "<div style='".$this->atributos."'>INFORMACION DEL BENEFICIARIO</div>";
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
        // EXIT;
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

                                    border-collapse:collapse; border-spacing: 0px;
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

                            </style>";

        $contenidoPagina = "<page backtop='2mm' backbottom='2mm' backleft='2mm' backright='2mm'>";

        $contenidoPagina .= $this->contenido;

        $contenidoPagina .= "</page>";

        $this->contenidoPagina = $contenidoPagina;

    }

}
$miDocumento = new GenerarDocumento($this->lenguaje, $this->sql);

?>
