<?php
/**
 *
 * Los datos del bloque se encuentran en el arreglo $esteBloque.
 */

// URL base
$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
$url .= "/indice.php?";
// Variables
$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$valor .= "&procesarAjax=true";
$valor .= "&action=indice.php";
$valor .= "&bloqueNombre=". $esteBloque ["nombre"];
$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$valor .= "&funcion=codificarNombre";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlFuncionCodificarNombre = $url . $cadena;
?>

var i=1;
var elementos=0;

var data = [{material:"123", unidad:"unidad", cantidad:46},{material:"123", unidad:"Metro(s)", cantidad:100}];

function cargarMateriales(){
	
	$.each(data , function(indice,valor){
	 	$("#addr0").html('');
		$('#addr'+ i).html("<td>" + i +"</td>" + "<td>" + '<input type="hidden" name="'+ data[indice].material + '" value="' +  data[indice].material + '">'  +  data[indice].material + "</td>" + "<td>" + '<input type="hidden" name="'+  data[indice].unidad + '" value="' +  data[indice].unidad + '">' +  data[indice].unidad + "</td><td>" + '<input type="hidden" name="'+  data[indice].cantidad + '" value="' +  data[indice].cantidad + '">' +  data[indice].cantidad + "</td>" + '<td><input type="number" min="0" max="' +  data[indice].cantidad + '" class="form-control" id="usr" required></td>');
		$('#tabla1').append('<tr id="addr'+(i+1)+'"></tr>');
		i++;
	});
}

cargarMateriales();
