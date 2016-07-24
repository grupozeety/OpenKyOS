<?php 
$rutaPrincipal = $this->miConfigurador->getVariableConfiguracion ( 'host' ) . $this->miConfigurador->getVariableConfiguracion ( 'site' );
$indice = $rutaPrincipal . "/index.php?";
$directorio = $rutaPrincipal . '/' . $this->miConfigurador->getVariableConfiguracion ( 'bloques' ) . "/menu_principal/imagen/";
$urlBloque = $this->miConfigurador->getVariableConfiguracion ( 'rutaUrlBloque' );

?>
<!--Division flotante para el panel-->
<div id="divPanelDerecho">
	<div class="iconoPanelDerecho"></div>
	<div class="tituloPanelDerecho">Seguridad</div>
	<div class="cuerpoPanelDerecho">
		<p>SARA tiene mecanismos para minimizar los efectos de las vulnerabilidades más conocidas de las aplicaciones
		orientadas a la web.
		</p>
	</div>
</div>
<!--Fin Division flotante para el Panel-->
</div>