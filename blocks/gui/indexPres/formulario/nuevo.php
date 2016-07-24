<?php
$esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

$rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
$rutaBloque .= $esteBloque ['grupo'] . "/" . $esteBloque ['nombre'];

$directorio = $this->miConfigurador->getVariableConfiguracion("host");
$directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
$directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");
?>


<div id="slider1_container" style="position: relative; top: 0px; left: 0px; width: 1800px; height: 600px; overflow: hidden;">
    <!-- Slides Container -->
    <div u="slides" style="cursor: move; position: absolute; overflow: hidden; left: 0px; top: 0px; width: 1800px; height: 600px; overflow: hidden;">
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/abstract_2.jpg" /></div>
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/abstract_3.jpg" /></div>
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/abstract_4.jpg" /></div>
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/abstract_5.jpg" /></div>
        <div><img u="image" src="<?php echo $rutaBloque ?>/images/abstract_6.jpg" /></div>
    </div>
</div>