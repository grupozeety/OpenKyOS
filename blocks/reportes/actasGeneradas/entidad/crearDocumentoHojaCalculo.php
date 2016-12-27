<?php

namespace reportes\actasGeneradas\entidad;

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel.php";
class GenerarReporteExcelInstalaciones {
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $informacion;
    public $objCal;
    public function __construct($sql, $informacion) {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 10000);
        date_default_timezone_set('America/Bogota');

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->informacion = $informacion;

        /**
         * 1.
         * Configuración Propiedades Documento
         */
        $this->configurarDocumento();

        /**
         * 2.
         * Estruturamiento Esquema Reporte
         */
        $this->generarEsquemaDocumento();

        /**
         * 3.
         * Estruturamiento Información
         */
        $this->estruturarInformacion();

        /**
         * XX.
         * Retornar Documento Reporte
         */
        $this->retornarDocumento();
    }

    public function estruturarInformacion() {

        // Estilos Celdas
        {
            $styleCentradoVertical = array(
                'alignment' => array(
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
        }

        $i = 2;

        foreach ($this->informacion as $key => $value) {

            $this->objCal->getActiveSheet()->getRowDimension($i)->setRowHeight(50);

            // Elemento
            $this->objCal->setActiveSheetIndex(0)->setCellValue('A' . $i, $value['municipio'])->getStyle('A' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('B' . $i, $value['urbanizacion'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('C' . $i, $value['id_beneficiario'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('D' . $i, $value['numero_identificacion'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

            // Contrato
            $this->objCal->setActiveSheetIndex(0)->setCellValue('E' . $i, $value['NombreBeneficiario'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('F' . $i, $value['numero_contrato'])->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('G' . $i, $value['direccion'])->getStyle('G' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('H' . $i, $value['manzana'])->getStyle('H' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('I' . $i, $value['torre'])->getStyle('I' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('J' . $i, $value['bloque'])->getStyle('J' . $i)->applyFromArray($styleCentradoVertical);

            // IDIO y plan de instalación
            $this->objCal->setActiveSheetIndex(0)->setCellValue('K' . $i, $value['interior'])->getStyle('K' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('L' . $i, $value['lote'])->getStyle('L' . $i)->applyFromArray($styleCentradoVertical);

            // Entrega en bodega (aplica para equipos, materiales, infraestructura)
            $this->objCal->setActiveSheetIndex(0)->setCellValue('M' . $i, $value['piso'])->getStyle('M' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('N' . $i, $value['casa_apartamento'])->getStyle('N' . $i)->applyFromArray($styleCentradoVertical);

            $i++;
        }

    }

    public function estruturarValor($valor) {

        if ($valor == '1') {
            return "SI";
        }

        if ($valor == '0') {
            return "NO";
        }

    }
    public function generarEsquemaDocumento() {

        // Estilos Celdas
        {
            $styleCentrado = array(
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
        }

        $this->objCal->getActiveSheet()->getRowDimension(1)->setRowHeight(80);

        {
            // Estilos Columnas
            $this->objCal->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('D')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('H')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('I')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('N')->setWidth(15);

            $this->objCal->getActiveSheet()->getStyle('A')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('C')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('D')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('E')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('F')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('G')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('H')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('I')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('J')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('K')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('L')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('M')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('N')->getAlignment()->setWrapText(true);
        }
        // Add some data

        $this->objCal->setActiveSheetIndex(0)->setCellValue('A1', 'Municipio')->getStyle("A1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('B1', 'Urbanización')->getStyle("B1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('C1', 'Id Beneficiario')->getStyle("C1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('D1', 'Número de Identificación')->getStyle("D1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('E1', 'Nombre Beneficiario')->getStyle("E1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('F1', 'Número de Contrato')->getStyle("F1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('G1', 'Dirección')->getStyle("G1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('H1', 'Manzana')->getStyle("H1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('I1', 'Torre')->getStyle("I1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('J1', 'Bloque')->getStyle("J1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('K1', 'Interior')->getStyle("K1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('L1', 'Lote')->getStyle("L1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('M1', 'Piso')->getStyle("M1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('N1', 'Casa/Apartamento')->getStyle("N1")->applyFromArray($styleCentrado);

    }
    public function configurarDocumento() {
        $this->objCal = new \PHPExcel();
        // Set document properties

        switch ($_REQUEST['tipo_acta']) {
            case '1':
                $nombre = "Portatil";
                break;

            case '2':
                $nombre = "Servicio";
                break;
        }

        $this->objCal->getProperties()->setCreator("OpenKyOS")->setLastModifiedBy("OpenKyOS")->setTitle("Reporte Actas " . $nombre . " Generadas")->setSubject("Reporte Actas " . $nombre . " Generadas")->setDescription("Reporte Actas " . $nombre . " Generadas")->setCategory("Reporte");
    }
    public function retornarDocumento() {

        switch ($_REQUEST['tipo_acta']) {
            case '1':
                $nombre = "Portatil";
                break;

            case '2':
                $nombre = "Servicio";
                break;
        }

        //$fecha_inicio = $_REQUEST ['fecha_inicio'];
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //header ( 'Content-Disposition: attachment;filename="ReporteQuincenal(' . $fecha_inicio . ")-(" . $fecha_fin . ")" . time () . '.xlsx"' );
        header('Content-Disposition: attachment;filename="Reporte Actas ' . $nombre . ' Generadas ' . date("Y-m-d") . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        ob_clean();

        $objWriter = \PHPExcel_IOFactory::createWriter($this->objCal, 'Excel2007');
        $objWriter->save('php://output');

        exit();
    }
}

$miProcesador = new GenerarReporteExcelInstalaciones($this->miSql, $this->informacion);

?>

