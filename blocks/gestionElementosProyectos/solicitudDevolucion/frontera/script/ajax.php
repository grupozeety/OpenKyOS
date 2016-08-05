<?php
/** 
 * Código Correspondiente a las Url de la peticiones Ajax.
 */

// URL base
$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
$url .= "/index.php?";

// Variables para Con
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar .= "&funcion=consultarProyectos";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );

// URL Consultar Proyectos
$urlConsultarProyectos = $url . $cadena;

// Variables
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar .= "&funcion=consultarActividades";

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );

// URL Consultar Actividades
$urlConsultarActividades = $url . $cadena;

?>


<script language="JavaScript" type='text/javascript'>

/** 
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */




	 $( "#<?php echo $this->campoSeguro('proyecto')?>" ).change(function() {



		   	if($("#<?php echo $this->campoSeguro('proyecto') ?>").val()==''){
		   		$("#id_proyecto").val('');
		       	
		       	}
		   
		   	
		           });
		   
		   $("#<?php echo $this->campoSeguro('proyecto') ?>").autocomplete({
		   	minChars: 3,
		   	serviceUrl: '<?php echo $urlConsultarProyectos ?>',
		   	onSelect: function (suggestion) {
		   		
		   	        $("#id_proyecto").val(suggestion.data);

		   	 	$("#marcoTabla").html("<center><table id='tabla_elementos_actividades' class='scroll'></table></center>");

		   	 $("#tabla_elementos_actividades").jqGrid({
		         url:	'<?php echo $urlConsultarActividades ?>&asd='+suggestion.data,
		         datatype: "json",
		         mtype: "GET",
		         styleUI : "Bootstrap",
		         colModel: [
		  		{
		  				label: 'Id Actividad',
		  		        name: 'id',
		  		        width: 5,
		  				key: true,
		  				editable: false,
		  				sorttype:'number',
		  				editrules : {required: true}
		  		 },
		             {
		  					label: 'Asunto o Descripción de la Actividad',
		                 name: 'descripcion',
		                 width: 40,
		  					editable: true,
		  					sorttype:'text',
		  					
		             }
		                                  
		             ],
		       	sortname: 'id',
		  			sortorder : 'asc',
		  			viewrecords: true,
		  			rownumbers: false,
		  			loadonce : false,
		        rowNum: 100, 
		        width:$("#marcoTabla").width() - 5,
		  		responsive: true,
		        pager: "#barra_herramientas",
		        caption: "Actividades"

		     });   

		   	 $("#tabla_elementos_actividades").trigger("reloadGrid"); 

		   	 	

		   	}
		               
		   });


	





   
 
	
	 



</script>

