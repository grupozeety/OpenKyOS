<?php
$indice = 0;

$estilo [$indice] = "estiloGrilla.css";
$indice ++;

$estilo [$indice++] = "bootstrap.min.css";
$estilo [$indice++] = "application.css";
$estilo [$indice++] = "simple-sidebar.css";

$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "host" );
$rutaBloque .= $this->miConfigurador->getVariableConfiguracion ( "site" );

if ($unBloque ["grupo"] == "") {
	$rutaBloque .= "/blocks/" . $unBloque ["nombre"];
} else {
	$rutaBloque .= "/blocks/" . $unBloque ["grupo"] . "/" . $unBloque ["nombre"];
}

foreach ( $estilo as $nombre ) {
	echo "<link rel='stylesheet' type='text/css' href='" . $rutaBloque . "/css/" . $nombre . "'>\n";
}
?>
