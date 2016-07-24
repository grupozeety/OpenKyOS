<?php 
$rutaPrincipal = $this->miConfigurador->getVariableConfiguracion ( 'host' ) . $this->miConfigurador->getVariableConfiguracion ( 'site' );
$indice = $rutaPrincipal . "/index.php?";
$directorio = $rutaPrincipal . '/' . $this->miConfigurador->getVariableConfiguracion ( 'bloques' ) . "/menu_principal/imagen/";

$urlBloque = $this->miConfigurador->getVariableConfiguracion ( 'rutaUrlBloque' );

$enlace= $this->miConfigurador->getVariableConfiguracion ( 'enlace' );
?>
<!--Division flotante para el panel General-->
		<div id="divPaneles">
<!--Division flotante para el panel-->
		<div id="divPanelIzquierdo">
			<div class="iconoPanelIzquierdo">
			</div>			
			<div class="tituloPanelIzquierdo">
				Proceso de Desarrollo
			</div>
			 <div class="cuerpoPanelIzquierdo">
			 <p><br></p>
			 <p>
			 El proceso OpenUP/OAS, es el proceso de desarrollo declarado, necesario para
			 emprender desarrollos de software alineados con las necesidades de la institución.  
			 </p>
			 </div>
		</div>
<!--Fin Division flotante para el Panel-->
		
