<?php

namespace facturacion\impresionFactura\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

include $ruta . "/plugin/html2pdf/html2pdf.class.php";

class GenerarDocumento {
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

    public function cargarEstructuraXML() {

        ini_set('xdebug.var_display_max_depth', 5);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 1024);

        $estrutura = simplexml_load_file($this->rutaXML);

//            var_dump($nodo->titulo->attributes());

        $estrutura = json_encode($estrutura);

        $this->estructura = json_decode($estrutura, true);

    }

    public function parametrizacionPosicion() {

        /** Configuracion Pagina Documento

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

        $this->contenido = "<table  style='width:100%; border: 1px solid #000000; ' >";

        $numero_secciones = count($this->estructura['seccion']);

        for ($i = 0; $i < $numero_secciones; $i++) {

            $this->contenido .= "<tr><td  style='width:100%;height:245px;border: 1px solid #000000;border-collapse:initial;border-spacing: 0px;' >HOLA MUNDO</td></tr>";

        }

        $this->contenido .= "</table>";

    }

//----------------------------------------------------------------------

    public function obtenerInformacionBeneficiario() {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionCertificador');
        $beneficiario = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        $this->beneficiario = $beneficiario;

    }

    public function crearPDF() {

        ob_start();
        $html2pdf = new \HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(
            1,
            1,
            1,
            1,
        ));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($this->contenidoPagina);
        $html2pdf->Output("Factura.pdf", 'D');
    }

    public function estruturaDocumento() {

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
