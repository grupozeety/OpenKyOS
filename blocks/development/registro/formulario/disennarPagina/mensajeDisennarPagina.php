<?php
// Si existe algun tipo de error en el login aparece el siguiente mensaje
$mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );

if ($mensaje) {
		
	$tipoMensaje = $this->miConfigurador->getVariableConfiguracion ( 'tipoMensaje' );
		
	if ($tipoMensaje == 'json') {

		$atributos ['mensaje'] = $mensaje;
		$atributos ['json'] = true;
	} else {
		$atributos ['mensaje'] = $this->lenguaje->getCadena ( $mensaje );
	}
	// -------------Control texto-----------------------
	$esteCampo = 'divMensaje';
	$atributos ['id'] = $esteCampo;
	$atributos ["tamanno"] = '';
	$atributos ["estilo"] = 'information';
	$atributos ["etiqueta"] = '';
	$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
	echo $this->miFormulario->campoMensaje ( $atributos );
	unset ( $atributos );
}
?>