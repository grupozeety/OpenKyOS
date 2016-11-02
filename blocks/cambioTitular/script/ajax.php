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
$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$valor .= "&procesarAjax=true";
$valor .= "&action=index.php";
$valor .= "&bloqueNombre=" . $esteBloque ["nombre"];
$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$valor .= "&funcion=consultarBeneficiarios";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlCargarInformacion = $url . $cadena;

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar .= "&funcion=consultaBeneficiarios";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );

// URL Consultar Proyectos
$urlConsultarBeneficiarios = $url . $cadena;

?>

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
$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$valor .= "&procesarAjax=true";
$valor .= "&action=index.php";
$valor .= "&bloqueNombre=". $esteBloque ["nombre"]; 
$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$valor .= "&funcion=consultarCabecera";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlCargarInformacion = $url . $cadena;
?>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */

$('#example').hide();

$("#<?php echo $this->campoSeguro('beneficiario');?>").autocomplete({
	minChars: 3,
	serviceUrl: '<?php echo $urlConsultarBeneficiarios;?>',
	onSelect: function (suggestion) {

	  	$("#<?php echo $this->campoSeguro('id');?>").val(suggestion.data);
		
		if($("#<?php echo $this->campoSeguro('id');?>").val()!=''){

			$('#example').DataTable().destroy();
			
			var table = $('#example').DataTable({
				"responsive": true,
				"processing": true,
				"searching": false,
				"info":false,
				"paging": false,
				ajax: {
					url: "<?php echo $urlCargarInformacion?>",
				    data: { valor:$("#<?php echo $this->campoSeguro('id')?>").val()},
				    dataSrc:"data"   
				},
				"language": {
					"sProcessing":     "Procesando...",
				    "sZeroRecords":    "No se encontraron resultados",
				    "sEmptyTable":     "Ningún dato disponible en esta tabla",
				    "sLoadingRecords": "Cargando...",
			    },
				"columns": [
					{ "data": "persona" },
			  		{
			          "data":   "id_checkbox",
			          render: function ( data, type, row ) {
			          	if ( type === 'display' ) {
				        	return '<input type="radio" name="' + data.id + '" value="' + data.value + '" class="editor-active">';
				      	}
				      	return data;
				       },
				                
				       className: "dt-body-center"
				    }
				],
				"columnDefs": [
					{"className": "dt-center", "targets": "_all"}
				],
			});
			
			$('#example').show();
		
		}
				
	}

});

$("#<?php echo $this->campoSeguro('beneficiario');?>").change(function() {

 	if($("#<?php echo $this->campoSeguro('id');?>").val()==''){

	  	$("#<?php echo $this->campoSeguro('beneficiario');?>").val('');
		$("#<?php echo $this->campoSeguro('direccion');?>").attr("required",true);

	}

});

$('.btn').click(function() {
	if (!$("input[type='radio']:checked").val()) {
    	return false;
    }
});

if ($("#<?php echo $this->campoSeguro('mensajemodal')?>").length > 0 ){
	$("#myModalMensaje").modal('show');
}

$(function() {
	$("#regresarConsultar").click(function( event ) {	
	 	$("#myModalMensaje").modal('hide');
	});
});
