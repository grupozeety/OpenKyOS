		/**
		 * Código JavaScript del Bloque
		 */


		 $("#<?php echo $this->campoSeguro('seleccion_proceso');?>").change(function() {
		     	
		     	switch ($("#<?php echo $this->campoSeguro('seleccion_proceso');?>").val()) {
					case '1':
								$("#generar_facturacion").css("display", "block");
								$("#consulta").css("display", "none");
								
						break;

					case '2':
							$("#generar_facturacion").css("display", "none");
						    $("#consulta").css("display", "block");
						break;
			

				}
		   });
