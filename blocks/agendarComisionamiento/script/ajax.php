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
$valorCodificadoReg = "pagina=agendarComisionamiento";
$variableReg = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificadoReg );
$enlaceReg = $directorioReg . '=' . $variableReg;

?>

$(document).ready(function() {

	var id = "";
	
	$('#example')
			.removeClass( 'display' )
			.addClass('table table-striped table-bordered');
	
			
	$(document).ready(function() {
	
		$('#example thead tr#filterrow th').each( function () {
	        var title = $('#example thead th').eq( $(this).index() ).text();
	        $(this).html( '<input type="text" onclick="stopPropagation(event);" placeholder=" '+title+'" />' );
	    } );
	    
	    function stopPropagation(evt) {
			if (evt.stopPropagation !== undefined) {
				evt.stopPropagation();
			} else {
				evt.cancelBubble = true;
			}
		}
		
	    // Apply the filter
	    $("#example thead input").on( 'keyup change', function () {
	    
	    	if( $(this)[0].id != "seleccionar_todo"){
	    		table
	            .column( $(this).parent().index()+':visible' )
	            .search( this.value )
	            .draw();
	    	}
	         
	    } );
	   
		$('#seleccionar_todo').change(function(){
	    	var cells = table.cells( ).nodes();
	   		$( cells ).find(':checkbox').prop('checked', $(this).is(':checked'));
		});
		
		
		
		 $('#<?php echo $this->campoSeguro("fecha_agendamiento");?>').datetimepicker({
	               format: 'yyyy-mm-dd',
	               language: "es",
	                weekStart: 1,
	                todayBtn:  1,
	                autoclose: 1,
	                todayHighlight: 1,
	                startView: 2,
	                minView: 2,
	                forceParse: 0
	            });
	});
	
	$("#<?php echo $this->campoSeguro('comisionador');?>").change(function() {
	
		$("#<?php echo $this->campoSeguro('nombre_comisionador');?>").val($("#<?php echo $this->campoSeguro('comisionador');?> option:selected").text());
	
	});
	
	if ($("#<?php echo $this->campoSeguro('mensajemodal')?>").length > 0 ){
		$("#myModalMensaje").modal('show');
	}
	
	$(function() {
			$("#regresarConsultar").click(function( event ) {	
		    	$("#myModalMensaje").modal('hide');
			});
	});
		
	$("#<?php echo $this->campoSeguro('tipo_agendamiento');?>").change(function() {

$('#example').DataTable().destroy();
	    var table = $('#example').DataTable( {
	    	"processing": true,
	        "searching": true,
	        "info":false,
	        "paging": false,
	        "scrollY":"300px",
	        "scrollX": true,
	        "scrollCollapse": true,
	        "responsive": true,
	    	"aoColumnDefs": [
	          { 'bSortable': false, 'aTargets': [ 7 ] }
	       	],
	       	"columnDefs": [
	        	{"className": "dt-center", "targets": "_all"}
	        ],
	    	"orderCellsTop": true,
	    	"language": {
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
	        ajax: {
	            url: "<?php echo $urlCargarInformacion?>",
	             data: { valor:$("#<?php echo $this->campoSeguro('tipo_agendamiento')?>").val()},
	            dataSrc:"data"   
	        },
	        "columns": [
	            { "data": "urbanizacion" },
	            { "data": "celda" },
	            { "data": "manzana" },
	            { "data": "bloque" },
	            { "data": "torre" },
	            { "data": "identificacion_beneficiario" },
	            { "data": "nombre_beneficiario" },
	            
	    		{
	              "data":   "id_checkbox",
	               render: function ( data, type, row ) {
	                   if ( type === 'display' ) {
	                       return '<input type="checkbox" name="' + data.id + '" value="' + data.value + '" class="editor-active">';
	                   }
	                   return data;
	               },
	                
	                className: "dt-body-center"
	            }
	        ]
	    } );
	    
	});
	
	
});