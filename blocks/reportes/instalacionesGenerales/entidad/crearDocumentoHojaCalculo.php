<?php
namespace reportes\instalacionesGenerales\entidad;

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";

require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel.php";

class GenerarReporteExcelInstalaciones {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;
    public $objCal;

    public function __construct($sql, $proyectos) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->proyectos = $proyectos;

        /**
         * 1. Configuración Propiedades Documento
         **/
        $this->configurarDocumento();

        /**
         * 2. Estruturamiento Esquema Reporte
         **/
        $this->generarEsquemaDocumento();

        /**
         * XX. Retornar Documento Reporte
         **/
        $this->retornarDocumento();

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
        // Add some data

        $this->objCal->setActiveSheetIndex(0)->mergeCells('B1:R1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('B1', 'Avance y  Estado Instalación NOC')
             ->getStyle("B1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('A3', 'Operador')
             ->getStyle("A3")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->mergeCells('B2:F2');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('B2', 'Centro de Gestión')
             ->getStyle("B2")->applyFromArray($styleCentrado);

        {

            {
                // Estilos Columnas
                $this->objCal->getActiveSheet()->getColumnDimension('B')->setWidth(50);
                $this->objCal->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->objCal->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->objCal->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->objCal->getActiveSheet()->getColumnDimension('F')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getStyle('C')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getStyle('D')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getStyle('E')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getStyle('F')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getRowDimension('3')->setRowHeight(100);

            }
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('B3', 'Descripcion actividades de instalación, parametrización, integración con la red, pruebas, recibo')
                 ->getStyle("B3")->applyFromArray($styleCentrado);
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('C3', 'Feha Inicio instalación y adecuaciones')
                 ->getStyle("C3")->applyFromArray($styleCentrado);
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('D3', 'Fecha terminación instalación, integracion con red y pruebas de recibo')
                 ->getStyle("D3")->applyFromArray($styleCentrado);
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('E3', 'Feha prevista en PI&PS Inicio instalación y adecuaciones')
                 ->getStyle("E3")->applyFromArray($styleCentrado);
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('F3', 'Fecha prevista PI&PS terminación instalación y puesta en servicio')
                 ->getStyle("F3")->applyFromArray($styleCentrado);

        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('G2:K2');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('G2', 'Mesa de Ayuda')
             ->getStyle("G2")->applyFromArray($styleCentrado);

        {

            {
                // Estilos Columnas
                $this->objCal->getActiveSheet()->getColumnDimension('G')->setWidth(50);
                $this->objCal->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $this->objCal->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $this->objCal->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $this->objCal->getActiveSheet()->getColumnDimension('K')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('G')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getStyle('H')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getStyle('I')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getStyle('J')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getStyle('K')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getRowDimension('3')->setRowHeight(100);

            }
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('G3', 'Descripcion actividades de instalación, parametrización, integración con la red, pruebas, recibo')
                 ->getStyle("G3")->applyFromArray($styleCentrado);
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('H3', 'Feha Inicio instalación y adecuaciones')
                 ->getStyle("H3")->applyFromArray($styleCentrado);
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('I3', 'Fecha terminación instalación, integracion con red y pruebas de recibo')
                 ->getStyle("I3")->applyFromArray($styleCentrado);
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('J3', 'Feha prevista en PI&PS Inicio instalación y adecuaciones')
                 ->getStyle("J3")->applyFromArray($styleCentrado);
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('K3', 'Fecha prevista PI&PS terminación instalación y puesta en servicio')
                 ->getStyle("K3")->applyFromArray($styleCentrado);

        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('L2:P2');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('L2', 'Otros Equipos o Sistemas en el NOC')
             ->getStyle("L2")->applyFromArray($styleCentrado);

        {
            $this->objCal->setActiveSheetIndex(0)->mergeCells('Q2:Q3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Q2', '% Avance Instalación NOC')
                 ->getStyle("Q2")->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)->mergeCells('R2:R3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('R2', 'Fecha prevista para verificación Interventoría')
                 ->getStyle("R2")->applyFromArray($styleCentrado);
        }

    }
    public function configurarDocumento() {

        $this->objCal = new \PHPExcel();

        // Set document properties
        $this->objCal->getProperties()->setCreator("OpenKyOS")
             ->setLastModifiedBy("OpenKyOS")
             ->setTitle("Reporte de Instalaciones (" . $_REQUEST['fecha_inicio'] . ")-(" . $_REQUEST['fecha_final'] . ")")
             ->setSubject("Reporte Instalaciones")
             ->setDescription("Reporte de Instalaciones en un determinado periodo de tiempo")
             ->setCategory("Reporte");

    }

    public function retornarDocumento() {

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReporteInstalaciones' . time() . '.xlsx"');
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

$miProcesador = new GenerarReporteExcelInstalaciones($this->miSql, $this->proyectos);

?>

