<?php
$indice=0;
$estilo[$indice++]="panelIzquierdo.css";
$estilo[$indice++]="panelDerecho.css";
$estilo[$indice++]="panelCentral.css";

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
