function cargarPHP(){
	$("#<?php echo $this->campoSeguro('tipo_beneficiario')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('genero_beneficiario')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('nivel_estudio')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('tipo_vivienda')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('urbanizacion')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('territorio')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('estrato')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('jefe_hogar')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('pertenencia_etnica')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('ocupacion')?>").select2({width:'100%'});
	$("#<?php echo $this->campoSeguro('tipo_documento')?>").select2({width:'100%'});
}

function cargar(){
	cargarAjax();
	cargarJS();
	cargarPHP();
}

window.onload = cargar();