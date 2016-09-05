<?php
/**
 * Código Correspondiente a las Url de la peticiones Ajax.
 */
// URL base
$url = $this->miConfigurador->getVariableConfiguracion("host");
$url .= $this->miConfigurador->getVariableConfiguracion("site");
$url .= "/index.php?";
// Variables
$variable = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$variable .= "&procesarAjax=true";
$variable .= "&action=index.php";
$variable .= "&bloqueNombre=" . "llamarApi";
$variable .= "&bloqueGrupo=" . "";
$variable .= "&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

// URL definitiva
$urlApi = $url . $cadena;
?>
<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */

 function consultarIdentificadoresSalida(){

	$.ajax({
		url: "<?php echo $urlApi;?>",
		dataType: "json",
		data: { metodo:'obtenerIdentificadoresSalida',proyecto: $("#<?php echo $this->campoSeguro('proyecto');?>").val()},
		success: function(data){


			variable = data;

			$.each(variable , function(indice,valor){

			  $.ajax({
					url: "<?php echo $urlApi;?>",
					dataType: "text",
					data: { metodo:'obtenerDetalleOrden',nombre: variable[indice]},
					success: function(data){



						if($("#<?php echo $this->campoSeguro('ordenes');?>").val()==''){

						$("#<?php echo $this->campoSeguro('ordenes');?>").val(btoa(data));

						}else{

						contenido = $("#<?php echo $this->campoSeguro('ordenes');?>").val();

						$("#<?php echo $this->campoSeguro('ordenes');?>").val(contenido +"@"+btoa(data));


						}






					}

				});


			 $.ajax({
					url: "<?php echo $urlApi;?>",
					dataType: "text",
					data: { metodo:'obtenerMateriales',nombre: variable[indice]},
					success: function(data){



						if($("#<?php echo $this->campoSeguro('elementos');?>").val()==''){

						$("#<?php echo $this->campoSeguro('elementos');?>").val(btoa(data));

						}else{

						contenido = $("#<?php echo $this->campoSeguro('elementos');?>").val();

						$("#<?php echo $this->campoSeguro('elementos');?>").val(contenido +"@"+btoa(data));


						}


						if($("#<?php echo $this->campoSeguro('ordenes');?>").val()!='' && $("#<?php echo $this->campoSeguro('elementos');?>").val()!=''){

   								$("#botones").css('display','block');

								}



					}

				});


			});
		}

	});

};








 function consultarProyectosSalida(){
	$("#<?php echo $this->campoSeguro('proyecto');?>").html('');
	$("<option value=''>Seleccione .....</option>").appendTo("#<?php echo $this->campoSeguro('proyecto');?>");
	$.ajax({
		url: "<?php echo $urlApi;?>",
		dataType: "json",
		data: { metodo:'obtenerProjectosSalida'},
		success: function(data){

			$.each(data , function(indice,valor){
				if(data[ indice ]){
					$("<option value='"+data[ indice ]+"'>"+data[ indice ] + "</option>").appendTo("#<?php echo $this->campoSeguro('proyecto');?>");
				}
			});
		}

	});

};


$(function() {

         	$("#<?php echo $this->campoSeguro('proyecto');?>").ready(function() {

					consultarProyectosSalida();

        	});


        	$("#<?php echo $this->campoSeguro('proyecto');?>").change(function() {

        			$("#botones").css('display','none');
					$("#<?php echo $this->campoSeguro('ordenes');?>").val('');
 					$("#<?php echo $this->campoSeguro('elementos');?>").val('');
					consultarIdentificadoresSalida();


        	});

 });

</script>

