<?php

namespace reportes\llamadasCompletadas\entidad;

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
public function __construct($sql, $informacion,$informacion1,$informacion2) {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 10000);
        date_default_timezone_set('America/Bogota');

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->informacion = $informacion;
        $this->informacion1 = $informacion1;
        $this->informacion2 = $informacion2;


        //  var_dump($this->informacion2);
        //   exit;

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
              $this->objCal->setActiveSheetIndex(0)->setCellValue('A' . $i, $value['id_info_llam'])->getStyle('A' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('B' . $i, $value['t1'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('C' . $i, $value['estado'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);


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
                  $this->objCal2->setCellValue('A' . $i, $value['fecha'])->getStyle("A" . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('B' . $i, $value['total_llamadas'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('C' . $i, $value['exito_llamadas'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('D' . $i, $value['calculo_indicador'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

                  $i++;
                }
            }
        }


        // Hoja Calculo #3
        $i = 2;
        {
          if($this->informacion2)
          {
            foreach ($this->informacion2 as $key => $value) {


                $this->objCal3->getRowDimension($i)->setRowHeight(50);

              //  var_dump( $value);
              //  exit;

                // Elemento
                $this->objCal3->setCellValue('A' . $i, $value['fecha'])->getStyle("A" . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('B' . $i, $value['total_llamadas'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('C' . $i, $value['exito_llamadas'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('D' . $i, $value['calculo_indicador'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);


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

            $this->objCal->getActiveSheet()->getStyle('A')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('B')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('C')->getAlignment()->setWrapText(true);

        }
        // Add some data

        $this->objCal->setActiveSheetIndex(0)->setCellValue('A1', 'ID llamada')->getStyle("A1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('B1', 'Fecha recepción')->getStyle("B1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('C1', 'Estado')->getStyle("C1")->applyFromArray($styleCentrado);


      //Hoja 2
            {
              $this->objCal2->getRowDimension(1)->setRowHeight(80);

              {
                  // Estilos Columnas
                  $this->objCal2->getColumnDimension('A')->setWidth(30);
                  $this->objCal2->getColumnDimension('B')->setWidth(15);
                  $this->objCal2->getColumnDimension('C')->setWidth(15);
                  $this->objCal2->getColumnDimension('D')->setWidth(15);


                  $this->objCal2->getStyle('A')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('B')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('C')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('D')->getAlignment()->setWrapText(true);



              }
              // Add some data

              $this->objCal2->setCellValue('A1', 'Fecha')->getStyle("A1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('B1', 'Total llamadas recibidas')->getStyle("B1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('C1', 'Total llamadas exitosas')->getStyle("C1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('D1', 'Indicador de llamadas completadas')->getStyle("D1")->applyFromArray($styleCentrado);



            }

            //Hoja 3
                  {
                    $this->objCal3->getRowDimension(1)->setRowHeight(80);

                    {
                        // Estilos Columnas
                        $this->objCal3->getColumnDimension('A')->setWidth(20);
                        $this->objCal3->getColumnDimension('B')->setWidth(15);
                        $this->objCal3->getColumnDimension('C')->setWidth(15);
                        $this->objCal3->getColumnDimension('D')->setWidth(15);


                        $this->objCal3->getStyle('A')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('B')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('C')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('D')->getAlignment()->setWrapText(true);


                    }
                    // Add some data

                    $this->objCal3->setCellValue('A1', 'Fecha')->getStyle("A1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('B1', 'Total llamadas recibidas')->getStyle("B1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('C1', 'Total llamadas exitosas')->getStyle("C1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('D1', 'Indicador de llamadas completadas')->getStyle("D1")->applyFromArray($styleCentrado);



                  }

    }
    public function configurarDocumento() {
        $this->objCal = new \PHPExcel();
        // Set document properties
        $this->objCal->getProperties()->setCreator("OpenKyOS")->setLastModifiedBy("OpenKyOS")->setTitle("Reporte Indicador de Velocidad")->setSubject("Reporte Velocidad minima")->setDescription("Reportes asociado al indicador de velocidad minima")->setCategory("Reporte");
        $this->objCal->getActiveSheet()->setTitle('llamadasCompletadas');

        $this->objCal2 = $this->objCal->createSheet();
        $this->objCal2->setTitle('IndicadorllamadasCompletadas');

        $this->objCal3 = $this->objCal->createSheet();
        $this->objCal3->setTitle('ConsolidadoHistóricoIndicador');
    }
    public function retornarDocumento() {



            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        //    var_dump($this->objCal);
        //    var_dump($this->objCal2);
        //    var_dump($this->objCal3);
        //      exit;

            header('Content-Disposition: attachment;filename="ReportesllamadasCompletadas' . time() . '.xlsx"');
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

$miProcesador = new GenerarReporteExcelInstalaciones($this->miSql, $this->informacion,$this->informacion1,$this->informacion2);

?>
