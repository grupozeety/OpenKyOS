<?php
namespace reporteAgendamientos\funcion;

use reporteAgendamientos\funcion\redireccionar;

include_once ('redireccionar.php');
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

class Registrar {
	

    var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSql;
	var $conexion;
	
	function __construct($lenguaje, $sql, $funcion) {
		
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miFuncion = $funcion;
	}
	
	function procesarFormulario() {

		$agendamientos = "(";
		$cont = 0;
		foreach ($_REQUEST as $key => $value){
			
			$name = explode("_", $key);
			
			if($name[0] == "checkbox"){
				
				if($cont > 0){
					$agendamientos .= ",";
				}
				
				$agendamientos .= $value;
				
				$cont++;
			}
			
		}
		
		$agendamientos .= ")";
		/**
         *  1. Consultar Los porcentajes por Proyecto Consumidos
         **/

        $this->consultarAgendamientos($agendamientos);

        /**
         *  2. Generar Documento PDF
         **/

        $this->generarDocumentoPDF();

    }

    public function generarDocumentoPDF() {
        include_once "generarDocumentoPdf.php";
    }

    public function consultarAgendamientos($agendamientos) {

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
        
        $cadenaSql = $this->miSql->getCadenaSql('agendamientosReporte', $agendamientos); 
        $this->elementos_reporte = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
    }
}

$miRegistrador = new Registrar ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();

?>

