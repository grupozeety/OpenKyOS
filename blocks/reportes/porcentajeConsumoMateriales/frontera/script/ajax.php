<?php
/**
 * Código Correspondiente a las Url de la peticiones Ajax.
 */
// URL base
$url = $this->miConfigurador->getVariableConfiguracion("host");
$url .= $this->miConfigurador->getVariableConfiguracion("site");
$url .= "/index.php?";
// Variables
$variable = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$variable .= "&procesarAjax=true";
$variable .= "&action=index.php";
$variable .= "&bloqueNombre=" . "llamarApi";
$variable .= "&bloqueGrupo=" . "";
$variable .= "&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

// URL definitiva
$urlApi = $url . $cadena;
?>

<?php
                     /**
 *
 * Los datos del bloque se encuentran en el arreglo $esteBloque.
 */

// URL base
$url = $this->miConfigurador->getVariableConfiguracion("host");
$url .= $this->miConfigurador->getVariableConfiguracion("site");
$url .= "/index.php?";
// Variables
$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$valor .= "&procesarAjax=true";
$valor .= "&action=index.php";
$valor .= "&bloqueNombre=" . $esteBloque["nombre"];
$valor .= "&bloqueGrupo=" . $esteBloque["grupo"];
$valor .= "&tiempo=" . $_REQUEST['tiempo'];
$valor .= "&funcion=" . "obtenerProyectos";
$valor .= "&opcion=" . "";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valor, $enlace);

// URL definitiva
$urlProyectoConsumoRegistrado = $url . $cadena;
?>

<script type='text/javascript'>

/**
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */

$( document ).ready(function() {

	$("#<?php echo $this->campoSeguro('proyecto');?>").select2({width:'100%'});
	
	function consultarProyectos(){
		 
		$("#<?php echo $this->campoSeguro('proyecto');?>").html('');
		$("<option value=''>Seleccione .....</option>").appendTo("#<?php echo $this->campoSeguro('proyecto');?>");
	
		var dataProjectsERP = [];
		var dataProjectsOK = [];
		var projects = [];
		var contador = 0;
		
		$.ajax({
			url: "<?php echo $urlApi;?>",
			dataType: "json",
			data: { metodo:'obtenerProyecto'},
			success: function(data){
	
				dataProjectsERP = unique(data);
	
				$.ajax({
					url: "<?php echo $urlProyectoConsumoRegistrado;?>",
					dataType: "json",
					data: { metodo:'obtenerProyecto'},
					success: function(data){
	
						dataProjectsOK = unique(data);
	
						$.each(dataProjectsERP , function(indice1,valor1){
	
							$.each(dataProjectsOK , function(indice2,valor2){
	
								if(dataProjectsOK[indice2].proyecto == dataProjectsERP[indice1].project){
	
									projects.push(dataProjectsOK[indice2].proyecto);
									$("<option value='" + dataProjectsOK[ indice2 ].proyecto + "'>" + dataProjectsOK[ indice2 ].proyecto + "</option>").appendTo("#<?php echo $this->campoSeguro('proyecto');?>");
									contador = contador + 1;
									
								}
							
							});
							
						});
	
						if (contador > 0){
			
							$("#botones").css('display','block');
							
						}
	
						if (contador > 1){
						
							$("<option value='" + "Todos los Proyectos" + "'>" + "Todos los Proyectos" + "</option>").appendTo("#<?php echo $this->campoSeguro('proyecto');?>");
							$("#<?php echo $this->campoSeguro('elementos');?>").val(JSON.stringify(projects));
							
						}
						
					}
	
				});
	
			}
	
		});
	
	};
	
	$(function() {
	
		$("#<?php echo $this->campoSeguro('proyecto');?>").ready(function() {
	
			$("#botones").css('display','none');
	    	consultarProyectos();
	
	    });
	
	 });
	
	function unique(obj){
	
	    var uniques=[];
	    var stringify={};
	    
	    for(var i=0;i<obj.length;i++){
	
	    	var keys=Object.keys(obj[i]);
	       	keys.sort(function(a,b) {return a-b});
	       	var str='';
	
	        for(var j=0;j<keys.length;j++){
	           str+= JSON.stringify(keys[j]);
	           str+= JSON.stringify(obj[i][keys[j]]);
	        }
	        
	        if(!stringify.hasOwnProperty(str)){
	            uniques.push(obj[i]);
	            stringify[str]=true;
	        }
	    }
	    
	    return uniques;
	}

});

</script>
