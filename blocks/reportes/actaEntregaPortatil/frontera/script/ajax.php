	
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

	$('#<?php echo $this->campoSeguro("fecha_instalacion");?>').datetimepicker({
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

	$('#<?php echo $this->campoSeguro("fecha_instalacion");?>').val(strDate);

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
	
</script>

