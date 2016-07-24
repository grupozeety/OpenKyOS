<?php
$indice=0;
//$estilo[$indice++]="black.css";
//$estilo[$indice++]="dcmegamenu.css";
$estilo[$indice++]="";


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

