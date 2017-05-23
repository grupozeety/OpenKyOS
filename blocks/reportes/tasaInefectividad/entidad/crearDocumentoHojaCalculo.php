<?php

namespace reportes\tasaInefectividad\entidad;

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
    public $informacion1;
    public $informacion2;
    public $objCal;
  //  public $objCal2;
  //  public $objCal3;
  //  public $entro=true;
public function __construct($sql, $informacion,$informacion1) {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 10000);
        date_default_timezone_set('America/Bogota');

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->informacion = $informacion;
        $this->informacion1 = $informacion1;


        //  var_dump($this->informacion);
      //    var_dump($this->informacion1);
      //     exit;

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

        if($this->informacion)
        {
          foreach ($this->informacion as $key => $value) {


              $this->objCal->getActiveSheet()->getRowDimension($i)->setRowHeight(50);
            //  var_dump( $value);
            //  exit;

              // Elemento
              $this->objCal->setActiveSheetIndex(0)->setCellValue('A' . $i, $value['dane_departamento'])->getStyle('A' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('B' . $i, $value['fecha'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('C' . $i, $value['accesos_retirados'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('D' . $i, $value['accesos_ingresados'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('E' . $i, $value['accesos_enreubicacion'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('F' . $i, $value['accesos_reemplazados_mesanterior'])->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('G' . $i, $value['accesos_porreemplazar_mesanterior'])->getStyle('G' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('H' . $i, $value['total_accesos'])->getStyle('H' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('I' . $i, $value['indicador'])->getStyle('I' . $i)->applyFromArray($styleCentradoVertical);


              $i++;
          }
        }

       // Hoja Calculo #2
        $i = 2;
        {
          if($this->informacion1)
          {
              foreach ($this->informacion1 as $key => $value) {


                  $this->objCal2->getRowDimension($i)->setRowHeight(50);

                //  var_dump( $value);
                //  exit;

                  // Elemento
                  $this->objCal2->setCellValue('A' . $i, $value['anho'])->getStyle("A" . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('B' . $i, $value['mes'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('C' . $i, $value['dane_municipio'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('D' . $i, $value['identificacion_proyecto'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('E' . $i, $value['id_beneficiario'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('F' . $i, $value['categoria'])->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('G' . $i, $value['subcategoria_nocon'])->getStyle("G" . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('H' . $i, $value['subcategoria_con'])->getStyle('H' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('I' . $i, $value['fecha_con'])->getStyle('I' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('J' . $i, $value['fecha_retiro'])->getStyle('J' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('K' . $i, $value['fecha_ingreso'])->getStyle('K' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('L' . $i, $value['meta'])->getStyle('L' . $i)->applyFromArray($styleCentradoVertical);

                  $i++;
                }
            }
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

      //Hoja 1

        $this->objCal->getActiveSheet()->getRowDimension(1)->setRowHeight(50);

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

            $this->objCal->getActiveSheet()->getStyle('A')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('C')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('D')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('E')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('F')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('G')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('H')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('I')->getAlignment()->setWrapText(true);

        }
        // Add some data
        $this->objCal->setActiveSheetIndex(0)->setCellValue('A1', 'Departamento')->getStyle("A1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('B1', 'Fecha')->getStyle("B1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('C1', 'Cantidad Accesos retirados en el mes')->getStyle("C1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('D1', 'Cantidad Accesos ingresados en el mes')->getStyle("D1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('E1', 'Cantidad Accesos en trámite de reemplazo en el mes')->getStyle("E1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('F1', 'Cantidad de Accesos reemplazados en el mes anterior al mes reportado')->getStyle("F1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('G1', 'Cantidad de Accesos por reemplazar desde el mes anterior')->getStyle("G1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('H1', 'Cantidad Accesos Totales')->getStyle("H1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('I1', 'Cálculo Indicador')->getStyle("I1")->applyFromArray($styleCentrado);


      //Hoja 2
            {
              $this->objCal2->getRowDimension(1)->setRowHeight(80);

              {
                  // Estilos Columnas
                  $this->objCal2->getColumnDimension('A')->setWidth(30);
                  $this->objCal2->getColumnDimension('B')->setWidth(15);
                  $this->objCal2->getColumnDimension('C')->setWidth(15);
                  $this->objCal2->getColumnDimension('D')->setWidth(15);
                  $this->objCal2->getColumnDimension('E')->setWidth(15);
                  $this->objCal2->getColumnDimension('F')->setWidth(15);
                  $this->objCal2->getColumnDimension('G')->setWidth(30);
                  $this->objCal2->getColumnDimension('H')->setWidth(15);
                  $this->objCal2->getColumnDimension('I')->setWidth(15);
                  $this->objCal2->getColumnDimension('J')->setWidth(15);
                  $this->objCal2->getColumnDimension('K')->setWidth(15);
                  $this->objCal2->getColumnDimension('L')->setWidth(15);


                  $this->objCal2->getStyle('A')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('B')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('C')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('D')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('E')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('F')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('G')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('H')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('I')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('J')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('K')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('L')->getAlignment()->setWrapText(true);



              }
              // Add some data

              $this->objCal2->setCellValue('A1', 'Año')->getStyle("A1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('B1', 'Mes')->getStyle("B1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('C1', 'Código Dane')->getStyle("C1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('D1', 'Id urbanización')->getStyle("D1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('E1', 'Id beneficiario')->getStyle("E1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('F1', 'Categoria')->getStyle("F1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('G1', 'Subcategoría sin servicio no conectados')->getStyle("G1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('H1', 'Subcategoría sin servicio conectados')->getStyle("H1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('I1', 'Fecha de inicio sin servicio')->getStyle("I1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('J1', 'Fecha de desconexión para los accesos retirados')->getStyle("J1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('K1', 'Fecha de conexión para los accesos ingresados')->getStyle("K1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('L1', 'Meta')->getStyle("L1")->applyFromArray($styleCentrado);



            }



    }
    public function configurarDocumento() {
        $this->objCal = new \PHPExcel();
        // Set document properties
        $this->objCal->getProperties()->setCreator("OpenKyOS")->setLastModifiedBy("OpenKyOS")->setTitle("Reporte Indicador de Velocidad")->setSubject("Reporte Velocidad minima")->setDescription("Reportes asociado al indicador de velocidad minima")->setCategory("Reporte");
        $this->objCal->getActiveSheet()->setTitle('IndicadorTasaInefectividad');

        $this->objCal2 = $this->objCal->createSheet();
        $this->objCal2->setTitle('tasaInefectividad');


    }
    public function retornarDocumento() {



            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        //    var_dump($this->objCal);
        //    var_dump($this->objCal2);
        //    var_dump($this->objCal3);
        //      exit;

            header('Content-Disposition: attachment;filename="ReportestasaInefectividad' . time() . '.xlsx"');
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

$miProcesador = new GenerarReporteExcelInstalaciones($this->miSql, $this->informacion,$this->informacion1);

?>
