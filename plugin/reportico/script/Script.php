<?php
$indice = 0;
$funcion [$indice ++] = "jquery.easing.1.3.js";
$funcion [$indice ++] = "jquery-1.11.0.min.js";
$funcion [$indice ++] = "jquery.jqGrid.min.js";
$funcion [$indice ++] = "jquery.jqGrid.src.js";
$funcion [$indice ++] = "jqueryui.js";
$funcion [$indice ++] = "jquery.validationEngine.js";
$funcion [$indice ++] = "jquery.validationEngine-es.js";
$funcion [$indice ++] = "jquery-te.js";
$funcion [$indice ++] = "select2.js";
$funcion [$indice ++] = "select2_locale_es.js";
$funcion [$indice ++] = "jquery.dataTables.js";
$funcion [$indice ++] = "jquery.dataTables.min.js";
$funcion [$indice ++] = "timepicker.js";
$funcion[$indice ++]="modernizr.custom.js";
$funcion[$indice ++]="jquery.dlmenu.js";

$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "host" );
$rutaBloque .= $this->miConfigurador->getVariableConfiguracion ( "site" );

if ($esteBloque ["grupo"] == "") {
	$rutaBloque .= "/blocks/" . $esteBloque ["nombre"];
} else {
	$rutaBloque .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"];
}

$_REQUEST['tiempo']=time();

if (isset ( $funcion [0] )) {
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

include ("ajax.php");

?>