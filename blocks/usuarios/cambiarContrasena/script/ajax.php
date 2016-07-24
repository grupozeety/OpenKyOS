<?php

?>

ocultar();

$("#<?php echo $this->campoSeguro('rol')?>").change(function() {
	if($("#<?php echo $this->campoSeguro('rol')?>").val() == ''){
		$("#datosBasicos").css('display','none');
	}else if($("#<?php echo $this->campoSeguro('rol')?>").val() == '3'){
		ocultar();
		$("#datosBasicos").css('display','block');
	}else if($("#<?php echo $this->campoSeguro('rol')?>").val() == '4'){
		ocultar();
	}else if($("#<?php echo $this->campoSeguro('rol')?>").val() == '5'){
		ocultar();
	}
});

function ocultar(){
	$("#datosBasicos").css('display','none');
}