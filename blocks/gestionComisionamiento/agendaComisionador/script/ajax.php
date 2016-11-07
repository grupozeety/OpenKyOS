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
$valor .= "&bloqueNombre=" . $esteBloque ["nombre"];
$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$valor .= "&funcion=consultarComisionador";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL Consultar Proyectos
$urlConsultarComisionador = $url . $cadena;

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
$valor .= "&bloqueNombre=" . $esteBloque ["nombre"];
$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$valor .= "&funcion=consultarUrbanizacion";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

$urlConsultarUrbanizacion = $url . $cadena;

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
$valor .= "&bloqueNombre=" . $esteBloque ["nombre"];
$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$valor .= "&funcion=consultarManzana";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

$urlConsultarManzana = $url . $cadena;

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
$valor .= "&funcion=consultarAgendamiento";
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

	
});

var urbanizacion = "";
var tipo = "";
var manzana = "";
var comisionador = "";

$("#<?php echo $this->campoSeguro('urbanizacion');?>").autocomplete({
	minChars: 1,
	serviceUrl: '<?php echo $urlConsultarUrbanizacion;?>',
	onSelect: function (suggestion) {

	  	$("#<?php echo $this->campoSeguro('id_urbanizacion');?>").val(suggestion.data);
		
		if($("#<?php echo $this->campoSeguro('id_urbanizacion');?>").val()!=''){
			urbanizacion = $("#<?php echo $this->campoSeguro('id_urbanizacion');?>").val();
		}else{
			$("#<?php echo $this->campoSeguro('id_urbanizacion');?>").val('');
			$("#<?php echo $this->campoSeguro('urbanizacion');?>").val('');
			urbanizacion = "";
		}
		
		actualizarTabla();
				
	}

});

$("#<?php echo $this->campoSeguro('tipo_agendamiento');?>").change(function() {
	if($("#<?php echo $this->campoSeguro('tipo_agendamiento');?>").val() != ""){
		tipo = $("#<?php echo $this->campoSeguro('tipo_agendamiento');?>").val();
	}else{
		tipo = "";
	}
	actualizarTabla();
});

$("#<?php echo $this->campoSeguro('manzana');?>").autocomplete({
	minChars: 1,
	serviceUrl: '<?php echo $urlConsultarManzana;?>',
	onSelect: function (suggestion) {

	  	$("#<?php echo $this->campoSeguro('id_manzana');?>").val(suggestion.data);
		
		if($("#<?php echo $this->campoSeguro('id_manzana');?>").val()!=""){
			manzana = $("#<?php echo $this->campoSeguro('id_manzana');?>").val();
		}else{
			$("#<?php echo $this->campoSeguro('id_manzana');?>").val('');
			$("#<?php echo $this->campoSeguro('manzana');?>").val('');
			manzana = "";
		}
		
		actualizarTabla();
	}
});

$("#<?php echo $this->campoSeguro('comisionador');?>").autocomplete({
	minChars: 1,
	serviceUrl: '<?php echo $urlConsultarComisionador;?>',
	onSelect: function (suggestion) {

	  	$("#<?php echo $this->campoSeguro('id_comisionador');?>").val(suggestion.data);
		
		if($("#<?php echo $this->campoSeguro('id_comisionador');?>").val()!=""){
			comisionador = $("#<?php echo $this->campoSeguro('id_comisionador');?>").val();
		}else{
			$("#<?php echo $this->campoSeguro('id_comisionador');?>").val('');
			$("#<?php echo $this->campoSeguro('comisionador');?>").val('');
			comisionador = "";
		}
		
		actualizarTabla();
	}
});

$("#<?php echo $this->campoSeguro('urbanizacion');?>").change(function() {
	if($("#<?php echo $this->campoSeguro('id_urbanizacion');?>").val() == ""){
		urbanizacion = $("#<?php echo $this->campoSeguro('urbanizacion');?>").val();
		actualizarTabla();
	}	    
});


$("#<?php echo $this->campoSeguro('comisionador');?>").change(function() {
	if($("#<?php echo $this->campoSeguro('id_comisionador');?>").val() == ""){
		comisionador = $("#<?php echo $this->campoSeguro('comisionador');?>").val();
		actualizarTabla();
	}	    
});

function actualizarTabla(){

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
	            data: { tipoA: tipo, urban: urbanizacion, manz: manzana, comis: comisionador },
	            dataSrc:"data"   
	        },
	        "columns": [
	           { "data": "id_agendamiento" },
	            { "data": "beneficiario" },
	            { "data": "tipo_agendamiento" },
	            { "data": "estado_agenda" },
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
	    
	    setInterval( function () {
    		table.fnReloadAjax();
		}, 30000 );
	    
	    		
		$('.btn-primary').prop('disabled', true);

	 	$('#seleccionar_todo').change(function(){
	
	       var cells = table.cells( ).nodes();
	       $( cells ).find(':checkbox').prop('checked', $(this).is(':checked'));
	
	       cont = 0;
	
	       var checkbox = $( cells ).find(':checkbox');
	
	       $.each(checkbox , function(indice,valor){
	       		if(valor['checked'] == true){
	        		cont++;
	        	}
	        });
	
	        if(cont > 0 ){
	            $('.btn-primary').prop('disabled', false);
	        }else{
	        	$('.btn-primary').prop('disabled', true);
	        }
	     });
	     
	     $('#example').change(function() {

                cont = 0;

                var cells = table.cells( ).nodes();
                var checkbox = $( cells ).find(':checkbox');

                $.each(checkbox , function(indice,valor){
                        if(valor['checked'] == true){
                                cont++;
                        }
                });

                if(cont > 0){
                        $('.btn-primary').prop('disabled', false);
                }else{
                        $('#seleccionar_todo').attr('checked', false);
                        $('.btn-primary').prop('disabled', true);
                }
  		});
		
	}

actualizarTabla();
		