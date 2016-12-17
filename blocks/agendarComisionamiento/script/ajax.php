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
$valor .= "&funcion=consultarBeneficiarios";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlCargarInformacion = $url . $cadena;

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
$valor .= "&funcion=consultarBeneficiarios";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

$urlConsultarBeneficiario = $url . $cadena;

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
$valor .= "&bloqueNombre=" . $esteBloque ["nombre"];
$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$valor .= "&funcion=consultarBloque";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

$urlConsultarBloque = $url . $cadena;

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
$valor .= "&funcion=consultarTorre";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

$urlConsultarTorre = $url . $cadena;

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
	var urbanizacion = "";
	var tipo = "";
	var bloque = "";
	var manzana = "";
	var torre = "";
	var agendamiento = "";
	var beneficiario = "";

	$("#tipo_tecnologia_div").hide();
	$("#<?php echo $this->campoSeguro('tipo_tecnologia');?>").attr("required", false);
	
	$("#<?php echo $this->campoSeguro('tipo_tecnologia');?>").attr("required", false);
				 $("#<?php echo $this->campoSeguro('tipo_tecnologia');?>").css("visibility", "hidden");
	
	
	$("#<?php echo $this->campoSeguro('tipo_agendamiento');?>").change(function() {
		if($("#<?php echo $this->campoSeguro('tipo_agendamiento');?>").val() == 1){
			$("#<?php echo $this->campoSeguro('tipo_tecnologia');?>").attr("required", true);
			$("#tipo_tecnologia_div").show();
		}else if($("#<?php echo $this->campoSeguro('tipo_agendamiento');?>").val() == 1){
			$("#<?php echo $this->campoSeguro('tipo_tecnologia');?>").attr("required", false);
			$("#tipo_tecnologia_div").hide();
		}else{
			$("#<?php echo $this->campoSeguro('tipo_tecnologia');?>").attr("required", false);
			$("#tipo_tecnologia_div").hide();
		}    
		
		agendamiento = $("#<?php echo $this->campoSeguro('tipo_agendamiento');?>").val();
		actualizarTabla();
	});
	
	$('#example')
			.removeClass( 'display' )
			.addClass('table table-striped table-bordered');
			
	$(document).ready(function() {
	
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
	
$("#<?php echo $this->campoSeguro('urbanizacion');?>").autocomplete({
	minChars: 1,
	serviceUrl: '<?php echo $urlConsultarUrbanizacion;?>',
	onSelect: function (suggestion) {
		urbanizacion = suggestion.data;
		actualizarTabla();
	}
});

$("#<?php echo $this->campoSeguro('beneficiario');?>").autocomplete({
	minChars: 1,
	serviceUrl: '<?php echo $urlConsultarBeneficiario;?>',
	onSelect: function (suggestion) {
		beneficiario = suggestion.data;
		actualizarTabla();
	}
});

$("#<?php echo $this->campoSeguro('tipo_vivienda');?>").change(function() {
	if($("#<?php echo $this->campoSeguro('tipo_vivienda');?>").val() != ""){
		tipo = $("#<?php echo $this->campoSeguro('tipo_vivienda');?>").val();
	}else{
		tipo = "";
	}
	actualizarTabla();
});

$("#<?php echo $this->campoSeguro('manzana');?>").autocomplete({
	minChars: 1,
	serviceUrl: '<?php echo $urlConsultarManzana;?>',
	onSelect: function (suggestion) {
		manzana = suggestion.data;
		actualizarTabla();
	}
});

$("#<?php echo $this->campoSeguro('bloque');?>").autocomplete({
	minChars: 1,
	serviceUrl: '<?php echo $urlConsultarBloque;?>',
	onSelect: function (suggestion) {
		bloque = suggestion.data;
		actualizarTabla();
	}
});

$("#<?php echo $this->campoSeguro('torre');?>").autocomplete({
	minChars: 1,
	serviceUrl: '<?php echo $urlConsultarTorre;?>',
	onSelect: function (suggestion) {
	  	torre = suggestion.data;
		actualizarTabla();
	}
});

$("#<?php echo $this->campoSeguro('manzana');?>").change(function() {
	manzana = $("#<?php echo $this->campoSeguro('manzana');?>").val();
	actualizarTabla();
});

$("#<?php echo $this->campoSeguro('bloque');?>").change(function() {
	bloque = $("#<?php echo $this->campoSeguro('bloque');?>").val();
	actualizarTabla();
});

$("#<?php echo $this->campoSeguro('torre');?>").change(function() {
	torre = $("#<?php echo $this->campoSeguro('torre');?>").val();
	actualizarTabla();
});

$("#<?php echo $this->campoSeguro('urbanizacion');?>").change(function() {
	urbanizacion = $("#<?php echo $this->campoSeguro('urbanizacion');?>").val();
	actualizarTabla();
});


$("#<?php echo $this->campoSeguro('beneficiario');?>").change(function() {
	beneficiario = $("#<?php echo $this->campoSeguro('beneficiario');?>").val();
	actualizarTabla();
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
	             data: { tipoV: tipo, urban: urbanizacion, man: manzana, bloq: bloque, torre: torre, agen: agendamiento, ben:beneficiario},
	            dataSrc:"data"   
	        },
	        "columns": [
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

});
