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
$valor .= "&funcion=consultarBeneficiarios";
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
$valor .= "&funcion=inhabilitarBeneficiario";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlEliminarBeneficiario = $url . $cadena;
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
$valorCodificado = "pagina=paginaPrincipal";
$valorCodificado .= "&id=";

?>

var id = "";

$('#example')
		.removeClass( 'display' )
		.addClass('table table-striped table-bordered');

		
$(document).ready(function() {
    var table = $('#example').DataTable( {
        processing: true,
        searching: true,
        ajax: {
            url: "<?php echo $urlCargarInformacion?>",
            dataSrc:"data"   
        },
        "columns": [
            { "data": "urbanizacion" },
            { "data": "nombre" },
            { "data": "identificacion" },
            { "data": "tipo" },
            {
      			"data": null,
      			"defaultContent": "<span class='glyphicon glyphicon-trash optionRemove'></span><span class='glyphicon glyphicon-pencil optionEdit'></span>"
    		}
            
        ]
    } );
    
    $('#example tbody').on( 'click', '.optionRemove', function () {
    	var data = table.row( $(this).parents('tr') ).data();
        id = data['id'];
        $("#myModal").modal("show");
    } );
    
    $('#example tbody').on( 'click', '.optionEdit', function () {
    	var data = table.row( $(this).parents('tr') ).data();
        id = data['id'];
        generarEnlace();
    } );
    
    $(function() {
		$("#botonCancelarElim").click(function( event ) {	
			$("#myModal").modal("hide");
		});
	}); 
	
	$(function() {
		$("#botonAceptarElim").click(function( event ) {	
			eliminarBeneficiario();
			$("#myModal").modal("hide");
		});
	});
	
	function eliminarBeneficiario(){

		$.ajax({
			url: "<?php echo $urlEliminarBeneficiario;?>",
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


			
 