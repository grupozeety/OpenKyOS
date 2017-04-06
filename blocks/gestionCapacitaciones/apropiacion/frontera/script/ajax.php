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




  function IsValidDate(Fecha)
  {

    var regex = new RegExp('/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/');
    if (regex.test(Fecha)) {
        return true;
    } else {
        return false;
    }

  }

      $('#<?php echo $this->campoSeguro("fechaApropiacion"); ?>').datetimepicker({
                format: 'yyyy-mm-dd',
                language: "es",
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0
             });


  $("#<?php echo $this->campoSeguro('fechaApropiacion'); ?>").blur(function() {
    if($("#<?php echo $this->campoSeguro('fechaApropiacion'); ?>").val()!=''){
        var validar=IsValidDate($("#<?php echo $this->campoSeguro('fechaApropiacion'); ?>").val());
        if(validar===false){
          $("#<?php echo $this->campoSeguro('fechaApropiacion'); ?>").val("");
        }

      }
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
