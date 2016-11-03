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
$valor .= "&funcion=consultarBeneficiarios";
$valor .= "&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valor, $enlace);

// URL definitiva
$urlCargarInformacion = $url . $cadena;

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultaBeneficiarios";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarBeneficiarios = $url . $cadena;

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
$valor .= "&bloqueNombre=" . $esteBloque["nombre"];
$valor .= "&bloqueGrupo=" . $esteBloque["grupo"];
$valor .= "&funcion=inhabilitarBeneficiario";
$valor .= "&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valor, $enlace);

// URL definitiva
$urlEliminarBeneficiario = $url . $cadena;
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
$valor .= "&bloqueNombre=" . $esteBloque["nombre"];
$valor .= "&bloqueGrupo=" . $esteBloque["grupo"];
$valor .= "&funcion=redireccionar";
$valor .= "&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valor, $enlace);

// URL definitiva
$urlGenerarEnlace = $url . $cadena;
?>

<?php

$directorio = $this->miConfigurador->getVariableConfiguracion("host");
$directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
$directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");
$valorCodificado = "pagina=registroBeneficiario";
$valorCodificado .= "&id=";

?>

<?php

$directorioReg = $this->miConfigurador->getVariableConfiguracion("host");
$directorioReg .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
$directorioReg .= $this->miConfigurador->getVariableConfiguracion("enlace");
$valorCodificadoReg = "pagina=registroBeneficiario";
$variableReg = $this->miConfigurador->fabricaConexiones->crypto->codificar($valorCodificadoReg);
$enlaceReg = $directorioReg . '=' . $variableReg;

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
$valor .= "&funcion=consultarBloqueManzana";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

$urlConsultarBloqueManzana = $url . $cadena;

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
$valor .= "&funcion=consultarCasaAparta";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

$urlConsultarCasaAparta = $url . $cadena;

?>

var id = "";

$('#example')
		.removeClass( 'display' )
		.addClass('table table-striped table-bordered');


$(document).ready(function() {
    var table = $('#example').DataTable( {
    responsive: true,
    "scrollX": true,
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
            url: "<?php echo $urlCargarInformacion;?>",
            dataSrc:"data"
        },
        "columns": [
            { "data": "urbanizacion" },
            { "data": "nombre" },
            { "data": "identificacion" },
            { "data": "tipo_beneficiario" },
            {
      			"data": null,
      			"defaultContent": "<span class='glyphicon glyphicon-trash optionRemove'></span><span class='glyphicon glyphicon-pencil optionEdit'></span>"
    		}
        ]
    } );

	$("div.toolbar").html('<button type="button" id="AgregarBeneficiario" class="btn btn-default">Registrar Beneficiario Potencial</button>');

    $('#example tbody').on( 'click', '.optionRemove', function () {
    	var data = table.row( $(this).parents('tr') ).data();
        id = data['id_beneficiario'];
        $("#myModal").modal("show");
    } );

    $('#example tbody').on( 'click', '.optionEdit', function () {
    	var data = table.row( $(this).parents('tr') ).data();
        id = data['id_beneficiario'];
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

	$(function() {
		$("#AgregarBeneficiario").click(function( event ) {
	    	location.href = "<?php echo $enlaceReg;?>";
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




/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */


 		   $("#<?php echo $this->campoSeguro('beneficiario');?>").autocomplete({
		   	minChars: 3,
		   	serviceUrl: '<?php echo $urlConsultarBeneficiarios;?>',
		   	onSelect: function (suggestion) {

			     	$("#<?php echo $this->campoSeguro('id');?>").val(suggestion.data);
			if($("#<?php echo $this->campoSeguro('id');?>").val()!=''){

		     $("#AgrupacionDireccion").find("input").removeAttr("required");

		     	}

		   	    }
		   });




$("#<?php echo $this->campoSeguro('beneficiario');?>").change(function() {

  	$("#<?php echo $this->campoSeguro('direccion');?>").attr("required",false);
  	$("#<?php echo $this->campoSeguro('urbanizacion');?>").attr("required",false);
	$("#<?php echo $this->campoSeguro('bloque_manzana');?>").attr("required",false);
	$("#<?php echo $this->campoSeguro('casa_aparta');?>").attr("required",false);
	$("#<?php echo $this->campoSeguro('direccion');?>").val('');
   	$("#<?php echo $this->campoSeguro('urbanizacion');?>").val('');
	$("#<?php echo $this->campoSeguro('bloque_manzana');?>").val('');
	$("#<?php echo $this->campoSeguro('casa_aparta');?>").val('');

});

 $("#<?php echo $this->campoSeguro('direccion');?>").change(function() {

   	$("#<?php echo $this->campoSeguro('beneficiario');?>").attr("required",false);
   	$("#<?php echo $this->campoSeguro('urbanizacion');?>").attr("required",false);
	$("#<?php echo $this->campoSeguro('bloque_manzana');?>").attr("required",false);
	$("#<?php echo $this->campoSeguro('casa_aparta');?>").attr("required",false);
	$("#<?php echo $this->campoSeguro('beneficiario');?>").val('');
   	$("#<?php echo $this->campoSeguro('urbanizacion');?>").val('');
	$("#<?php echo $this->campoSeguro('bloque_manzana');?>").val('');
	$("#<?php echo $this->campoSeguro('casa_aparta');?>").val('');

});

$(function() {
	$(".btn").click(function( event ) {
		if($("#<?php echo $this->campoSeguro('beneficiario');?>").val()=='' && $("#<?php echo $this->campoSeguro('direccion');?>").val()==''  && $("#<?php echo $this->campoSeguro('urbanizacion');?>").val()==''  && $("#<?php echo $this->campoSeguro('bloque_manzana');?>").val()==''  && $("#<?php echo $this->campoSeguro('casa_aparta');?>").val()==''){
			$("#mensajeModal").modal("show");
			event.preventDefault();
		}
	});
	
	$("#registrar").click(function( event ) {	
	   	location.href = "<?php echo $enlaceReg;?>";
	});
	
	$("#mensaje").modal("show");
}); 
	
var urbanizacion = "";
var tipo = "";
var bloque_manzana = "";

$("#<?php echo $this->campoSeguro('urbanizacion');?>").autocomplete({
	minChars: 1,
	serviceUrl: '<?php echo $urlConsultarUrbanizacion;?>',
	onSelect: function (suggestion) {

	  	$("#<?php echo $this->campoSeguro('id_urbanizacion');?>").val(suggestion.data);
		
		if($("#<?php echo $this->campoSeguro('id_urbanizacion');?>").val()!=''){
			urbanizacion = $("#<?php echo $this->campoSeguro('id');?>").val();
		}else{
			$("#<?php echo $this->campoSeguro('urbanizacion');?>").val('');
			$("#<?php echo $this->campoSeguro('urbanizacion');?>").val('');
			urbanizacion = "";
		}
		
	}

});

$("#<?php echo $this->campoSeguro('bloque_manzana');?>").autocomplete({
	minChars: 1,
	serviceUrl: '<?php echo $urlConsultarBloqueManzana;?>',
	onSelect: function (suggestion) {

	  	$("#<?php echo $this->campoSeguro('id_bloque_manzana');?>").val(suggestion.data);
		
		if($("#<?php echo $this->campoSeguro('id_bloque_manzana');?>").val()!=""){
			bloque_manzana = $("#<?php echo $this->campoSeguro('id_bloque_manzana');?>").val();
		}else{
			$("#<?php echo $this->campoSeguro('id_bloque_manzana');?>").val('');
			$("#<?php echo $this->campoSeguro('bloque_manzana');?>").val('');
			bloque_manzana = "";
		}
		
	}
});

$("#<?php echo $this->campoSeguro('casa_aparta');?>").autocomplete({
	minChars: 1,
	serviceUrl: '<?php echo $urlConsultarCasaAparta;?>',
	onSelect: function (suggestion) {

	  	$("#<?php echo $this->campoSeguro('id_casa_aparta');?>").val(suggestion.data);
		
		if($("#<?php echo $this->campoSeguro('id_casa_aparta');?>").val()!=""){
			bloque_manzana = $("#<?php echo $this->campoSeguro('id_casa_aparta');?>").val();
		}else{
			$("#<?php echo $this->campoSeguro('id_casa_aparta');?>").val('');
			$("#<?php echo $this->campoSeguro('casa_aparta');?>").val('');
			bloque_manzana = "";
		}
		
	}
});

$("#<?php echo $this->campoSeguro('urbanizacion');?>").change(function() {

	<!-- $("#<?php echo $this->campoSeguro('beneficiario');?>").val(''); -->
	$("#<?php echo $this->campoSeguro('beneficiario');?>").attr("required",false);
	$("#<?php echo $this->campoSeguro('direccion');?>").attr("required",false);
	
	$("#<?php echo $this->campoSeguro('urbanizacion');?>").attr("required",true);
	$("#<?php echo $this->campoSeguro('bloque_manzana');?>").attr("required",true);
	$("#<?php echo $this->campoSeguro('casa_aparta');?>").attr("required",true);

	$("#<?php echo $this->campoSeguro('beneficiario');?>").val('');
	$("#<?php echo $this->campoSeguro('direccion');?>").val('');

});

$("#<?php echo $this->campoSeguro('bloque_manzana');?>").change(function() {

	<!-- $("#<?php echo $this->campoSeguro('beneficiario');?>").val(''); -->
	$("#<?php echo $this->campoSeguro('beneficiario');?>").attr("required",false);
	$("#<?php echo $this->campoSeguro('direccion');?>").attr("required",false);
		
	$("#<?php echo $this->campoSeguro('urbanizacion');?>").attr("required",true);
	$("#<?php echo $this->campoSeguro('bloque_manzana');?>").attr("required",true);
	$("#<?php echo $this->campoSeguro('casa_aparta');?>").attr("required",true);

	$("#<?php echo $this->campoSeguro('beneficiario');?>").val('');
	$("#<?php echo $this->campoSeguro('direccion');?>").val('');
});

$("#<?php echo $this->campoSeguro('casa_aparta');?>").change(function() {

	<!-- $("#<?php echo $this->campoSeguro('beneficiario');?>").val(''); -->
	$("#<?php echo $this->campoSeguro('beneficiario');?>").attr("required",false);
	$("#<?php echo $this->campoSeguro('direccion');?>").attr("required",false);
		
	$("#<?php echo $this->campoSeguro('urbanizacion');?>").attr("required",true);
	$("#<?php echo $this->campoSeguro('bloque_manzana');?>").attr("required",true);
	$("#<?php echo $this->campoSeguro('casa_aparta');?>").attr("required",true);
		
	$("#<?php echo $this->campoSeguro('beneficiario');?>").val('');
	$("#<?php echo $this->campoSeguro('direccion');?>").val('');

});