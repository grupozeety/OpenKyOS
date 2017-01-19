		/**
		 * Código JavaScript del Bloque
    **/

$("#<?php echo $this->campoSeguro('proceso');?>").change(function() {
		     	
		     	switch ($("#<?php echo $this->campoSeguro('proceso');?>").val()) {
					case '1':
								$("#consulta_reporte").css("display", "block");
								$("#consulta_proceso").css("display", "none");
								
						break;

					case '2':
							$("#consulta_reporte").css("display", "none");
						    $("#consulta_proceso").css("display", "block");
						break;
			

				}
		   });







