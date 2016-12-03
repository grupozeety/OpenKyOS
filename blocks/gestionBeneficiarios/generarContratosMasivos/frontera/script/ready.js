		/**
		 * Código JavaScript del Bloque
		 */




		     $("#seleccion_proceso").change(function() {
		     	

		     	$("#<?php echo $this->campoSeguro('proceso');?>").val($("#<?php echo $this->campoSeguro('seleccion_proceso');?>").val());

				switch ($("#seleccion_proceso").val()) {
					case '1':
								$("#validacion").css("display", "block");
								$("#cargue").css("display", "none");
						break;

					case '2':
						$("#validacion").css("display", "none");
								$("#cargue").css("display", "block");
						break;
					
				}
		   });


