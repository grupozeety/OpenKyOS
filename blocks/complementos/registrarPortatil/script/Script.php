<?php
/**
 * Importante: Este script es invocado desde la clase ArmadorPagina. La información del bloque se encuentra
 * en el arreglo $esteBloque. Esto también aplica para todos los archivos que se incluyan.
 */
$indice = 0;
/*
 $funcion[$indice]="jquery.validationEngine.js";
$indice++;
$funcion[$indice]="jquery.validationEngine-es.js";
$indice++;
*/
$funcion[$indice]="validator.js";
$indice++;
$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "host" );
$rutaBloque .= $this->miConfigurador->getVariableConfiguracion ( "site" );

if ($esteBloque ["grupo"] == "") {
	$rutaBloque .= "/blocks/" . $esteBloque ["nombre"];
} else {
	$rutaBloque .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"];
}

if (isset ( $funcion )) {
	foreach ( $funcion as $clave => $nombre ) {
		if (! isset ( $embebido [$clave] )) {
			echo "<script type='text/javascript' src='" . $rutaBloque . "/script/" . $nombre . "'></script>\n";
		} else {
			echo "<script type='text/javascript'>";
			include ($nombre);
			echo "</script>\n";
		}
	}
}

?>
