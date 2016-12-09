	
	<?php
	/**
	 * Código Correspondiente a las Url de la peticiones Ajax.
	 */
	
	// URL base
	$url = $this->miConfigurador->getVariableConfiguracion("host");
	$url .= $this->miConfigurador->getVariableConfiguracion("site");
	$url .= "/index.php?";
	
	// Variables para Con
	$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
	$cadenaACodificar .= "&procesarAjax=true";
	$cadenaACodificar .= "&action=index.php";
	$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
	$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
	$cadenaACodificar .= "&funcion=consultaBeneficiarios";
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);
	
	// URL Consultar Proyectos
	$urlConsultarBeneficiarios = $url . $cadena;
	
	?>
	
	<?php
	
	/**
	 * Código Correspondiente a las Url de la peticiones Ajax.
	 */
	
	// URL base
	$url = $this->miConfigurador->getVariableConfiguracion("host");
	$url .= $this->miConfigurador->getVariableConfiguracion("site");
	$url .= "/index.php?";
	
	// Variables para Consultar Proyectos
	$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
	$cadenaACodificar .= "&procesarAjax=true";
	$cadenaACodificar .= "&action=index.php";
	$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
	$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
	$cadenaACodificar .= "&funcion=consultarProyectos";
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);
	
	// URL Consultar Proyectos
	$urlConsultarProyectos = $url . $cadena;
	
	?>
<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */


 $(document).ready(function() {

	$("#<?php echo $this->campoSeguro('beneficiario')?>").tokenfield();

});


	
</script>

