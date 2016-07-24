<?php
$indice=0;
$estilo[$indice++]="timepicker.css";
$estilo[$indice++]="validationEngine.jquery.css";
$estilo[$indice++]="jquery.auto-complete.css";
$estilo[$indice++]="select2.css";
$estilo[$indice++]="miestilo.css";
// $estilo[$indice++]="formToWizard.css";

// Tablas
$estilo[$indice++]="demo_page.css";
// $estilo[$indice++]="demo_table.css";
$estilo[$indice++]="jquery.dataTables.css";
$estilo[$indice++]="jquery.dataTables_themeroller.css";

$rutaBloque=$this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site");

if($unBloque["grupo"]==""){
	$rutaBloque.="/blocks/".$unBloque["nombre"];
}else{
	$rutaBloque.="/blocks/".$unBloque["grupo"]."/".$unBloque["nombre"];
}

foreach ($estilo as $nombre){
	echo "<link rel='stylesheet' type='text/css' href='".$rutaBloque."/css/".$nombre."'>\n";
}
?>
