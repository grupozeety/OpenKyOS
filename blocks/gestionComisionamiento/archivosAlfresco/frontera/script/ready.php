$(document).ready(function() {

	$("#<?php echo $this->campoSeguro('carpeta')?>").select2({width:'100%'});

	
$("#mensaje").modal("show");
});