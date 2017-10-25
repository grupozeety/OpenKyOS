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

// URL Consultar Proyectos
$urlConsultarBeneficiarios = $url . $cadena;

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&action=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
$cadenaACodificar .= "&funcion=consultaFirma";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarFirma = $url . $cadena;

?>
<script type='text/javascript'>

$("#mensaje").modal("show");

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */



  $("#<?php echo $this->campoSeguro('beneficiario'); ?>").keypress(function(){

    $("#<?php echo $this->campoSeguro('id_beneficiario'); ?>").val('');

  });

  $("#<?php echo $this->campoSeguro('beneficiario'); ?>").blur(function(){

      var valor= $("#<?php echo $this->campoSeguro('id_beneficiario'); ?>").val();
      if (valor=='') {
        $("#<?php echo $this->campoSeguro('beneficiario'); ?>").val('');
      }
  });



  $("#<?php echo $this->campoSeguro('beneficiario'); ?>").autocomplete({
      minChars: 3,
      serviceUrl: '<?php echo $urlConsultarBeneficiarios; ?>',
      onSelect: function (suggestion) {

      $("#<?php echo $this->campoSeguro('id_beneficiario'); ?>").val(suggestion.data);

      cargarImagen(suggestion.data);
    }
  });



function cargarImagen(info){

  $.ajax({
    url: "<?php echo $urlConsultarFirma ?>",
    dataType: "json",
    data: { value: info},
    success: function(data){

     document.getElementById('imagen').src = data;

     $("#imagenBeneficiario").modal("show");

    }
  });
};



</script>