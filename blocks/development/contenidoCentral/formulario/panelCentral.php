<?php
$rutaPrincipal = $this->miConfigurador->getVariableConfiguracion ( 'host' ) . $this->miConfigurador->getVariableConfiguracion ( 'site' );
$indice = $rutaPrincipal . "/index.php?";
$directorio = $rutaPrincipal . '/' . $this->miConfigurador->getVariableConfiguracion ( 'bloques' ) . "/menu_principal/imagen/";

$urlBloque = $this->miConfigurador->getVariableConfiguracion ( 'rutaUrlBloque' );


?>
<!--Division flotante para el panel-->
		<div id="divPanelCentral">
			<div class="iconoPanelCentral">
			</div>			
			<div class="tituloPanelCentral">
				¿Qué es SARA?
			</div>
			 <div class="cuerpoPanelCentral">
			 <p>
			 SARA es un marco de trabajo para crear aplicaciones en PHP. Tiene los elemento necesarios para crear aplicaciones
			 de manera ágil, gestionando varios aspectos relacionados con la seguridad, la estructura y la integración de módulos. 
			 </p>			 
			 </div>
		</div>
	<!--Fin Division flotante para el Panel-->
		