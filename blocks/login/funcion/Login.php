<?php

class Login {
	var $miConfigurador;
	var $miAutenticador;
	
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miAutenticador = \Autenticador::singleton ();
	}
	function procesarFormulario() {
		$resultado = $this->miAutenticador->iniciarAutenticacion();
		//Si la autenticación fue exitosa va a la página bienvenido
		if($resultado){
			$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
			$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
			$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
			$valorCodificado = "pagina=paginaPrincipal";
			$valorCodificado .= "&sesion=true";
			$valorCodificado .= "&respuesta=''";
			$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
			$enlace = $directorio.'='.$valorCodificado;
			header('Location: '.$enlace);
		}
		return $resultado;
	}
}

$miProcesador = new Login ();
$miProcesador->procesarFormulario();
?>