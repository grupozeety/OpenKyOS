<?php

namespace reportes\disponibilidad\entidad;

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
    public $informacion3;
    public $informacion4;
    public $informacion5;
    public $informacion6;
    public $informacion7;
    public $objCal;
  //  public $objCal2;
  //  public $objCal3;
  //  public $entro=true;
public function __construct($sql, $informacion,$informacion1,$informacion2, $informacion3,$informacion4,$informacion5,$informacion6,$informacion7) {
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 10000);
        date_default_timezone_set('America/Bogota');

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->informacion = $informacion;
        $this->informacion1 = $informacion1;
        $this->informacion2 = $informacion2;
        $this->informacion3 = $informacion3;
        $this->informacion4 = $informacion4;
        $this->informacion5 = $informacion5;
        $this->informacion6 = $informacion6;
        $this->informacion7 = $informacion7;

        //  var_dump($this->informacion);

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
              $this->objCal->setActiveSheetIndex(0)->setCellValue('A' . $i, $value['fecha'])->getStyle('A' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('B' . $i, $value['dane_municipio'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('C' . $i, $value['tickes_abiertos'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('D' . $i, $value['tickes_cerrados'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('E' . $i, $value['tickets_anteriores'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('F' . $i, $value['tickes_indisponibilidad'])->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('G' . $i, $value['tiempo_indisponibilidad'])->getStyle('G' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('H' . $i, $value['tickes_parada'])->getStyle('H' . $i)->applyFromArray($styleCentradoVertical);

              $this->objCal->setActiveSheetIndex(0)->setCellValue('I' . $i, $value['tiempo_parada_reloj'])->getStyle('I' . $i)->applyFromArray($styleCentradoVertical);



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

              //    var_dump( $value);
              //    exit;

                  // Elemento
                  $this->objCal2->setCellValue('A' . $i, $value['mes'])->getStyle("A" . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('B' . $i, $value['dane_municipio'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('C' . $i, $value['indicador'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('D' . $i, $value['umbral'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

                  $this->objCal2->setCellValue('E' . $i, $value['cumplimiento'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

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
                $this->objCal3->setCellValue('A' . $i, $value['anho'])->getStyle("A" . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('B' . $i, $value['mes'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('C' . $i, $value['dane_municipio'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('D' . $i, $value['identificacion_proyecto'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('E' . $i, $value['id_beneficiario'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('F' . $i, $value['fuente_generacion'])->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('G' . $i, $value['numero_ticket'])->getStyle("G" . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('H' . $i, $value['fecha_apertura'])->getStyle('H' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('I' . $i, $value['fecha_cierre'])->getStyle('I' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('J' . $i, $value['fecha_registro'])->getStyle('J' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('K' . $i, $value['afectacion_servicio'])->getStyle('K' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('L' . $i, $value['bandera_parad_reloj'])->getStyle('L' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('M' . $i, $value['fecha_inicio_parada_reloj'])->getStyle("M" . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('N' . $i, $value['fecha_fin_parada_reloj'])->getStyle('N' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('O' . $i, $value['tiempo_falla'])->getStyle('O' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('P' . $i, $value['tiempo_parada_reloj'])->getStyle('P' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('Q' . $i, $value['responsable'])->getStyle('Q' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('R' . $i, $value['tipo_ticket'])->getStyle('R' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('S' . $i, $value['estado_ticket'])->getStyle("S" . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('T' . $i, $value['tiempo_resolucion'])->getStyle('T' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('U' . $i, $value['justificacion_parada_reloj'])->getStyle('U' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('V' . $i, $value['descripcion_ticket'])->getStyle('V' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('W' . $i, $value['descripcion_diagnostico_contratista'])->getStyle('W' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('X' . $i, $value['solucion'])->getStyle('X' . $i)->applyFromArray($styleCentradoVertical);

                $this->objCal3->setCellValue('Y' . $i, $value['meta_proyecto'])->getStyle('Y' . $i)->applyFromArray($styleCentradoVertical);


                $i++;
              }
            }
        }
        // Hoja Calculo #4
         $i = 2;
         {
           if($this->informacion3)
           {
               foreach ($this->informacion3 as $key => $value) {


                   $this->objCal4->getRowDimension($i)->setRowHeight(50);

                 //  var_dump( $value);
                 //  exit;

                   // Elemento
                   $this->objCal4->setCellValue('A' . $i, $value['dane_municipio'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                   $this->objCal4->setCellValue('B' . $i, $value['identificacion_proyecto'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

                   $this->objCal4->setCellValue('C' . $i, $value['id_beneficiario'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

                   $this->objCal4->setCellValue('D' . $i, $value['estado_servicio'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

                   $this->objCal4->setCellValue('E' . $i, $value['causa'])->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

                   $i++;
                 }
             }
         }
         // Hoja Calculo #5
          $i = 2;
          {
            if($this->informacion4)
            {
                foreach ($this->informacion4 as $key => $value) {


                    $this->objCal5->getRowDimension($i)->setRowHeight(50);

                  //  var_dump( $value);
                  //  exit;

                    // Elemento
                    $this->objCal5->setCellValue('A' . $i, $value['anho'])->getStyle("A" . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal5->setCellValue('B' . $i, $value['mes'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal5->setCellValue('C' . $i, $value['numero_ticket'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal5->setCellValue('D' . $i, $value['accion_seguimiento'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);


                    $i++;
                  }
              }
          }
          // Hoja Calculo #6
           $i = 2;
           {
             if($this->informacion5)
             {
                 foreach ($this->informacion5 as $key => $value) {


                     $this->objCal6->getRowDimension($i)->setRowHeight(50);

                   //  var_dump( $value);
                   //  exit;

                     // Elemento
                     $this->objCal6->setCellValue('A' . $i, $value['numero_ticket'])->getStyle("A" . $i)->applyFromArray($styleCentradoVertical);

                     $this->objCal6->setCellValue('B' . $i, $value['fecha'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                     $this->objCal6->setCellValue('C' . $i, $value['descripcion_diagnostico_contratista'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

                     $this->objCal6->setCellValue('D' . $i, $value['pruebas_mantenimiento'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

                     $this->objCal6->setCellValue('E' . $i, $value['solucion'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);


                     $i++;
                   }
               }
           }
           // Hoja Calculo #7
            $i = 2;
            {
              if($this->informacion6)
              {
                  foreach ($this->informacion6 as $key => $value) {


                      $this->objCal7->getRowDimension($i)->setRowHeight(50);

                    //  var_dump( $value);
                    //  exit;

                      // Elemento
                      $this->objCal7->setCellValue('A' . $i, $value['dane_municipio'])->getStyle("A" . $i)->applyFromArray($styleCentradoVertical);

                      $this->objCal7->setCellValue('B' . $i, $value['id_beneficiario'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                      $this->objCal7->setCellValue('C' . $i, $value['mes'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

                      $this->objCal7->setCellValue('D' . $i, $value['fecha'])->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

                      $this->objCal7->setCellValue('E' . $i, $value['velocidad_subida'])->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

                      $this->objCal7->setCellValue('F' . $i, $value['velocidad_bajada'])->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

                      $this->objCal7->setCellValue('G' . $i, $value['observaciones_no_medicion'])->getStyle('G' . $i)->applyFromArray($styleCentradoVertical);

                      $i++;
                    }
                }
            }
            // Hoja Calculo #8
             $i = 2;
             {
               if($this->informacion7)
               {
                   foreach ($this->informacion7 as $key => $value) {


                       $this->objCal8->getRowDimension($i)->setRowHeight(50);

                     //  var_dump( $value);
                     //  exit;

                       // Elemento
                       $this->objCal8->setCellValue('A' . $i, $value['id_beneficiario'])->getStyle("A" . $i)->applyFromArray($styleCentradoVertical);

                       $this->objCal8->setCellValue('B' . $i, $value['mes'])->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

                       $this->objCal8->setCellValue('C' . $i, $value['paginas'])->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

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

        $this->objCal->setActiveSheetIndex(0)->setCellValue('A1', 'Fecha')->getStyle("A1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('B1', 'Código DANE')->getStyle("B1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('C1', 'Tickets abiertos')->getStyle("C1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('D1', 'Tickets cerrados')->getStyle("D1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('E1', 'Tickets abiertos mes anterior')->getStyle("E1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('F1', 'Tickets de indisponibilidad')->getStyle("F1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('G1', 'Tiempo de indisponibilidad')->getStyle("G1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('H1', 'Tickets con parada de reloj')->getStyle("H1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->setCellValue('I1', 'Tiempo de parada de reloj')->getStyle("I1")->applyFromArray($styleCentrado);


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


                  $this->objCal2->getStyle('A')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('B')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('C')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('D')->getAlignment()->setWrapText(true);
                  $this->objCal2->getStyle('E')->getAlignment()->setWrapText(true);


              }
              // Add some data

              $this->objCal2->setCellValue('A1', 'Mes')->getStyle("A1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('B1', 'Código Dane')->getStyle("B1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('C1', 'Indicador')->getStyle("C1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('D1', 'Umbral')->getStyle("D1")->applyFromArray($styleCentrado);

              $this->objCal2->setCellValue('E1', 'Cumplimiento')->getStyle("E1")->applyFromArray($styleCentrado);


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
                        $this->objCal3->getColumnDimension('F')->setWidth(15);
                        $this->objCal3->getColumnDimension('G')->setWidth(20);
                        $this->objCal3->getColumnDimension('H')->setWidth(15);
                        $this->objCal3->getColumnDimension('I')->setWidth(15);
                        $this->objCal3->getColumnDimension('J')->setWidth(15);
                        $this->objCal3->getColumnDimension('K')->setWidth(15);
                        $this->objCal3->getColumnDimension('L')->setWidth(15);
                        $this->objCal3->getColumnDimension('M')->setWidth(20);
                        $this->objCal3->getColumnDimension('N')->setWidth(15);
                        $this->objCal3->getColumnDimension('O')->setWidth(15);
                        $this->objCal3->getColumnDimension('P')->setWidth(15);
                        $this->objCal3->getColumnDimension('Q')->setWidth(15);
                        $this->objCal3->getColumnDimension('R')->setWidth(15);
                        $this->objCal3->getColumnDimension('S')->setWidth(20);
                        $this->objCal3->getColumnDimension('T')->setWidth(15);
                        $this->objCal3->getColumnDimension('U')->setWidth(15);
                        $this->objCal3->getColumnDimension('V')->setWidth(15);
                        $this->objCal3->getColumnDimension('W')->setWidth(15);
                        $this->objCal3->getColumnDimension('X')->setWidth(15);
                        $this->objCal3->getColumnDimension('Y')->setWidth(15);


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
                        $this->objCal3->getStyle('K')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('L')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('M')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('N')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('O')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('P')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('Q')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('R')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('S')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('T')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('U')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('V')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('W')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('X')->getAlignment()->setWrapText(true);
                        $this->objCal3->getStyle('Y')->getAlignment()->setWrapText(true);


                    }
                    // Add some data

                    $this->objCal3->setCellValue('A1', 'Año')->getStyle("A1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('B1', 'Mes')->getStyle("B1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('C1', 'Dane municipio')->getStyle("C1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('D1', 'Id proyecto')->getStyle("D1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('E1', 'Id beneficiario')->getStyle("E1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('F1', 'Fuente de ticket')->getStyle("F1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('G1', 'Id ticket')->getStyle("G1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('H1', 'Fecha apertura')->getStyle("H1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('I1', 'Fecha cierre')->getStyle("I1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('J1', 'Fecha creación')->getStyle("J1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('K1', 'Bandera de afectación del servicio')->getStyle("K1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('L1', 'Bandera de Parada de reloj')->getStyle("L1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('M1', 'Fechas de inicio de la(s) parada(s) de reloj')->getStyle("M1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('N1', 'Fechas fin de la(s) parada(s) de reloj')->getStyle("N1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('O1', 'Tiempo de indisponibilidad')->getStyle("O1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('P1', 'Tiempo Parada de Reloj')->getStyle("P1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('Q1', 'Responsabilidad')->getStyle("Q1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('R1', 'Tipificación del ticket')->getStyle("R1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('S1', 'Estado Ticket')->getStyle("S1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('T1', 'Tiempo de resolución')->getStyle("T1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('U1', 'Descripción de la justificación de parada de reloj')->getStyle("U1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('V1', 'Descripción del requerimiento')->getStyle("V1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('W1', 'Descripción del diagnóstico del contratista')->getStyle("W1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('X1', 'Descripción de la solución')->getStyle("X1")->applyFromArray($styleCentrado);

                    $this->objCal3->setCellValue('Y1', 'Meta de entrada en operación del acceso')->getStyle("Y1")->applyFromArray($styleCentrado);



                  }

                  //Hoja 4
                        {
                          $this->objCal4->getRowDimension(1)->setRowHeight(80);

                          {
                              // Estilos Columnas
                              $this->objCal4->getColumnDimension('A')->setWidth(30);
                              $this->objCal4->getColumnDimension('B')->setWidth(15);
                              $this->objCal4->getColumnDimension('C')->setWidth(15);
                              $this->objCal4->getColumnDimension('D')->setWidth(15);
                              $this->objCal4->getColumnDimension('E')->setWidth(15);


                              $this->objCal4->getStyle('A')->getAlignment()->setWrapText(true);
                              $this->objCal4->getStyle('B')->getAlignment()->setWrapText(true);
                              $this->objCal4->getStyle('C')->getAlignment()->setWrapText(true);
                              $this->objCal4->getStyle('D')->getAlignment()->setWrapText(true);
                              $this->objCal4->getStyle('E')->getAlignment()->setWrapText(true);


                          }
                          // Add some data

                          $this->objCal4->setCellValue('A1', 'Dane Municipio')->getStyle("A1")->applyFromArray($styleCentrado);

                          $this->objCal4->setCellValue('B1', 'Id urbanización')->getStyle("B1")->applyFromArray($styleCentrado);

                          $this->objCal4->setCellValue('C1', 'Id beneficiario')->getStyle("C1")->applyFromArray($styleCentrado);

                          $this->objCal4->setCellValue('D1', 'Estado')->getStyle("D1")->applyFromArray($styleCentrado);

                          $this->objCal4->setCellValue('E1', 'Causa')->getStyle("E1")->applyFromArray($styleCentrado);


                        }

                        //Hoja 5
                              {
                                $this->objCal5->getRowDimension(1)->setRowHeight(80);

                                {
                                    // Estilos Columnas
                                    $this->objCal5->getColumnDimension('A')->setWidth(30);
                                    $this->objCal5->getColumnDimension('B')->setWidth(15);
                                    $this->objCal5->getColumnDimension('C')->setWidth(15);
                                    $this->objCal5->getColumnDimension('D')->setWidth(15);

                                    $this->objCal5->getStyle('A')->getAlignment()->setWrapText(true);
                                    $this->objCal5->getStyle('B')->getAlignment()->setWrapText(true);
                                    $this->objCal5->getStyle('C')->getAlignment()->setWrapText(true);
                                    $this->objCal5->getStyle('D')->getAlignment()->setWrapText(true);
                                  }
                                // Add some data

                                $this->objCal5->setCellValue('A1', 'Año')->getStyle("A1")->applyFromArray($styleCentrado);

                                $this->objCal5->setCellValue('B1', 'Mes')->getStyle("B1")->applyFromArray($styleCentrado);

                                $this->objCal5->setCellValue('C1', 'No. de ticket')->getStyle("C1")->applyFromArray($styleCentrado);

                                $this->objCal5->setCellValue('D1', 'Acciones de seguimiento')->getStyle("D1")->applyFromArray($styleCentrado);


                              }

                              //Hoja 6
                                    {
                                      $this->objCal6->getRowDimension(1)->setRowHeight(80);

                                      {
                                          // Estilos Columnas
                                          $this->objCal6->getColumnDimension('A')->setWidth(30);
                                          $this->objCal6->getColumnDimension('B')->setWidth(15);
                                          $this->objCal6->getColumnDimension('C')->setWidth(15);
                                          $this->objCal6->getColumnDimension('D')->setWidth(15);
                                          $this->objCal6->getColumnDimension('E')->setWidth(15);


                                          $this->objCal6->getStyle('A')->getAlignment()->setWrapText(true);
                                          $this->objCal6->getStyle('B')->getAlignment()->setWrapText(true);
                                          $this->objCal6->getStyle('C')->getAlignment()->setWrapText(true);
                                          $this->objCal6->getStyle('D')->getAlignment()->setWrapText(true);
                                          $this->objCal6->getStyle('E')->getAlignment()->setWrapText(true);


                                      }
                                      // Add some data

                                      $this->objCal6->setCellValue('A1', 'No. de ticket')->getStyle("A1")->applyFromArray($styleCentrado);

                                      $this->objCal6->setCellValue('B1', 'Fecha')->getStyle("B1")->applyFromArray($styleCentrado);

                                      $this->objCal6->setCellValue('C1', 'Actividad del diagnóstico')->getStyle("C1")->applyFromArray($styleCentrado);

                                      $this->objCal6->setCellValue('D1', 'Pruebas adelantadas')->getStyle("D1")->applyFromArray($styleCentrado);

                                      $this->objCal6->setCellValue('E1', 'Solución dada')->getStyle("E1")->applyFromArray($styleCentrado);


                                    }

                                    //Hoja 7
                                          {
                                            $this->objCal7->getRowDimension(1)->setRowHeight(80);

                                            {
                                                // Estilos Columnas
                                                $this->objCal7->getColumnDimension('A')->setWidth(30);
                                                $this->objCal7->getColumnDimension('B')->setWidth(15);
                                                $this->objCal7->getColumnDimension('C')->setWidth(15);
                                                $this->objCal7->getColumnDimension('D')->setWidth(15);
                                                $this->objCal7->getColumnDimension('E')->setWidth(15);
                                                $this->objCal7->getColumnDimension('F')->setWidth(15);


                                                $this->objCal7->getStyle('A')->getAlignment()->setWrapText(true);
                                                $this->objCal7->getStyle('B')->getAlignment()->setWrapText(true);
                                                $this->objCal7->getStyle('C')->getAlignment()->setWrapText(true);
                                                $this->objCal7->getStyle('D')->getAlignment()->setWrapText(true);
                                                $this->objCal7->getStyle('E')->getAlignment()->setWrapText(true);
                                                $this->objCal7->getStyle('F')->getAlignment()->setWrapText(true);


                                            }
                                            // Add some data

                                            $this->objCal7->setCellValue('A1', 'Id Beneficiario')->getStyle("A1")->applyFromArray($styleCentrado);

                                            $this->objCal7->setCellValue('B1', 'Mes')->getStyle("B1")->applyFromArray($styleCentrado);

                                            $this->objCal7->setCellValue('C1', 'Fecha medición')->getStyle("C1")->applyFromArray($styleCentrado);

                                            $this->objCal7->setCellValue('D1', 'Velocidad de subida')->getStyle("D1")->applyFromArray($styleCentrado);

                                            $this->objCal7->setCellValue('E1', 'Velocidad de bajada')->getStyle("E1")->applyFromArray($styleCentrado);

                                            $this->objCal7->setCellValue('F1', 'Observaciones')->getStyle("F1")->applyFromArray($styleCentrado);


                                          }
                                          //Hoja 8
                                                {
                                                  $this->objCal8->getRowDimension(1)->setRowHeight(80);

                                                  {
                                                      // Estilos Columnas
                                                      $this->objCal8->getColumnDimension('A')->setWidth(30);
                                                      $this->objCal8->getColumnDimension('B')->setWidth(15);
                                                      $this->objCal8->getColumnDimension('C')->setWidth(15);

                                                      $this->objCal8->getStyle('A')->getAlignment()->setWrapText(true);
                                                      $this->objCal8->getStyle('B')->getAlignment()->setWrapText(true);
                                                      $this->objCal8->getStyle('C')->getAlignment()->setWrapText(true);


                                                  }
                                                  // Add some data

                                                  $this->objCal8->setCellValue('A1', 'Beneficiario')->getStyle("A1")->applyFromArray($styleCentrado);

                                                  $this->objCal8->setCellValue('B1', 'Mes')->getStyle("B1")->applyFromArray($styleCentrado);

                                                  $this->objCal8->setCellValue('C1', 'Páginas visitadas')->getStyle("C1")->applyFromArray($styleCentrado);

                                                    }

    }
    public function configurarDocumento() {
        $this->objCal = new \PHPExcel();
        // Set document properties
        $this->objCal->getProperties()->setCreator("OpenKyOS")->setLastModifiedBy("OpenKyOS")->setTitle("Reporte Indicador de disponibilidad")->setSubject("Reporte Velocidad minima")->setDescription("Reportes asociado al indicador de velocidad minima")->setCategory("Reporte");
        $this->objCal->getActiveSheet()->setTitle('Disponibilidad');

        $this->objCal2 = $this->objCal->createSheet();
        $this->objCal2->setTitle('IndicadorDisponibilidad');

        $this->objCal3 = $this->objCal->createSheet();
        $this->objCal3->setTitle('TickesMensuales');

        $this->objCal4 = $this->objCal->createSheet();
        $this->objCal4->setTitle('EstadoAccesos');

        $this->objCal5 = $this->objCal->createSheet();
        $this->objCal5->setTitle('InformacionCualitativa');

        $this->objCal6 = $this->objCal->createSheet();
        $this->objCal6->setTitle('Mantenimientos');

        $this->objCal7 = $this->objCal->createSheet();
        $this->objCal7->setTitle('Tráfico');

        $this->objCal8 = $this->objCal->createSheet();
        $this->objCal8->setTitle('PáginasConsultadas');
    }
    public function retornarDocumento() {



            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        //    var_dump($this->objCal);
        //    var_dump($this->objCal2);
        //    var_dump($this->objCal3);
        //      exit;

            header('Content-Disposition: attachment;filename="Reportesdisponibilidad' . time() . '.xlsx"');
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

$miProcesador = new GenerarReporteExcelInstalaciones($this->miSql, $this->informacion,$this->informacion1,$this->informacion2,$this->informacion3,$this->informacion4,$this->informacion5,$this->informacion6,$this->informacion7);

?>
