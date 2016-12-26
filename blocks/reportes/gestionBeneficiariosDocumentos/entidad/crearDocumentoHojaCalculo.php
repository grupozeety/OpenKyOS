<?php

namespace reportes\gestionBeneficiariosDocumentos\entidad;

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
            $this->objCal->setActiveSheetIndex(0)->setCellValue('E' . $i, $value['Nombre Beneficiario'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

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

            $this->objCal->setActiveSheetIndex(0)->setCellValue('O' . $i, $this->estruturarValor($value['Cedula Beneficiario (Frente)']))->getStyle('O' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('P' . $i, $this->estruturarValor($value['Cédula Beneficiario (Reverso)']))->getStyle('P' . $i)->applyFromArray($styleCentradoVertical);

            // Entrega en sitio de instalación (aplica para equipos, materiales, infraestructura)
            $this->objCal->setActiveSheetIndex(0)->setCellValue('Q' . $i, $this->estruturarValor($value['Fotocopia Acta de entrega VIP']))->getStyle('Q' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('R' . $i, $this->estruturarValor($value['Certificado de servicio publico']))->getStyle('R' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('S' . $i, $this->estruturarValor($value['Certificado del proyecto catalogado como VIP']))->getStyle("S" . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('T' . $i, $this->estruturarValor($value['Documento que demuestra dirección de la vivienda del beneficia']))->getStyle("T" . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('U' . $i, $this->estruturarValor($value['Certificado No Internet ultimos 6 meses']))->getStyle("U" . $i)->applyFromArray($styleCentradoVertical);

            // Entrega servicios, interconexión ISP
            $this->objCal->setActiveSheetIndex(0)->setCellValue('V' . $i, $this->estruturarValor($value['Marco Contrato']))->getStyle("V" . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('W' . $i, $this->estruturarValor($value['Formato de recibo entrega de portátil']))->getStyle("W" . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('X' . $i, $this->estruturarValor($value['Fotografias de los equipos instalados en la vivienda']))->getStyle('X' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('Y' . $i, $this->estruturarValor($value['Fotografias de la vivienda con la dirección']))->getStyle('Y' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('Z' . $i, $this->estruturarValor($value['Fotografia panorámica de la vivienda']))->getStyle("Z" . $i)->applyFromArray($styleCentradoVertical);

            // PI&PS
            $this->objCal->setActiveSheetIndex(0)->setCellValue('AA' . $i, $this->estruturarValor($value['Foto en sitio portátil se entrega embalado']))->getStyle('AA' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('AB' . $i, $this->estruturarValor($value['Fotografias del computador navegando con el acceso instalado y ']))->getStyle('AB' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('AC' . $i, $this->estruturarValor($value['Fotografias del serial del computador']))->getStyle('AC' . $i)->applyFromArray($styleCentradoVertical);

            //Observaciones
            $this->objCal->setActiveSheetIndex(0)->setCellValue('AD' . $i, $this->estruturarValor($value['Foto personalización de la la carcasa']))->getStyle('AD' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('AE' . $i, $this->estruturarValor($value['Pantallazo o fotografia de la prueba de velocidad']))->getStyle('AE' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('AF' . $i, $this->estruturarValor($value['Foto personalización de la la carcasa']))->getStyle('AF' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)->setCellValue('AG' . $i, $this->estruturarValor($value['Acta de Entrega de Servicios de Banda Ancha al Usuario']))->getStyle('AG' . $i)->applyFromArray($styleCentradoVertical);

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
            $this->objCal->getActiveSheet()->getColumnDimension('O')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('P')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('R')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('S')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('T')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('U')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('V')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('W')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('X')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('Y')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('AA')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('AB')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('AC')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('AD')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('AE')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('AF')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('AG')->setWidth(15);

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
            $this->objCal->getActiveSheet()->getStyle('O')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('P')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('Q')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('R')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('S')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('T')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('U')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('V')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('W')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('X')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('Y')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('Z')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AA')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AB')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AC')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AD')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AE')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AF')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AG')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getRowDimension('1')->setRowHeight(100);

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

        $this->objCal->setActiveSheetIndex(0)->setCellValue('O1', 'Fotocopia Cédula (Frente)')->getStyle("O1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('P1', 'Fotocopia Cédula (Reverso)')->getStyle("P1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('Q1', 'Fotocopia Acta de entrega VIP')->getStyle("Q1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('R1', 'Certificado de servicio publico')->getStyle("R1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('S1', 'Certificado del proyecto catalogado como VIP')->getStyle("S1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('T1', ' Documento que demuestra dirección de la vivienda del beneficiario')->getStyle("T1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('U1', 'Certificado No Internet ultimos 6 meses')->getStyle("U1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('V1', 'Contrato Marco')->getStyle("V1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('W1', 'Formato de recibo entrega de portátil')->getStyle("W1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('X1', 'Fotografias de los equipos instalados en la vivienda')->getStyle("X1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('Y1', 'Fotografias de la vivienda con la dirección')->getStyle("Y1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('Z1', 'Fotografia panorámica de la vivienda')->getStyle("Z1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('AA1', 'Foto en sitio portátil se entrega embalado')->getStyle("AA1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('AB1', 'Fotografias del computador navegando con el acceso instalado y cartel')->getStyle("AB1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('AC1', 'Fotografias del serial del computador')->getStyle("AC1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('AD1', 'Foto personalización de la la carcasa')->getStyle("AD1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('AE1', 'Pantallazo o fotografia de la prueba de velocidad')->getStyle("AE1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('AF1', 'Pantallazo aprovisionamiento velocidad contratada')->getStyle("AF1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('AG1', 'Acta de Entrega de Servicios de Banda Ancha al Usuario')->getStyle("AG1")->applyFromArray($styleCentrado);

    }
    public function configurarDocumento() {
        $this->objCal = new \PHPExcel();
        // Set document properties
        $this->objCal->getProperties()->setCreator("OpenKyOS")->setLastModifiedBy("OpenKyOS")->setTitle("Reporte Beneficiario Vs Documentos")->setSubject("Reporte Instalaciones")->setDescription("Reporte de Quincenal en un determinado periodo de tiempo")->setCategory("Reporte");
    }
    public function retornarDocumento() {

        //$fecha_inicio = $_REQUEST ['fecha_inicio'];
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //header ( 'Content-Disposition: attachment;filename="ReporteQuincenal(' . $fecha_inicio . ")-(" . $fecha_fin . ")" . time () . '.xlsx"' );
        header('Content-Disposition: attachment;filename="Reporte Beneficiarios Vs Documentacion ' . date("Y-m-d") . '.xlsx"');
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

