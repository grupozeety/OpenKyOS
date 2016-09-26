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

        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('A3', 'Operador')
             ->getStyle("A3")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->mergeCells('B1:R1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('B1', 'Avance y  Estado Instalación NOC')
             ->getStyle("B1")->applyFromArray($styleCentrado);
        {
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

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('L')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('M')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('N')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('O')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('P')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('L')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('M')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('N')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('O')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('P')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getRowDimension('3')->setRowHeight(100);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('L3', 'Descripcion actividades de instalación, parametrización, integración con la red, pruebas, recibo')
                     ->getStyle('L3')->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('M3', 'Feha Inicio instalación y adecuaciones')
                     ->getStyle('M3')->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('N3', 'Fecha terminación instalación, integracion con red y pruebas de recibo')
                     ->getStyle('N3')->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('O3', 'Feha prevista en PI&PS Inicio instalación y adecuaciones')
                     ->getStyle('O3')->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('P3', 'Fecha prevista PI&PS terminación instalación y puesta en servicio')
                     ->getStyle('P3')->applyFromArray($styleCentrado);

            }

            {
                {
                    // Estilos

                    $this->objCal->getActiveSheet()->getStyle('Q')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getColumnDimension('Q')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('R')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getColumnDimension('R')->setWidth(15);

                }

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
        $this->objCal->setActiveSheetIndex(0)->mergeCells('S1:V1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('S1', 'Proyectos /Municipios')
             ->getStyle("S1")->applyFromArray($styleCentrado);

        {
            {
                // Estilos

                $this->objCal->getActiveSheet()->getStyle('S')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getColumnDimension('S')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('T')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getColumnDimension('T')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('U')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getColumnDimension('U')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('V')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getColumnDimension('V')->setWidth(15);

            }
            $this->objCal->setActiveSheetIndex(0)->mergeCells('S2:S3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('S2', 'DEPARTAMENTO')
                 ->getStyle('S2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)->mergeCells('T2:T3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('T2', 'MUNICIPIO')
                 ->getStyle('T2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)->mergeCells('U2:U3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('U2', 'Código DANE')
                 ->getStyle('U2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)->mergeCells('V2:V3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('V2', 'Proyecto / Urbanización')
                 ->getStyle('V2')->applyFromArray($styleCentrado);

        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('W1:AJ1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('W1', 'Avance y Estado Instalación Nodo Cabecera')
             ->getStyle("W1")->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)->mergeCells('W2:Y2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('W2', 'Infraestructura Nodos')
                 ->getStyle("W2")->applyFromArray($styleCentrado);

            {
                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('W')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('X')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('Y')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('W')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('X')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('Y')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('W3', 'Descripción obra o actividad')
                     ->getStyle("W3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('X3', 'Estado Avance (en construcción, terminado)')
                     ->getStyle("X3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('Y3', 'Fecha Prevista Terminación')
                     ->getStyle("Y3")->applyFromArray($styleCentrado);

            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('W2:Y2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('W2', 'Infraestructura Nodos')
                 ->getStyle("W2")->applyFromArray($styleCentrado);

            {
                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('W')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('X')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('Y')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('W')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('X')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('Y')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('W3', 'Descripción obra o actividad')
                     ->getStyle("W3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('X3', 'Estado Avance (en construcción, terminado)')
                     ->getStyle("X3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('Y3', 'Fecha Prevista Terminación')
                     ->getStyle("Y3")->applyFromArray($styleCentrado);

            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('Z2:AC2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Z2', 'Instalación Red troncal o interconexión ISP')
                 ->getStyle("Z2")->applyFromArray($styleCentrado);

            {
                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('Z')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('AA')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AB')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AC')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('Z')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AA')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AB')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AC')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('Z3', 'Descripción Actividad')
                     ->getStyle("Z3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AA3', 'Estado avance Instalación o entrega (Adquirido, instalado, probado, en funcionamiento)')
                     ->getStyle("AA3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AB3', 'Fecha Prevista Funcionamiento')
                     ->getStyle("AB3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AC3', 'Fecha Prevista en el PI&PS para la Instalación o Entrega Interconexión  ISP ')
                     ->getStyle("AC3")->applyFromArray($styleCentrado);

            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('AD2:AI2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AD2', 'Instalación y Puesta en Funcionamiento Equipos')
                 ->getStyle("AD2")->applyFromArray($styleCentrado);

            {
                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('AD')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AE')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AF')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AG')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AH')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AI')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('AD')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AE')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AF')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AG')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AH')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AI')->getAlignment()->setWrapText(true);
                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AD3', 'OLTs(Instalado, Probado, En Funcionamiento)')
                     ->getStyle("AD3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AE3', 'Equipos Networking (Instalados, Probados, En Funcionamiento)')
                     ->getStyle("AE3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AF3', 'Equipos de Energía y Complementarios (Instalados, Probados, En Funcionamiento)')
                     ->getStyle("AF3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AG3', 'Fecha Prevista Funcionamiento Nodo Cabecera')
                     ->getStyle("AG3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AH3', 'Fecha prevista en el PI&PS para el inicio instalación nodo Cabecera')
                     ->getStyle("AH3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AI3', 'Fecha Prevista Funcionamiento Nodo Cabecera')
                     ->getStyle("AI3")->applyFromArray($styleCentrado);

            }

            {
                {
                    // Estilos

                    $this->objCal->getActiveSheet()->getStyle('AJ')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getColumnDimension('AJ')->setWidth(15);

                }

                $this->objCal->setActiveSheetIndex(0)->mergeCells('AJ2:AJ3');
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AJ2', '% Avance Instalación Nodo Cabecera')
                     ->getStyle("AJ2")->applyFromArray($styleCentrado);

            }
        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('AK1:AQ1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('AK1', 'Avance y Estado Instalación Red de Distribución')
             ->getStyle("AK1")->applyFromArray($styleCentrado);

        {

            $this->objCal->setActiveSheetIndex(0)->mergeCells('AK2:AM2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AK2', 'Estado Construcción Red de Distribución')
                 ->getStyle("AK2")->applyFromArray($styleCentrado);

            {
                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('AK')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('AL')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AM')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('AK')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AL')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AM')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AK3', 'Descripción Construcción (Postería, Canalizaciones, Cámaras, Acometidas, Etc,  Cuando Aplique)')
                     ->getStyle("AK3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AL3', 'Estado Avance (En Construcción, Terminado)')
                     ->getStyle("AL3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AM3', 'Fecha Prevista Terminación')
                     ->getStyle("AM3")->applyFromArray($styleCentrado);

            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('AN2:AP2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AN2', 'Tendido y Puesta en Funcionamiento Fibra Óptica')
                 ->getStyle("AN2")->applyFromArray($styleCentrado);

            {

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('AN')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('AO')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AP')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('AN')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AO')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AP')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AN3', 'Descripción Actividades')
                     ->getStyle("AN3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AO3', 'Estado Avance (En Construcción, Terminado, Probado, En Funcionamiento)')
                     ->getStyle("AO3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AP3', 'Fecha Prevista Puesta en Funcionamiento')
                     ->getStyle("AP3")->applyFromArray($styleCentrado);

            }

            {
                {
                    // Estilos

                    $this->objCal->getActiveSheet()->getStyle('AQ')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getColumnDimension('AQ')->setWidth(15);

                }

                $this->objCal->setActiveSheetIndex(0)->mergeCells('AQ2:AQ3');
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AQ2', '% Avance Instalación Red Distribución')
                     ->getStyle("AQ2")->applyFromArray($styleCentrado);

            }
        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('AR1:AZ1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('AR1', 'Avance y Estado Instalación Nodo EOC')
             ->getStyle("AR1")->applyFromArray($styleCentrado);

        {

            $this->objCal->setActiveSheetIndex(0)->mergeCells('AR2:AT2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AR2', 'Infraestructura Nodo')
                 ->getStyle("AR2")->applyFromArray($styleCentrado);

            {

            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('AU2:AY2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AU2', 'Instalación y Puesta en Funcionamiento Equipos ')
                 ->getStyle("AY2")->applyFromArray($styleCentrado);

            {

            }

            {
                {
                    // Estilos

                    $this->objCal->getActiveSheet()->getStyle('AZ')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getColumnDimension('AZ')->setWidth(15);

                }

                $this->objCal->setActiveSheetIndex(0)->mergeCells('AZ2:AZ3');
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AZ2', '% Avance instalación Nodo EOC')
                     ->getStyle("AZ2")->applyFromArray($styleCentrado);

            }
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

