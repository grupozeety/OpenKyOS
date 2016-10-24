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
$valor .= "&funcion=consultarNodo";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlCargarInformacion = $url . $cadena;
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
$valor .= "&funcion=inhabilitarAsociacion";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlEliminarNodo = $url . $cadena;
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
$valor .= "&funcion=redireccionar";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlGenerarEnlace = $url . $cadena;
?>

<?php

$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$valorCodificado = "pagina=asociarBeneficiarioNodo&opcion=agregar";
$valorCodificado .= "&id=";

?>

<?php

$directorioReg = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$valorCodificadoReg = "pagina=asociarBeneficiarioNodo&opcion=agregar";
$variableReg = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificadoReg );
$enlaceReg = $directorioReg . '=' . $variableReg;

?>

$(document).ready(function() {

	var id = "";
	
	$('#example')
			.removeClass( 'display' )
			.addClass('table table-striped table-bordered');
	
			
	$(document).ready(function() {
	    var table = $('#example').DataTable( {
			"sDom": "<'dt-toolbar'<'col-xs-4'l><'col-xs-4'<'toolbar'>><'col-xs-4'f>>"+
			"t"+"<'dt-toolbar-footer'<'col-xs-6'i><'col-xs-6'p>>",
	        processing: true,
	        searching: true,
	        ajax: {
	            url: "<?php echo $urlCargarInformacion?>",
	            dataSrc:"data"   
	        },
	        "columns": [
	            { "data": "codigo_nodo" },
	            { "data": "urbanizacion" },
	            { "data": "id_beneficiario" },
	            { "data": "identificacion" },
	            { "data": "nombre" },
	            {
	      			"data": null,
	      			"defaultContent": "<span class='glyphicon glyphicon-trash optionRemove'></span><span class='glyphicon glyphicon-pencil optionEdit'></span>"
	    		}
	        ]
	    } );
	    
		$("div.toolbar").html('<button type="button" id="agregarNodo" class="btn btn-primary">Asociar Beneficiario a Celda o Nodo EOC</button>'); 
		    
	    $('#example tbody').on( 'click', '.optionRemove', function () {
	    	var data = table.row( $(this).parents('tr') ).data();
	        id = data['id_beneficiario'];
	        $("#myModal").modal("show");
	    } );
	    
	    $('#example tbody').on( 'click', '.optionEdit', function () {
	    	var data = table.row( $(this).parents('tr') ).data();
	        id = data['id_beneficiario'];
	        generarEnlace();
	    } );
	    
	    $(function() {
			$("#botonCancelarElim").click(function( event ) {	
				$("#myModal").modal("hide");
			});
		}); 
		
		$(function() {
			$("#botonAceptarElim").click(function( event ) {	
				eliminarNodo();
				$("#myModal").modal("hide");
			});
		});
		
		$(function() {
			$("#agregarNodo").click(function( event ) {	
		    	location.href = "<?php echo $enlaceReg;?>";
			});
		});
		
		function eliminarNodo(){
	
			$.ajax({
				url: "<?php echo $urlEliminarNodo;?>",
				dataType: "json",
				data: { valor: id},
				success: function(data){
					if(data == true){
						table.ajax.reload();
						$("#confirmacionElim").modal("show");
					}else{
						table.ajax.reload();
						$("#confirmacionNoElim").modal("show");
					}
				}
				
			});
		};
		
		function generarEnlace(){
	
			$.ajax({
				url: "<?php echo $urlGenerarEnlace;?>",
				dataType: "json",
				data: { valor: "<?php echo $valorCodificado;?>",
						directorio: "<?php echo $directorio;?>",
						id: id},
				success: function(data){
					location.href = data;
				}
				
			});
		};
		
		$(function() {
			$("#botonCerrar").click(function( event ) {	
				$("#confirmacionElim").modal("hide");
			});
		});
		
		$(function() {
			$("#botonCerrar2").click(function( event ) {	
				$("#confirmacionNoElim").modal("hide");
			});
		});
	
	});

}); 

<?php

$directorioReg = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$valorCodificadoReg = "pagina=asociarBeneficiarioNodo";
$variableReg = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificadoReg );
$enlaceReg = $directorioReg . '=' . $variableReg;

?>

$(document).ready(function() {

	$(function() {
			$("#regresarConsultar").click(function( event ) {	
		    	location.href = "<?php echo $enlaceReg;?>";
			});
	});
	
});

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
$urlCargarCab = $url . $cadena;
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
$valor .= "&funcion=consultarBeneficiario";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlCargarCab = $url . $cadena;
?>

$(document).ready(function() {

	var users = new Bloodhound({
	         datumTokenizer: function(d) { return [d.label]; },
	         queryTokenizer: Bloodhound.tokenizers.whitespace,
	         remote: {
		        url: "<?php echo $urlCargarCab; ?>" + "&query=%QUERY",
	            wildcard: '%QUERY'
	         },
	    });
	    
	    users.initialize(true);
	        
	$("#<?php echo $this->campoSeguro('beneficiario')?>").tokenfield({
	  typeahead: {
	    source: users.ttAdapter(),
	    displayKey: 'label'
	  }
	});
	
});

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
$valor .= "&funcion=consultarBeneficiarioEspecifico";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlBeneficiarioEspecifico = $url . $cadena;
?>

$(document).ready(function() {

	$("#<?php echo $this->campoSeguro('beneficiario')?>").on('tokenfield:createtoken', function (event) {
		
		var existingTokens = $(this).tokenfield('getTokens');
	   		$.each(existingTokens, function(index, token) {
		        if (token.value === event.attrs.value){
		        	event.preventDefault();
		            $(".token-input").val("");
		        }
	
	   	});
	   		
	    if(event['attrs']['id'] == undefined){
	    
	    	$.ajax({
				url: "<?php echo $urlBeneficiarioEspecifico;?>",
				dataType: "json",
				data: { valor: event.attrs.value},
				async: false,
				success: function(data){console.log(data);
					if(data[0].id === null){
						event.preventDefault();
	        			$(".token-input").val("");
					}else{
						event.attrs.id = data[0].id;
						event.attrs.value = data[0].value;
						event.attrs.label = data[0].label;
						
					}
				}
			});
			
			var existingTokens = $(this).tokenfield('getTokens');
			$.each(existingTokens, function(index, token) {
				if (token.value === event.attrs.value){
					event.preventDefault();
			    	$(".token-input").val("");
			    }
			});
			
	    }
	});
	
	$("#<?php echo $this->campoSeguro('beneficiario')?>").blur(function() {
	  $(".token-input").val("");
	});
	
	 
	if ($("#<?php echo $this->campoSeguro('mensajemodal')?>").length > 0 ){
		$("#myModalMensaje").modal('show');
	}

});