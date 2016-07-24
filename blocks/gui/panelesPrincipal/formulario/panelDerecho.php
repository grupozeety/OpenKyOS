<?php 
$rutaPrincipal = $this->miConfigurador->getVariableConfiguracion ( 'host' ) . $this->miConfigurador->getVariableConfiguracion ( 'site' );
$indice = $rutaPrincipal . "/index.php?";
$directorio = $rutaPrincipal . '/' . $this->miConfigurador->getVariableConfiguracion ( 'bloques' ) . "/menu_principal/imagen/";
$urlBloque = $this->miConfigurador->getVariableConfiguracion ( 'rutaUrlBloque' );

?>
<!--Division flotante para el panel-->
<div id="divPanelDerecho">
	<div class="iconoPanelDerecho"></div>
	<div class="tituloPanelDerecho">SGSI</div>
	<div class="cuerpoPanelDerecho">
		<p>Para fomentar una modelo de gestión que garantice la seguridad de los datos personales, la
		Universidad Distrital está en proceso de implementación de su Sistema de Gestión de Seguridad de 
		la Información.
		</p>
	</div>
</div>
<!--Fin Division flotante para el Panel-->
