//Deshabilitar el comportamiento predeterminado de los botones 

$("#<?php echo $this->campoSeguro('proyecto')?>").keydown(function(e){
    e.preventDefault();
});

$("#<?php echo $this->campoSeguro('actividad')?>").keydown(function(e){
    e.preventDefault();
});

