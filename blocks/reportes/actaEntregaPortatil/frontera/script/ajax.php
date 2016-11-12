	
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

 	$("#<?php echo $this->campoSeguro('tipo_documento')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('tipo_beneficiario')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('estrato')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('urbanizacion')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('tipo_tecnologia')?>").select2({width:'100%'});

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

	var urbanizacion;

	function urbanizacion(){
		$("#<?php echo $this->campoSeguro('urbanizacion')?>").html('');
		$("<option value=''>Seleccione .....</option>").appendTo("#<?php echo $this->campoSeguro('urbanizacion')?>");
				
		$.ajax({
			url: "<?php echo $urlConsultarProyectos; ?>",
			dataType: "json",
			data: { metodo:''},
			success: function(data){
				urbanizacion = data;
				$.each(data , function(indice,valor){
					$("<option value='"+data[ indice ].id+"'>" + data[ indice ].urbanizacion + "</option>").appendTo("#<?php echo $this->campoSeguro('urbanizacion')?>");
				});
				$("#<?php echo $this->campoSeguro('urbanizacion')?>").val($("#<?php echo $this->campoSeguro('id_urbanizacion')?>").val()).change();
			}
		});
	};
	
	$("#<?php echo $this->campoSeguro('urbanizacion');?>").change(function() {
		$("#<?php echo $this->campoSeguro('id_urbanizacion');?>").val($("#<?php echo $this->campoSeguro('urbanizacion');?> option:selected").text());
	});
	
	$("#<?php echo $this->campoSeguro('urbanizacion');?>").change(function() {
		if($("#<?php echo $this->campoSeguro('urbanizacion');?>").val() != ""){
			$.each(urbanizacion , function(indice,valor){
				if(urbanizacion[indice].id == $("#<?php echo $this->campoSeguro('urbanizacion');?>").val()){
					var departamento = urbanizacion[indice].departamento.split(" - ");
					var municipio = urbanizacion[indice].municipio.split(" - ");
					$("#<?php echo $this->campoSeguro('departamento');?>").val(departamento[1]);
					$("#<?php echo $this->campoSeguro('municipio');?>").val(municipio[1]);
					$("#<?php echo $this->campoSeguro('codigo_dane');?>").val(departamento[0]);
				}
			});
		}
	});
	 
	urbanizacion();

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

