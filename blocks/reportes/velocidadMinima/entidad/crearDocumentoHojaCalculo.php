<?php

namespace reportes\velocidadMinima\entidad;

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
              $this->objCal->setActiveSheetIndex(0)->setCellValue('A' . $i, $value['fecha'])->getStyle('A' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('B' . $i, $value['dane_municipio'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('C' . $i, $value['tecnologia'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('D' . $i, $value['accesos_activos'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('E' . $i, $value['total_pruebas'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('F' . $i, $value['total_pruebaexitosa'])->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('G' . $i, $value['total_accesosexito'])->getStyle('G' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('H' . $i, $value['total_accesosfalla'])->getStyle('H' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('I' . $i, $value['vel_contratada_sub'])->getStyle('I' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('J' . $i, $value['vel_contratada_baj'])->getStyle('J' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('K' . $i, $value['velocidad_subida'])->getStyle('K' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('L' . $i, $value['velocidad_bajada'])->getStyle('L' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('M' . $i, $value['condicion_obtenida'])->getStyle('M' . $i)->applyFromArray($styleCentradoVertical);



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

                  $this->objCal2->setCellValue('B' . $i, $value['dane_municipio'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('C' . $i, $value['urbanizacion'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('D' . $i, $value['id_beneficiario'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('E' . $i, $value['velocidad_bajada'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('F' . $i, $value['sentido'])->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('G' . $i, $value['tecnologia_instalada'])->getStyle('G' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('H' . $i, $value['meta'])->getStyle('H' . $i)->applyFromArray($styleCentradoVertical);

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
                $this->objCal3->setCellValue('A' . $i, $value['fecha_medicion'])->getStyle("A" . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('B' . $i, $value['dane_municipio'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('C' . $i, $value['identificacion_proyecto'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('D' . $i, $value['id_beneficiario'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('E' . $i, $value['numero_ticket'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('F' . $i, $value['fecha_apertura'])->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('G' . $i, $value['identificador_ticket'])->getStyle('G' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('H' . $i, $value['descripcion_diagnostico_contratista'])->getStyle('H' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('I' . $i, $value['solucion'])->getStyle('I' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('J' . $i, $value['meta_proyecto'])->getStyle('J' . $i)->applyFromArray($styleCentradoVertical);

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
            $this->objCal->getActiveSheet()->getColumnDimension('J')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('K')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $this->objCal->getActiveSheet()->getColumnDimension('M')->setWidth(15);

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

        }
        // Add some data

        $this->objCal->setActiveSheetIndex(0)->setCellValue('A1', 'Fecha')->getStyle("A1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('B1', 'Dane municipio')->getStyle("B1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('C1', 'Tecnología')->getStyle("C1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('D1', 'Accesos activos')->getStyle("D1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('E1', 'Total Pruebas')->getStyle("E1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('F1', 'Pruebas exitosas')->getStyle("F1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('G1', 'Accesos exitosos')->getStyle("G1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('H1', 'Accesos fallidos')->getStyle("H1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('I1', 'Velocidad contratada de subida')->getStyle("I1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('J1', 'Velocidad contratada de bajada')->getStyle("J1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('K1', 'Indicador Velocidad de subida')->getStyle("K1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('L1', 'Indicador velocidad de bajada')->getStyle("L1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('M1', 'Condición Obtenida')->getStyle("M1")->applyFromArray($styleCentrado);


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
                  $this->objCal2->getColumnDimension('G')->setWidth(15);
                  $this->objCal2->getColumnDimension('H')->setWidth(15);


                  $this->objCal2->getStyle('A')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('B')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('C')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('D')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('E')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('F')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('G')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('H')->getAlignment()->setWrapText(true);


              }
              // Add some data

              $this->objCal2->setCellValue('A1', 'Fecha')->getStyle("A1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('B1', 'Dane municipio')->getStyle("B1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('C1', 'Urbanización')->getStyle("C1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('D1', 'Id beneficiario')->getStyle("D1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('E1', 'Tasa de Transmisión')->getStyle("E1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('F1', 'Sentido')->getStyle("F1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('G1', 'Tecnología')->getStyle("G1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('H1', 'Meta')->getStyle("H1")->applyFromArray($styleCentrado);

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
                        $this->objCal3->getColumnDimension('E')->setWidth(15);
                        $this->objCal3->getColumnDimension('F')->setWidth(20);
                        $this->objCal3->getColumnDimension('G')->setWidth(15);
                        $this->objCal3->getColumnDimension('H')->setWidth(30);
                        $this->objCal3->getColumnDimension('I')->setWidth(30);
                        $this->objCal3->getColumnDimension('J')->setWidth(15);

                        $this->objCal3->getStyle('A')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('B')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('C')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('D')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('E')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('F')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('G')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('H')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('I')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('J')->getAlignment()->setWrapText(true);

                    }
                    // Add some data

                    $this->objCal3->setCellValue('A1', 'Fecha de medición')->getStyle("A1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('B1', 'Dane municipio')->getStyle("B1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('C1', 'Urbanicación')->getStyle("C1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('D1', 'Id beneficiario')->getStyle("D1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('E1', 'No. Ticket')->getStyle("E1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('F1', 'Fecha apertura')->getStyle("F1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('G1', 'Estado')->getStyle("G1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('H1', 'Descripción')->getStyle("H1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('I1', 'Solución')->getStyle("I1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('J1', 'Meta')->getStyle("J1")->applyFromArray($styleCentrado);

                  }

    }
    public function configurarDocumento() {
        $this->objCal = new \PHPExcel();
        // Set document properties
        $this->objCal->getProperties()->setCreator("OpenKyOS")->setLastModifiedBy("OpenKyOS")->setTitle("Reporte Indicador de Velocidad")->setSubject("Reporte Velocidad minima")->setDescription("Reportes asociado al indicador de velocidad minima")->setCategory("Reporte");
        $this->objCal->getActiveSheet()->setTitle('ConsolidadoVelocidadMinima');

        $this->objCal2 = $this->objCal->createSheet();
        $this->objCal2->setTitle('PruebasVelocidadMinima');

        $this->objCal3 = $this->objCal->createSheet();
        $this->objCal3->setTitle('PruebasFallidas');
    }
    public function retornarDocumento() {



            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        //    var_dump($this->objCal);
        //    var_dump($this->objCal2);
        //    var_dump($this->objCal3);
        //      exit;

            header('Content-Disposition: attachment;filename="ReportesVelocidadMinima' . time() . '.xlsx"');
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
