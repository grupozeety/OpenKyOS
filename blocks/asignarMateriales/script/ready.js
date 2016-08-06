
$("#<?php echo $this->campoSeguro('proyecto')?>").select2({width:'100%'});
$("#<?php echo $this->campoSeguro('actividad')?>").select2({width:'100%'});
$("#<?php echo $this->campoSeguro('identBeneficiario')?>").select2({width:'100%'});
$("#<?php echo $this->campoSeguro('material')?>").select2({width:'100%'});
$("#<?php echo $this->campoSeguro('unidad')?>").select2({width:'100%'});

var i=1;

$("#<?php echo $this->campoSeguro('botonAgregar')?>").click(function(){

	$("#addr0").html('');
	
	 
	var material = $("#<?php echo $this->campoSeguro('material')?> option:selected").text();
	var unidad = $("#<?php echo $this->campoSeguro('unidad')?> option:selected").text();
	var cantidad = $("#<?php echo $this->campoSeguro('cantidad')?>").val();
	
	var a = "<?php echo $a; ?>";
	alert(a);
	
	$('#addr'+ i).html("<td>" + "<input type='checkbox' id='checkbox"+(i)+"'>" +"</td>" + "<td>" + '<input type="hidden" name="'+ a + '" value="' + material + '">' + "</td><td>"+unidad+"</td><td>"+cantidad+"</td>");
	$('#tabla1').append('<tr id="addr'+(i+1)+'"></tr>');
 
	$("#<?php echo $this->campoSeguro('material')?>").val(''); 
	$("#<?php echo $this->campoSeguro('material')?>").change();
	$("#<?php echo $this->campoSeguro('unidad')?>").val('');
	$("#<?php echo $this->campoSeguro('unidad')?>").change();
	$("#<?php echo $this->campoSeguro('cantidad')?>").val("1");
	
	$('#myModal').modal('hide');
	
	i++;
 
});

$("#remove").click(function(){
    for(j=0; j<i; j++){
    	if( $('#checkbox' + (j)).prop('checked') ) {
    		$("#addr"+(j)).remove();
    	}
    }
});

