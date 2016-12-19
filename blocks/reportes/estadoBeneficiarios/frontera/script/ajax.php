<?php
/**
 * Código Correspondiente a las Url de la peticiones Ajax.
 */

// URL base
$url = $this->miConfigurador->getVariableConfiguracion("host");
$url .= $this->miConfigurador->getVariableConfiguracion("site");
$url .= "/index.php?";

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultaGeneral";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultaGeneral = $url . $cadena;

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultaParticular";
if (isset($_REQUEST['id_proyecto'])) {
    $cadenaACodificar .= "&id_proyecto=" . $_REQUEST['id_proyecto'];
}

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultaParticular = $url . $cadena;

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&opcion=consultaParticular";
$cadenaACodificar .= "&proyecto=EL RECUERDO";
$cadenaACodificar .= "&id_proyecto=11";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarParticularEnlace = $url . $cadena;

?>
<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax(Aprobación Contrato).
 */

 $(document).ready(function() {
      $('#example').DataTable( {
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
//
    		"fnRowCallback": function( nRow, myInfraArray){
                  $(nRow).children().each(function(index, td, myInfraArray) {
                    if ($(td).html() >=0 && $(td).html() <= 20) {
                          $(td).css("background-color", "#F08080");
                       }else if ($(td).html() >= 21 && $(td).html() <= 50) {
                          $(td).css("background-color", "#f3aa51");
                       }else if ($(td).html() >= 51 && $(td).html() <= 80) {
                          $(td).css("background-color", "#f0ed80");
                       }else if ($(td).html() >= 81 && $(td).html() <= 99) {
                          $(td).css("background-color", "#b0e6c8");
                       }else if ($(td).html() == 100 ) {
                          $(td).css("background-color", "#0d7b3e");
                       }

                  } );
                    return nRow;
             },
              responsive: true,
                   ajax:{
                      url:"<?php echo $urlConsultaGeneral;?>",
                      dataSrc:"data"
                  },
                  columns: [
                  { data :"proyecto"},
                  { data :"beneficiarios" },
                  { data :"preventas" },
                  { data :"ventas"},
                  { data :"accPortatil" },
                  { data :"accServicio" },
                  { data :"activacion"},
                  { data :"revision" },
                  { data :"aprobacion" },
                           ]
//
    } );
	$('#example_2').DataTable( {
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
//
    		"fnRowCallback": function( nRow, myInfraArray){
                  $(nRow).children().each(function(index, td, myInfraArray) {
                    if ($(td).html() >=0 && $(td).html() <= 20) {
                          $(td).css("background-color", "#F08080");
                       }else if ($(td).html() >= 21 && $(td).html() <= 50) {
                          $(td).css("background-color", "#f3aa51");
                       }else if ($(td).html() >= 51 && $(td).html() <= 80) {
                          $(td).css("background-color", "#f0ed80");
                       }else if ($(td).html() >= 81 && $(td).html() <= 99) {
                          $(td).css("background-color", "#b0e6c8");
                       }else if ($(td).html() == 100 ) {
                          $(td).css("background-color", "#0d7b3e");
                       }

                  } );
                    return nRow;
             },
              responsive: true,
                   ajax:{
                      url:"<?php echo $urlConsultaParticular;?>",
                      dataSrc:"data"
                  },
                  columns: [
                  { data :"beneficiario" },
                  { data :"contrato" },
                  { data :"accPortatil" },
                  { data :"accServicio" },
                  { data :"activacion"},
                  { data :"revision" },
                  { data :"aprobacion" },
                           ]

//
    } );

} );

</script>