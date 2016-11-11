$(document).ready(function() {
	$("#<?php echo $this->campoSeguro('tipo_agendamiento')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('comisionador')?>").select2({width:'100%'});
    $("#<?php echo $this->campoSeguro('comisionador_nuevo')?>").select2({width:'100%'});
	
});