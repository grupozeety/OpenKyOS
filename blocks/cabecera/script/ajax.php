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
$valor .= "&funcion=inhabilitarCabecera";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlEliminarCabecera = $url . $cadena;
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
$valorCodificado = "pagina=cabecera&opcion=agregar";
$valorCodificado .= "&id=";

?>

<?php

$directorioReg = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$valorCodificadoReg = "pagina=cabecera&opcion=agregar";
$variableReg = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificadoReg );
$enlaceReg = $directorioReg . '=' . $variableReg;

?>

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
            { "data": "codigo_cabecera" },
            { "data": "descripcion" },
            { "data": "departamento" },
            { "data": "municipio" },
            { "data": "urbanizacion" },
            {
      			"data": null,
      			"defaultContent": "<span class='glyphicon glyphicon-trash optionRemove'></span><span class='glyphicon glyphicon-pencil optionEdit'></span>"
    		}
        ]
    } );
    
	$("div.toolbar").html('<button type="button" id="agregarCabecera" class="btn btn-primary">Agregar Cabecera</button>'); 
	    
    $('#example tbody').on( 'click', '.optionRemove', function () {
    	var data = table.row( $(this).parents('tr') ).data();
        id = data['codigo_cabecera'];
        $("#myModal").modal("show");
    } );
    
    $('#example tbody').on( 'click', '.optionEdit', function () {
    	var data = table.row( $(this).parents('tr') ).data();
        id = data['codigo_cabecera'];
        generarEnlace();
    } );
    
    $(function() {
		$("#botonCancelarElim").click(function( event ) {	
			$("#myModal").modal("hide");
		});
	}); 
	
	$(function() {
		$("#botonAceptarElim").click(function( event ) {	
			eliminarCabecera();
			$("#myModal").modal("hide");
		});
	});
	
	$(function() {
		$("#agregarCabecera").click(function( event ) {	
	    	location.href = "<?php echo $enlaceReg;?>";
		});
	});
	
	function eliminarCabecera(){

		$.ajax({
			url: "<?php echo $urlEliminarCabecera;?>",
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

function ordenTrabajo(){

	$("#<?php echo $this->campoSeguro('urbanizacion')?>").html('');
	$("<option value=''>Seleccione .....</option>").appendTo("#<?php echo $this->campoSeguro('urbanizacion')?>");
			
	$.ajax({
		url: "<?php echo $urlFuncionCodificarNombre?>",
		dataType: "json",
		data: { metodo:'urbanizaciones'},
		success: function(data){
	
			$.each(data , function(indice,valor){
				$("<option value='"+data[ indice ].custom+"'>" + data[ indice ].text + "</option>").appendTo("#<?php echo $this->campoSeguro('urbanizacion')?>");
			});
			
			$("#<?php echo $this->campoSeguro('urbanizacion')?>").val($("#<?php echo $this->campoSeguro('select_urbanizacion')?>").val()).change();
		}
		
	});
};

$("#<?php echo $this->campoSeguro('urbanizacion');?>").change(function() {

	$("#<?php echo $this->campoSeguro('id_urbanizacion');?>").val($("#<?php echo $this->campoSeguro('urbanizacion');?> option:selected").text());

});

 ordenTrabajo();
 
 if ($("#<?php echo $this->campoSeguro('mensajemodal')?>").length > 0 ){
	$("#myModalMensaje").modal('show');
}

<?php

$directorioReg = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$valorCodificadoReg = "pagina=cabecera";
$variableReg = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificadoReg );
$enlaceReg = $directorioReg . '=' . $variableReg;

?>

$(function() {
		$("#regresarConsultar").click(function( event ) {	
	    	location.href = "<?php echo $enlaceReg;?>";
		});
});