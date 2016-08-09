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
$valor .= "&funcion=codificarNombre";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlFuncionCodificarNombre = $url . $cadena;
?>


function codificarNombre(elem, request, response){
	$.ajax({
		url: "<?php echo $urlFuncionCodificarNombre?>",
		dataType: "json",
		data: { valor:i},
		success: function(data){
			
			var material = $("#<?php echo $this->campoSeguro('material')?> option:selected").text();
			var unidad = $("#<?php echo $this->campoSeguro('unidad')?> option:selected").text();
			var cantidad = $("#<?php echo $this->campoSeguro('cantidad')?>").val();
			
			$('#addr'+ i).html("<td>" + "<input type='checkbox' id='checkbox"+(i)+"'>" +"</td>" + "<td>" + '<input type="hidden" name="'+ data['material'] + '" value="' + material + '">'  + material + "</td>" + "<td>" + '<input type="hidden" name="'+ data['unidad'] + '" value="' + unidad + '">' + unidad + "</td><td>" + '<input type="hidden" name="'+ data['cantidad'] + '" value="' + cantidad + '">' + cantidad + "</td>");
			$('#tabla1').append('<tr id="addr'+(i+1)+'"></tr>');
		 
			$("#<?php echo $this->campoSeguro('material')?>").val(''); 
			$("#<?php echo $this->campoSeguro('material')?>").change();
			$("#<?php echo $this->campoSeguro('unidad')?>").val('');
			$("#<?php echo $this->campoSeguro('unidad')?>").change();
			$("#<?php echo $this->campoSeguro('cantidad')?>").val("1");
			
			$('#myModal').modal('hide');
			
			i++;
 
		}
	});
};



var i=1;

$("#<?php echo $this->campoSeguro('botonAgregar')?>").click(function(){

	
	
	$("#addr0").html('');
	
	codificarNombre();
	 
	
});

$("#remove").click(function(){
    for(j=0; j<i; j++){
    	if( $('#checkbox' + (j)).prop('checked') ) {
    		$("#addr"+(j)).remove();
    	}
    }
});
