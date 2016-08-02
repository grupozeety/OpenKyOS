<?php
/**
 * Listado de funciones javascript que deben ser incluidas en todas las páginas del aplicativo.
 * 
 * @var unknown $host
 */
$host = $this->miConfigurador->getVariableConfiguracion ( 'host' );
$sitio = $this->miConfigurador->getVariableConfiguracion ( 'site' );
$estiloPredeterminado = $this->miConfigurador->getVariableConfiguracion ( 'estiloPredeterminado' );

$indice = 0;
$estilo = array ();

$funcion [$indice] = "funciones.js";
$indice ++;

if (isset ( $_REQUEST ['jquery'] )) {
	if($_REQUEST ['jquery'] != 'true'){//Se carga una versión de jquery en particular
		$funcion [$indice] = 'javascript/jquery-'. $_REQUEST ['jquery'] . '.js';
	} else {
		$funcion [$indice] = 'javascript/jquery.js';
	}
	$indice ++;
}
if (isset ( $_REQUEST ['jquery-ui'] )) {
    $funcion [$indice] = 'javascript/jquery-ui/jquery-ui.js';
    $estilo [$indice] = 'javascript/jquery-ui/jquery-ui-themes/themes/' . $estiloPredeterminado . '/jquery-ui.css';
    $indice ++;
}
if (isset ( $_REQUEST ['jquery-validation'] )) {
    $funcion [$indice] = "javascript/jquery.validationEngine.js";
    $indice ++;
    $funcion [$indice] = "javascript/jquery.validationEngine-es.js";
    $indice ++;
}
if (isset ( $_REQUEST ['bootstrap'] )) {
	if($_REQUEST ['bootstrap'] != 'true'){
		$boostrap = explode(".min", $_REQUEST ['bootstrap']);
		if(!strrpos($_REQUEST ['bootstrap'],".min")){
			$funcion [$indice] = 'javascript/bootstrap/bootstrap-'. $boostrap[0] .'-dist/js/bootstrap.js';
		} else {
			$funcion [$indice] = 'javascript/bootstrap/bootstrap-'. $boostrap[0] .'-dist/js/bootstrap.min.js';
		}		
	} else {
		$funcion [$indice] = 'javascript/bootstrap/bootstrap-3.3.5-dist/js/bootstrap.min.js';
		$indice++;	
		
	}
	$indice ++;
	$funcion [$indice] = 'javascript/bootstrap/bootstrap-filestyle.min.js';
	$indice ++;	
}

foreach ( $funcion as $nombre ) {
    echo "<script type='text/javascript' src='" . $host . $sitio . '/plugin/scripts/' . $nombre . "'></script>\n";
}

foreach ( $estilo as $nombre ) {
    echo "<link rel='stylesheet' type='text/css' href='" . $host . $sitio . '/plugin/scripts/' . $nombre . "'>\n";
}
?>
