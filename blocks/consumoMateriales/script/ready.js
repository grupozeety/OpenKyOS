//Deshabilitar el comportamiento predeterminado de los botones 

$(function() {
	$("button").button().click(function(event) {
		event.preventDefault();
});
});

$("#<?php echo $this->campoSeguro('proyecto')?>").keydown(function(e){
    e.preventDefault();
});

$("#<?php echo $this->campoSeguro('actividad')?>").keydown(function(e){
    e.preventDefault();
});

