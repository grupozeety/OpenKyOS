<?php

namespace reportes\reporteQuincenal\entidad;

class GenerarReporteInstalaciones {
	public $miConfigurador;
	public $lenguaje;
	public $miFormulario;
	public $miSql;
	public $conexion;
	public $informacion;
	public $encriptador;
	
	// _______________________________
	public $esteRecursoERP;
	// ________________________________
	public function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
		
		$conexion = "erpnext";
		$this->esteRecursoERP = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$_REQUEST ['tiempo'] = time ();
		
		/**
		 * XX.
		 * Consultar Informacion(Reporte)
		 */
		
		$this->consultarInformacion ();
		
		/**
		 * 6.
		 * Crear Documento Hoja de Calculo(Reporte)
		 */
		
		$this->crearHojaCalculo ();
	}
	public function consultarInformacion() {
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarSeguimiento', $_REQUEST['fecha_final'] );
		$this->informacion = $this->esteRecursoERP->ejecutarAcceso ( $cadenaSql, "busqueda" );

		//var_dump($this->esteRecursoERP);
	}
	public function crearHojaCalculo() {
		include_once "crearDocumentoHojaCalculo.php";
	}
}

$miProcesador = new GenerarReporteInstalaciones ( $this->sql );

?>

