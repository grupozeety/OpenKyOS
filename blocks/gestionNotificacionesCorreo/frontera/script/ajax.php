	
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
$valor .= "&funcion=consultarUsuarios";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlCargarInformacion = $url . $cadena;
?>

<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */


 $(document).ready(function() {

	 actualizarTabla();

});

 function actualizarTabla(){
		
		$('#example').DataTable().destroy();
	    var table = $('#example').DataTable( {
	    	"processing": true,
	        "searching": true,
	        "info":false,
	        "paging": true,
	        "scrollCollapse": true,
	        "responsive": true,
	       	"columnDefs": [
	        	{"className": "dt-center" ,"targets": "_all"}
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
	            data: {},
	            dataSrc:"data"   
	        },
	        "columns": [
	            { "data": "uid" },
	            { "data": "givenname" },
	            { "data": "description" },
	            { "data": "mail" }
	        ]
	    } );
	    
	    setInterval( function () {
 		table.fnReloadAjax();
		}, 30000 );
		
 }

	
	$("#<?php echo $this->campoSeguro('rol');?>").attr("required", true);

	$('input[name="<?php echo $this->campoSeguro('estado_cuenta');?>"]').on('change', function() {
		  var radioValue = $('input[name="<?php echo $this->campoSeguro('estado_cuenta');?>"]:checked').val();        
		  if(radioValue == "1"){
				$("#<?php echo $this->campoSeguro('rol');?>").attr("required", true);
			}else if(radioValue == "2"){
				$("#<?php echo $this->campoSeguro('rol');?>").attr("required", false);
			}
	});  
	
</script>

