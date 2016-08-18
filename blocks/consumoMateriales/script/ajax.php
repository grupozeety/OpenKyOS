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
var data = [{material:"material 1", unidad:"unidad", cantidad:46},{material:"material 2", unidad:"Metro(s)", cantidad:100}];
var inputConsumo = [];
var porcentaje = 0;

<!-- Función que se encarga del proceso de codificación de los nombres de los input's de consumo por medio de ajax -->

function codificarNombre(nombre, material, unidad, cantidad){

	$.ajax({
		url: "<?php echo $urlFuncionCodificarNombre?>",
		dataType: "json",
		data: { valor:nombre},
		success: function(data){
			
			var objeto = {};
			objeto['id'] = data['material'];
			objeto['asignado'] = cantidad;
			inputConsumo.push(objeto);
			
			$("#addr0").html('');
			$('#addr'+ nombre).html("<td>" + nombre +"</td><td>" + material + "</td><td>" + unidad + "</td><td>" + cantidad + "</td><td><input type='number' min='0' max='" +  cantidad + "' class='form-control' id='" + data['material'] + "' required></td>");
			$('#tabla1').append('<tr id="addr'+(nombre+1)+'"></tr>');
			
			<!-- Se genera dinámicamente el evento change para estar revisando los cambios en los campos -->
			$('#' + data['material']).click(function(){
				
				porcentaje = 0;
				cont = 0;
        		$.each(inputConsumo , function(indice,valor){
					porcentaje = porcentaje + ( $('#' + inputConsumo[indice]['id']).val() *100 ) / inputConsumo[indice]['asignado'];
					cont++;
				});
				
				porcentaje = porcentaje / cont;
				
				$("#<?php echo $this->campoSeguro('porcentajecons')?>").val(porcentaje);
				$("#<?php echo $this->campoSeguro('porcentajecons')?>").change();
        		
        	});
        	
        	$('#' + data['material']).keyup(function(){
				
				porcentaje = 0;
				cont = 0;
        		$.each(inputConsumo , function(indice,valor){
					porcentaje = porcentaje + ( $('#' + inputConsumo[indice]['id']).val() *100 ) / inputConsumo[indice]['asignado'];
					cont++;
				});
				
				porcentaje = porcentaje / cont;
				
				$("#<?php echo $this->campoSeguro('porcentajecons')?>").val(porcentaje);
				$("#<?php echo $this->campoSeguro('porcentajecons')?>").change();
        		
        	});
			
		}
	});
};

function cargarMateriales(){
	
	$("#<?php echo $this->campoSeguro('proyecto')?>").val('Nombre Proyecto');
	$("#<?php echo $this->campoSeguro('proyecto')?>").change();
	$("#<?php echo $this->campoSeguro('actividad')?>").val('Nombre Actividad');
	$("#<?php echo $this->campoSeguro('actividad')?>").change();
	
	$.each(data , function(indice,valor){
		codificarNombre(i, data[indice].material, data[indice].unidad, data[indice].cantidad);
		i++;
		elementos++;
	});
}

<!-- cargarMateriales(); -->

$("#<?php echo $this->campoSeguro('ordenTrabajo')?>").change(function() {
	
	if($("#<?php echo $this->campoSeguro('ordenTrabajo')?>").val() != ""){
		cargarMateriales();
	}else{
		$("#<?php echo $this->campoSeguro('proyecto')?>").val('');
		$("#<?php echo $this->campoSeguro('proyecto')?>").change();
		$("#<?php echo $this->campoSeguro('actividad')?>").val('');
		$("#<?php echo $this->campoSeguro('actividad')?>").change();
		$("#<?php echo $this->campoSeguro('geolocalizacion')?>").val('');
		$("#<?php echo $this->campoSeguro('geolocalizacion')?>").change();
		$("#<?php echo $this->campoSeguro('porcentajecons')?>").val('');
		
		for(j = 0; j <= elementos + 1; j++){
			$("#addr"+(j)).remove();
		}
		
		$('#tabla1').append('<tr id="addr0"></tr>');
		$('#addr0').html("<td> </td><td> </td><td> </td><td> </td>");
		$('#tabla1').append('<tr id="addr1"></tr>');
		
		i=1;
		elementos=0;
		inputConsumo = [];
		porcentaje = 0;

	}
	
});
