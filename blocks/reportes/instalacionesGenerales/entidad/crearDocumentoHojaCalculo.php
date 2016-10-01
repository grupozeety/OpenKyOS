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
         * 3. Estruturamiento Información OpenProject
         **/
        $this->estruturarInformacion();

        /**
         * XX. Retornar Documento Reporte
         **/
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
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('A4', 'Politécnica')
             ->getStyle("A4")->applyFromArray($styleCentrado);

        {
            //Avance y  Estado Instalación NOC

            {
                // Centro de Gestión
                $contenido_CentroGestion = $this->compactarAvances($this->proyectos[2], "Centro de gestión");
                $paquete_CentroGestion = $this->consultarPaqueteTrabajo($this->proyectos[2], "Centro de gestión");
                //var_dump($paquete_CentroGestion);exit;

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('B4', (($contenido_CentroGestion != false) ? $contenido_CentroGestion : ""))
                     ->getStyle("B4")->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('C4', ((!is_null($paquete_CentroGestion['cf_12'])) ? $paquete_CentroGestion['cf_12'] : ""))
                     ->getStyle("C4")->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('D4', ((!is_null($paquete_CentroGestion['cf_13'])) ? $paquete_CentroGestion['cf_13'] : ""))
                     ->getStyle("D4")->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('E4', ((isset($paquete_CentroGestion['start_date']) && $paquete_CentroGestion['start_date'] != '') ? $paquete_CentroGestion['start_date'] : ""))
                     ->getStyle("E4")->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('F4', ((isset($paquete_CentroGestion['due_date']) && $paquete_CentroGestion['due_date'] != '') ? $paquete_CentroGestion['due_date'] : ""))
                     ->getStyle("F4")->applyFromArray($styleCentradoVertical);

            }

            {
                // Mesa Ayuda
                $contenido_MesaAyuda = $this->compactarAvances($this->proyectos[2], "Mesa de ayuda");
                $paquete_MesaAyuda = $this->consultarPaqueteTrabajo($this->proyectos[2], "Mesa de ayuda");

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('G4', (($contenido_MesaAyuda != false) ? $contenido_MesaAyuda : ""))
                     ->getStyle("G4")->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('H4', ((!is_null($paquete_MesaAyuda['cf_12'])) ? $paquete_MesaAyuda['cf_12'] : ""))
                     ->getStyle("H4")->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('I4', ((!is_null($paquete_MesaAyuda['cf_13'])) ? $paquete_MesaAyuda['cf_13'] : ""))
                     ->getStyle("I4")->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('J4', ((isset($paquete_MesaAyuda['start_date']) && $paquete_MesaAyuda['start_date'] != '') ? $paquete_MesaAyuda['start_date'] : ""))
                     ->getStyle("J4")->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('K4', ((isset($paquete_MesaAyuda['due_date']) && $paquete_MesaAyuda['due_date'] != '') ? $paquete_MesaAyuda['due_date'] : ""))
                     ->getStyle("K4")->applyFromArray($styleCentradoVertical);

            }

            {

                // Otros Sistemas
                $contenido_OtrosSistemas = $this->compactarAvances($this->proyectos[2], "Otros equipos o sistemas en el NOC");

                $paquete_OtrosSistemas = $this->consultarPaqueteTrabajo($this->proyectos[2], "Otros equipos o sistemas en el NOC");

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('L4', (($contenido_OtrosSistemas != false) ? $contenido_OtrosSistemas : ""))
                     ->getStyle("L4")->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('M4', ((!is_null($paquete_OtrosSistemas['cf_12'])) ? $paquete_OtrosSistemas['cf_12'] : ""))
                     ->getStyle("M4")->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('N4', ((!is_null($paquete_OtrosSistemas['cf_13'])) ? $paquete_OtrosSistemas['cf_13'] : ""))
                     ->getStyle("N4")->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('O4', ((isset($paquete_OtrosSistemas['start_date']) && $paquete_OtrosSistemas['start_date'] != '') ? $paquete_OtrosSistemas['start_date'] : ""))
                     ->getStyle("O4")->applyFromArray($styleCentradoVertical);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('P4', ((isset($paquete_OtrosSistemas['due_date']) && $paquete_OtrosSistemas['due_date'] != '') ? $paquete_OtrosSistemas['due_date'] : ""))
                     ->getStyle("P4")->applyFromArray($styleCentradoVertical);

            }

            $paquete_avance_instalacion_noc = $this->consultarPaqueteTrabajo($this->proyectos[2], "Avance y  estado instalación NOC");

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('Q4', "% " . $paquete_avance_instalacion_noc['done_ratio'])
                 ->getStyle("Q4")->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('R4', ((!is_null($paquete_avance_instalacion_noc['cf_15'])) ? $paquete_avance_instalacion_noc['cf_15'] : ""))
                 ->getStyle("R4")->applyFromArray($styleCentradoVertical);

        }

        $i = 4;
        foreach ($this->proyectos as $key => $value) {

            $var = strpos($value['identifier'], 'becera');
            if ($var == false && $value['identifier'] != 'ins') {

                $clave_departamento = array_search(1, array_column($value['campos_personalizados'], 'id'), true);
                $longitud = strlen($value['campos_personalizados'][$clave_departamento]['value']);
                $departamento = substr($value['campos_personalizados'][$clave_departamento]['value'], 5, $longitud);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('S' . $i, $departamento)
                     ->getStyle("S" . $i)->applyFromArray($styleCentradoVertical);

                $clave_municipio = array_search(2, array_column($value['campos_personalizados'], 'id'), true);
                $longitud = strlen($value['campos_personalizados'][$clave_municipio]['value']);
                $municipio = substr($value['campos_personalizados'][$clave_municipio]['value'], 8, $longitud);

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('T' . $i, $municipio)
                     ->getStyle("T" . $i)->applyFromArray($styleCentradoVertical);

                $codigo_dane = substr($value['campos_personalizados'][$clave_municipio]['value'], 0, 4);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('U' . $i, $codigo_dane)
                     ->getStyle("U" . $i)->applyFromArray($styleCentradoVertical);

                $clave_urbanizacion = array_search(33, array_column($value['campos_personalizados'], 'id'), true);
                $urbanizacion = $value['campos_personalizados'][$clave_urbanizacion]['value'];

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('V' . $i, $urbanizacion)
                     ->getStyle("V" . $i)->applyFromArray($styleCentradoVertical);

                {
                    //Avance y Estado Instalación Nodo Cabecera

                    $clave_cabecera_campo = array_search(43, array_column($value['campos_personalizados'], 'id'), true);
                    $cabecera_campo = $value['campos_personalizados'][$clave_cabecera_campo]['value'];
                    $clave_cabecera_proyecto = array_search($cabecera_campo, array_column($this->proyectos, 'name'), true);
                    $cabecera = $this->proyectos[$clave_cabecera_proyecto];

                    {
                        //Infraestructura Nodos

                        $contenido_InfraestructuraNodos = $this->compactarAvances($cabecera, "Infraestructura Nodos");
                        $paquete_InfraestructuraNodos = $this->consultarPaqueteTrabajo($cabecera, "Infraestructura Nodos");

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('W' . $i, (($contenido_InfraestructuraNodos != false) ? $contenido_InfraestructuraNodos : ""))
                             ->getStyle("W" . $i)->applyFromArray($styleCentradoVertical);

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('X' . $i, ((!is_null($paquete_InfraestructuraNodos['cf_14'])) ? $paquete_InfraestructuraNodos['cf_14'] : ""))
                             ->getStyle('X' . $i)->applyFromArray($styleCentradoVertical);

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('Y' . $i, ((isset($paquete_InfraestructuraNodos['due_date']) && $paquete_InfraestructuraNodos['due_date'] != '') ? $paquete_InfraestructuraNodos['due_date'] : ""))
                             ->getStyle('Y' . $i)->applyFromArray($styleCentradoVertical);

                    }
                    {
                        //Instalación Red troncal o interconexión ISP

                        $contenido_RedTroncalISP = $this->compactarAvances($cabecera, "Instalación red troncal o interconexión ISP");
                        $paquete_RedTroncalISP = $this->consultarPaqueteTrabajo($cabecera, "Instalación red troncal o interconexión ISP");

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('Z' . $i, (($contenido_RedTroncalISP != false) ? $contenido_RedTroncalISP : ""))
                             ->getStyle("Z" . $i)->applyFromArray($styleCentradoVertical);

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('AA' . $i, ((!is_null($paquete_RedTroncalISP['cf_14'])) ? $paquete_RedTroncalISP['cf_14'] : ""))
                             ->getStyle('AA' . $i)->applyFromArray($styleCentradoVertical);

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('AB' . $i, ((!is_null($paquete_RedTroncalISP['cf_16'])) ? $paquete_RedTroncalISP['cf_16'] : ""))
                             ->getStyle('AB' . $i)->applyFromArray($styleCentradoVertical);

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('AC' . $i, ((!is_null($paquete_RedTroncalISP['cf_17'])) ? $paquete_RedTroncalISP['cf_17'] : ""))
                             ->getStyle('AC' . $i)->applyFromArray($styleCentradoVertical);
                    }

                    {
                        //Instalación Red troncal o interconexión ISP

                        $contenido_RedTroncalISP = $this->compactarAvances($cabecera, "Instalación red troncal o interconexión ISP");
                        $paquete_RedTroncalISP = $this->consultarPaqueteTrabajo($cabecera, "Instalación red troncal o interconexión ISP");

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('Z' . $i, (($contenido_RedTroncalISP != false) ? $contenido_RedTroncalISP : ""))
                             ->getStyle("Z" . $i)->applyFromArray($styleCentradoVertical);

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('AA' . $i, ((!is_null($paquete_RedTroncalISP['cf_14'])) ? $paquete_RedTroncalISP['cf_14'] : ""))
                             ->getStyle('AA' . $i)->applyFromArray($styleCentradoVertical);

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('AB' . $i, ((!is_null($paquete_RedTroncalISP['cf_16'])) ? $paquete_RedTroncalISP['cf_16'] : ""))
                             ->getStyle('AB' . $i)->applyFromArray($styleCentradoVertical);

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('AC' . $i, ((!is_null($paquete_RedTroncalISP['cf_17'])) ? $paquete_RedTroncalISP['cf_17'] : ""))
                             ->getStyle('AC' . $i)->applyFromArray($styleCentradoVertical);
                    }

                    {
                        //Instalación Red troncal o interconexión ISP

                        $paquete_InstFuncEquiNodoCab = $this->consultarPaqueteTrabajo($cabecera, "Instalación y puesta en funcionamiento equipos");

                        //var_dump($paquete_InstFuncEquiNodoCab);exit;

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('AD' . $i, ((!is_null($paquete_InstFuncEquiNodoCab['cf_44'])) ? $paquete_InstFuncEquiNodoCab['cf_44'] : ""))
                             ->getStyle('AD' . $i)->applyFromArray($styleCentradoVertical);

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('AE' . $i, ((!is_null($paquete_InstFuncEquiNodoCab['cf_45'])) ? $paquete_InstFuncEquiNodoCab['cf_45'] : ""))
                             ->getStyle('AE' . $i)->applyFromArray($styleCentradoVertical);

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('AF' . $i, ((!is_null($paquete_InstFuncEquiNodoCab['cf_46'])) ? $paquete_InstFuncEquiNodoCab['cf_46'] : ""))
                             ->getStyle('AF' . $i)->applyFromArray($styleCentradoVertical);

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('AG' . $i, ((!is_null($paquete_InstFuncEquiNodoCab['cf_16'])) ? $paquete_InstFuncEquiNodoCab['cf_16'] : ""))
                             ->getStyle('AG' . $i)->applyFromArray($styleCentradoVertical);

                        $this->objCal->setActiveSheetIndex(0)
                             ->setCellValue('AH' . $i, ((!is_null($paquete_InstFuncEquiNodoCab['cf_17'])) ? $paquete_InstFuncEquiNodoCab['cf_17'] : ""))
                             ->getStyle('AH' . $i)->applyFromArray($styleCentradoVertical);
                    }

                }

                $i++;

            }

        }

    }

    public function consultarPaqueteTrabajo($proyecto = '', $nombre_paquete = '') {

        foreach ($proyecto['paquetesTrabajo'] as $key => $value) {

            if ($value['subject'] == $nombre_paquete) {
                $contenido = $value;
            }

        }

        return $contenido;
    }

    public function compactarAvances($proyecto = '', $tema = '') {

        $contenido = '';
        foreach ($proyecto['paquetesTrabajo'] as $key => $value) {

            if ($value['subject'] == $tema) {

                foreach ($value['actividades'] as $llave => $valor) {

                    $fecha_actividad = substr($valor['createdAt'], 0, 10);

                    $contenido .= "(" . $fecha_actividad . ") " . $valor['comment']['raw'] . "\n";
                }

            }

        }

        if ($contenido == '') {

            $contenido = false;

        }

        return $contenido;
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

        $this->objCal->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $this->objCal->getActiveSheet()->getStyle('A')->getAlignment()->setWrapText(true);

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
                    $this->objCal->getActiveSheet()->getRowDimension('2')->setRowHeight(75);
                    $this->objCal->getActiveSheet()->getRowDimension('4')->setRowHeight(100);

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
                     ->setCellValue('AI3', 'Fecha prevista en el PI&PS para la Puesta en Funcionamiento Nodo Cabecera')
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

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('AR')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('AS')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AT')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('AR')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AS')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AT')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AR3', 'Descripción Obra o Actividad')
                     ->getStyle("AR3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AS3', 'Estado Avance (En Construcción, Terminado)')
                     ->getStyle("AS3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AT3', 'Fecha Prevista Terminación')
                     ->getStyle("AT3")->applyFromArray($styleCentrado);

            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('AU2:AY2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('AU2', 'Instalación y Puesta en Funcionamiento Equipos ')
                 ->getStyle("AU2")->applyFromArray($styleCentrado);

            {

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('AU')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AV')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AW')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AX')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('AY')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('AU')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AV')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AW')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AX')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('AY')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AU3', 'Cantidad de EOCs a Instalar Requeridos')
                     ->getStyle("AU3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AV3', 'Cantidad de EOCs Instalados, Probados y En Funcionamiento')
                     ->getStyle("AV3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AW3', 'Equipos Networking (Instalados, Probados,En Funcionamiento)')
                     ->getStyle("AW3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AX3', 'Fecha Prevista Nodo EOC en Funcionameinto')
                     ->getStyle("AX3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('AY3', 'Fecha Prevista en el PI&PS Nodo EOC en Funcionamiento')
                     ->getStyle("AY3")->applyFromArray($styleCentrado);

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

        $this->objCal->setActiveSheetIndex(0)->mergeCells('BA1:BH1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('BA1', 'Avance y Estado Instalación Nodo Inalámbrico')
             ->getStyle("BA1")->applyFromArray($styleCentrado);

        {

            $this->objCal->setActiveSheetIndex(0)->mergeCells('BA2:BC2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BA2', 'Infraestructura Nodo')
                 ->getStyle("BA2")->applyFromArray($styleCentrado);

            {

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('BA')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('BB')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('BC')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('BA')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BB')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BC')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BA3', 'Descripción Obra o Actividad')
                     ->getStyle("BA3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BB3', 'Estado Avance (En Construcción, Terminado)')
                     ->getStyle("BB3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BC3', 'Fecha Prevista Terminación')
                     ->getStyle("BC3")->applyFromArray($styleCentrado);
            }

            $this->objCal->setActiveSheetIndex(0)->mergeCells('BD2:BG2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BD2', 'Instalación y Puesta en Funcionamiento Equipos')
                 ->getStyle("BD2")->applyFromArray($styleCentrado);

            {

                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('BD')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('BE')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('BF')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('BG')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('BD')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BE')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BF')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BG')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BD3', 'Cantidad de Celdas a Instalar Requeridos')
                     ->getStyle("BD3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BE3', 'Cantidad de Celdas Instaladas, Probadas y En Funcionamiento')
                     ->getStyle("BE3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BF3', 'Fecha Prevista Nodo Inalámbrico en Funcionameinto')
                     ->getStyle("BF3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BG3', 'Fecha Prevista en el PI&PS Nodo Inalámbrico en Funcionamiento')
                     ->getStyle("BG3")->applyFromArray($styleCentrado);

            }

            {
                {
                    // Estilos

                    $this->objCal->getActiveSheet()->getStyle('BH')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getColumnDimension('BH')->setWidth(15);

                }

                $this->objCal->setActiveSheetIndex(0)->mergeCells('BH2:BH3');
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BH2', '% Avance Instalación Nodo Inalámbrico')
                     ->getStyle("BH2")->applyFromArray($styleCentrado);

            }

        }

        $this->objCal->getActiveSheet()->getStyle('BI')->getAlignment()->setWrapText(true);
        $this->objCal->getActiveSheet()->getColumnDimension('BI')->setWidth(30);
        $this->objCal->setActiveSheetIndex(0)->mergeCells('BI1:BI3');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('BI1', 'Fecha Prevista para Verificación por la Interventoría del Nodo de Cabecera, Nodo EOC, Nodo Inalámbrico y Red de Distribución')
             ->getStyle("BI1")->applyFromArray($styleCentrado);

        $this->objCal->setActiveSheetIndex(0)->mergeCells('BJ1:BR1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('BJ1', 'Avance y Estado Instalación Accesos HFC')
             ->getStyle("BJ1")->applyFromArray($styleCentrado);

        {

            $this->objCal->getActiveSheet()->getStyle('BJ')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('BJ')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BJ2:BJ3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BJ2', 'Cantidad de Accesos HFC a Instalar Requeridos HFC')
                 ->getStyle("BJ2")->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getStyle('BK')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('BK')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BK2:BK3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BK2', 'Fecha Prevista en el PI&PS para el Inicio Instalación Accesos HFC')
                 ->getStyle("BK2")->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getStyle('BL')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('BL')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BL2:BL3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BL2', 'Fecha Prevista en el PI&PS para la Terminación Instalación Accesos HFC')
                 ->getStyle("BL2")->applyFromArray($styleCentrado);

            $this->objCal->setActiveSheetIndex(0)->mergeCells('BM2:BO2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BM2', 'Tendido y Puesta en Funcionameinto Red Coaxial')
                 ->getStyle("BM2")->applyFromArray($styleCentrado);

            {
                {
                    // Estilos Columnas
                    $this->objCal->getActiveSheet()->getColumnDimension('BM')->setWidth(50);
                    $this->objCal->getActiveSheet()->getColumnDimension('BN')->setWidth(15);
                    $this->objCal->getActiveSheet()->getColumnDimension('BO')->setWidth(15);

                    $this->objCal->getActiveSheet()->getStyle('BM')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BN')->getAlignment()->setWrapText(true);
                    $this->objCal->getActiveSheet()->getStyle('BO')->getAlignment()->setWrapText(true);

                }
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BM3', 'Descripción Actividades')
                     ->getStyle("BM3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BN3', 'Estado avance (En Construcción, Terminado, Probado, En Funcionamiento)')
                     ->getStyle("BN3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BO3', 'Fecha Prevista Puesta en Funcionameinto')
                     ->getStyle("BO3")->applyFromArray($styleCentrado);

            }

            {
                // Estilos Columnas

                $this->objCal->getActiveSheet()->getColumnDimension('BP')->setWidth(15);
                $this->objCal->getActiveSheet()->getColumnDimension('BQ')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('BP')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getStyle('BQ')->getAlignment()->setWrapText(true);

            }
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BP2:BQ2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BP2', 'Cantidad de Accesos HFC Instalados en la Semana Reportada')
                 ->getStyle("BP2")->applyFromArray($styleCentrado);

            {
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BP3', 'EOC Cliente (Cantidad Instalados)')
                     ->getStyle("BP3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BQ3', 'Accesos VIP')
                     ->getStyle("BQ3")->applyFromArray($styleCentrado);

            }

            {
                // Estilos Columnas

                $this->objCal->getActiveSheet()->getColumnDimension('BR')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('BR')->getAlignment()->setWrapText(true);

            }
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BR2', 'Cantidad de Accesos HFC Instalados Acumulados')
                 ->getStyle("BR2")->applyFromArray($styleCentrado);

            {

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BR3', 'Accesos VIP')
                     ->getStyle("BR3")->applyFromArray($styleCentrado);

            }

        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('BS1:BX1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('BS1', 'Avance y Estado Instalación Accesos Inalámbricos')
             ->getStyle("BS1")->applyFromArray($styleCentrado);

        {

            $this->objCal->getActiveSheet()->getStyle('BS')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('BS')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BS2:BS3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BS2', 'Cantidad de Accesos Inalámbricos a Instalar Requeridos')
                 ->getStyle("BS2")->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getStyle('BT')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('BT')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BT2:BT3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BT2', 'Fecha Prevista en el PI&PS para el Inicio Instalación Accesos Inalámbricos')
                 ->getStyle("BT2")->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getStyle('BU')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('BU')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BU2:BU3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BU2', 'Fecha Prevista en el PI&PS para la Terminación Instalación Accesos Inalámbricos')
                 ->getStyle("BU2")->applyFromArray($styleCentrado);

            {
                // Estilos Columnas

                $this->objCal->getActiveSheet()->getColumnDimension('BV')->setWidth(15);
                $this->objCal->getActiveSheet()->getColumnDimension('BW')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('BV')->getAlignment()->setWrapText(true);
                $this->objCal->getActiveSheet()->getStyle('BW')->getAlignment()->setWrapText(true);

            }
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BV2:BW2');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BV2', 'Cantidad de Accesos Inalámbricos Instalados en la Semana Reportada')
                 ->getStyle("BV2")->applyFromArray($styleCentrado);

            {

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BV3', 'SM /CPE (Cantidad Instalados)')
                     ->getStyle("BV3")->applyFromArray($styleCentrado);
                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BW3', 'Accesos E1 y E2')
                     ->getStyle("BW3")->applyFromArray($styleCentrado);

            }

            {
                // Estilos Columnas

                $this->objCal->getActiveSheet()->getColumnDimension('BX')->setWidth(15);

                $this->objCal->getActiveSheet()->getStyle('BX')->getAlignment()->setWrapText(true);

            }
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BX2', 'Cantidad de Accesos Inalámbricos Instalados Acumulados')
                 ->getStyle("BX2")->applyFromArray($styleCentrado);

            {

                $this->objCal->setActiveSheetIndex(0)
                     ->setCellValue('BX3', 'Accesos E1 y E2')
                     ->getStyle("BX3")->applyFromArray($styleCentrado);

            }
        }

        $this->objCal->setActiveSheetIndex(0)->mergeCells('BY1:BZ1');
        $this->objCal->setActiveSheetIndex(0)
             ->setCellValue('BY1', 'Reporte Accesos')
             ->getStyle("BY1")->applyFromArray($styleCentrado);

        {

            $this->objCal->getActiveSheet()->getStyle('BY')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('BY')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BY2:BY3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BY2', 'Cantidad de Accesos a Reportar a la Interventoría')
                 ->getStyle("BY2")->applyFromArray($styleCentrado);

            $this->objCal->getActiveSheet()->getStyle('BZ')->getAlignment()->setWrapText(true);
            $this->objCal->getActiveSheet()->getColumnDimension('BZ')->setWidth(15);
            $this->objCal->setActiveSheetIndex(0)->mergeCells('BZ2:BZ3');
            $this->objCal->setActiveSheetIndex(0)
                 ->setCellValue('BZ2', 'Fecha Prevista Reporte Accesos Instalados a Interventoría')
                 ->getStyle("BZ2")->applyFromArray($styleCentrado);

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

