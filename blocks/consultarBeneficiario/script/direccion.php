<?php /**
 * Código Correspondiente a las Url de la peticiones Ajax.
 */
echo "";
?>




<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */

 	function eliminarRequiredBeneficiario() {


 	$("#AgrupacionBeneficiario").find("input").removeAttr("required");

 	}

     $("#<?php echo $this->campoSeguro('direccion');?>").keyup(function() {
			$("#<?php echo $this->campoSeguro('direccion');?>").val('');
		   });


     $("#<?php echo $this->campoSeguro('tipo_via_ng');?>").change(function() {
			if($("#<?php echo $this->campoSeguro('tipo_via_ng');?>").val()!=''){

eliminarRequiredBeneficiario();
		     	$("#<?php echo $this->campoSeguro('direccion');?>").val($("#<?php echo $this->campoSeguro('direccion');?>").val()+$("#<?php echo $this->campoSeguro('tipo_via_ng');?>").val());

		     	}


		   });


     $("#<?php echo $this->campoSeguro('numero_pr');?>").change(function() {
			if($("#<?php echo $this->campoSeguro('numero_pr');?>").val()!=''){
eliminarRequiredBeneficiario();

		     	$("#<?php echo $this->campoSeguro('direccion');?>").val($("#<?php echo $this->campoSeguro('direccion');?>").val()+" "+$("#<?php echo $this->campoSeguro('numero_pr');?>").val());

		     	}


		   });



     $("#<?php echo $this->campoSeguro('bis_pr');?>").change(function() {
			if($("#<?php echo $this->campoSeguro('bis_pr');?>").val()!=''){
eliminarRequiredBeneficiario();

		     	$("#<?php echo $this->campoSeguro('direccion');?>").val($("#<?php echo $this->campoSeguro('direccion');?>").val()+" "+$("#<?php echo $this->campoSeguro('bis_pr');?>").val());

		     	}


		   });


          $("#<?php echo $this->campoSeguro('letra_pr');?>").change(function() {
			if($("#<?php echo $this->campoSeguro('letra_pr');?>").val()!=''){
eliminarRequiredBeneficiario();

		     	$("#<?php echo $this->campoSeguro('direccion');?>").val($("#<?php echo $this->campoSeguro('direccion');?>").val()+""+$("#<?php echo $this->campoSeguro('letra_pr');?>").val());

		     	}


		   });

          $("#<?php echo $this->campoSeguro('cuadrante_pr');?>").change(function() {
			if($("#<?php echo $this->campoSeguro('cuadrante_pr');?>").val()!=''){
eliminarRequiredBeneficiario();

		     	$("#<?php echo $this->campoSeguro('direccion');?>").val($("#<?php echo $this->campoSeguro('direccion');?>").val()+" "+$("#<?php echo $this->campoSeguro('cuadrante_pr');?>").val());

		     	}


		   });


                 $("#<?php echo $this->campoSeguro('numero_vg');?>").change(function() {
			if($("#<?php echo $this->campoSeguro('numero_vg');?>").val()!=''){
eliminarRequiredBeneficiario();

		     	$("#<?php echo $this->campoSeguro('direccion');?>").val($("#<?php echo $this->campoSeguro('direccion');?>").val()+" N#"+$("#<?php echo $this->campoSeguro('numero_vg');?>").val());

		     	}


		   });



             $("#<?php echo $this->campoSeguro('letra_vg');?>").change(function() {
			if($("#<?php echo $this->campoSeguro('letra_vg');?>").val()!=''){
eliminarRequiredBeneficiario();

		     	$("#<?php echo $this->campoSeguro('direccion');?>").val($("#<?php echo $this->campoSeguro('direccion');?>").val()+""+$("#<?php echo $this->campoSeguro('letra_vg');?>").val());

		     	}


		   });


           $("#<?php echo $this->campoSeguro('placa_vg');?>").change(function() {
			if($("#<?php echo $this->campoSeguro('placa_vg');?>").val()!=''){
eliminarRequiredBeneficiario();

		     	$("#<?php echo $this->campoSeguro('direccion');?>").val($("#<?php echo $this->campoSeguro('direccion');?>").val()+"-"+$("#<?php echo $this->campoSeguro('placa_vg');?>").val());

		     	}


		   });




           $("#<?php echo $this->campoSeguro('cuadrante_vg');?>").change(function() {
			if($("#<?php echo $this->campoSeguro('cuadrante_vg');?>").val()!=''){
eliminarRequiredBeneficiario();

		     	$("#<?php echo $this->campoSeguro('direccion');?>").val($("#<?php echo $this->campoSeguro('direccion');?>").val()+" "+$("#<?php echo $this->campoSeguro('cuadrante_vg');?>").val());

		     	}


		   });


</script>

