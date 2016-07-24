$( document ).ready(function() {

	asignarPuntaje();

	$("#<?php echo $this->campoSeguro('contexto')?>").change(function(){
		asignarPuntaje();
	});
	
	$("#<?php echo $this->campoSeguro('categoria')?>").change(function(){
		asignarPuntaje();
	});
	
	function asignarPuntaje(){
		var contexto = $("#<?php echo $this->campoSeguro('contexto')?>").val();
		var categoria = $("#<?php echo $this->campoSeguro('categoria')?>").val();
		if(contexto == 1){
			$("#<?php echo $this->campoSeguro('puntaje')?>").attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all   validate[required, custom[number],min[0.1],max[3.6]]");
		}else if(contexto==2){
			$("#<?php echo $this->campoSeguro('puntaje')?>").attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all   validate[required, custom[number],min[0.1]");
			if(categoria == 4){
				$("#<?php echo $this->campoSeguro('puntaje')?>").attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all   validate[required, custom[number],min[0.1],max[4.5]]");
			}else if(categoria == 5){
				$("#<?php echo $this->campoSeguro('puntaje')?>").attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all   validate[required, custom[number],min[0.1],max[3.6]]");
			}else if(categoria == 6){
				$("#<?php echo $this->campoSeguro('puntaje')?>").attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all   validate[required, custom[number],min[0.1],max[2.4]]");
			}else if(categoria == 7){
				$("#<?php echo $this->campoSeguro('puntaje')?>").attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all   validate[required, custom[number],min[0.1],max[0.9]]");
			}
		}
	}
});
