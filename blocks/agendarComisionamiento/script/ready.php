$(document).ready(function() {

	$("#<?php echo $this->campoSeguro('tipo_agendamiento')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('tipo_tecnologia')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('tipo_vivienda')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('comisionador')?>").select2({width:'100%'});
	
});