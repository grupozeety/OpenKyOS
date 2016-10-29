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
$cadenaACodificar .= "&funcion=consultarAgendamiento";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarAgendamiento = $url . $cadena;

?>
<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */

  $(document).ready(function() {





      $("#mensaje").modal("show");

    var i=1;




       var table=$('#example').DataTable( {
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
                   processing: true,
                       searching: true,
                       info:true,
                       paging: false,
                       "columnDefs": [
                                      {"className": "dt-center", "targets": "_all"}
                        ],
                        "scrollY":"300px",
                        "scrollCollapse": true,
                        responsive: true,
                        ajax:{
                            url:"<?php echo $urlConsultarAgendamiento;?>",
                            dataSrc:"data",

                        },
                        columns: [
                        { data :"numero_agendamiento" },
                        { data :"nodo" },
                        { data :"cantidad_beneficiarios" },
                        { data :"fecha_agendamiento" },
                        { data :"responsable" },
                        { data :"opcion","searchable": false },

                                  ]
          } );

    new $.fn.dataTable.DtColSearch(table,{
    placement: "foot",


});




} );

</script>

