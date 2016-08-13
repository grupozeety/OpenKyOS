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
var cont=0;
var dataGlobal;

<!-- Función que se encarga del proceso de codificación de los nombres de los input's por medio de ajax -->

function codificarNombre(elem, request, response){

	$.ajax({
		url: "<?php echo $urlFuncionCodificarNombre?>",
		dataType: "json",
		data: { valor:i},
		success: function(data){
			
			var material = $("#<?php echo $this->campoSeguro('material')?> option:selected").text();
			var unidad = $("#<?php echo $this->campoSeguro('unidad')?>").val();
			var cantidad = $("#<?php echo $this->campoSeguro('cantidad')?>").val();
			
			$('#addr'+ i).html("<td>" + "<input type='checkbox' id='checkbox"+(i)+"'>" +"</td>" + "<td>" + '<input type="hidden" name="'+ data['material'] + '" value="' + material + '">'  + material + "</td>" + "<td>" + '<input type="hidden" name="'+ data['unidad'] + '" value="' + unidad + '">' + unidad + "</td><td>" + '<input type="hidden" name="'+ data['cantidad'] + '" value="' + cantidad + '">' + cantidad + "</td>");
			$('#tabla1').append('<tr id="addr'+(i+1)+'"></tr>');
		 
		 	$("#<?php echo $this->campoSeguro('elementos')?>").val(i); 
		 
			$("#<?php echo $this->campoSeguro('material')?>").val(''); 
			$("#<?php echo $this->campoSeguro('material')?>").change();
			$("#<?php echo $this->campoSeguro('unidad')?>").val('');
			$("#<?php echo $this->campoSeguro('unidad')?>").change();
			$("#<?php echo $this->campoSeguro('cantidad')?>").val("1");
			
			$('#myModal').modal('hide');
			
			i++;
			elementos++;
 
		}
	});
};

<!-- Función que se encargade eliminar los los materiales que su ítem ha sido seleccionado. -->
$("#remove").click(function(){
    for(j=0; j<i; j++){
    	if( $('#checkbox' + (j)).prop('checked') ) {
    		$("#addr"+(j)).remove();
    		elementos--;
    	}
    }
});

<!-- Se hace el llamado a la función codificarNombre. -->
$("#formmyModalBootstrap").submit(function(e){
    e.preventDefault();
    $("#addr0").html('');
	codificarNombre();
  });
  
//Función que se encarga de verificar que se hayan adicionado materiales.
$(function() {
  $("#asignarMateriales").submit(function(e){
  
  	if(elementos==0 ){
  		alert("No se han agregado materiales");
  	    e.preventDefault();
  	}
  });
});



<?php

$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
$url .= "/index.php?";

// Variables
$componenteLlamarApi = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$componenteLlamarApi .= "&procesarAjax=true";
$componenteLlamarApi .= "&action=index.php";
$componenteLlamarApi .= "&bloqueNombre=" . "llamarApi";
$componenteLlamarApi .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$componenteLlamarApi .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $componenteLlamarApi, $enlace );

// URL definitiva
$urlllamarApi = $url . $cadena;

?>

<!-- función encargada de llamar al componente llamarApi. -->

function consulMateriales(elem, request, response){
	
	$.ajax({
		url: "<?php echo $urlllamarApi?>",
		dataType: "json",
		data: {metodo:'almacenes'},
		success: function(data){
		
			dataGlobal = data;
		
			var material = $("#<?php echo $this->campoSeguro('material')?> option:selected").text();
			var unidad = $("#<?php echo $this->campoSeguro('unidad')?> option:selected").text();
			var cantidad = $("#<?php echo $this->campoSeguro('cantidad')?>").val();
			
			if(data[0]!=" "){
				$("#<?php echo $this->campoSeguro('material')?>").html('');
				$("<option value=''>Seleccione .....</option>").appendTo("#<?php echo $this->campoSeguro('material')?>");
				$.each(data , function(indice,valor){
					$("<option value='"+data[ indice ].name+"'>"+data[ indice ].name+"</option>").appendTo("#<?php echo $this->campoSeguro('material')?>");
				});
			}
 
		}
	});
};


function consulProyectos(elem, request, response){
	
	$.ajax({
		url: "<?php echo $urlllamarApi?>",
		dataType: "json",
		data: {metodo:'proyectos'},
		success: function(data){
		
			var $tree =$('#proyectos_tree').treeview({data: data});

			$('#proyectos_tree').treeview('collapseAll', { silent: true });

			$('#proyectos_tree').on('nodeSelected', function(event, node) {
    			$("#<?php echo $this->campoSeguro('proyecto')?>").val(node[0].name);
    			$("#<?php echo $this->campoSeguro('proyecto')?>").change();
			});
		}
	});
};

function consulActividades(elem, request, response){
	
	$.ajax({
		url: "<?php echo $urlllamarApi?>",
		dataType: "json",
		data: {metodo:'actividades'},
		success: function(data){
		
			var $tree =$('#actividades_tree').treeview({data: data});

			$('#actividades_tree').treeview('collapseAll', { silent: true });

			$('#actividades_tree').on('nodeSelected', function(event, node) {
    			$("#<?php echo $this->campoSeguro('actividad')?>").val(node[0].subject);
    			$("#<?php echo $this->campoSeguro('actividad')?>").change();
			});
		}
	});
};

<!-- Al iniciarce el formulario se llama la función consultarMateriales, que es la función encargada de llamar al componente llamarApi. -->
 
consulMateriales();

consulProyectos();

consulActividades();


<!-- Función que establece las unidades según sea el material seleccionado -->

$("#<?php echo $this->campoSeguro('material')?>").change(function() {
	
	if($("#<?php echo $this->campoSeguro('material')?>").val() == ""){
		$("#<?php echo $this->campoSeguro('unidad')?>").val('');
	}else{
		$.each(dataGlobal , function(indice,valor){
			if(dataGlobal[indice].name == $("#<?php echo $this->campoSeguro('material')?>").val()){
				$("#<?php echo $this->campoSeguro('unidad')?>").val(dataGlobal[indice].stock_uom);
			}
		});
	}
});


