<?php
/**
 * Código Correspondiente a las Url de la peticiones Ajax.
 */

// URL base
$url = $this->miConfigurador->getVariableConfiguracion("host");
$url .= $this->miConfigurador->getVariableConfiguracion("site");
$url .= "/index.php?";

// Variables
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultarBeneficiarios";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarBeneficiarios = $url . $cadena;

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultarEquipos";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarEquipos = $url . $cadena;

?>
<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */


$('#tablaEquipos').ready(function() {

             $('#tablaEquipos').DataTable( {
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
                  ajax:{
                      url:"<?php echo $urlConsultarEquipos;?>",
                      dataSrc:"data"
                  },
                  columns: [
                  { data :"identificador" },
                  { data :"marca" },
                  { data :"serial" },
                  { data :"descripcion" },
                  { data :"asignar" },

                    ]
             });

} );



 $( "#<?php echo $this->campoSeguro('beneficiario');?>" ).change(function() {



		   	if($("#<?php echo $this->campoSeguro('beneficiario');?>").val()==''){
		   		$("#<?php echo $this->campoSeguro('id_beneficiario');?>").val('');

		    }

 	});




 $( "#<?php echo $this->campoSeguro('beneficiario');?>" ).blur(function() {

 	       if($("#<?php echo $this->campoSeguro('id_beneficiario');?>").val()==''){
				$("#<?php echo $this->campoSeguro('beneficiario');?>").val('');
		    }

 	});


$("#<?php echo $this->campoSeguro('beneficiario');?>").autocomplete({
		   	minChars: 3,
		   	serviceUrl: '<?php echo $urlConsultarBeneficiarios;?>',
		   	onSelect: function (suggestion) {


		   		$("#<?php echo $this->campoSeguro('id_beneficiario');?>").val(suggestion.data);

			}
		});


</script>

