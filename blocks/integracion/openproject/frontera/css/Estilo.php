<?php
$indice = 0;

/**
 *Esquema de Adición estilos css.
 */

$estilo[$indice++] = "estiloBloque.css";

$indice++;

$rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site");

if ($unBloque["grupo"] == "") {
    $rutaBloque .= "/blocks/" . $unBloque["nombre"];
} else {
    $rutaBloque .= "/blocks/" . $unBloque["grupo"] . "/" . $unBloque["nombre"];
}

foreach ($estilo as $nombre) {
    echo "<link rel='stylesheet' type='text/css' href='" . $rutaBloque . "/frontera/css/" . $nombre . "'>\n";
}
?>
