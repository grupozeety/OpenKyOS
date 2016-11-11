<?php
/**
 * Importante: Este script es invocado desde la clase ArmadorPagina. La información del bloque se encuentra
 * en el arreglo $esteBloque. Esto también aplica para todos los archivos que se incluyan.
 */
$indice = 0;

$funcion[$indice++] = "bootstrap-datepicker.js";
$funcion[$indice++] = "bootstrap-datepicker.min.js";
$funcion[$indice++] = "bootstrap-datepicker.es.min.js";
$funcion[$indice++] = "jquery.dataTables.js";
$funcion[$indice++] = "jquery.autocomplete.min.js";
$funcion[$indice++] = "jquery.autocomplete.js";
$funcion[$indice++] = "dataTables.bootstrap.min.js";
$funcion[$indice++] = "dataTables.responsive.min.js";
$funcion[$indice++]="select2.min.js";
$embebido [$indice] = true;

$funcion [$indice ++] = "ajax.php";
$funcion [$indice ++] = "ready.php";
$funcion [$indice ++] = "ready.js";

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
			echo "\n<script type='text/javascript' src='" . $rutaBloque . "/script/" . $nombre . "'>\n</script>\n";
		} else {
			echo "\n<script type='text/javascript'>";
			include ($nombre);
			echo "\n</script>\n";
		}
	}
}

?>
