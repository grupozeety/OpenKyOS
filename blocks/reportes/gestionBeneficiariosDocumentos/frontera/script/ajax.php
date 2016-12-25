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
<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */

  $(document).ready(function() {
            $('#<?php echo $this->campoSeguro("fecha_inicio");?>').datetimepicker({
               format: 'yyyy-mm-dd',
               language: "es",
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0
            }).on('changeDate', function(ev){
                 $('#<?php echo $this->campoSeguro("fecha_final");?>').val('');
               $('#<?php echo $this->campoSeguro("fecha_final");?>').datetimepicker('setStartDate', $('#<?php echo $this->campoSeguro("fecha_inicio");?>').val());
               $('#<?php echo $this->campoSeguro('fecha_final');?>')[0].focus();

                });

            $('#<?php echo $this->campoSeguro("fecha_final");?>').datetimepicker({
                format: 'yyyy-mm-dd',
                language: "es",
                weekStart: 1,
                todayBtn:  0,
                autoclose: 1,
                todayHighlight: 0,
                startView: 2,
                minView: 2,
                forceParse: 0
            }).on('changeDate', function(ev){

                });




      $("#mensaje").modal("show");




$('#example').on('xhr.dt', function ( e, settings, json, xhr) {
            $('#<?php echo $this->campoSeguro("info_proyectos");?>').val(btoa(JSON.stringify(json.proyectos)));
    } )
    .dataTable( {
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
                      url:"<?php echo $urlConsultarProyectos;?>",
                      dataSrc:"data",

                  },
                  columns: [
                  { data :"numero" },
                  { data :"urbanizacion" },
                  { data :"opcion" }
                            ]
    } );


} );

</script>

