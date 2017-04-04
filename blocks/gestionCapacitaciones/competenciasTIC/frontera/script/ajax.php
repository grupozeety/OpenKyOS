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

?>
<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax(Aprobación Contrato).
 */






function informacionBeneficiario(elem, request, response){
      $.ajax({
        url: "<?php echo $urlConsultarInformacionBeneficiarios; ?>",
        dataType: "json",
        data: { beneficiario:$("#<?php echo $this->campoSeguro('id_beneficiario') ?>").val() },
        success: function(data){
            if(data){
                $("#<?php echo $this->campoSeguro('nombre') ?>").val(data.nombre_beneficiario);
                $("#<?php echo $this->campoSeguro('identificacion') ?>").val(data.numero_identificacion);
                $("#<?php echo $this->campoSeguro('edad') ?>").val(data.edad);
                $("#<?php echo $this->campoSeguro('correo') ?>").val(data.correo);
                $("#<?php echo $this->campoSeguro('telefono') ?>").val(data.telefono);
                if(data.genero){
                    document.getElementById('<?php echo $this->campoSeguro('genero') ?>').value=data.genero;

                }



              }else{

                $("#<?php echo $this->campoSeguro('id_beneficiario') ?>").val('NO ASIGNADO');
                $("#<?php echo $this->campoSeguro('beneficiario') ?>").val('NO ASIGNADO');


              }

           }

       });
    };



 $(document).ready(function() {



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
