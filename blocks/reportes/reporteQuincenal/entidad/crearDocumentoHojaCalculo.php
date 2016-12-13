<?php

namespace reportes\reporteQuincenal\entidad;

$ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/plugin/html2pfd/";

require_once $ruta . "/plugin/PHPExcel/Classes/PHPExcel.php";
class GenerarReporteExcelInstalaciones {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $conexion;
	public $informacion;
	public $objCal;
	public function __construct($sql, $informacion) {
		
		date_default_timezone_set ( 'America/Bogota' );
		
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
		$this->informacion = $informacion;
		
		/**
		 * 1.
		 * Configuración Propiedades Documento
		 */
		$this->configurarDocumento ();
		
		/**
		 * 2.
		 * Estruturamiento Esquema Reporte
		 */
		$this->generarEsquemaDocumento ();
		
		/**
		 * 3.
		 * Estruturamiento Información OpenProject
		 */
		$this->estruturarInformacion ();
		
		/**
		 * XX.
		 * Retornar Documento Reporte
		 */
		$this->retornarDocumento ();
	}
	public function asignarValoresCampos($valor) {
		
		$this->A = $valor ['a'];
		$this->B = $valor ['b'];
		$this->C = $valor ['c'];
		$this->D = $valor ['d'];
		$this->E = $valor ['e'];
		$this->F = $valor ['f'];
		$this->G = $valor ['g'];
		$this->H = $valor ['h'];
		$this->I = $valor ['i'];
		$this->J = $valor ['j'];
		$this->K = $valor ['k'];
		$this->L = $valor ['l'];
		$this->M = $valor ['m'];
		$this->N = $valor ['n'];
		$this->O = $valor ['o'];
		$this->P = $valor ['p'];
		$this->Q = $valor ['q'];
		$this->R = $valor ['r'];
		$this->S = $valor ['s'];
		$this->T = $valor ['t'];
		$this->U = $valor ['u'];
		$this->V = $valor ['v'];
		$this->W = $valor ['w'];
		$this->X = $valor ['x'];
		$this->Y = $valor ['y'];
		$this->Z = $valor ['z'];
		$this->AA = $valor ['aa'];
		$this->AB = $valor ['ab'];
		$this->AC = $valor ['ac'];
		$this->AD = '';
	}
	
	public function estruturarInformacion() {
		
		// Estilos Celdas
		{
			$styleCentrado = array (
					'alignment' => array (
							'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER 
					) 
			);
			$styleCentradoVertical = array (
					'alignment' => array (
							'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER 
					) 
			);
		}
		
		$i = 5;
		
		foreach ( $this->informacion as $key => $value ) {
			
			$this->asignarValoresCampos($value);
			
			// Elemento
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'A' . $i, $this->A )->getStyle ( 'A' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'B' . $i, $this->B )->getStyle ( 'B' . $i )->applyFromArray ( $styleCentradoVertical );
				
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'C' . $i, $this->C )->getStyle ( 'C' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'D' . $i, $this->D )->getStyle ( 'D' . $i )->applyFromArray ( $styleCentradoVertical );
			
			// Contrato
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'E' . $i, $this->E )->getStyle ( 'E' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'F' . $i, $this->F )->getStyle ( 'F' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'G' . $i, $this->G )->getStyle ( 'G' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'H' . $i, $this->H )->getStyle ( 'H' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'I' . $i, $this->I )->getStyle ( 'I' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'J' . $i, $this->J )->getStyle ( 'J' . $i )->applyFromArray ( $styleCentradoVertical );
			
			// IDIO y plan de instalación
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'K' . $i, $this->K )->getStyle ( 'K' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'L' . $i, $this->L )->getStyle ( 'L' . $i )->applyFromArray ( $styleCentradoVertical );
			
			// Entrega en bodega (aplica para equipos, materiales, infraestructura)
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'M' . $i, $this->M )->getStyle ( 'M' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'N' . $i, $this->N )->getStyle ( 'N' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'O' . $i, $this->O )->getStyle ( 'O' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'P' . $i, $this->P )->getStyle ( 'P' . $i )->applyFromArray ( $styleCentradoVertical );
			
			// Entrega en sitio de instalación (aplica para equipos, materiales, infraestructura)
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'Q' . $i, $this->Q )->getStyle ( 'Q' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'R' . $i, $this->R )->getStyle ( 'R' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'S' . $i, $this->S )->getStyle ( "S" . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'T' . $i, $this->T )->getStyle ( "T" . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'U' . $i, $this->U )->getStyle ( "U" . $i )->applyFromArray ( $styleCentradoVertical );
			
			// Entrega servicios, interconexión ISP
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'V' . $i, $this->V )->getStyle ( "V" . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'W' . $i, $this->W )->getStyle ( "W" . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'X' . $i, $this->X )->getStyle ( 'X' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'Y' . $i, $this->Y )->getStyle ( 'Y' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'Z' . $i, $this->Z )->getStyle ( "Z" . $i )->applyFromArray ( $styleCentradoVertical );
			
			// PI&PS
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'AA' . $i, $this->AA )->getStyle ( 'AA' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'AB' . $i, $this->AB )->getStyle ( 'AB' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'AC' . $i, $this->AC )->getStyle ( 'AC' . $i )->applyFromArray ( $styleCentradoVertical );
			
			//Observaciones
			$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'AD' . $i, $this->AD )->getStyle ( 'AD' . $i )->applyFromArray ( $styleCentradoVertical );
			
			$i ++;
		}
	}
	public function generarEsquemaDocumento() {
		
		// Estilos Celdas
		{
			$styleCentrado = array (
					'alignment' => array (
							'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER 
					) 
			);
		}
		// Add some data
		
		$this->objCal->setActiveSheetIndex ( 0 )->mergeCells ( 'A1:AD1' );
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'A1', 'Reporte Quincenal Avance Plan de Compras  y contrataciones- corte' )->getStyle ( "A1" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->mergeCells ( 'A2:AD2' );
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'A2', 'Proyecto Conexiones Digitales II' )->getStyle ( "A2" )->applyFromArray ( $styleCentrado );
		
		// Elemento
		$this->objCal->setActiveSheetIndex ( 0 )->mergeCells ( 'A3:D3' );
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'A3', 'Elemento' )->getStyle ( "A3" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'A' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'B' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'C' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'D' )->setWidth ( 16 );
		
		$this->objCal->getActiveSheet ()->getStyle ( 'A' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'B' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'C' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'D' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getRowDimension ( '4' )->setRowHeight ( 100 );
		$this->objCal->getActiveSheet ()->getRowDimension ( '3' )->setRowHeight ( 75 );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'A4', 'Nombre Elemento (equipo, materiales, servicio, interconexión ISP, infraestructura)' )->getStyle ( "A4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'B4', 'Identificación  o referencia del elemento' )->getStyle ( "B4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'C4', 'Descripción  detallada del elemento (equipo, materiales, servicio, interconexion ISP, infraestructura)' )->getStyle ( "C4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'D4', 'Destino Instalación Nivel Red (Noc, Nodo, red troncal, red distribución, acceso (CPE), red acceso)' )->getStyle ( "D4" )->applyFromArray ( $styleCentrado );
		
		// Contrato
		$this->objCal->setActiveSheetIndex ( 0 )->mergeCells ( 'E3:J3' );
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'E3', 'Contrato' )->getStyle ( "E3" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'E' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'F' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'G' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'H' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'H' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'H' )->setWidth ( 16 );
		
		$this->objCal->getActiveSheet ()->getStyle ( 'E' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'F' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'G' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'H' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'I' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'J' )->getAlignment ()->setWrapText ( true );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'E4', 'Estado de compra' )->getStyle ( "E4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'F4', 'Número OC  o Contrato' )->getStyle ( "F4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'G4', 'Nombre Proveedor' )->getStyle ( "G4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'H4', 'Cantidad o capacidad Comprada o a adquirir' )->getStyle ( "H4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'I4', 'Unidad' )->getStyle ( "I4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'J4', 'Marca o fabricante elemento' )->getStyle ( "J4" )->applyFromArray ( $styleCentrado );
		
		// IDIO y plan de instalación
		$this->objCal->setActiveSheetIndex ( 0 )->mergeCells ( 'K3:L3' );
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'K3', 'IDIO y plan de instalación' )->getStyle ( "K3" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'K' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'L' )->setWidth ( 16 );
		
		$this->objCal->getActiveSheet ()->getStyle ( 'K' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'L' )->getAlignment ()->setWrapText ( true );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'K4', 'Cantidad o capacidad requerida en el proyecto' )->getStyle ( "K4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'L4', 'Unidad' )->getStyle ( "L4" )->applyFromArray ( $styleCentrado );
		
		// Entrega en bodega (aplica para equipos, materiales, infraestructura)
		$this->objCal->setActiveSheetIndex ( 0 )->mergeCells ( 'M3:P3' );
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'M3', 'Entrega en bodega (aplica para equipos, materiales, infraestructura)' )->getStyle ( "M3" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'M' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'N' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'O' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'P' )->setWidth ( 16 );
		
		$this->objCal->getActiveSheet ()->getStyle ( 'M' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'N' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'O' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'P' )->getAlignment ()->setWrapText ( true );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'M4', 'Ubicación actual' )->getStyle ( "M4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'N4', 'Fecha entrega en bodega' )->getStyle ( "N4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'O4', 'Cantidad en Bodega' )->getStyle ( "O4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'P4', 'Fecha prevista de entrega en bodega según plan de compras aprobado(PI&PS)' )->getStyle ( "P4" )->applyFromArray ( $styleCentrado );
		
		// Entrega en sitio de instalación (aplica para equipos, materiales, infraestructura)
		$this->objCal->setActiveSheetIndex ( 0 )->mergeCells ( 'Q3:U3' );
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'Q3', 'Entrega en sitio de instalación (aplica para equipos, materiales, infraestructura)' )->getStyle ( "Q3" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'Q' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'R' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'S' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'T' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'U' )->setWidth ( 16 );
		
		$this->objCal->getActiveSheet ()->getStyle ( 'Q' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'R' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'S' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'T' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'U' )->getAlignment ()->setWrapText ( true );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'Q4', 'Sitio de instalación' )->getStyle ( "Q4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'R4', 'Fecha entrega en sitio de instalación' )->getStyle ( "R4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'S4', 'Cantidad en Sitio de instalación' )->getStyle ( "S4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'T4', 'Fecha prevista de entrega en sitio de instalación según plan de compras aprobado(PI&PS)' )->getStyle ( "T4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'U4', 'Cantidad requerida en sitio de instalación (IDIO y PI&PS)' )->getStyle ( "U4" )->applyFromArray ( $styleCentrado );
		
		// Entrega servicios, interconexión ISP
		$this->objCal->setActiveSheetIndex ( 0 )->mergeCells ( 'V3:Z3' );
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'V3', 'Entrega servicios, interconexión ISP' )->getStyle ( "V3" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'V' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'W' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'X' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'Y' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'Z' )->setWidth ( 16 );
		
		$this->objCal->getActiveSheet ()->getStyle ( 'V' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'W' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'X' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'Y' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'Z' )->getAlignment ()->setWrapText ( true );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'V4', 'Estado actual' )->getStyle ( "V4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'W4', 'Fecha inicio' )->getStyle ( "W4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'X4', 'Fecha terminación' )->getStyle ( "X4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'Y4', 'Fecha prevista inicio en PI&PS' )->getStyle ( "Y4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'Z4', 'Fecha prevista terminación en PI&PS' )->getStyle ( "Z4" )->applyFromArray ( $styleCentrado );
		
		// PI&PS
		$this->objCal->setActiveSheetIndex ( 0 )->mergeCells ( 'AA3:AC3' );
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'AA3', 'PI&PS' )->getStyle ( "AA3" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'AA' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'AB' )->setWidth ( 16 );
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'AC' )->setWidth ( 16 );
		
		$this->objCal->getActiveSheet ()->getStyle ( 'AA' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'AB' )->getAlignment ()->setWrapText ( true );
		$this->objCal->getActiveSheet ()->getStyle ( 'AC' )->getAlignment ()->setWrapText ( true );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'AA4', 'Proyecto / Municipio a quien se destina el elemento (equipo, materiales, servicio, interconexion ISP, infraestructura)' )->getStyle ( "AA4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'AB4', 'Fecha prevista inicio instalación proyecto / municipio' )->getStyle ( "AB4" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'AC4', 'Fecha prevista fin instalación proyecto / municipio' )->getStyle ( "AC4" )->applyFromArray ( $styleCentrado );
		
		// PI&PS
		$this->objCal->setActiveSheetIndex ( 0 )->mergeCells ( 'AD3:AD3' );
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'AD3', '' )->getStyle ( "AD3" )->applyFromArray ( $styleCentrado );
		
		$this->objCal->getActiveSheet ()->getColumnDimension ( 'AD' )->setWidth ( 16 );
		
		$this->objCal->getActiveSheet ()->getStyle ( 'AD' )->getAlignment ()->setWrapText ( true );
		
		$this->objCal->setActiveSheetIndex ( 0 )->setCellValue ( 'AD4', 'Observaciones' )->getStyle ( "AD4" )->applyFromArray ( $styleCentrado );
	}
	public function configurarDocumento() {
		$this->objCal = new \PHPExcel ();
		// Set document properties
		$this->objCal->getProperties ()->setCreator ( "OpenKyOS" )->setLastModifiedBy ( "OpenKyOS" )->setTitle ( "Reporte Quincenal (" .  $_REQUEST ['fecha_final'] . ")" )->setSubject ( "Reporte Instalaciones" )->setDescription ( "Reporte de Quincenal en un determinado periodo de tiempo" )->setCategory ( "Reporte" );
	}
	public function retornarDocumento() {
		//$fecha_inicio = $_REQUEST ['fecha_inicio'];
		$fecha_fin = $_REQUEST ['fecha_final'];
		
		// Redirect output to a client’s web browser (Excel2007)
		header ( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
		//header ( 'Content-Disposition: attachment;filename="ReporteQuincenal(' . $fecha_inicio . ")-(" . $fecha_fin . ")" . time () . '.xlsx"' );
		header ( 'Content-Disposition: attachment;filename="Formato Seguimiento Compras FOR-1264-TEC-013-REV-00 31-10-16 (' .$fecha_fin . ")" .  '.xlsx"' );
		header ( 'Cache-Control: max-age=0' );
		// If you're serving to IE 9, then the following may be needed
		header ( 'Cache-Control: max-age=1' );
		
		// If you're serving to IE over SSL, then the following may be needed
		header ( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' ); // Date in the past
		header ( 'Last-Modified: ' . gmdate ( 'D, d M Y H:i:s' ) . ' GMT' ); // always modified
		header ( 'Cache-Control: cache, must-revalidate' ); // HTTP/1.1
		header ( 'Pragma: public' ); // HTTP/1.0
		ob_clean ();
		$objWriter = \PHPExcel_IOFactory::createWriter ( $this->objCal, 'Excel2007' );
		$objWriter->save ( 'php://output' );
		
		exit ();
	}
}

$miProcesador = new GenerarReporteExcelInstalaciones ( $this->miSql, $this->informacion );

?>

