<?php
namespace development\registro\formulario;

class DisennadorPagina {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSQL;
	function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );		
		$this->lenguaje = $lenguaje;		
		$this->miFormulario = $formulario;		
		$this->miSQL = $sql;
	}
	
	function armarFormulario() {
		
		// Iniciar el formulario
		$this->parametrosGenerales ();
		
		// Cargar los controles
		$this->controlesFormulario ();
		
		$this->definirGrilla ();
		
		// Cargar los botones
		$this->botonesFormulario ();
		
		// Cargar variables a pasar entre formularios y finalizar el formulario
		$this->pasoVariables ();
		
		return true;
	}
	
	function definirGrilla() {
		include_once 'disennarPagina/grillaDisennarPagina.php';
	}
	
	/**
	 * Esta función se llama desde definirGrilla()
	 *
	 * @return unknown
	 */
	function construirLista() {
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( 'rutaBloque' );
		// 1. Realizar la búsqueda en la db
		include_once $rutaBloque . 'funcion/listaBloques.php';
	
		if (is_array ( $resultado )) {
			// 2. Construir la lista
			include_once 'disennarPagina/listaBloques.php';
		}
		return true;
	}
	
	function parametrosGenerales() {
		include_once 'disennarPagina/parametrosDisennarPagina.php';
	}
	
	function botonesFormulario() {
		include_once 'disennarPagina/botonesDisennarPagina.php';
	}
	
	function pasoVariables() {
		include_once 'disennarPagina/variablesDisennarPagina.php';
	}
	
	function controlesFormulario() {
	}
	
	function mensaje() {
		
		include_once 'disennarPagina/mensajeDisennarPagina.php';
	}
}

// Para no tener que crear objtos nuevos se pasan como argumentos algunos atributos de la clase ProcesarAjax
$miRegistrador = new DisennadorPagina ( $this->lenguaje, $this->miFormulario, $this->sql );

$miRegistrador->armarFormulario ();
$miRegistrador->mensaje ();

?>
