//Deshabilitar el comportamiento predeterminado de los botones 

$("#<?php echo $this->campoSeguro("proyecto")?>").keydown(function(e){
    e.preventDefault();
});

$("#<?php echo $this->campoSeguro("actividad")?>").keydown(function(e){
    e.preventDefault();
});

$("#<?php echo $this->campoSeguro("geomodal")?>").keydown(function(e){
    e.preventDefault();
});

$("#<?php echo $this->campoSeguro("porcentajecons")?>").keydown(function(e){
    e.preventDefault();
});

$("#<?php echo $this->campoSeguro("geolocalizacion")?>").keydown(function(e){
    e.preventDefault();
});

$(function() {
	$("#<?php echo $this->campoSeguro("geolocalizacion")?>").focus(function() {
        $("#myModal").modal("show");
    });
});

$("#formmyModalBootstrap").submit(function(e){
    e.preventDefault();
    $("#<?php echo $this->campoSeguro("geolocalizacion")?>").val( $("#geomodal").val());
	$("#<?php echo $this->campoSeguro("geolocalizacion")?>").change();
	$('#myModal').modal('hide');
  });
