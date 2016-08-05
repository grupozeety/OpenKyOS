	<?php
	/**
	 * 
	 * Los datos del bloque se encuentran en el arreglo $esteBloque.
	 */
	// URL base
	$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
	$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
	$url .= "/index.php?";
	
	// Variables
	$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
	$cadenaACodificar .= "&procesarAjax=true";
	$cadenaACodificar .= "&action=index.php";
	$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
	$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
	$cadenaACodificar .= "&funcion=consultarConexionesDB";
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );
	
	// URL definitiva
	$urlTablaDinamica = $url . $cadena;
	
	// Variables
	$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
	$cadenaACodificar .= "&procesarAjax=true";
	$cadenaACodificar .= "&action=index.php";
	$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
	$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
	$cadenaACodificar .= "&funcion=crearConexion";
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );
	
	// URL definitiva
	$urlCrearConexion= $url . $cadena;
	
	// Variables
	$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
	$cadenaACodificar .= "&procesarAjax=true";
	$cadenaACodificar .= "&action=index.php";
	$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
	$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
	$cadenaACodificar .= "&funcion=editarConexion";
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );
	
	// URL definitiva
	$urlEditarConexion = $url . $cadena;
	
	// Variables
	$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
	$cadenaACodificar .= "&procesarAjax=true";
	$cadenaACodificar .= "&action=index.php";
	$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
	$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
	$cadenaACodificar .= "&funcion=eliminarConexion";
	
	// Codificar las variables
	$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
	$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );
	
	// URL definitiva
	$urlEliminarConexion = $url . $cadena;
	
	
	
	?>
<script type='text/javascript'>


$(document).ready(function() {

	
	$(function() {
	         	$('#tabla_gestion').ready(function() {
	         		 $("#tabla_gestion").jqGrid({
	                     url:	"<?php echo $urlTablaDinamica?>",
	                     datatype: "json",
	                     mtype: "GET",
	                     colModel: [
						{
								label: 'ID',
						        name: 'id',
						        width: 40,
								key: true,
								editable: false,
								sorttype:'number',
								editrules : {required: true}
						 },
	                         {
	     						label: 'Nombre',
	                             name: 'nombre',
	                             width: 40,
	     						editable: true,
	     						sorttype:'text',
	     						editrules : {required: true}
	     						
	                         },

	                         {
	     						label: 'DBMS',
	                             name: 'dbms',
	                             width: 30,
	     						editable: true,
	     						edittype: 'select',
	     						editrules : {required: true},
	                            editoptions: {
                                 value: "mysql: Mysql;pgsql: PostgreSQL;oracle: Oracle",
                                 }
            					     						
		                      },
	                         {
		     						label: 'Dirección(Host)',
		                             name: 'host',
		                             width: 40,
		     						editable: true,
		     						sorttype:'text',
		     						editrules : {required: true}
			     						
			                     },
			                        
		                     {
		     						label: 'Puerto',
		                             name: 'puerto',
		                             width: 20,
		     						editable: true,
		     						sorttype:'number',
		     						editrules : {required: true,number:true}	
			                     },

		                     {
		     						label: 'Conexión SSH',
		                             name: 'conxssh',
		                             width: 40,
		     						editable: false,
		     						sorttype:'text',
		     							
			                     },
		                     {
		     						label: 'Nombre Base de Datos',
		                             name: 'namedb',
		                             width: 62,
		     						editable: true,
		     						sorttype:'text',
		     						editrules : {required: true}		
			                     },

			                     {
			     						label: 'Esquema Base de Datos',
			                             name: 'esquemadb',
			                             width: 65,
			     						editable: true,
			     						sorttype:'text',
			     						editrules : {required: true}	
				                     },
			                     
			                     {
			     						label: 'Usuario',
			                             name: 'usuario',
			                             width: 40,
			     						editable: true,
			     						sorttype:'text',
			     						editrules : {required: true}	
				                     },	
				                  {
				     						label: 'Contraseña',
				                             name: 'contrasena',
				                             width: 70,
				     						editable: true,
				     						edittype: 'password',
				     						formatter: 'customPassFormat',
				     						editrules : {required: true}
					     						
					                },	

			                                      
	                         ],
	                   	sortname: 'id',
	     				sortorder : 'asc',
	     				viewrecords: true,
	     				rownumbers: false,
	     				loadonce : false,
                        rowNum: 100, 
	                    width: 1050,
	                    height: 300,
	                    pager: "#barra_herramientas",
	                    caption: "Gestión Conexiones DB",
	                 	
	    
	                 });




	          		$("#tabla_gestion").navGrid('#barra_herramientas',
	                    {	
	              	    add:true,
	              	    addtext:'Crear Conexión',
	              		edit:true,
	              		edittext:'Actualizar Conexión',	    		
	              		del:true ,
	              		deltext:'Eliminar Conexión',
	              		alertcap:"Alerta",
	                    alerttext:"Seleccione Conexión",
	              		search:false ,
	              		refresh:true,
	              		refreshstate: 'current',
	              		refreshtext:'Recargar Conexiones',
	              		},
	                  { 
	                         width: 350,
	                         height: 400,
	                         mtype:'GET',
	                         url:'<?php echo $urlEditarConexion?>',
	                         bSubmit: "Actualizar",
	                         bCancel: "Cancelar",
	                         bClose: "Close",
	                         saveData: "Data has been changed! Save changes?",
	                         bYes : "Yes",
	                         bNo : "No",
	                         bExit : "Cancel",
	                         closeOnEscape:true,
	                         closeAfterEdit:true,
	                         refresh:true,
	                         reloadAfterSubmit:true,
	                         recreateForm: true,
	                         onclickSubmit:function(params, postdata){
	                             //save add
	                             var p=params;
	                             var pt=postdata;
	                         },
	                         beforeSubmit : function(postdata, formid) { 
	                             var p = postdata;
	                             var id=id;
	                             var success=true;
	                             var message="continue";
	                             return[success,message]; 
	                         },    
	                         afterSubmit : function(response, postdata) 
	                         { 
	                             var r=response; 
	                             var p=postdata;
	                             var responseText=jQuery.jgrid.parse(response.responseText);
	                             var success=true;
	                             var message="continue";
	                             return [success,message] 
	                         },
	                         afterComplete : function (response, postdata, formid) {        
	                             var responseText=jQuery.jgrid.parse(response.responseText);
	                             var r=response;
	                             var p=postdata;
	                             var f=formid;
	                         }
	                          },//edit
	                  { 
	                        	
	 	                         width: 350, 
	 	                         height: 400,
	 	                         mtype:'GET',
	 	                         url:'<?php echo $urlCrearConexion?>',
	 	                         bSubmit: "Crear",
	 	                         bCancel: "Cancelar",
	 	                         bClose: "Close",
	 	                         saveData: "Data has been changed! Save changes?",
	 	                         bYes : "Yes",
	 	                         bNo : "No",
	 	                         bExit : "Cancel",
	 	                         closeOnEscape:true,
		                         closeAfterAdd:true,
		                         refresh:true,
		                         reloadAfterSubmit:true,
		                         recreateForm: true,
	 	                         onclickSubmit:function(params, postdata){
	 	                             //save add
	 	                             var p=params;
	 	                             var pt=postdata;
	 	                         },
	 	                         beforeSubmit : function(postdata, formid) { 
	 	                             var p = postdata;
	 	                             var id=id;
	 	                             var success=true;
	 	                             var message="continue";
	 	                             return[success,message]; 
	 	                         },    
	 	                         afterSubmit : function(response, postdata) 
	 	                         { 
	 	                             var r=response; 
	 	                             var p=postdata;
	 	                             var responseText=jQuery.jgrid.parse(response.responseText);
	 	                             var success=true;
	 	                             var message="continue";
	 	                             return [success,message] 
	 	                         },
	 	                         afterComplete : function (response, postdata, formid) {        
	 	                             var responseText=jQuery.jgrid.parse(response.responseText);
	 	                             var r=response;
	 	                             var p=postdata;
	 	                             var f=formid;
	 	                         }


	        	                  },//add
	                   {
	              			  url:'<?php echo $urlEliminarConexion?>',
	                          width: 350,
	                          height:125,
	                          mtype:'GET',
	                          bSubmit: "Eliminar",
	                          bCancel: "Cancelar",
	                          bClose: "Close",
	                          msg:" <b>¿Desea Eliminar Conexión?</b><br>¡ Recordar <b>NO</b> se Podran Reversar los Cambios !",
	                          bYes : "Yes",
	                          bNo : "No",
	                          bExit : "Cancel",
	                          closeOnEscape:true,
	                          closeAfterDell:true,
	                          refresh:true,
	                          reloadAfterSubmit:true,
	                          recreateForm: true,
	                          onclickSubmit:function(params, postdata,id_items){
	                              //save add
	                              var p=params;
	                              var pt=postdata;
	                              
	                              
	                          },
	                          beforeSubmit : function(postdata, formid) { 
	                              var p = postdata;
	                              var id=formid;
	                              var success=true;
	                              var message="continue";
	                              return[success,message]; 
	                          }, 
	                          afterSubmit : function(response, postdata) 
	                          { 
	                              var r=response; 
	                              var p=postdata;
	                              var responseText=jQuery.jgrid.parse(response.responseText);
	                              var success=true;
	                              var message="continue";
	                              return [success,message] 
	                          },
	                          afterComplete : function (response, postdata, formid) {        
	                              var responseText=jQuery.jgrid.parse(response.responseText);
	                              var r=response;
	                              var p=postdata;
	                              var f=formid;
                              
	                          } 
	                          },//del 
	                   {},
	                   {}
	                 	);


	                 
	         			                  
	       });

	});
});

</script>

