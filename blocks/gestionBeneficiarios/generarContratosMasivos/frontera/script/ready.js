		/**
		 * Código JavaScript del Bloque
		 */

	$("#mensajeModal").modal("show");

	$("#<?php echo $this->campoSeguro('seleccion_proceso');?>").change(function() {
		     	

		     	$("#<?php echo $this->campoSeguro('proceso');?>").val($("#<?php echo $this->campoSeguro('seleccion_proceso');?>").val());

				switch ($("#<?php echo $this->campoSeguro('seleccion_proceso');?>").val()) {
					case '1':
								$("#validacion").css("display", "block");
								$("#cargue").css("display", "none");
								$("#consulta").css("display", "none");
						break;

					case '2':
						$("#validacion").css("display", "none");
						$("#cargue").css("display", "block");
						$("#consulta").css("display", "none");
						break;
					

					case '3':
						$("#validacion").css("display", "none");
						$("#cargue").css("display", "none");
						$("#consulta").css("display", "block");
						break;
				}
		   });


