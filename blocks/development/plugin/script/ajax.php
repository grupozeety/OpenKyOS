<?php
/**
 * 
 * Los datos del bloque se encuentran en el arreglo $esteBloque.
 */
// URL base
$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
$url .= "/index.php?";

// Variables
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar .= $cadenaACodificar . "&funcion=nombre";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );

// URL definitiva
$urlFinal = $url . $cadena;

?>
<script type='text/javascript'>

$(document).ready(function() {

	$("#seleccionar").change(function() {

	//Quien procesará la petición ajax	
	  $.ajax({ 
		  url: "<?php echo $urlFinal?>",
		  data: {opcion : $( "#seleccionar" ).val()}, 
		  dataType: "html"	  
	    })
	    
	    //  Función que se ejecuta una vez se reciba la respuesta
	    .done(function( data ) {

		  $('#division1').html(data);

		    
		  });
	})
});

</script>

