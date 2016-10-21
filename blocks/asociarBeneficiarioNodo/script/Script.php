<?php
/**
 * Importante: Este script es invocado desde la clase ArmadorPagina. La información del bloque se encuentra
 * en el arreglo $esteBloque. Esto también aplica para todos los archivos que se incluyan.
 */
$indice = 0;


$funcion [$indice ++] = "jquery-ui.js";

$funcion [$indice ++] = "dataTables.bootstrap.min.js";
$funcion [$indice ++] = "typeahead.bundle.js";

$funcion [$indice ++] = "bootstrap-tokenfield.js";
$funcion [$indice ++] = "bootstrap.min.js";
$funcion [$indice ++] = "select2.min.js";

$funcion [$indice ++] = "scrollspy.js";
$funcion [$indice ++] = "affix.js";
$funcion [$indice ++] = "docs.js";




$funcion [$indice ++] = "bootstrap-typeahead.js";

$embebido [$indice] = true;
$funcion [$indice ++] = "ajax.php";

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
