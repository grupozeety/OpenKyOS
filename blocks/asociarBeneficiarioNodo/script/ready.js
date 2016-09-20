//Deshabilitar el comportamiento predeterminado de los botones 
$(function() {
	$("button").button().click(function(event) {
		event.preventDefault();
	});
});

function removeRequiredNodo(){
	
	$("#<?php echo $this->campoSeguro('mac_master_eoc');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('ip_master_eoc');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('mac_onu_eoc');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('ip_onu_eoc');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('mac_hub_eoc');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('ip_hub_eoc');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('mac_cpe_eoc');?>").removeAttr('required');
}

function removeRequiredCelda(){
	
	$("#<?php echo $this->campoSeguro('ip_celda');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('mac_celda');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('nombre_nodo');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('nombre_sectorial');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('ip_switch_celda');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('mac_sm_celda');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('ip_sm_celda');?>").removeAttr('required');
	$("#<?php echo $this->campoSeguro('mac_cpe_celda');?>").removeAttr('required');
	
}

function clearNodo(){
	
	$("#<?php echo $this->campoSeguro('mac_master_eoc');?>").val("").change();
	$("#<?php echo $this->campoSeguro('ip_master_eoc');?>").val("").change();
	$("#<?php echo $this->campoSeguro('mac_onu_eoc');?>").val("").change();
	$("#<?php echo $this->campoSeguro('ip_onu_eoc');?>").val("").change();
	$("#<?php echo $this->campoSeguro('mac_hub_eoc');?>").val("").change();
	$("#<?php echo $this->campoSeguro('ip_hub_eoc');?>").val("").change();
	$("#<?php echo $this->campoSeguro('mac_cpe_eoc');?>").val("").change();
}

function clearCelda(){
	
	$("#<?php echo $this->campoSeguro('ip_celda');?>").val("").change();
	$("#<?php echo $this->campoSeguro('mac_celda');?>").val("").change();
	$("#<?php echo $this->campoSeguro('nombre_nodo');?>").val("").change();
	$("#<?php echo $this->campoSeguro('nombre_sectorial');?>").val("").change();
	$("#<?php echo $this->campoSeguro('ip_switch_celda');?>").val("").change();
	$("#<?php echo $this->campoSeguro('mac_sm_celda');?>").val("").change();
	$("#<?php echo $this->campoSeguro('ip_sm_celda');?>").val("").change();
	$("#<?php echo $this->campoSeguro('mac_cpe_celda');?>").val("").change();
}

function addRequiredNodo(){
	
	$("#<?php echo $this->campoSeguro('mac_master_eoc');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('ip_master_eoc');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('mac_onu_eoc');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('ip_onu_eoc');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('mac_hub_eoc');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('ip_hub_eoc');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('mac_cpe_eoc');?>").attr("required", "true");
}

function addRequiredCelda(){
	
	$("#<?php echo $this->campoSeguro('ip_celda');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('mac_celda');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('nombre_nodo');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('nombre_sectorial');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('ip_switch_celda');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('mac_sm_celda');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('ip_sm_celda');?>").attr("required", "true");
	$("#<?php echo $this->campoSeguro('mac_cpe_celda');?>").attr("required", "true");
	
}

function validarTipoTecnologia(){
	
	if($("#<?php echo $this->campoSeguro('tipo_tecnologia');?>").val() == "1"){
			
			$("#infoCelda").show();
			$("#infoNodo").hide();
			removeRequiredNodo();
			clearNodo();
			addRequiredCelda();
			
		}else if($("#<?php echo $this->campoSeguro('tipo_tecnologia');?>").val() == "2"){
			
			$("#infoCelda").hide();
			$("#infoNodo").show();
			removeRequiredCelda();
			clearCelda();
			addRequiredNodo();
			
		}else if($("#<?php echo $this->campoSeguro('tipo_tecnologia');?>").val() == ""){
			
			$("#infoCelda").hide();
			$("#infoNodo").hide();
			addRequiredNodo();
			addRequiredCelda();
			clearNodo();
			clearCelda();
			
		}
}

validarTipoTecnologia();

$("#<?php echo $this->campoSeguro('tipo_tecnologia');?>").change(function() {

	validarTipoTecnologia();
	
});


