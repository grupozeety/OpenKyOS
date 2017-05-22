
 $('#infoAbono').hide();

 
 var anterior= parseFloat($('#<?php echo $this->campoSeguro('valor_recibido');?>').val());
 var n=0;
$("#<?php echo $this->campoSeguro('abono');?>").click(function() {

        if (this.checked) {
           $('#infoAbono').show();
        }else {
            $('#<?php echo $this->campoSeguro('valor_abono');?>').val(0);
            $('#<?php echo $this->campoSeguro('valor_recibido');?>').val(anterior);
            $('#infoAbono').hide();
        }
         
    });
    
    $("#<?php echo $this->campoSeguro('valor_abono');?>").change(function() {
    a=parseFloat($('#<?php echo $this->campoSeguro('valor_abono');?>').val());
    n=parseFloat($('#<?php echo $this->campoSeguro('valor_abono');?>').val())+anterior;
            $('#<?php echo $this->campoSeguro('valor_recibido');?>').val(n);
            $("#<?php echo $this->campoSeguro('botonPagar');?>").prop('disabled', false);
         });

    $("#<?php echo $this->campoSeguro('valor_recibido');?>").change(function() {
            var r=parseFloat($('#<?php echo $this->campoSeguro('valor_recibido');?>').val());
            if(r < anterior){
           $('#<?php echo $this->campoSeguro('valor_recibido');?>').val(anterior+a);
            }
         });


        $("#<?php echo $this->campoSeguro('botonPagar');?>").click(function() {
        if($("#<?php echo $this->campoSeguro('medio_pago');?>").val()!=''){
           $("#<?php echo $this->campoSeguro('botonPagar');?>").prop('disabled', true);
           }
        });