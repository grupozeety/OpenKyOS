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
$cadenaACodificar .= "&action=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultaBeneficiarios";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Beneficiarios
$urlConsultarBeneficiarios = $url . $cadena;

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultarProcesos";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarProcesos = $url . $cadena;

?>
<script type='text/javascript'>

  function IsValidDate(Fecha)
  {

    var regex = new RegExp('/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/');
    if (regex.test(Fecha)) {
        return true;
    } else {
        return false;
    }

  }


      $('#<?php echo $this->campoSeguro("fecha_oportuna_pago"); ?>').datetimepicker({
                format: 'yyyy-mm-dd',
                language: "es",
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0,
                });


  $("#<?php echo $this->campoSeguro('fecha_oportuna_pago'); ?>").blur(function() {
    if($("#<?php echo $this->campoSeguro('fecha_oportuna_pago'); ?>").val()!=''){
        var validar=IsValidDate($("#<?php echo $this->campoSeguro('fecha_oportuna_pago'); ?>").val());
        if(validar===false){
          $("#<?php echo $this->campoSeguro('fecha_oportuna_pago'); ?>").val("");
        }

      }
  });



        $('#<?php echo $this->campoSeguro("fecha_oportuna_pago"); ?>').datetimepicker('setStartDate', new Date());







$("#mensaje").modal("show");

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */




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

                 responsive: true,
                   ajax:{
                      url:"<?php echo $urlConsultarProcesos; ?>",
                      dataSrc:"data"
                  },
                columns: [
                  { data :"proceso"},
                  { data :"estado" },
                  { data :"archivo" },
                  { data :"tamanio_archivo"},
                  { data :"num_inicial"},
                  { data :"num_final" },
                  { data :"urbanizaciones"},
                  { data :"fecha_generacion"},
                  { data :"eliminar_proceso"},
                           ]
    } );




   $("#<?php echo $this->campoSeguro('beneficiario'); ?>").autocomplete({
        minChars: 3,
        serviceUrl: '<?php echo $urlConsultarBeneficiarios; ?>',
        minChars:3,
        multiple: true,
        multipleSeparator: "",
        delimiter: /(,|;)\s*/, // regex or character
        maxHeight:1000,
        width:1000,
        zIndex: 9999,
        deferRequestBy: 0, //miliseconds
        noCache: false,
        onSelect: function (suggestion) {
            $("#<?php echo $this->campoSeguro('beneficiario'); ?>").val($("#<?php echo $this->campoSeguro('beneficiario'); ?>").val()+";");
        }

       });




</script>

