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
$cadenaACodificar .= "&funcion=consultaParticular";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultaParticular = $url . $cadena;

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

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultaInformacionBeneficiarios";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarInformacionBeneficiarios = $url . $cadena;

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultarActividad";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarActividad = $url . $cadena;

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultarInformacionCapacitacion";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarInformacionCapacitacion = $url . $cadena;

?>
<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax(Aprobación Contrato).
 */





function informacionCapacitacion(elem, request, response){
      $.ajax({
        url: "<?php echo $urlConsultarInformacionCapacitacion; ?>",
        dataType: "json",
        data: { idActividad:$("#<?php echo $this->campoSeguro('identificadorActividad') ?>").val() },
        success: function(data){
            if(data){
                $("#<?php echo $this->campoSeguro('fechaCapacitacion') ?>").val(data.fecha_capacitacion);
                $("#<?php echo $this->campoSeguro('horas') ?>").val(data.horas_capacitacion);

                if(data.detalle_servicio){
                    document.getElementById('<?php echo $this->campoSeguro('detalleServicio') ?>').value=data.detalle_servicio;
                }

                if(data.servicio_capacitacion){
                    document.getElementById('<?php echo $this->campoSeguro('servicio') ?>').value=data.servicio_capacitacion;

                }

              }else{

                $("#<?php echo $this->campoSeguro('fechaCapacitacion') ?>").val('');
                $("#<?php echo $this->campoSeguro('horas') ?>").val('');
                document.getElementById('<?php echo $this->campoSeguro('servicio') ?>').value='';
                document.getElementById('<?php echo $this->campoSeguro('detalleServicio') ?>').value='';

              }

           }

       });
    };




function informacionBeneficiario(elem, request, response){
      $.ajax({
        url: "<?php echo $urlConsultarInformacionBeneficiarios; ?>",
        dataType: "json",
        data: { beneficiario:$("#<?php echo $this->campoSeguro('id_beneficiario') ?>").val() },
        success: function(data){
            if(data){

              console.log(data.municipio);
              console.log(data.departamento);

                if(data.municipio){
                    document.getElementById('<?php echo $this->campoSeguro('municipio') ?>').value=data.municipio;

                }

                if(data.departamento){
                    document.getElementById('<?php echo $this->campoSeguro('departamento') ?>').value=data.departamento;

                }



              }else{

                $("#<?php echo $this->campoSeguro('id_beneficiario') ?>").val('NO ASIGNADO');
                $("#<?php echo $this->campoSeguro('beneficiario') ?>").val('NO ASIGNADO');


              }

           }

       });
    };



 $(document).ready(function() {



  $('#limpiarBn').bind('click', function(e){
                $("#<?php echo $this->campoSeguro('fechaCapacitacion') ?>").val('');
                $("#<?php echo $this->campoSeguro('horas') ?>").val('');
                document.getElementById('<?php echo $this->campoSeguro('servicio') ?>').value='';
                document.getElementById('<?php echo $this->campoSeguro('detalleServicio') ?>').value='';
                $("#<?php echo $this->campoSeguro('actividad') ?>").val('');
                $("#<?php echo $this->campoSeguro('identificadorActividad') ?>").val('');
  });




  function IsValidTime(timeString)
  {

    var regex = new RegExp('([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])');
    if (regex.test(timeString)) {
        return true;
    } else {
        return false;
    }

  }







  $("#<?php echo $this->campoSeguro('horas'); ?>").change(function() {
    if($("#<?php echo $this->campoSeguro('horas'); ?>").val()!=''){
        var validar=IsValidTime($("#<?php echo $this->campoSeguro('horas'); ?>").val());
        if(validar===false){
          $("#<?php echo $this->campoSeguro('horas'); ?>").val("");
        }

      }
  });




      $('#<?php echo $this->campoSeguro("fechaCapacitacion"); ?>').datetimepicker({
             format: 'yyyy-mm-dd HH:ii:00',
              language: "es"

            });





  $("#<?php echo $this->campoSeguro('beneficiario'); ?>").autocomplete({
      minChars: 3,
      serviceUrl: '<?php echo $urlConsultarBeneficiarios; ?>',
      onSelect: function (suggestion) {

      if(suggestion){
      $("#<?php echo $this->campoSeguro('id_beneficiario'); ?>").val(suggestion.data);

      informacionBeneficiario();

      }else{
        $("#<?php echo $this->campoSeguro('id_beneficiario') ?>").val('NO ASIGNADO');
        $("#<?php echo $this->campoSeguro('beneficiario') ?>").val('NO ASIGNADO');



      }

    }
  });



  $("#<?php echo $this->campoSeguro('beneficiario'); ?>").change(function() {
    if($("#<?php echo $this->campoSeguro('id_beneficiario'); ?>").val()=='NO ASIGNADO'){
        $("#<?php echo $this->campoSeguro('beneficiario'); ?>").val('NO ASIGNADO');
        $("#<?php echo $this->campoSeguro('id_beneficiario'); ?>").val('NO ASIGNADO');
      }
  });



  $("#<?php echo $this->campoSeguro('actividad'); ?>").autocomplete({
      minChars: 1,
      serviceUrl: '<?php echo $urlConsultarActividad; ?>',
      onSelect: function (suggestion) {
      if(suggestion.data){
      $("#<?php echo $this->campoSeguro('identificadorActividad'); ?>").val(suggestion.data);
      informacionCapacitacion();
      }

    }
  });




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
                      url:"<?php echo $urlConsultaParticular; ?>",
                      dataSrc:"data"
                  },
                  columns: [
                  { data :"ident" },
                  { data :"unidad" },
                  { data :"valor" },
                  { data :"actualizar" },
                  { data :"eliminar" }
                           ]

//
    } );






} );

</script>
