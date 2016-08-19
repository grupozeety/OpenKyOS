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
$valor .= "&bloqueNombre=". "llamarApi";
$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
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
var orden;
<!-- Función que se encarga del proceso de codificación de los nombres de los input's de consumo por medio de ajax -->

function ordenTrabajo(){

	$("#<?php echo $this->campoSeguro('ordenTrabajo')?>").html('');
	$("<option value=''>Seleccione .....</option>").appendTo("#<?php echo $this->campoSeguro('ordenTrabajo')?>");
			
	$.ajax({
		url: "<?php echo $urlFuncionCodificarNombre?>",
		dataType: "json",
		data: { metodo:'ordenTrabajo'},
		success: function(data){
		
			data = unique(data);
		
			$.each(data , function(indice,valor){
				$("<option value='"+data[ indice ].item_code+"'>"+data[ indice ].item_code+"</option>").appendTo("#<?php echo $this->campoSeguro('ordenTrabajo')?>");
			});
		}
		
	});
};

function detalleOrden(){

	$.ajax({
		url: "<?php echo $urlFuncionCodificarNombre?>",
		dataType: "json",
		data: { metodo:'obtenerDetalleOrden', nombre: orden},
		success: function(data){
			$("#<?php echo $this->campoSeguro('proyecto')?>").val(data[0].project);
			$("#<?php echo $this->campoSeguro('proyecto')?>").change();
			$("#<?php echo $this->campoSeguro('actividad')?>").val(data[0].descripcion_orden);
			$("#<?php echo $this->campoSeguro('actividad')?>").change();
		}
		
	});
};

function cargarMateriales(){

	$.ajax({
		url: "<?php echo $urlFuncionCodificarNombre?>",
		dataType: "json",
		data: { metodo:'obtenerMateriales', nombre: orden},
		success: function(data){
			
			if(data[0]!=" "){
		
				$.each(data , function(indice,valor){ 
				
					var objeto = {};
					objeto['id'] = data[indice].material;
					objeto['asignado'] = data[indice].qty;
					inputConsumo.push(objeto);
					
					$("#addr0").html('');
					$('#addr'+ i).html("<td>" + i +"</td><td>" + data[indice].item_name + "</td><td>" + data[indice].uom + "</td><td>" + data[indice].qty + "</td><td><input type='number' min='0' max='" +  data[indice].qty + "' class='form-control' id='" + data[indice].material + "' value='0' name='" + data[indice].material + "' required></td>");
					$('#tabla1').append('<tr id="addr'+(i+1)+'"></tr>');
					
					ordenConsumoMaterial(data[indice].name, data[indice].material);
					
					<!-- Se genera dinámicamente el evento change para estar revisando los cambios en los campos -->
					$('#' + data[indice].material).click(function(){
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
		        	
		        	$('#' + data[indice].material).keyup(function(){
						
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
		        	
					i++;
					elementos++;
					
				});
			}
			
		}
	});
};

$("#<?php echo $this->campoSeguro('ordenTrabajo')?>").change(function() {
	
	limpiar();
	
	if($("#<?php echo $this->campoSeguro('ordenTrabajo')?>").val() != ""){
		
		orden = $("#<?php echo $this->campoSeguro('ordenTrabajo')?>").val();
		cargarMateriales();
		detalleOrden();
		
	}
	
});

function limpiar(){

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

function unique(obj){
    var uniques=[];
    var stringify={};
    for(var i=0;i<obj.length;i++){
       var keys=Object.keys(obj[i]);
       keys.sort(function(a,b) {return a-b});
       var str='';
        for(var j=0;j<keys.length;j++){
           str+= JSON.stringify(keys[j]);
           str+= JSON.stringify(obj[i][keys[j]]);
        }
        if(!stringify.hasOwnProperty(str)){
            uniques.push(obj[i]);
            stringify[str]=true;
        }
    }
    return uniques;
}

ordenTrabajo();

if ($("#<?php echo $this->campoSeguro('mensajemodal')?>").length > 0 ){
	$("#myModalMensaje").modal('show');
}

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
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlFuncionObtenerConsumo = $url . $cadena;
?>

function ordenConsumoMaterial(nombre, id){

	$.ajax({
		url: "<?php echo $urlFuncionObtenerConsumo?>",
		dataType: "json",
		data: { valor: nombre},
		success: function(data){
			$("#" + id).val(data[0]['consumo']);
			$("#" + id).change();
			
			$("#<?php echo $this->campoSeguro('porcentajecons')?>").val(data[0]['porcentaje_consumo']);
			$("#<?php echo $this->campoSeguro('porcentajecons')?>").change();
			$("#<?php echo $this->campoSeguro('geolocalizacion')?>").val(data[0]['geolocalizacion']);
			$("#<?php echo $this->campoSeguro('porcentajecons')?>").change();
		}
		
	});
};

ordenConsumoMaterial();