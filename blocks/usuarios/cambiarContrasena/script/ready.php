$("#cambiarContrasena").validationEngine({
	promptPosition : "bottomRight:-150", 
	scroll: false,
	autoHidePrompt: true,
	autoHideDelay: 2000
});

$("#cambiarContrasena").submit(function() {
	$resultado=$("#cambiarContrasena").validationEngine("validate");
	if ($resultado) {
		return true;
	}
	return false;
});

$("button").button().click(function (event) { 
    event.preventDefault();
});

// Asociar el widget de validación al formulario

/////////Se define el ancho de los campos de listas desplegables///////
$('#<?php echo $this->campoSeguro('campo')?>').width(370);      

//////////////////**********Se definen los campos que requieren campos de select2**********////////////////
$("#<?php echo $this->campoSeguro('campo')?>").select2();

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
