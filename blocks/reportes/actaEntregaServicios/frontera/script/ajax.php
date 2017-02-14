ffech
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


 $(document).ready(function() {

	$("#<?php echo $this->campoSeguro("geolocalizacion")?>").keydown(function(e){
		e.preventDefault();
	});

	$(function() {
		$("#<?php echo $this->campoSeguro("geolocalizacion")?>").focus(function() {
			 initMap();
	        $("#myModal").modal("show");

	    });
	});

	$("#botonAgregarLocalizacion").click(function( e ) {
	    $("#<?php echo $this->campoSeguro("geolocalizacion")?>").val( $("#geomodal").val());
		$("#<?php echo $this->campoSeguro("geolocalizacion")?>").change();
		$('#myModal').modal('hide');
	});

 	var markers = [];
	function initMap() {
	    var map = new google.maps.Map(document.getElementById("map-canvas"), {
	        center: {lat: 4.6482837, lng: -74.2478939},
	        zoom: 6
	    });
	    var infoWindow = new google.maps.InfoWindow({map: map});

	     if (navigator.geolocation) {
	         navigator.geolocation.getCurrentPosition(function(position) {
	         var pos = {
	                 lat: position.coords.latitude,
	                 lng: position.coords.longitude
	         };

			$("#<?php echo $this->campoSeguro('geolocalizacion');?>").val(pos.lat + "," + pos.lng).change();

	         infoWindow.setPosition(pos);
	         infoWindow.setContent("Localización Encontrada.");
	         map.setCenter(pos);
	         }, function() {
	         handleLocationError(true, infoWindow, map.getCenter());
	         });
	 } else {
	         // Browser doesnt support Geolocation
	         handleLocationError(false, infoWindow, map.getCenter());
	 }

	    if(typeof document.getElementById("myModal")!==undefined){
	        $("#myModal").on("shown.bs.modal", function () {
	            initMap();
	        });
	    }

	    google.maps.event.addListener(map, "click", function (e) {

	        DeleteMarkers();

	        //lat and lng is available in e object
	        var latLng = e.latLng;

			$("#geomodal").val(e.latLng.lat() + ", " + e.latLng.lng());

	        var marker=new google.maps.Marker({
	            position:e.latLng,
	        });

	        marker.setMap(map);

	        markers.push(marker);
	    });

	    function DeleteMarkers() {
	        //Loop through all the markers and remove
	        for (var i = 0; i < markers.length; i++) {
	            markers[i].setMap(null);
	        }
	        markers = [];
	    };
	}

	function handleLocationError(browserHasGeolocation, infoWindow, pos) {
	    infoWindow.setPosition(pos);
	    infoWindow.setContent(browserHasGeolocation ?
	          "Error: The Geolocation service failed." :
	          "Error: Your browser doesn\'t support geolocation.");
	}


 	$("#<?php echo $this->campoSeguro('tipo_documento_int')?>").select2({width:'100%',readonly: true});
 	$("#<?php echo $this->campoSeguro('tipo_documento_int')?>").prop('disabled', true);

	$("#<?php echo $this->campoSeguro('tipo_beneficiario_int')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('tipo_beneficiario_int')?>").prop('disabled', true);

	$("#<?php echo $this->campoSeguro('estrato_int')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('estrato_int')?>").prop('disabled', true);

	$("#<?php echo $this->campoSeguro('tipo_tecnologia_int')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('tipo_tecnologia_int')?>").prop('disabled', true);

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
	    todayHighlight: 0,
	    startView: 2,
	    minView: 2,
	    forceParse: 0
	});

	$('#<?php echo $this->campoSeguro("fecha_instalacion");?>').val(strDate);

	if($("#<?php echo $this->campoSeguro('urbanizacion')?>").length > 0){
		var urbanizacion;
	}

/*

	function urbanizacion(){
		$("#<?php echo $this->campoSeguro('urbanizacion')?>").html('');
		$("<option value=''>Seleccione .....</option>").appendTo("#<?php echo $this->campoSeguro('urbanizacion')?>");

		$.ajax({
			url: "<?php echo $urlConsultarProyectos;?>",
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
					$("#<?php echo $this->campoSeguro('codigo_dane');?>").val(departamento[0] + " - " + municipio[0]);
				}
			});
		}
	});

	urbanizacion();
*/

	$("#div1").hide();

	$("#<?php echo $this->campoSeguro('tipo_beneficiario');?>").change(function() {
		if($("#<?php echo $this->campoSeguro('tipo_beneficiario');?>").val() == 3){
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

	if($("#<?php echo $this->campoSeguro('firmaBeneficiario');?>").val() != ""){
		$("#firma_digital_beneficiario").css("display","none");
		$("#mensaje_firma_bn").css("display","block");
		$("#guardarBn").css("display","none");
	}

/*
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

*/

 });



</script>

