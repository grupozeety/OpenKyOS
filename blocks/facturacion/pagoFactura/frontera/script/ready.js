/**
 * Código JavaScript del Bloque
 */

$("#mensajeModal").modal("show");

$("#<?php echo $this->campoSeguro('valor_recibido');?>").change(function() {
	
	alert("3434");
	$("#<?php echo $this->campoSeguro('valor_recibido');?>").val();

});
