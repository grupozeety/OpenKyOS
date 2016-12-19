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
$cadenaACodificar .= "&funcion=consultaPortatiles";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarPortatiles = $url . $cadena;

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
$cadenaACodificar .= "&funcion=consultaInformacionPortatiles";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL Consultar Proyectos
$urlConsultarInformacionPortatiles = $url . $cadena;

?>
<script type='text/javascript'>



function informacionPortatil(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlConsultarInformacionPortatiles;?>",
	    dataType: "json",
	    data: { valor:$("#<?php echo $this->campoSeguro('id_serial');?>").val()},
	    success: function(data){
	    	 if(data!=" "){

				$("#<?php echo $this->campoSeguro('marca');?>").val(data.marca);
				$("#<?php echo $this->campoSeguro('modelo');?>").val(data.modelo);
				$("#<?php echo $this->campoSeguro('procesador');?>").val(data.procesador);
				$("#<?php echo $this->campoSeguro('memoria_ram');?>").val(data.memoria_ram);
				$("#<?php echo $this->campoSeguro('disco_duro');?>").val(data.disco_duro);
				$("#<?php echo $this->campoSeguro('sistema_operativo');?>").val(data.sistema_operativo);
				$("#<?php echo $this->campoSeguro('camara');?>").val(data.camara);
				$("#<?php echo $this->campoSeguro('audio');?>").val(data.audio);
				$("#<?php echo $this->campoSeguro('bateria');?>").val(data.bateria);
				$("#<?php echo $this->campoSeguro('targeta_red_alambrica');?>").val(data.red_alamnbrica);
				$("#<?php echo $this->campoSeguro('targeta_red_inalambrica');?>").val(data.red_inalambrica);
				$("#<?php echo $this->campoSeguro('cargador');?>").val(data.cargador);
				$("#<?php echo $this->campoSeguro('pantalla');?>").val(data.pantalla);

		      }


	    }

	   });
	};



 	$("#<?php echo $this->campoSeguro('serial');?>").autocomplete({
	   	minChars: 2,
	   	serviceUrl: '<?php echo $urlConsultarPortatiles;?>',
	   	onSelect: function (suggestion) {
			$("#<?php echo $this->campoSeguro('id_serial');?>").val(suggestion.data);

			informacionPortatil();

		}
	});






/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */

 	$("#<?php echo $this->campoSeguro('tipo_documento');?>").select2({width:'100%'});





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


	var d = new Date();
	var strDate = d.getDate() + "-" + (d.getMonth()+1) + "-" + d.getFullYear();

	$('#<?php echo $this->campoSeguro("fecha_entrega");?>').datetimepicker({
		format: 'dd-mm-yyyy',
	    language: "es",
	    weekStart: 1,
	    todayBtn:  1,
		autoclose: 1,
	    todayHighlight: 1,
	    startView: 2,
	    minView: 2,
	    forceParse: 0
	});

	$('#<?php echo $this->campoSeguro("fecha_entrega");?>').val(strDate);




	$("#div1").hide();

	$("#<?php echo $this->campoSeguro('tipo_beneficiario');?>").change(function() {
		if($("#<?php echo $this->campoSeguro('tipo_beneficiario');?>").val() == 2){
			$("#div1").show();
		}else{
			$("#div1").hide();
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

	var $sigdiv1 =$("#firma_digital_instalador").jSignature();

	$('#limpiarIns').bind('click', function(e){
		$sigdiv1.jSignature('reset');
		$("#<?php echo $this->campoSeguro('firmaInstalador');?>").val('');
		$("#firma_digital_instalador").css("display","block");
		$("#mensaje_firma_ins").css("display","none");
		$("#guardarIns").css("display","block");
	});

	$('#guardarIns').bind('click', function(e){
		$("#<?php echo $this->campoSeguro('firmaInstalador');?>").val(btoa($sigdiv1.jSignature("getData", "svg")));
		$("#firma_digital_instalador").css("display","none");
		$("#mensaje_firma_ins").css("display","block");
		$("#guardarIns").css("display","none");
	});





</script>

