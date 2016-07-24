<?php
$indice = 0;
$estilo [$indice] = "general.css";
$indice ++;
$estilo [$indice] = "estiloCuadrosMensaje.css";
$indice ++;
$estilo [$indice] = "estiloTexto.css";
$indice ++;

$host = $this->miConfigurador->getVariableConfiguracion ( "host" );
$sitio = $this->miConfigurador->getVariableConfiguracion ( "site" );

if (isset ( $_REQUEST ['jquery'] )) {
	$estilo [$indice] = "estiloFormulario.css";
	$indice ++;
}
if (isset ( $_REQUEST ['jquery-validation'] ) ) {
    $estilo [$indice] = 'validationEngine.jquery.css';
    $indice ++;
}

if (isset ( $_REQUEST ['bootstrap'] )) {
	if($_REQUEST ['bootstrap'] != 'true'){
		$boostrap = explode(".min", $_REQUEST ['bootstrap']);
		if(!strrpos($_REQUEST ['bootstrap'],".min")){
			$estilo [$indice] = 'scripts/bootstrap/bootstrap-'. $boostrap[0] .'-dist/css/bootstrap.css';
		} else {
			$estilo [$indice] = 'scripts/bootstrap/bootstrap-'. $boostrap[0] .'-dist/css/bootstrap.min.css';
		}
	} else {
		$estilo [$indice] = 'scripts/bootstrap/bootstrap-3.3.5-dist/css/bootstrap.min.css';
	}
	$plugin [$indice] = true; //El css está en la carpeta plugin
	$indice ++;
}

foreach ( $estilo as $indice => $nombre) {
	if(isset($plugin [$indice])){
		echo "<link rel='stylesheet' type='text/css' href='" . $host . $sitio . "/plugin/" . $nombre . "'>\n";
	} else {
		echo "<link rel='stylesheet' type='text/css' href='" . $host . $sitio . "/theme/basico/css/" . $nombre . "'>\n";
	}
}
?>
