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
$cadenaACodificar .= "&funcion=consultaBeneficiarios";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarBeneficiarios = $url . $cadena;

?>
<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */


	   $("#<?php echo $this->campoSeguro('beneficiario');?>").autocomplete({
		   	minChars: 3,
		   	serviceUrl: '<?php echo $urlConsultarBeneficiarios;?>',
		   	onSelect: function (suggestion) {

			     	$("#<?php echo $this->campoSeguro('id');?>").val(suggestion.data);

		   	    }
		   });




		     $("#<?php echo $this->campoSeguro('beneficiario');?>").change(function() {
		     	if($("#<?php echo $this->campoSeguro('id');?>").val()==''){

		     	$("#<?php echo $this->campoSeguro('beneficiario');?>").val('');

		     	}

		   });

var $sigdiv2 =$("#firma_digital_beneficiario").jSignature();


$('#limpiarBn').bind('click', function(e){
		$sigdiv2.jSignature('reset');

		$("#<?php echo $this->campoSeguro('firmaBeneficiario');?>").val('');
		$("#firma_digital_beneficiario").css("display","block");
		$("#mensaje_firma_bn").css("display","none");
		$("#guardarBn").css("display","block");
	});



$('#guardarBn').bind('click', function(e){

 $("#<?php echo $this->campoSeguro('firmaBeneficiario');?>").val(btoa($sigdiv2.jSignature("getData", "svg")));

$("#firma_digital_beneficiario").css("display","none");
$("#mensaje_firma_bn").css("display","block");
$("#guardarBn").css("display","none");

	});



</script>

