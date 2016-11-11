<?php

namespace agendarModificar\funcion;

use agendarModificar\funcion\redireccionar;

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
		foreach ( $_REQUEST as $key => $value ) {
			
			$name = explode ( "_", $key );
			
			if ($name [0] == "checkbox") {
				
				if ($cont > 0) {
					$agendamientos .= ",";
				}
				$agendaCrear [$key] = $value;
				$agendamientos .= $value;
				
				$cont ++;
			}
		}
		$agendamientos .= ")";
		
		$agenda = array (
				'items' => $agendamientos,
				'fecha' => $_REQUEST ['fecha_agendamiento_nueva'],
				'id_comisionador' => $_REQUEST ['id_comisionador_nuevo'],
				'comisionador' => $_REQUEST ['comisionador_nuevo'] 
		);
		

		/**
		 * 2.
		 * Modificar Agendamientos
		 */
		if (!empty($agenda) && !empty($agendamientos)) {
			$this->modificarAgendamientos ( $agenda );
		}
		
		/**
		 * 3.
		 * Crear Nuevos Agendamientos
		 */
		
		$resultado = $this->crearAgendamientos ( $agendaCrear );
	

		if ($resultado == true) {
			
			redireccion::redireccionar ( 'inserto' );
		} else {
			redireccion::redireccionar ( 'noinserto' );
		}
	}
	public function modificarAgendamientos($agendamientos) {
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarAgenda', $agendamientos );
		
		$this->resultadoAgenda = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
	}
	public function crearAgendamientos($agendamientos) {
		$result = false;
		
		$conexion = "interoperacion";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		foreach ( $agendamientos as $key => $values ) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultarAgenda', $agendamientos [$key] );
			$data = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			if (! empty ( $data )) {
				$nuevasAgendas [$key] = array (
						'id_beneficiario' => $data [0] ['id_beneficiario'],
						'tipo_agendamiento' => $data [0] ['tipo_agendamiento'],
						'id_comisionador' => $_REQUEST ['id_comisionador_nuevo'],
						'nombre_comisionador' => $_REQUEST ['comisionador_nuevo'],
						'fecha_agendamiento' => $_REQUEST ['fecha_agendamiento_nueva'] 
				);
			}
		}
		
		if (! empty ( $nuevasAgendas )) {
			$cadenaSql = $this->miSql->getCadenaSql ( 'registrarAgendamiento', $nuevasAgendas );
			
			$cadenaSql = str_replace ( "''", 'null', $cadenaSql );
			$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
			
			if ($resultado == true) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'registrarConsecutivoAgendamiento' );
				$result = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar" );
			}
		}
		
		return $result;
	}
}

$miRegistrador = new Registrar ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();

?>

