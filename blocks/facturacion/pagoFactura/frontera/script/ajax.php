<?php
/**
 *
 * Los datos del bloque se encuentran en el arreglo $esteBloque.
 */

// URL base
$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
$url .= "/index.php?";

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar .= "&funcion=consultaBeneficiarios";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );

// URL Consultar Proyectos
$urlConsultarBeneficiarios = $url . $cadena;

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
$valor .= "&funcion=consultarFacturas";
$valor .= "&id_ben=" . $_REQUEST['id_beneficiario'] ;
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
$valor .= "&funcion=inhabilitarMetodo";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlEliminarMetodo = $url . $cadena;
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
$valor .= "&funcion=redireccionarPago";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlGenerarEnlace2 = $url . $cadena;
?>



<?php

$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$valorCodificado = "pagina=pagoFactura&opcion=agregar";
$valorCodificado .= "&id=";



$directorio2 = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorio2 .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorio2 .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$valorCodificado2 = "pagina=pagoFactura&opcion=pagar";
$valorCodificado2 .= "&id=";
?>

  $("#<?php echo $this->campoSeguro('beneficiario');?>").autocomplete({
 	minChars: 3,
 	serviceUrl: '<?php echo $urlConsultarBeneficiarios;?>',
 	onSelect: function (suggestion) {

	     	$("#<?php echo $this->campoSeguro('id_beneficiario');?>").val(suggestion.data);
 	    }
 });
 
$(document).ready(function() {
    $('#example2').DataTable();
} );

$(document).ready(function() {

	var id = "";
	
	$('#example')
			.removeClass( 'display' )
			.addClass('table table-striped table-bordered');
	
			
	$(document).ready(function() {
	    var table = $('#example').DataTable( {
	    	     
	    language: {
        
    "sProcessing":     "Procesando...",
    "sLengthMenu":     "Mostrar _MENU_ registros",
    "sZeroRecords":    "No se encontraron resultados",
    "sEmptyTable":     "Ningún dato disponible en esta tabla",
    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
    "sInfoPostFix":    "",
    "sSearch":         "Buscar:",
    "sUrl":            "",
    "sInfoThousands":  ",",
    "sLoadingRecords": "Cargando...",
    "oPaginate": {
        "sFirst":    "Primero",
        "sLast":     "Último",
        "sNext":     "Siguiente",
        "sPrevious": "Anterior"
    },
    "oAria": {
        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
    }

    	},
			"sDom": "<'dt-toolbar'<'col-xs-4'l><'col-xs-4'<'toolbar'>><'col-xs-4'f>>"+
			"t"+"<'dt-toolbar-footer'<'col-xs-6'i><'col-xs-6'p>>",
	        processing: true,
	        searching: true,
	        ajax: {
	            url: "<?php echo $urlCargarInformacion?>",
	            dataSrc:"data"   
	        },
	        "columns": [
	            { "data": "id_factura" },
	            { "data": "id_ciclo" },
                { "data": "beneficiario" },
                { "data": "total" },
	            { "data": "estado_factura" },
	            {
	      			"data": null,
	      			"defaultContent": "<span class='glyphicon glyphicon-search optionEdit'></span> <span class='glyphicon glyphicon-usd optionPagar'></span>"
	    		}
	        ]
	    } );
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
//		$("div.toolbar").html('<button type="button" id="agregarCabecera" class="btn btn-primary">Agregar Cabecera</button>'); 
		    
	    $('#example tbody').on( 'click', '.optionPagar', function () {
	    	var data = table.row( $(this).parents('tr') ).data();
	        id = data['id_factura'];
	         generarEnlace2();
	    } );
	    
	    $('#example tbody').on( 'click', '.optionEdit', function () {
	    	var data = table.row( $(this).parents('tr') ).data();
	        id = data['id_factura'];
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
		
		function eliminarCabecera(){
	
			$.ajax({
				url: "<?php echo $urlEliminarMetodo;?>",
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
		
			
		function generarEnlace2(){
	
			$.ajax({
				url: "<?php echo $urlGenerarEnlace2;?>",
				dataType: "json",
				data: { valor: "<?php echo $valorCodificado2;?>",
						directorio: "<?php echo $directorio2;?>",
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
	
});
