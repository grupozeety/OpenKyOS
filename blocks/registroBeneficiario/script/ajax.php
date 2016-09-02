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
$valor .= "&funcion=codificar";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlCodificacionCampos = $url . $cadena;
?>

var id = 1;
	
$(function() {
	$("#botonAgregar").click(function( event ) {	
	
		if(id > 0){
			codificacionCampos(id);
		}else if(id==0){
			$('#div_1').show();
		}
		$('#botonEliminar').show();
		id++;	
			
	});
}); 

$(function() {
	$("#botonEliminar").click(function( event ) {
		if(id > 1){
			$('#hogar fieldset').remove('#div_' + id);
			id--;
		}else if(id == 1){
			$('#div_' + id).hide();
			$('#botonEliminar').hide();

			id--;
		}
			
	});
}); 	
		
		
function codificacionCampos(id){

	$.ajax({
		url: "<?php echo $urlCodificacionCampos?>",
		dataType: "json",
		data: { valor: id},
		success: function(data){
			
			
				<!--Se remueven los select2 de los select para realizar una clonación efectiva del campo -->
				$($( '#div_' + id + ' :input')[2]).select2("destroy");
				$($( '#div_' + id + ' :input')[3]).select2("destroy");
				$($( '#div_' + id + ' :input')[5]).select2("destroy");
				$($( '#div_' + id + ' :input')[9]).select2("destroy");
				$($( '#div_' + id + ' :input')[10]).select2("destroy");
				
				<!--Se clona el div -->
				$newClone = $('#div_' + id).clone(true);
				$newClone.attr("id",'div_' + (id+1));
				$newClone.insertAfter($('#div_'+id));
				
				$('#div_'+id + ' img').remove( "#botonAgregar" );
				
				<!--Se restablecen los select2 -->
				$($( '#div_' + id + ' :input')[2]).select2({width:'100%'});
				$($( '#div_' + id + ' :input')[3]).select2({width:'100%'});
				$($( '#div_' + id + ' :input')[5]).select2({width:'100%'});
				$($( '#div_' + id + ' :input')[9]).select2({width:'100%'});
				$($( '#div_' + id + ' :input')[10]).select2({width:'100%'});
				
				<!--Se agregan los select2 a los campos clonados-->
				$($( '#div_' + (id + 1) + ' :input')[2]).select2({width:'100%'});
				$($( '#div_' + (id + 1) + ' :input')[3]).select2({width:'100%'});
				$($( '#div_' + (id + 1) + ' :input')[5]).select2({width:'100%'});
				$($( '#div_' + (id + 1) + ' :input')[9]).select2({width:'100%'});
				$($( '#div_' + (id + 1) + ' :input')[10]).select2({width:'100%'});
				
				$($( '#div_' + (id + 1) + ' :input')[0]).attr('id', data.identificacion).val("");
				$($( '#div_' + (id + 1) + ' :input')[0]).attr('name', data.identificacion);
				
				$($( '#div_' + (id + 1) + ' :input')[1]).attr('id', data.nombre).val("");;
				$($( '#div_' + (id + 1) + ' :input')[1]).attr('name', data.nombre);
				
				$($( '#div_' + (id + 1) + ' :input')[2]).attr('id', data.parentesco).val("");;
				$($( '#div_' + (id + 1) + ' :input')[3]).attr('name', data.parentesco);
				
				$($( '#div_' + (id + 1) + ' :input')[3]).attr('id', data.genero).val("");;
				$($( '#div_' + (id + 1) + ' :input')[3]).attr('name', data.genero);
				
				$($( '#div_' + (id + 1) + ' :input')[4]).attr('id', data.edad).val("");;
				$($( '#div_' + (id + 1) + ' :input')[4]).attr('name', data.edad);
				
				$($( '#div_' + (id + 1) + ' :input')[5]).attr('id', data.nivel_estudio).val("");;
				$($( '#div_' + (id + 1) + ' :input')[5]).attr('name', data.nivel_estudio);
				
				$($( '#div_' + (id + 1) + ' :input')[6]).attr('id', data.correo).val("");;
				$($( '#div_' + (id + 1) + ' :input')[6]).attr('name', data.correo);
				
				$($( '#div_' + (id + 1) + ' :input')[7]).attr('id', data.grado);
				$($( '#div_' + (id + 1) + ' :input')[7]).attr('name', data.grado);
				
				$($( '#div_' + (id + 1) + ' :input')[8]).attr('id', data.institucion_educativa).val("");;
				$($( '#div_' + (id + 1) + ' :input')[8]).attr('name', data.institucion_educativa);
				
				$($( '#div_' + (id + 1) + ' :input')[9]).attr('id', data.pertenencia_etnica).val("");;
				$($( '#div_' + (id + 1) + ' :input')[9]).attr('name', data.pertenencia_etnica);
				
				$($( '#div_' + (id + 1) + ' :input')[10]).attr('id', data.ocupacion).val("");;
				$($( '#div_' + (id + 1) + ' :input')[10]).attr('name', data.ocupacion);
				
			
		}
		
	});
};

			
 
function delRow() {
// Funcion que destruye el elemento actual una vez echo el click
$(this).parent('div').remove();
 
}
