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
$valor .= "&funcion=consultarCabecera";
$valor .= "&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valor, $enlace);

// URL definitiva
$urlCargarInformacion = $url . $cadena;
?>



$("#mensaje").modal("show");

$(document).ready(function() {

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
            responsive: true,
            ajax: {
                url: "<?php echo $urlCargarInformacion ?>",
                dataSrc:"data"
            },
            "columns": [
                { "data": "codigo_cabecera" },
                { "data": "descripcion" },
                { "data": "departamento" },
                { "data": "municipio" },
                { "data": "urbanizacion" },
                { "data": "actualizacion" },

            ]
        } );




    });

});


