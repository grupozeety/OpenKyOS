function cargarAjax(){
	<?php
	/**
	 *
	 * Los datos del bloque se encuentran en el arreglo $esteBloque.
	 */
	
	// URL base
	$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
	$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
	$url .= "/index.php?";
	// Variables
	$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
	$valor .= "&procesarAjax=true";
	$valor .= "&action=index.php";
	$valor .= "&bloqueNombre=". $esteBloque ["nombre"]; 
	$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
	$valor .= "&funcion=codificar";
	$valor .= "&tiempo=" . $_REQUEST ['tiempo'];
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );
	
	// URL definitiva
	$urlCodificacionCampos = $url . $cadena;
	?>
	
	<?php
	/**
	 *
	 * Los datos del bloque se encuentran en el arreglo $esteBloque.
	 */
	
	// URL base
	$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
	$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
	$url .= "/index.php?";
	// Variables
	$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
	$valor .= "&procesarAjax=true";
	$valor .= "&action=index.php";
	$valor .= "&bloqueNombre=". $esteBloque ["nombre"]; 
	$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
	$valor .= "&funcion=codificarSelect";
	$valor .= "&tiempo=" . $_REQUEST ['tiempo'];
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );
	
	// URL definitiva
	$urlCodificacionCamposSelect = $url . $cadena;
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
	
	<?php
	/**
	 *
	 * Los datos del bloque se encuentran en el arreglo $esteBloque.
	 */
	
	// URL base
	$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
	$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
	$url .= "/index.php?";
	// Variables
	$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
	$valor .= "&procesarAjax=true";
	$valor .= "&action=index.php";
	$valor .= "&bloqueNombre=". $esteBloque ["nombre"]; 
	$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
	$valor .= "&funcion=eliminarImagen";
	$valor .= "&tiempo=" . $_REQUEST ['tiempo'];
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );
	
	// URL definitiva
	$urlEliminarImagen = $url . $cadena;
	?>
	
	<?php
	/**
	 *
	 * Los datos del bloque se encuentran en el arreglo $esteBloque.
	 */
	
	// URL base
	$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
	$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
	$url .= "/index.php?";
	// Variables
	$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
	$valor .= "&procesarAjax=true";
	$valor .= "&action=index.php";
	$valor .= "&bloqueNombre=". $esteBloque ["nombre"]; 
	$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
	$valor .= "&funcion=cargarImagen";
	$valor .= "&eliminar=" . $urlEliminarImagen;
	$valor .= "&tiempo=" . $_REQUEST ['tiempo'];
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );
	
	// URL definitiva
	$urlCargarImagen = $url . $cadena;
	?>
	
	<?php 
	// URL base
	$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
	$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
	$url .= "/index.php?";
	// Variables
	$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
	$valor .= "&procesarAjax=true";
	$valor .= "&action=index.php";
	$valor .= "&bloqueNombre=". $esteBloque ["nombre"];
	$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
	$valor .= "&funcion=actualizarCampo";
	$valor .= "&tiempo=" . $_REQUEST ['tiempo'];
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );
	
	// URL definitiva
	$urlActualizar = $url . $cadena;
	
	?>
	<?php 
	// URL base
	$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
	$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
	$url .= "/index.php?";
	// Variables
	$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
	$valor .= "&procesarAjax=true";
	$valor .= "&action=index.php";
	$valor .= "&bloqueNombre=". $esteBloque ["nombre"];
	$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
	$valor .= "&funcion=actualizarCampoUrb";
	$valor .= "&tiempo=" . $_REQUEST ['tiempo'];
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );
	
	// URL definitiva
	$urlActualizarUrb = $url . $cadena;
	
	?>
	<?php
	
	$directorioReg = $this->miConfigurador->getVariableConfiguracion ( "host" );
	$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
	$directorioReg .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
	$valorCodificadoReg = "pagina=consultarBeneficiario";
	$variableReg = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificadoReg );
	$enlaceReg = $directorioReg . '=' . $variableReg;
	
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
	$cadenaACodificar .= "&funcion=consultarExistencia";
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);
	
	// URL Consultar Proyectos
	$urlConsultarExistencia= $url . $cadena;
	
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
	$cadenaACodificar .= "&funcion=consultarCodigo";
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);
	
	// URL Consultar código para actualizar la nomenclatrua y el id_hogar
	$urlActualizarNomHog = $url . $cadena;
	
	?>
	
	$(document).ready(function() {
	
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
		
		
		initMap();
		
		var beneficiario =$("#<?php echo $this->campoSeguro('id_beneficiario')?>").val();
	
		//Here we are
	
		<?php $arreglo=array(
				$this->campoSeguro('identificacion_beneficiario'),
				$this->campoSeguro('nombre_beneficiario'),
				$this->campoSeguro('primer_apellido'),
				$this->campoSeguro('segundo_apellido'),
				$this->campoSeguro('edad_beneficiario'),
				$this->campoSeguro('correo'),
				$this->campoSeguro('direccion'),
				$this->campoSeguro('manzana'),
				$this->campoSeguro('torre'),
				$this->campoSeguro('interior'),
				$this->campoSeguro('lote'),
				$this->campoSeguro('bloque'),
				$this->campoSeguro('apartamento'),
				$this->campoSeguro('telefono'),
				$this->campoSeguro('celular'),
				$this->campoSeguro('whatsapp'),
				$this->campoSeguro('geolocalizacion'),
				//$this->campoSeguro('id_hogar'),
				//$this->campoSeguro('nomenclatura'),
				//$this->campoSeguro('resolucion_adjudicacion'),
		);
		
		$arreglo2=array(
				$this->campoSeguro('tipo_beneficiario'),
				$this->campoSeguro('tipo_documento'),
				$this->campoSeguro('genero_beneficiario'),
				$this->campoSeguro('tipo_documento'),
				$this->campoSeguro('nivel_estudio'),
				$this->campoSeguro('tipo_vivienda'),
				//$this->campoSeguro('urbanizacion'),
				//$this->campoSeguro('departamento'),
				//$this->campoSeguro('municipio'),
				$this->campoSeguro('territorio'),
				$this->campoSeguro('estrato'),
				$this->campoSeguro('jefe_hogar'),
				$this->campoSeguro('pertenencia_etnica'),
				$this->campoSeguro('ocupacion'),
		);
		
		foreach ($arreglo as $key=>$values){
			?>
			$("#<?php echo $values;?>").on('click touchstart', function() {
				$("#<?php echo $values?>").attr('readonly', false);
			});
			
				$( "#<?php echo $values;?>" ).dblclick(function() {
					$("#<?php echo $values?>").attr('readonly', false);
				});
			
				 $( "#<?php echo $values;?>" ).blur(function() {
				 	var id =$("#<?php echo $values;?>").val();
				 	$.ajax({
				 		url: "<?php echo $urlActualizar?>",
				 		dataType: "json",
				 		data: { valor: id, id:beneficiario, campo:'<?php echo $values?>'},
				 		success: function(data){
				 			$( "#<?php echo $values;?>" ).attr('readonly', 'readonly');
				 		}
				 	});
				 });
				 	 
				 	//Here we leave
		<?php }
	
		foreach ($arreglo2 as $key2=>$values2){
			?>
				 $( "#<?php echo $values2;?>" ).change(function() {
				 	var id =$("#<?php echo $values2;?>").val();
				 	$.ajax({
				 		url: "<?php echo $urlActualizar?>",
				 		dataType: "json",
				 		data: { valor: id, id:beneficiario, campo:'<?php echo $values2?>'},
				 		success: function(data){
				 			$( "#<?php echo $values2;?>" ).attr('disable', 'disable');
				 		}
				 	});
				 });
		<?php }?>	
	
		var id = parseInt($("#<?php echo $this->campoSeguro('familiares')?>").val());
		
		function codificacionCamposSelect(id){
		
			$.ajax({
				url: "<?php echo $urlCodificacionCampos?>",
				dataType: "json",
				data: { valor: id},
				success: function(data){
					$("#" + data['tipo_documento']).select2({width:'100%'});
					$("#" + data['genero']).select2({width:'100%'});
					$("#" + data['nivel_estudio']).select2({width:'100%'});
					$("#" + data['genero']).select2({width:'100%'});
					$("#" + data['pertenencia_etnica']).select2({width:'100%'});
					$("#" + data['ocupacion']).select2({width:'100%'});
					$("#" + data['parentesco']).select2({width:'100%'});
				}
				
			});
		};
		
		for(i=0; i<id; i++){
			codificacionCamposSelect(i);
		}
		
		$(function() {
			$("#botonAgregar").click(function( event ) {	
				alert(id);
				if(id > 0){
					codificacionCampos(id);
				}else if(id==0){
					$('#div_1').show();
					$("#<?php echo $this->campoSeguro('identificacion_familiar_0')?>").attr("required", "true");
				}
				$('#botonEliminar').show();
				id++;	
				
				$("#<?php echo $this->campoSeguro('familiares')?>").val(id);
					
			});
		}); 
		
		$(function() {
			
			$("#botonEliminar").click(function( event ) {
				if(id > 1){
					$('#hogar #div_' + id).remove();
					id--;
					$("#<?php echo $this->campoSeguro('familiares')?>").val(id);
				}else if(id == 1){
					$('#div_' + id).hide();
					$('#botonEliminar').hide();
					$( '#div_' + (id) + ' a').html("Familiar");
					id--;
					
					$("#<?php echo $this->campoSeguro('tipo_documento_familiar_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('identificacion_familiar_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('nombre_familiar_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('primer_apellido_familiar_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('segundo_apellido_familiar_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('parentesco_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('genero_familiar_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('edad_familiar_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('nivel_estudio_familiar_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('grado_estudio_familiar_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('pertenencia_etnica_familiar_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('institucion_educativa_familiar_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('correo_familiar_0')?>").val("").change();
					$("#<?php echo $this->campoSeguro('ocupacion_familiar_0')?>").val("").change();
					
					$("#<?php echo $this->campoSeguro('identificacion_familiar_0')?>").removeAttr('required');
					$("#<?php echo $this->campoSeguro('nombre_familiar_0')?>").removeAttr('required');
					$("#<?php echo $this->campoSeguro('parentesco_0')?>").removeAttr('required');
					$("#<?php echo $this->campoSeguro('tipo_documento_familiar_0')?>").removeAttr('required');
					$("#<?php echo $this->campoSeguro('primer_apellido_familiar_0')?>").removeAttr('required');
					$("#<?php echo $this->campoSeguro('segundo_apellido_familiar_0')?>").removeAttr('required');
					$("#<?php echo $this->campoSeguro('celular_familiar_0')?>").removeAttr('required');
					
					
					$("#<?php echo $this->campoSeguro('familiares')?>").val(id);
				}
					
			});
		}); 	
				
				
		function codificacionCampos(id){
		
			$.ajax({
				url: "<?php echo $urlCodificacionCampos?>",
				dataType: "json",
				data: { valor: id},
				success: function(data){
					
						<!--Se remueven los select2 de los select para realizar una clonación efectiva del campo -->
						$($( '#div_' + id + ' :input')[0]).select2("destroy");
						$($( '#div_' + id + ' :input')[5]).select2("destroy");
						$($( '#div_' + id + ' :input')[6]).select2("destroy");
						$($( '#div_' + id + ' :input')[9]).select2("destroy");
						$($( '#div_' + id + ' :input')[13]).select2("destroy");
						$($( '#div_' + id + ' :input')[14]).select2("destroy");
						
						<!--Se clona el div -->
						
						$newClone = $('#div_' + id).clone(true);
						$newClone.attr("id",'div_' + (id + 1));
						$newClone.insertAfter($('#div_'+id));
						
						$('#div_'+id + ' img').remove( "#botonAgregar" );
						
						<!--Se restablecen los select2 -->
						$($( '#div_' + id + ' :input')[0]).select2({width:'100%'});
						$($( '#div_' + id + ' :input')[5]).select2({width:'100%'});
						$($( '#div_' + id + ' :input')[6]).select2({width:'100%'});
						$($( '#div_' + id + ' :input')[9]).select2({width:'100%'});
						$($( '#div_' + id + ' :input')[13]).select2({width:'100%'});
						$($( '#div_' + id + ' :input')[14]).select2({width:'100%'});
						
						<!--Se agregan los select2 a los campos clonados-->
						$($( '#div_' + (id + 1) + ' :input')[0]).select2({width:'100%'});
						$($( '#div_' + (id + 1) + ' :input')[5]).select2({width:'100%'});
						$($( '#div_' + (id + 1) + ' :input')[6]).select2({width:'100%'});
						$($( '#div_' + (id + 1) + ' :input')[9]).select2({width:'100%'});
						$($( '#div_' + (id + 1) + ' :input')[13]).select2({width:'100%'});
						$($( '#div_' + (id + 1) + ' :input')[14]).select2({width:'100%'});
						
						$($( '#div_' + (id + 1) + ' :input')[0]).attr('id', data.tipo_documento).val("");
						$($( '#div_' + (id + 1) + ' :input')[0]).attr('name', data.tipo_documento);
						
						$($( '#div_' + (id + 1) + ' :input')[1]).attr('id', data.identificacion).val("");
						$($( '#div_' + (id + 1) + ' :input')[1]).attr('name', data.identificacion);
						
						$($( '#div_' + (id + 1) + ' :input')[2]).attr('id', data.nombre).val("");;
						$($( '#div_' + (id + 1) + ' :input')[2]).attr('name', data.nombre);
						
						$($( '#div_' + (id + 1) + ' :input')[3]).attr('id', data.primer_apellido).val("");;
						$($( '#div_' + (id + 1) + ' :input')[3]).attr('name', data.primer_apellido);
						
						$($( '#div_' + (id + 1) + ' :input')[4]).attr('id', data.segundo_apellido).val("");;
						$($( '#div_' + (id + 1) + ' :input')[4]).attr('name', data.segundo_apellido);
						
						$($( '#div_' + (id + 1) + ' :input')[5]).attr('id', data.parentesco).val("").change();
						$($( '#div_' + (id + 1) + ' :input')[5]).attr('name', data.parentesco);
						
						$($( '#div_' + (id + 1) + ' :input')[6]).attr('id', data.genero).val("").change();
						$($( '#div_' + (id + 1) + ' :input')[6]).attr('name', data.genero);
						
						$($( '#div_' + (id + 1) + ' :input')[7]).attr('id', data.edad).val("");;
						$($( '#div_' + (id + 1) + ' :input')[7]).attr('name', data.edad);
						
						$($( '#div_' + (id + 1) + ' :input')[8]).attr('id', data.celular).val("");;
						$($( '#div_' + (id + 1) + ' :input')[8]).attr('name', data.celular);
						
						$($( '#div_' + (id + 1) + ' :input')[9]).attr('id', data.nivel_estudio).val("").change();
						$($( '#div_' + (id + 1) + ' :input')[9]).attr('name', data.nivel_estudio);
						
						$($( '#div_' + (id + 1) + ' :input')[10]).attr('id', data.grado);
						$($( '#div_' + (id + 1) + ' :input')[10]).attr('name', data.grado);
						
						$($( '#div_' + (id + 1) + ' :input')[11]).attr('id', data.institucion_educativa).val("");;
						$($( '#div_' + (id + 1) + ' :input')[11]).attr('name', data.institucion_educativa);
						
						$($( '#div_' + (id + 1) + ' :input')[12]).attr('id', data.correo).val("");;
						$($( '#div_' + (id + 1) + ' :input')[12]).attr('name', data.correo);
						
						$($( '#div_' + (id + 1) + ' :input')[13]).attr('id', data.pertenencia_etnica).val("").change();
						$($( '#div_' + (id + 1) + ' :input')[13]).attr('name', data.pertenencia_etnica);
						
						$($( '#div_' + (id + 1) + ' :input')[14]).attr('id', data.ocupacion).val("").change();
						$($( '#div_' + (id + 1) + ' :input')[14]).attr('name', data.ocupacion);
						
						$( '#div_' + (id + 1) + ' a').attr('href', '#familiar' + (id + 1)).change();
						$( '#div_' + (id + 1) + ' .panel-collapse').attr('id', 'familiar' + (id + 1)).change();
						$( '#div_' + (id + 1) + ' a').html("Familiar");
						
				}
				
			});
		};
		
					
		 
		function delRow() {
		// Funcion que destruye el elemento actual una vez echo el click
		$(this).parent('div').remove();
		 
		}
		
		if ($("#<?php echo $this->campoSeguro('mensajemodal')?>").length > 0 ){
			$("#myModalMensaje").modal('show');
		}
		
		$(function() {
				$("#regresarConsultar").click(function( event ) {	
			    	location.href = "<?php echo $enlaceReg;?>";
				});
		});
		
		$(function() {
			if($("#<?php echo $this->campoSeguro('familiares')?>").val() == 0){
				codificacionCamposSelect(id);
				$('#div_1').hide();
				$('#botonEliminar').hide();
				$("#<?php echo $this->campoSeguro('identificacion_familiar_0')?>").removeAttr('required');
			}
		});
			
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
						$("#<?php echo $this->campoSeguro('departamento');?>").val(urbanizacion[indice].departamento);
						$("#<?php echo $this->campoSeguro('municipio');?>").val(urbanizacion[indice].municipio);
					}
					
				});
				
			}
		
		});
		 
		urbanizacion();
		
			 $( "#<?php echo $this->campoSeguro('urbanizacion');?>" ).change(function() {
			 
			    var urb='';
			 	var dep='';
			 	var mun='';
			 	var abc='';
			 	
			 	urb =$("#<?php echo $this->campoSeguro('urbanizacion');?>").val();
			 	dep =$("#<?php echo $this->campoSeguro('departamento');?>").val();
			 	mun =$("#<?php echo $this->campoSeguro('municipio');?>").val();
			 	abc=$("#<?php echo $this->campoSeguro('id_urbanizacion');?>").val();
			 	
			 	$.ajax({
			 		url: "<?php echo $urlActualizarUrb?>",
			 		dataType: "json",
			 		data: {urba:urb, depa:dep, muni: mun, proy:abc,id:beneficiario, campo:'<?php echo $this->campoSeguro('urbanizacion')?>'},
			 		success: function(data){
			 			$( "#<?php echo $this->campoSeguro('urbanizacion');?>" ).attr('disable', 'disable');
			 		}
			 	});
			 	
			 });
			 
			  $( "#<?php echo $this->campoSeguro('identificacion_beneficiario');?>" ).change(function() {
			 
			    var urb='';
			 	
			 	urb =$("#<?php echo $this->campoSeguro('urbanizacion');?>").val();
			 	
			 	if(urb != ""){
				 	$.ajax({
				 		url: "<?php echo $urlActualizarNomHog?>",
				 		dataType: "json",
				 		data: {urba:urb},
				 		success: function(data){
				 			$( "#<?php echo $this->campoSeguro('nomenclatura');?>" ).val(data.abr_mun + '_' + data.abr_urb + '_' + $( "#<?php echo $this->campoSeguro('identificacion_beneficiario');?>" ).val()).change();
				 			$( "#<?php echo $this->campoSeguro('consecutivo');?>" ).val(data.abr_benf);
				 			
				 		}
				 	});
				 }
			 });
	
		$("#mensaje").modal("show");
	  
	  $("#<?php echo $this->campoSeguro('identificacion_beneficiario');?>").change(function() {
		
				 	var id =$("#<?php echo $this->campoSeguro('identificacion_beneficiario');?>").val();
				 	 			
				 	$.ajax({
				 		url: "<?php echo $urlConsultarExistencia?>",
				 		dataType: "json",
				 		data: { valor: id},
				 		success: function(data){
				 		if(data!=false){
				            $("#modalVerificar").modal('show');
				 			$( "#<?php echo $this->campoSeguro('identificacion_beneficiario');?>" ).val('');
				 		}}
				 	});
		
		});
	 
	});
  
  }