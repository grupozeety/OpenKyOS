<?php 
$rutaPrincipal = $this->miConfigurador->getVariableConfiguracion ( 'host' ) . $this->miConfigurador->getVariableConfiguracion ( 'site' );
$indice = $rutaPrincipal . "/index.php?";
$directorio = $rutaPrincipal . '/' . $this->miConfigurador->getVariableConfiguracion ( 'bloques' ) . "/menu_principal/imagen/";

$urlBloque = $this->miConfigurador->getVariableConfiguracion ( 'rutaUrlBloque' );

$enlace= $this->miConfigurador->getVariableConfiguracion ( 'enlace' );
?>
<!--Division flotante para el panel-->
		<div id="divPanelNoticias">
			<div class="iconoPanelNoticias">
			</div>			
			<div class="tituloPanelNoticias">
				Noticias de Interés
			</div>
			 <div class="cuerpoPanelNoticias">
			 <p><br></p>
			 <p>
			 La Oficina Asesora de Sistemas se encuentra reestructurando sus procesos para satisfacer los servicios declarados en
			 el Catálogo de Servicios. 
			 </p>
			 </div>
		</div>
<!--Fin Division flotante para el Panel-->
</div>
<!--Fin Division flotante para el panel general-->
		
		
