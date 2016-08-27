$( document ).ready(function() {
	
	<?php 
			if(!isset($_REQUEST['numeroEstudiantes'])){
				$_REQUEST['numeroEstudiantes'] = 1;
			}
	?>;
		
	var estudianteRequerido = 1;
	var estudiante = 3;
	var numEstudiante = <?php echo $_REQUEST['numeroEstudiantes'];?>;
	var indice = 0;
	
	for(var i = estudiante; i > estudianteRequerido; i--){
		$("#marcoEstudiante" + i).hide();
	}
	
	for(var i = 1; i<= numEstudiante; i++){
		$("#marcoEstudiante" + i).show();
	}
	
	$("#botonEliminar1").click(function( event ) { eliminar1(); });
	$("#botonEliminar2").click(function( event ) { eliminar2(); });
	$("#botonEliminar3").click(function( event ) { eliminar3(); });
	
	function eliminar1()  {
		if(numEstudiante == estudianteRequerido){
			$('#<?php echo $this->campoSeguro('nombreEstudiante1')?>').val("");
			alert("Debe haber por lo menos un Estudiante");
	
		}else{
			confirmar=confirm("¿Desea eliminar a esté Estudiante?");  
		    if (confirmar){
		       	<?php for($i=1; $i<=3; $i++):?>      
		       		$('#<?php echo $this->campoSeguro('nombreEstudiante'.$i)?>').val($('#<?php echo $this->campoSeguro('nombreEstudiante'.($i+1))?>').val());
		       		$('#<?php echo $this->campoSeguro('codigoEstudiante'.$i)?>').val($('#<?php echo $this->campoSeguro('codigoEstudiante'.($i+1))?>').val());
		            <?php if($i==3): ?>
		           		$('#<?php echo $this->campoSeguro('nombreEstudiante'.$i)?>').val("");
		           		$('#<?php echo $this->campoSeguro('codigoEstudiante'.$i)?>').val("");
		            <?php endif; ?>
		        <?php endfor; ?>
		           
				numEstudiante--;
					
				for(var i = estudiante; i > numEstudiante; i--){
		       		$("#marcoEstudiante" + i).hide();
		       	}
		    }
		}
	}
	
	function eliminar2()  {
		if(numEstudiante == estudianteRequerido){
			$('#<?php echo $this->campoSeguro('nombreEstudiante2')?>').val("");
		}else{
			confirmar=confirm("¿Desea eliminar a esté Estudiante?");  
		    if (confirmar){
		       	<?php for($i=2; $i<=3; $i++):?>        	 
		       		$('#<?php echo $this->campoSeguro('nombreEstudiante'.$i)?>').val($('#<?php echo $this->campoSeguro('nombreEstudiante'.($i+1))?>').val());
		       		$('#<?php echo $this->campoSeguro('codigoEstudiante'.$i)?>').val($('#<?php echo $this->campoSeguro('codigoEstudiante'.($i+1))?>').val());
		            <?php if($i==3): ?>
		           		$('#<?php echo $this->campoSeguro('nombreEstudiante'.$i)?>').val("");
		           		$('#<?php echo $this->campoSeguro('codigoEstudiante'.$i)?>').val("");
		            <?php endif; ?>
		        <?php endfor; ?>
		           
				numEstudiante--;
					
				for(var i = estudiante; i > numEstudiante; i--){
		       		$("#marcoEstudiante" + i).hide();
		       	}
		    }
		}
	}
	
	function eliminar3()  {
		if(numEstudiante == estudianteRequerido){
			$('#<?php echo $this->campoSeguro('nombreEstudiante3')?>').val("");
		}else{
			confirmar=confirm("¿Desea eliminar a esté Estudiante?");  
		    if (confirmar){
		       	<?php for($i=3; $i<=3; $i++):?>        	 
		            <?php if($i==3): ?>
		           		$('#<?php echo $this->campoSeguro('nombreEstudiante'.$i)?>').val("");
		           		$('#<?php echo $this->campoSeguro('codigoEstudiante'.$i)?>').val("");
		            <?php endif; ?>
		        <?php endfor; ?>
		           
				numEstudiante--;
					
				for(var i = estudiante; i > numEstudiante; i--){
		       		$("#marcoEstudiante" + i).hide();
		       	}
		    }
		}
	}
					
	$(function() {
		$("#botonAgregar").click(function( event ) {		
			if(numEstudiante < 3){
				numEstudiante++;
	 			$("#marcoEstudiante"+ numEstudiante).show(); 			
			}
		});
	}); 	
				
});

String.prototype.insertAt=function(index, string) { 
	  return this.substr(0, index) + string + this.substr(index);
	}

var idDatosEstudiantes = [
                          <?php for($i=1; $i<=3; $i++):?> 
                       	 '<?php echo $this->campoSeguro('nombreEstudiante'.$i)?>',
                       	 '<?php echo $this->campoSeguro('codigoEstudiante'.$i)?>',
                          <?php endfor; ?>
                       ];
//Agrega required a todos los campos de los estudiantes
$.each(idDatosEstudiantes,function (i,v){
	var obj = $("#"+v);
	if(obj.length>0){
		var clases = obj.attr('class').split(' ');
		var claseValidate = $.grep(clases,function(v,i){return v.indexOf('validate')>-1})[0];
		obj.removeClass(claseValidate);
		claseValidate = claseValidate.insertAt(claseValidate.indexOf("[")+1,'required,');
		obj.addClass(claseValidate);
	}
});