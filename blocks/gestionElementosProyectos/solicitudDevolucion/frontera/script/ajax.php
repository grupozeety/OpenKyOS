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

		   	 	$("#marcoTabla").html("<center><table id='tabla_elementos_actividades' class='scroll'></table><div id='barra_herramientas' class='scroll'></div></center>");

		   	 $("#tabla_elementos_actividades").jqGrid({
		         url:	'<?php echo $urlConsultarActividades ?>&Proyecto='+suggestion.data,
		         datatype: "json",
		         mtype: "GET",
		         styleUI : "Bootstrap",
		         colModel: [
		  		{
		  				label: 'Id Actividad',
		  		        name: 'id',
		  		        width: 5,
		  		        align: 'center',
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
		        rowNum: 10, 
		        height: 500,
		        width:$("#marcoTabla").width() - 5,
		  		responsive: true,
		        pager: "#barra_herramientas",
		        caption: "Actividades",
		    	subGrid: true,
                subGridOptions: {
                    "plusicon"  : "ui-icon-triangle-1-e",
                    "minusicon" : "ui-icon-triangle-1-s",
                    "openicon"  : "ui-icon-arrowreturn-1-e",
            		// load the subgrid data only once
            		// and the just show/hide
            		"reloadOnExpand" : true,
            		// select the row when the expand column is clicked
            		"selectOnExpand" : true
            	},
           	subGridRowExpanded: function(subgrid_id, row_id) {

               	var ident= row_id;
           		var subgrid_table_id, pager_id;
           		subgrid_table_id = subgrid_id+"_t";
           		pager_id = "p_"+subgrid_table_id;
           		$("#"+subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");
           		jQuery("#"+subgrid_table_id).jqGrid({
           			url:'<?php ?>&id='+row_id,
           			datatype: "json",
	                     mtype: "GET",
           			colNames: ['Identificador Elemento ','Descripción Elemento','Cantidad Sobrante','Devolución'],
           			colModel: [
  	                		{
               				name:"idelemento",
               				index:"idelemento",
               				width:30,
               				editable: true,
               				edittype: 'text',
               				align:"center",
               				key: true,
               				
               			},
           				{
               				name:"nombre",
               				index:"nombre",
               				width:70,
               				key:true,
               				align:"left",
               				sorttype:'text', 
	                		},

                		{
               				name:"cantidad",
               				index:"cantidad",
               				width:15,
               				key:true,
               				align:"left",
               				sorttype:'number', 
	                		},
           				{
                			name:"devolucion",
               				index:"devolucion",
               				width:15,
               				key:true,
               				align:"left",
               				sorttype:'text',
           				 }
           			],
           		   	rowNum:20,
           		   	pager: pager_id,
           		    styleUI : "Bootstrap",
           		   	viewrecords: false,
           			responsive: true,
           		   	sortname: 'num',
           		    sortorder: "asc",
           		    height: 100,
           		    width:$("#marcoTabla").width() - 60,
           		    caption: "Plugins",
               		    
           		}).navGrid("#"+pager_id,
	                		{
               		       edit:false,
               		       add:false,
               		       del:false, 
               		       search:false,
               		       refresh:true,
      	                       
               		    },

               		    {  },//edit
  	                        {   },//add
  	                   {}//Del





               		    );

           	},

		     });   


		   	$("#tabla_elementos_actividades").navGrid('#barra_herramientas',
		   	      {	
          	    add:false,
          		edit:false,
          		del:false ,
          		search:false ,
          		refresh:true,
          		refreshstate: 'current',
          		refreshtext:'Recargar Actividades',
          		},
                {  },//edit
                {  },//add
                {  },//del 
                {  },
                {  }
             	);
		     

		   	 $("#tabla_elementos_actividades").trigger("reloadGrid"); 

		   	 	

		   	}
		               
		   });


	





   
 
	
	 



</script>

