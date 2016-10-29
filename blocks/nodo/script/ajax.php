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
$valor .= "&funcion=consultarNodo";
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
$valor .= "&funcion=inhabilitarNodo";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlEliminarNodo = $url . $cadena;
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
$valorCodificado = "pagina=nodo&opcion=agregar";
$valorCodificado .= "&id=";

?>

<?php

$directorioReg = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$valorCodificadoReg = "pagina=nodo&opcion=agregar";
$variableReg = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificadoReg );
$enlaceReg = $directorioReg . '=' . $variableReg;

?>

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
	            { "data": "codigo_nodo" },
	            { "data": "tipo_tecnologia" },
	            { "data": "codigo_cabecera" },
	            { "data": "urbanizacion" },
	            {
	      			"data": null,
	      			"defaultContent": "<span class='glyphicon glyphicon-trash optionRemove'></span><span class='glyphicon glyphicon-pencil optionEdit'></span>"
	    		}
	        ]
	    } );
	    
		$("div.toolbar").html('<button type="button" id="agregarNodo" class="btn btn-primary">Agregar Celda o Nodo EOC</button>'); 
		    
	    $('#example tbody').on( 'click', '.optionRemove', function () {
	    	var data = table.row( $(this).parents('tr') ).data();
	        id = data['codigo_nodo'];
	        $("#myModal").modal("show");
	    } );
	    
	    $('#example tbody').on( 'click', '.optionEdit', function () {
	    	var data = table.row( $(this).parents('tr') ).data();
	        id = data['codigo_nodo'];
	        generarEnlace();
	    } );
	    
	    $(function() {
			$("#botonCancelarElim").click(function( event ) {	
				$("#myModal").modal("hide");
			});
		}); 
		
		$(function() {
			$("#botonAceptarElim").click(function( event ) {	
				eliminarNodo();
				$("#myModal").modal("hide");
			});
		});
		
		$(function() {
			$("#agregarNodo").click(function( event ) {	
		    	location.href = "<?php echo $enlaceReg;?>";
			});
		});
		
		function eliminarNodo(){
	
			$.ajax({
				url: "<?php echo $urlEliminarNodo;?>",
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
	
});

<?php

/**
 * Código Correspondiente a las Url de la peticiones Ajax.
 */

// URL base
$url = $this->miConfigurador->getVariableConfiguracion("host");
$url .= $this->miConfigurador->getVariableConfiguracion("site");
$url .= "/index.php?";

// Variables para Consultar Proyectos
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultarProyectos";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarProyectos = $url . $cadena;

?>

$(document).ready(function() {

	var urbanizacion;
	
	function urbanizacion(){
	
		$("#<?php echo $this->campoSeguro('urbanizacion')?>").html('');
		$("<option value=''>Seleccione .....</option>").appendTo("#<?php echo $this->campoSeguro('urbanizacion')?>");
				
		$.ajax({
			url: "<?php echo $urlConsultarProyectos; ?>",
			dataType: "json",
			data: { metodo:''},
			success: function(data){
				
				urbanizacion = data;
				
				$.each(data , function(indice,valor){
					$("<option value='"+data[ indice ].id+"'>" + data[ indice ].urbanizacion + "</option>").appendTo("#<?php echo $this->campoSeguro('urbanizacion')?>");
				});
				
				$("#<?php echo $this->campoSeguro('urbanizacion')?>").val($("#<?php echo $this->campoSeguro('select_urbanizacion')?>").val()).change();
			}
			
		});
	};
	
	$("#<?php echo $this->campoSeguro('urbanizacion');?>").change(function() {
	
		$("#<?php echo $this->campoSeguro('id_urbanizacion');?>").val($("#<?php echo $this->campoSeguro('urbanizacion');?> option:selected").text());
	
	});
	
	$("#<?php echo $this->campoSeguro('urbanizacion');?>").change(function() {
	
		if($("#<?php echo $this->campoSeguro('urbanizacion');?>").val() != ""){
		
			$.each(urbanizacion , function(indice,valor){
				
				if(urbanizacion[indice].id == $("#<?php echo $this->campoSeguro('urbanizacion');?>").val()){
					$("#<?php echo $this->campoSeguro('departamento');?>").val(urbanizacion[indice].departamento);
					$("#<?php echo $this->campoSeguro('municipio');?>").val(urbanizacion[indice].municipio);
				}
				
			});
			
		}
	
	});
	 
	urbanizacion();
	 
	if ($("#<?php echo $this->campoSeguro('mensajemodal')?>").length > 0 ){
		$("#myModalMensaje").modal('show');
	}

});

$(document).ready(function() {
	
	 if ($("#<?php echo $this->campoSeguro('mensajemodal')?>").length > 0 ){
		$("#myModalMensaje").modal('show');
	}

});

<?php

$directorioReg = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$valorCodificadoReg = "pagina=nodo";
$variableReg = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificadoReg );
$enlaceReg = $directorioReg . '=' . $variableReg;

?>

$(document).ready(function() {

	$(function() {
			$("#regresarConsultar").click(function( event ) {	
		    	location.href = "<?php echo $enlaceReg;?>";
			});
	});
	
});