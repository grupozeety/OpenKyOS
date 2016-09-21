<?php
/**
 *
 * Los datos del bloque se encuentran en el arreglo $esteBloque.
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
$valor .= "&bloqueGrupo=" . $esteBloque["grupo"];
$valor .= "&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valor, $enlace);

// URL definitiva
$urlFuncionCodificarNombre = $url . $cadena;
?>

<?php
/**
 *
 * Los datos del bloque se encuentran en el arreglo $esteBloque.
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
$valor .= "&bloqueGrupo=" . $esteBloque["grupo"];
$valor .= "&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valor, $enlace);

// URL definitiva
$urlObtenerProyectos = $url . $cadena;
?>

var i=1;
var elementos=0;
var inputConsumo = [];
var porcentaje = 0;
var orden = [];
var data_orden = [];


<!-- Función que se encarga del proceso de codificación de los nombres de los input's de consumo por medio de ajax -->

function proyecto(){

	$("#<?php echo $this->campoSeguro('proyecto');?>").html('');
	$("<option value=''>Seleccione .....</option>").appendTo("#<?php echo $this->campoSeguro('proyecto');?>");

	$.ajax({
		url: "<?php echo $urlObtenerProyectos;?>",
		dataType: "json",
		data: { metodo:'obtenerProyecto'},
		success: function(data){
			data = unique(data);
			$.each(data , function(indice,valor){
				if(data[ indice ].project != null){
					$("<option value='"+data[ indice ].project + "'>"+data[ indice ].project + "</option>").appendTo("#<?php echo $this->campoSeguro('proyecto');?>");
				}
			});
		}

	});
};

proyecto();

<!-- Función que se encarga del proceso de codificación de los nombres de los input's de consumo por medio de ajax -->

function ordenTrabajo(proyecto){

	$("#<?php echo $this->campoSeguro('ordenTrabajo');?>").html('');
	
	$("<option value=''>Seleccione .....</option>").appendTo("#<?php echo $this->campoSeguro('ordenTrabajo');?>");

	$.ajax({
		url: "<?php echo $urlFuncionCodificarNombre;?>",
		dataType: "json",
		data: { metodo:'ordenTrabajoModificada', nombre: proyecto},
		success: function(data){
			
			$.each(data , function(indice,valor){
				
				orden.push({"name":data[ indice ].name, "id_orden_trabajo":data[ indice ].id_orden_trabajo});
					
			});
			

			$.each(data , function(indice,valor){
			
				delete data[ indice ].name;
				
			});
			
			data = unique(data);
			
			$.each(data , function(indice,valor){

				
				if(data[ indice ].purpose == "Material Issue"){
					$("<option value='"+data[ indice ].id_orden_trabajo +"'>"+data[ indice ].descripcion_orden + "</option>").appendTo("#<?php echo $this->campoSeguro('ordenTrabajo');?>");
				}
				
			});
			
		}

	});
};

function detalleOrden(nombre){

	$.ajax({
		url: "<?php echo $urlFuncionCodificarNombre;?>",
		dataType: "json",
		data: { metodo:'obtenerDetalleOrden', nombre: nombre},
		success: function(data){
			

		}

	});
};

function cargarMateriales(nombre_ord){

	$.ajax({
		url: "<?php echo $urlFuncionCodificarNombre;?>",
		dataType: "json",
		data: { metodo:'obtenerMaterialesModificado', nombre: nombre_ord},
		success: function(data){

			if(data[0]!=" "){

<!-- 				detalleOrden(data[0].parent); -->

				$.each(data , function(indice,valor){

					var objeto = {};
					objeto['id'] = data[indice].material;
					objeto['asignado'] = data[indice].qty;
					inputConsumo.push(objeto);

					$("#addr0").html('');
					$('#addr'+ i).html("<td>" + i +"</td><td>" + data[indice].parent + "</td><td>" + data[indice].item_name + "</td><td>" + data[indice].uom + "</td><td>" + data[indice].qty + "</td><td><input type='number' min='0' max='" +  data[indice].qty + "' class='form-control' id='" + data[indice].material + "' value='0' name='" + data[indice].material + "' required></td>");
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

						$("#<?php echo $this->campoSeguro('porcentajecons');?>").val(porcentaje);
						$("#<?php echo $this->campoSeguro('porcentajecons');?>").change();

		        	});

		        	$('#' + data[indice].material).keyup(function(){

						porcentaje = 0;
						cont = 0;
		        		$.each(inputConsumo , function(indice,valor){
							porcentaje = porcentaje + ( $('#' + inputConsumo[indice]['id']).val() *100 ) / inputConsumo[indice]['asignado'];
							cont++;
						});

						porcentaje = porcentaje / cont;

						$("#<?php echo $this->campoSeguro('porcentajecons');?>").val(porcentaje);
						$("#<?php echo $this->campoSeguro('porcentajecons');?>").change();

		        	});

					i++;
					elementos++;

				});
			}

		}
	});
};

$("#<?php echo $this->campoSeguro('ordenTrabajo');?>").change(function() {

	limpiar();

	if($("#<?php echo $this->campoSeguro('ordenTrabajo');?>").val() != ""){
	
		$("#<?php echo $this->campoSeguro('ordenTrabajoDesc');?>").val($("#<?php echo $this->campoSeguro('ordenTrabajo');?> option:selected").text());
		$("#<?php echo $this->campoSeguro('ordenTrabajoDesc');?>").change();
		
		var nombre_orden = [];
		  
		$.each(orden , function(indice,valor){
			
			if(orden[indice].id_orden_trabajo == $("#<?php echo $this->campoSeguro('ordenTrabajo');?>").val()){
				nombre_orden.push(orden[ indice ].name);
			}
			
		});
		
		nombre_orden = JSON.stringify(nombre_orden);	
	
		cargarMateriales(nombre_orden);
		
	}

});

function limpiar(){

		$("#<?php echo $this->campoSeguro('ordenTrabajoDesc');?>").val('');
		$("#<?php echo $this->campoSeguro('ordenTrabajoDesc');?>").change();
		$("#<?php echo $this->campoSeguro('geolocalizacion');?>").val('');
		$("#<?php echo $this->campoSeguro('geolocalizacion');?>").change();
		$("#<?php echo $this->campoSeguro('porcentajecons');?>").val('');
		$("#<?php echo $this->campoSeguro('porcentajecons');?>").change();

		for(j = 0; j <= elementos + 1; j++){
			$("#addr"+(j)).remove();
		}

		$('#tabla1').append('<tr id="addr0"></tr>');
		$('#addr0').html("<td> </td><td> </td><td> </td><td> </td><td> </td>");
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

if ($("#<?php echo $this->campoSeguro('mensajemodal');?>").length > 0 ){
	$("#myModalMensaje").modal('show');
}

<?php
                     /**
 *
 * Los datos del bloque se encuentran en el arreglo $esteBloque.
 */

// URL base
$url = $this->miConfigurador->getVariableConfiguracion("host");
$url .= $this->miConfigurador->getVariableConfiguracion("site");
$url .= "/index.php?";
// Variables
$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$valor .= "&procesarAjax=true";
$valor .= "&action=index.php";
$valor .= "&bloqueNombre=" . $esteBloque["nombre"];
$valor .= "&bloqueGrupo=" . $esteBloque["grupo"];
$valor .= "&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valor, $enlace);

// URL definitiva
$urlFuncionObtenerConsumo = $url . $cadena;
?>

function ordenConsumoMaterial(nombre, id){

	$.ajax({
		url: "<?php echo $urlFuncionObtenerConsumo;?>",
		dataType: "json",
		data: { valor: nombre},
		success: function(data){

			if(typeof data[0] != "undefined"){

				$("#" + id).val(data[0]['consumo']);
				$("#" + id).change();

				$("#<?php echo $this->campoSeguro('porcentajecons');?>").val(data[0]['porcentaje_consumo']);
				$("#<?php echo $this->campoSeguro('porcentajecons');?>").change();
				$("#<?php echo $this->campoSeguro('geolocalizacion');?>").val(data[0]['geolocalizacion']);
				$("#<?php echo $this->campoSeguro('geolocalizacion');?>").change();
			}

		}

	});
};

ordenConsumoMaterial();

$("#<?php echo $this->campoSeguro('proyecto');?>").change(function() {

	limpiar();
	
	orden = [];
	data_orden = [];

	if($("#<?php echo $this->campoSeguro('proyecto');?>").val() != ""){

		ordenTrabajo($("#<?php echo $this->campoSeguro('proyecto');?>").val());
		
	}


});
