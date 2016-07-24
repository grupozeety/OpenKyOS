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
				Proyectos de Desarrollo
			</div>
			 <div class="cuerpoPanelCentral">
			 <p>
			 Actualmente se tienen estructurados más de 30 proyectos de desarrollo. La mayoría están en la fase de inicio y
			 están disponibles para grupos de investigación, grupos de trabajo o estudiantes que deseen realizarlos como
			 trabajo de grado. 
			 </p>			 
			 </div>
		</div>
	<!--Fin Division flotante para el Panel-->
		