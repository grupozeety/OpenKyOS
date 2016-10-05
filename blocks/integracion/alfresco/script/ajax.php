<?php
/**
 *
 * API Proyectos
 */

// URL base
$url = $this->miConfigurador->getVariableConfiguracion("host");
$url .= $this->miConfigurador->getVariableConfiguracion("site");
$url .= "/index.php?";
// Variables
$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$valor .= "&procesarAjax=true";
$valor .= "&action=index.php";
$valor .= "&bloqueNombre=" . "llamarApi";
$valor .= "&bloqueGrupo=";
$valor .= "&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valor, $enlace);

// URL definitiva
$urlObtenerProyectos = $url . $cadena;
?>
function proyecto(){

	$.ajax({
		url: "<?php echo $urlObtenerProyectos;?>",
		dataType: "json",
		data: { metodo:'obtenerProyecto'},
		success: function(data){

		}

	});
};

proyecto();