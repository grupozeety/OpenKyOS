<?php
namespace reportes\comisionamientoInstalaciones\entidad;

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
        $i = 3;

        foreach ($this->beneficiarios as $key => $value) {

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('A' . $i, $value['departamento'])
                 ->getStyle("A" . $i)->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('B' . $i, $value['municipio'])
                 ->getStyle('B' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('C' . $i, $value['urbanizacion'])
                 ->getStyle('C' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('D' . $i, $value['numero_identificacion'])
                 ->getStyle('D' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('E' . $i, $value['nombres'] . " " . $value['primer_apellido'] . " " . $value['segundo_apellido'])
                 ->getStyle('E' . $i)->applyFromArray($styleCentradoVertical);

            $anexo_dir = '';

            if ($value['manzana'] != 0) {
                $anexo_dir .= " Manzana  #" . $value['manzana'] . " - ";
            }

            if ($value['bloque'] != 0) {
                $anexo_dir .= " Bloque #" . $value['bloque'] . " - ";
            }

            if ($value['torre'] != 0) {
                $anexo_dir .= " Torre #" . $value['torre'] . " - ";
            }

            if ($value['casa_apartamento'] != 0) {
                $anexo_dir .= " Casa/Apartamento #" . $value['casa_apartamento'];
            }

            if ($value['interior'] != 0) {
                $anexo_dir .= " Interior #" . $value['interior'];
            }

            if ($value['lote'] != 0) {
                $anexo_dir .= " Lote #" . $value['lote'];
            }

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('F' . $i, $value['direccion_domicilio'] . " " . $anexo_dir)
                 ->getStyle('F' . $i)->applyFromArray($styleCentradoVertical);

            /**
             * Kit Portatil
             */

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('G' . $i, $value['serial_portatil'])
                 ->getStyle('G' . $i)->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('H' . $i, $value['mac_esc'])
                 ->getStyle('H' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('I' . $i, $value['mac2_esc'])
                 ->getStyle('I' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('J' . $i, $value['serial_esc'])
                 ->getStyle('J' . $i)->applyFromArray($styleCentradoVertical);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('K' . $i, $value['ip_esc'])
                 ->getStyle('K' . $i)->applyFromArray($styleCentradoVertical);

            switch ($value['tipo_tecnologia']) {

            }

            switch ($value['tipo_tecnologia']) {

                case '95':

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('L' . $i, "N/A")
                         ->getStyle('L' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('M' . $i, "N/A")
                         ->getStyle('M' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('N' . $i, "N/A")
                         ->getStyle('N' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('O' . $i, "N/A")
                         ->getStyle('O' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('P' . $i, "N/A")
                         ->getStyle('P' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('Q' . $i, "N/A")
                         ->getStyle('Q' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('R' . $i, "N/A")
                         ->getStyle('R' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('S' . $i, "N/A")
                         ->getStyle('S' . $i)->applyFromArray($styleCentradoVertical);
                    //-------------------------

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('T' . $i, $value['ip_olt'])
                         ->getStyle('T' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('U' . $i, $value['mac_olt'])
                         ->getStyle('U' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('V' . $i, $value['port_olt'])
                         ->getStyle('V' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('W' . $i, $value['nombre_olt'])
                         ->getStyle('W' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('X' . $i, $value['puerto_olt'])
                         ->getStyle('X' . $i)->applyFromArray($styleCentrado);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('Y' . $i, $value['mac_master_eoc'])
                         ->getStyle('Y' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('Z' . $i, $value['ip_master_eoc'])
                         ->getStyle('Z' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AA' . $i, $value['ip_onu_eoc'])
                         ->getStyle('AB' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AB' . $i, $value['mac_onu_eoc'])
                         ->getStyle('AB' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AC' . $i, $value['ip_hub_eoc'])
                         ->getStyle('AC' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AD' . $i, $value['mac_hub_eoc'])
                         ->getStyle('AD' . $i)->applyFromArray($styleCentrado);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AE' . $i, $value['mac_cpe_eoc'])
                         ->getStyle('AE' . $i)->applyFromArray($styleCentradoVertical);
                    break;

                case '96':

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('L' . $i, $value['ip_celda'])
                         ->getStyle('L' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('M' . $i, $value['mac_celda'])
                         ->getStyle('M' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('N' . $i, $value['nombre_nodo'])
                         ->getStyle('N' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('O' . $i, $value['nombre_sectorial'])
                         ->getStyle('O' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('P' . $i, $value['ip_switch_celda'])
                         ->getStyle('P' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('Q' . $i, $value['ip_sm_celda'])
                         ->getStyle('Q' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('R' . $i, $value['mac_sm_celda'])
                         ->getStyle('R' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('S' . $i, $value['mac_cpe_celda'])
                         ->getStyle('S' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('T' . $i, "N/A")
                         ->getStyle('T' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('U' . $i, "N/A")
                         ->getStyle('U' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('V' . $i, "N/A")
                         ->getStyle('V' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('W' . $i, "N/A")
                         ->getStyle('W' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('X' . $i, "N/A")
                         ->getStyle('X' . $i)->applyFromArray($styleCentrado);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('Y' . $i, "N/A")
                         ->getStyle('Y' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('Z' . $i, "N/A")
                         ->getStyle('Z' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AA' . $i, "N/A")
                         ->getStyle('AA' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AB' . $i, "N/A")
                         ->getStyle('AB' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AC' . $i, "N/A")
                         ->getStyle('AC' . $i)->applyFromArray($styleCentradoVertical);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AD' . $i, "N/A")
                         ->getStyle('AD' . $i)->applyFromArray($styleCentrado);

                    $this->objCal->setActiveSheetIndex(0)
                         ->setCellValue('AE' . $i, "N/A")
                         ->getStyle('AE' . $i)->applyFromArray($styleCentradoVertical);

                    break;
            }

            $this->objCal->getActiveSheet()->getRowDimension($i)->setRowHeight(100);

            $i++;

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
            $this->objCal->getActiveSheet()->getStyle('AA')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AB')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AC')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AD')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AE')->getAlignment()->setWrapText(true);

        }

        // Add some data

        $this->objCal->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $this->objCal->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

        $this->objCal->setActiveSheetIndex(0)->mergeCells('A1:F1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('A1', 'INFORMACIÓN BENEFICIARIO')
             ->getStyle('A1')->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('A2', 'Departamento')
                 ->getStyle('A2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('B2', 'Municipio')
                 ->getStyle('B2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('C2', 'Nombre Proyecto')
                 ->getStyle('C2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('D2', 'Número de Indentificación (Cédula persona Beneficiaria)')
                 ->getStyle('D2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('E2', 'Nombre Beneficiario')
                 ->getStyle('E2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('F2', 'Dirección')
                 ->getStyle('F2')->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('F')->setWidth(20);

        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('G1:K1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('G1', 'KIT COMISIONAMIENTO')
             ->getStyle('G1')->applyFromArray($styleCentrado);
        {

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('G2', 'Serial Portatil')
                 ->getStyle('G2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('H2', 'MAC 1')
                 ->getStyle('H2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('I2', 'MAC 2')
                 ->getStyle('I2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('J2', 'Serial Esclavo')
                 ->getStyle('J2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('K2', 'IP Esclavo')
                 ->getStyle('K2')->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        }
        $this->objCal->setActiveSheetIndex(0)->mergeCells('L1:S1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('L1', 'INFORMACIÓN TÉCNICA DE LA INSTALACIÓN (Aplica solo para tecnología INALÁMBRICA, si es diferente rellene con N/A)')
             ->getStyle('L1')->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('L2', 'IP CELDA')
                 ->getStyle('L2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('M2', 'MAC_CELDA')
                 ->getStyle('M2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('N2', 'Nombre del Nodo')
                 ->getStyle('N2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('O2', 'Nombre del Sectorial  (Sectorial 1,2,3…n)')
                 ->getStyle('O2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('P2', 'IP Switch')
                 ->getStyle('P2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Q2', 'IP SM')
                 ->getStyle('Q2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('R2', 'MAC SM')
                 ->getStyle('R2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('S2', 'MAC-CPE')
                 ->getStyle('S2')->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('R')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('S')->setWidth(20);
        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('T1:AE1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('T1', 'INFORMACIÓN TÉCNICA DE LA INSTALACIÓN (Aplica solo para tecnología HFC, si es diferente rellene con N/A)')
             ->getStyle('T1')->applyFromArray($styleCentrado);

        {

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('T2', 'IP OLT')
                 ->getStyle('T2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('U2', 'MAC_OLT')
                 ->getStyle('U2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('V2', 'PORT OLT')
                 ->getStyle('V2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('W2', 'NOMBRE OLT')
                 ->getStyle('W2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('X2', 'PUERTO_OLT')
                 ->getStyle('X2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Y2', 'MAC del Master EOC')
                 ->getStyle('Y2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Z2', 'IP del Master EOC')
                 ->getStyle('Z2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AA2', 'IP ONU')
                 ->getStyle('AA2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AB2', 'MAC ONU')
                 ->getStyle('AB2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AC2', 'IP HUB')
                 ->getStyle('AC2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AD2', 'MAC HUB')
                 ->getStyle('AD2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AE2', 'MAC-CPE')
                 ->getStyle('AE2')->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getColumnDimension('T')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('U')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('V')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('W')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('X')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AC')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AD')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AE')->setWidth(20);

        }

    }

    public function configurarDocumento() {

        $this->objCal = new \PHPExcel();

        // Set document properties
        $this->objCal->getProperties()->setCreator("OpenKyOS")
             ->setLastModifiedBy("OpenKyOS")
             ->setTitle("Reporte de Kiy e Instalaciones (" . date('Y-m-d') . ")")
             ->setSubject("Reporte de Kit Comisionamiento e Instalaciones")
             ->setDescription("Reporte de Kit Comisionamiento e Instalaciones")
             ->setCategory("Reporte");

        $this->objCal->getActiveSheet()->setTitle('FormatoReporteKitInstalaciones');

    }

    public function retornarDocumento() {

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReporteKitComisionamientoInstalaciones' . time() . '.xlsx"');
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

