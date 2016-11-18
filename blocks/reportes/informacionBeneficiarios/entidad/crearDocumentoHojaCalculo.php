<?php
namespace reportes\informacionBeneficiarios\entidad;

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
            $this->objCal->getActiveSheet()->getStyle('AF')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AG')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AH')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AI')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AJ')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AK')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AL')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AM')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AN')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AO')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AP')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AQ')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AR')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AS')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AT')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AU')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AV')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AW')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AX')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AY')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('AZ')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('BA')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('BB')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('BC')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('BD')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('BE')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('BF')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('BG')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getStyle('BH')->getAlignment()->setWrapText(true);

        }

        // Add some data

        $this->objCal->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        $this->objCal->getActiveSheet()->getRowDimension('2')->setRowHeight(60);

        $this->objCal->setActiveSheetIndex(0)->mergeCells('A1:G1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('A1', 'FOR-1264-TEC-016-REV-00   INFORMACIÓN  PROYECTO')
             ->getStyle('A1')->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('A2', 'Contratista')
                 ->getStyle('A2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('B2', 'Fecha instalación Servicio')
                 ->getStyle('B2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('C2', 'Departamento')
                 ->getStyle('C2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('D2', 'Municipio')
                 ->getStyle('D2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('E2', 'Nombre Proyecto')
                 ->getStyle('E2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('F2', 'VIP')
                 ->getStyle('F2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('G2', 'META')
                 ->getStyle('G2')->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('E')->setWidth(20);

        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('H1:O1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('H1', 'FOR-1264-TEC-016-REV-00 INFORMACION BENEFICARIO')
             ->getStyle('H1')->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('H2', 'ID Beneficiario Contratista')
                 ->getStyle('H2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('I2', 'Dirección')
                 ->getStyle('I2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('J2', 'Estrato SocioEconomico')
                 ->getStyle('J2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('K2', 'Documento Identificación (Cédula persona beneficiaria)')
                 ->getStyle('K2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('L2', 'Nombre Beneficiario')
                 ->getStyle('L2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('M2', 'Celular')
                 ->getStyle('M2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('N2', 'Correo Eléctrónico')
                 ->getStyle('N2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('O2', 'TIPO DE VIVIENDA (CASA/APARTAMENTO)')
                 ->getStyle('O2')->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('O')->setWidth(20);
        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('P1:AE1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('P1', 'FOR-1264-TEC-016-REV-00 CONFORMACIÓN DEL HOGAR DONDE SE INSTALÓ EL SERVICIO')
             ->getStyle('P1')->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('P2', 'Cantidad de personas que pertenecen al género femenino')
                 ->getStyle('P2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Q2', 'Cantidad de personas que pertenecen al género masculino')
                 ->getStyle('Q2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('R2', 'Cantidad de personas cuya edad Menor 18 años')
                 ->getStyle('R2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('S2', 'Cantidad de personas cuya edad Entre 18 y 25 años')
                 ->getStyle('S2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('T2', 'Cantidad de personas cuya edad Entre 26 y 30 años')
                 ->getStyle('T2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('U2', 'Cantidad de personas cuya edad Entre 31 y 40 años')
                 ->getStyle('U2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('V2', 'Cantidad de personas cuya edad Entre 41 y 65 años')
                 ->getStyle('V2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('W2', 'Cantidad de personas cuya edad  es mayor a 65 años')
                 ->getStyle('W2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('X2', 'Cantidad de personas cuya ocupación es Empleado')
                 ->getStyle('X2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Y2', 'Cantidad de personas cuya ocupación es Trabajador Informal')
                 ->getStyle('Y2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Z2', 'Cantidad de personas cuya ocupación es Estudiante')
                 ->getStyle('Z2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AA2', 'Cantidad de personas cuya ocupación  trabajador independiente')
                 ->getStyle('AA2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AB2', 'Cantidad de personas cuya ocupación doméstico fuera del hogar')
                 ->getStyle('AB2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AC2', 'Cantidad de personas cuya ocupación doméstico en el hogar')
                 ->getStyle('AC2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AD2', 'Cantidad de personas cuya ocupación es No trabaja')
                 ->getStyle('AD2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AE2', 'Cantidad de personas cuya ocupación es Otro')
                 ->getStyle('AE2')->applyFromArray($styleCentrado);

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
            $this->objCal->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AC')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AD')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AE')->setWidth(20);

        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('AF1:AG1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('AF1', 'FOR-1264-TEC-016-REV-00 PRUEBA VELOCIDAD')
             ->getStyle('AF1')->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AF2', 'Resultado Velocidad  SUBIDA (UPLINK)  (NÚMERO) en  Kbps')
                 ->getStyle('AF2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AG2', 'Resultado Velocidad (DOWNLINK) (NÚMERO) en  Kbps')
                 ->getStyle('AG2')->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getColumnDimension('AF')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AG')->setWidth(20);

        }

        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('AH1', 'Tecnología')
             ->getStyle('AH1')->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AH2', 'Resultado Velocidad  SUBIDA (UPLINK)  (NÚMERO) en  Kbps')
                 ->getStyle('AH2')->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getColumnDimension('AH')->setWidth(20);

        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('AI1:AN1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('AI1', 'FOR-1264-TEC-016-REV-00  INFORMACIÓN TÉCNICA DE LA INSTALACIÓN (Aplica solo para tecnología GPON, si es diferente rellene con N/A)')
             ->getStyle('AI1')->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AI2', 'IP OLT')
                 ->getStyle('AI2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AJ2', 'MAC_OLT')
                 ->getStyle('AJ2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AK2', 'PORT OLT')
                 ->getStyle('AK2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AL2', 'MAC-CPE')
                 ->getStyle('AL2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AM2', 'NOMBRE OLT')
                 ->getStyle('AM2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AN2', 'PUERTO_OLT')
                 ->getStyle('AN2')->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getColumnDimension('AI')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AJ')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AK')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AL')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AM')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AN')->setWidth(20);

        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('AO1:AV1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('AO1', 'FOR-1264-TEC-016-REV-00 INFORMACIÓN TÉCNICA DE LA INSTALACIÓN (Aplica solo para tecnología INALÁMBRICA, si es diferente rellene con N/A)')
             ->getStyle('AO1')->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AO2', 'IP CELDA')
                 ->getStyle('AO2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AP2', 'MAC_CELDA')
                 ->getStyle('AP2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AQ2', 'Nombre del Nodo')
                 ->getStyle('AQ2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AR2', 'Nombre del Sectorial  (Sectorial 1,2,3…n)')
                 ->getStyle('AR2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AS2', 'IP Switch')
                 ->getStyle('AS2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AT2', 'IP SM')
                 ->getStyle('AT2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AU2', 'MAC SM')
                 ->getStyle('AU2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AV2', 'MAC-CPE')
                 ->getStyle('AV2')->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getColumnDimension('AO')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AP')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AQ')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AR')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AT')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AU')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AV')->setWidth(20);

        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('AW1:BH1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('AO1', 'FOR-1264-TEC-016-REV-00 INFORMACIÓN TÉCNICA DE LA INSTALACIÓN (Aplica solo para tecnología INALÁMBRICA, si es diferente rellene con N/A)')
             ->getStyle('AO1')->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AO2', 'IP CELDA')
                 ->getStyle('AO2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AP2', 'MAC_CELDA')
                 ->getStyle('AP2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AQ2', 'Nombre del Nodo')
                 ->getStyle('AQ2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AR2', 'Nombre del Sectorial  (Sectorial 1,2,3…n)')
                 ->getStyle('AR2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AS2', 'IP Switch')
                 ->getStyle('AS2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AT2', 'IP SM')
                 ->getStyle('AT2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AU2', 'MAC SM')
                 ->getStyle('AU2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AV2', 'MAC-CPE')
                 ->getStyle('AV2')->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getColumnDimension('AO')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AP')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AQ')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AR')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AT')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AU')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AV')->setWidth(20);

        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('AW1:BH1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('AW1', 'FOR-1264-TEC-016-REV-00 INFORMACIÓN TÉCNICA DE LA INSTALACIÓN (Aplica solo para tecnología HFC, si es diferente rellene con N/A)')
             ->getStyle('AW1')->applyFromArray($styleCentrado);
        {
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AW2', 'IP OLT')
                 ->getStyle('AW2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AX2', 'MAC_OLT')
                 ->getStyle('AX2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AY2', 'PORT OLT')
                 ->getStyle('AY2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AZ2', 'NOMBRE OLT')
                 ->getStyle('AZ2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BA2', 'PUERTO_OLT')
                 ->getStyle('BA2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BB2', 'MAC del Master EOC')
                 ->getStyle('BB2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BC2', 'IP del Master EOC')
                 ->getStyle('BC2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BD2', 'IP ONU')
                 ->getStyle('BD2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BE2', 'MAC ONU')
                 ->getStyle('BE2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BF2', 'IP HUB')
                 ->getStyle('BF2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BG2', 'MAC HUB')
                 ->getStyle('BG2')->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BH2', 'MAC-CPE')
                 ->getStyle('BH2')->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getColumnDimension('AW')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AX')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AY')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('AZ')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('BA')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('BB')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('BC')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('BD')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('BE')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('BF')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('BG')->setWidth(20);
            $this->objCal->getActiveSheet()->getColumnDimension('BH')->setWidth(20);

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

        exit();

    }

}

$miProcesador = new GenerarReporteExcelInstalaciones($this->miSql, $this->proyectos);

?>

