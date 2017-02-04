<?php
$indice = 0;

/**
 *Esquema de Adición estilos css.
 */

$estilo[$indice++] = "estiloBloque.css";
$estilo[$indice++] = "responsive.bootstrap.min.css";
$estilo [$indice ++] = "bootstrap-datepicker.min.css";
$estilo [$indice ++] = "bootstrap-datepicker.css ";
$estilo[$indice++]="dataTables.bootstrap.min.css";
$estilo[$indice++]="miestilo.css";
$estilo[$indice++]="modalLoad.css";
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
