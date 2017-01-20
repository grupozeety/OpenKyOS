<?php
namespace reportes\beneficiarios\entidad;

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
    public $beneficiarios;
    public $ruta_directorio = '';

    public function __construct($sql, $beneficiarios, $ruta_directorio) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->beneficiarios = $beneficiarios;
        $this->ruta_directorio_xls = $ruta_directorio;

        /**
         * 1. Configuración Propiedades Documento
         **/
        $this->configurarDocumento();

        /**
         * 2. Estruturamiento Esquema Reporte
         **/
        $this->generarEsquemaDocumento();

        /**
         * 3. Estruturamiento Esquema Reporte
         **/
        $this->estructurarInformacion();

        /**
         *4. Retornar Documento Reporte
         **/
        $this->retornarDocumento();

    }

    public function estructurarInformacion() {

        // Estilos Celdas
        {
            $styleCentrado = array(
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
            $styleCentradoVertical = array(
                'alignment' => array(
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
        }
        $i = 2;

        foreach ($this->beneficiarios as $key => $value) {

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('A' . $i, $value['departamento'])
                 ->getStyle("A" . $i)->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('B' . $i, $value['municipio'])
                 ->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('C' . $i, $value['id_proyecto'])
                 ->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('D' . $i, $value['proyecto'])
                 ->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('E' . $i, $value['tipo_beneficiario'])
                 ->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('F' . $i, $value['tipo_documento'])
                 ->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('G' . $i, $value['identificacion'])
                 ->getStyle('G' . $i)->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('H' . $i, $value['nombre'])
                 ->getStyle('H' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('I' . $i, $value['primer_apellido'])
                 ->getStyle('I' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('J' . $i, $value['segundo_apellido'])
                 ->getStyle('J' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('K' . $i, $value['genero'])
                 ->getStyle('K' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('L' . $i, $value['edad'])
                 ->getStyle('L' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('M' . $i, $value['nivel_estudio'])
                 ->getStyle('M' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('N' . $i, $value['correo'])
                 ->getStyle('N' . $i)->applyFromArray($styleCentradoVertical);

            //Pendiente Ajuste adicion si casa o Apto
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('O' . $i, (($value['telefono'] == '0') ? '' : $value['telefono']))
                 ->getStyle('O' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('P' . $i, $value['direccion'])
                 ->getStyle('P' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Q' . $i, (($value['manzana'] == '0') ? '' : $value['manzana']))
                 ->getStyle('Q' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('R' . $i, (($value['bloque'] == '0') ? '' : $value['bloque']))
                 ->getStyle('R' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('S' . $i, (($value['torre'] == '0') ? '' : $value['torre']))
                 ->getStyle('S' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('T' . $i, (($value['apartamento'] == '0') ? '' : $value['apartamento']))
                 ->getStyle('T' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('U' . $i, (($value['interior'] == '0') ? '' : $value['interior']))
                 ->getStyle('U' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('V' . $i, (($value['lote'] == '0') ? '' : $value['lote']))
                 ->getStyle('V' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('W' . $i, (($value['piso'] == '0') ? '' : $value['piso']))
                 ->getStyle('W' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('X' . $i, $value['minvi'])
                 ->getStyle('X' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Y' . $i, $value['barrio'])
                 ->getStyle('Y' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Z' . $i, $value['estrato'])
                 ->getStyle('Z' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
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

        }

        // Add some data

        $this->objCal->getActiveSheet()->getRowDimension('1')->setRowHeight(60);

        {
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('A1', 'Departamento')
                 ->getStyle('A1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('B1', 'Municipio')
                 ->getStyle('B1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('C1', 'ID Urbanización/Proyecto')
                 ->getStyle('C1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('D1', 'Urbanización/Proyecto')
                 ->getStyle('D1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('E1', 'Tipo Beneficiario')
                 ->getStyle('E1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('F1', 'Tipo Identificación')
                 ->getStyle('F1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('G1', 'Número Identificación')
                 ->getStyle('G1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('H1', 'Nombre')
                 ->getStyle('H1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('I1', 'Primer Apellido')
                 ->getStyle('I1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('J1', 'Segundo Apellido')
                 ->getStyle('J1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('K1', 'Genero')
                 ->getStyle('K1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('L1', 'Edad')
                 ->getStyle('L1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('M1', 'Nivel de Estudio')
                 ->getStyle('M1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('N1', 'Correo')
                 ->getStyle('N1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('O1', 'Teléfono')
                 ->getStyle('O1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('P1', 'Dirección')
                 ->getStyle('P1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Q1', 'Manzana')
                 ->getStyle('Q1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('R1', 'Bloque')
                 ->getStyle('R1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('S1', 'Torre')
                 ->getStyle('S1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('T1', 'Casa/Apartamento')
                 ->getStyle('T1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('U1', 'Interior')
                 ->getStyle('U1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('V1', 'Lote')
                 ->getStyle('V1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('W1', 'Piso')
                 ->getStyle('W1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('X1', 'MinVivienda')
                 ->getStyle('X1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Y1', 'Barrio')
                 ->getStyle('Y1')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Z1', 'Estrato Socioeconómico')
                 ->getStyle('Z1')->applyFromArray($styleCentrado);

        }

        $this->objCal->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('H')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('L')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('M')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('N')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('P')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('R')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('T')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('U')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('V')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('W')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('X')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
        $this->objCal->getActiveSheet()->getColumnDimension('Z')->setWidth(20);

    }

    public function configurarDocumento() {

        $this->objCal = new \PHPExcel();

        // Set document properties
        $this->objCal->getProperties()->setCreator("OpenKyOS")
             ->setLastModifiedBy("OpenKyOS")
             ->setTitle("Reporte de Beneficiarios (" . date('Y-m-d') . ")")
             ->setSubject("Reporte Beneficiarios")
             ->setDescription("Reporte de Beneficiarios")
             ->setCategory("Reporte");

        $this->objCal->getActiveSheet()->setTitle('ReporteBeneficiarios');

    }

    public function retornarDocumento() {

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReporteBeneficiarios' . time() . '.xlsx"');
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
        exit;

    }

}

$miProcesador = new GenerarReporteExcelInstalaciones($this->miSql, $this->beneficiarios, $this->ruta_directorio);

?>

