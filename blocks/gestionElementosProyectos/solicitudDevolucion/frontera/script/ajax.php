<?php
/** 
 * Código Correspondiente a las Url de la peticiones Ajax.
 */
?>
<script type='text/javascript'>

/** 
 * Código JavaScript Correspondiente a la utilización de las Peticiones Ajax.
 */


 $(document).ready(function() {

		
		$(function() {
		         	$('#tabla_elementos_actividades').ready(function() {
		         		 $("#tabla_elementos_actividades").jqGrid({
		                     url:	"<?php// echo $urlTablaDinamica?>",
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
		                    width: 1050,
// 		                    height: 300,
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
		                			url:'',
		                			datatype: "json",
		    	                     mtype: "GET",
		                			colNames: ['Tipo:','Nombre:','Archivo:'],
		                			colModel: [
		       	                		{
			                				name:"tipo",
			                				index:"tipo",
			                				width:130,
			                				editable: true,
			                				edittype: 'select',
			                				editoptions: {
			                                     value: "css: Css;javascript: JavaScript",
			                				}
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
			                				name: 'archivo',
		                				    index: 'archivo',
		                				    hidden: true,
		                				    enctype: "multipart/form-data",
		                				    editable: true,
		                				    edittype: 'file',
		                				    editrules: {
		                				        edithidden: true,
		                				        required: true
		                				    },
		                				    formoptions: {
		                				        elmsuffix: '*'
		                				    }
		                				 }
		                			],
		                		   	rowNum:20,
		                		   	pager: pager_id,
		                		   	viewrecords: false,
		                		   	sortname: 'num',
		                		    sortorder: "asc",
		                		    height: '100%',
		                		    width: 965,
		                		    caption: "Plugins",
			                		    
		                		}).navGrid("#"+pager_id,
		    	                		{
	    	                		       edit:false,
	    	                		       add:true,
	    	                		       del:true, 
	    	                		       search:false,
	    	                		       alertcap:"Alerta",
	    	       	                       alerttext:"Seleccione Plugin",
	    	                		    },

	    	                		    {  },//edit
	    	   	                        { 
	    	   	                        	 caption:"Adición Plugin",
	    	   	 	                         addCaption: "Adición Plugin",
	    	   	 	                         width: 320, 
	    	   	 	                         height: 150,
	    	   	 	                         mtype:'GET',
	    	   	 	                         url:'',
	    	   	 	                         bSubmit: "Guardar",
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
	    	   	 	                     
	    	   	 	                         afterSubmit : function(response, postdata) 
	    	   	 	                         { 

	    	   	 	                         var file_data = $("#archivo").prop("files")[0];   
		    	   	 	                     var form_data = new FormData();                  
		    	   	 	                     form_data.append("archivo", file_data)
		    	   	 	                            
		    	   	 	                     $.ajax({
		   	 	                                 url: '',//
		   	 	                                 dataType: 'json',
		   	 	                                 cache: false,
		   	 	                                 contentType: false,
		   	 	                                 processData: false,
		   	 	                                 data: form_data,                         
		   	 	                                 type: 'post'
			   	 	                                 });

	   	   	                              var r=response; 
		   	                              var p=postdata;
		   	                              var responseText=jQuery.jgrid.parse(response.responseText);
		   	                              var success=true;
		   	                              var message="continue";
		   	                              return [success,message] ;

	    	   	 	                    
	    	   	 	                         },
	    	   	 	                         afterComplete : function (response, postdata, formid) {        
	    	   	 	                             var responseText=jQuery.jgrid.parse(response.responseText);
	    	   	 	                             var r=response;
	    	   	 	                             var p=postdata;
	    	   	 	                             var f=formid;
	    	   	 	                         }


	    	   	        	                  },//add
	    	   	                   {
	    	   	              			  url:'',
	    	   	                          caption: "Eliminar Plugin",
	    	   	                          width: 350,
	    	   	                          height:125,
	    	   	                          mtype:'GET',
	    	   	                          bSubmit: "Eliminar",
	    	   	                          bCancel: "Cancelar",
	    	   	                          bClose: "Close",
	    	   	                          msg:" <b>¿Desea Eliminar Plugin?</b><br>¡ Recordar <b>NO</b> se podran reversar los Cambios!",
	    	   	                          bYes : "Yes",
	    	   	                          bNo : "No",
	    	   	                          bExit : "Cancel",
	    	   	                          closeOnEscape:true,
	    	   	                          closeAfterDel:true,
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
	    	   	                          }//Del





	    	                		    );
		                	}
		    
		                 });




		          		$("#tabla_elementos_actividades").navGrid('#barra_herramientas',
		                    {	
		              	    add:false,
		              	    edit:false,
		              		del:false,
		              		alerttext:"Seleccione Conexión",
		              		search:true ,
		              		refresh:true,
		              		refreshstate: 'Cargando Actividades',
		              		refreshtext:'Recargar Actividades',
		              		},
		                  {      caption:"Actualizar Bloque",
		                         addCaption: "Actualizar Bloque",
		                         width: 350,
		                         height: 400,
		                         mtype:'GET',
		                         url:'<?php//echo $urlEditarBloque?>',
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
		                        	 caption:"Crear Bloque",
		 	                         addCaption: "Crear Bloque",
		 	                         width: 350, 
		 	                         height: 400,
		 	                         mtype:'GET',
		 	                         url:'<?php //echo $urlCrearBloque?>',
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
		              			  url:'<?php // echo $urlEliminarBloque?>',
		                          caption: "Eliminar Bloque",
		                          width: 350,
		                          height:125,
		                          mtype:'GET',
		                          bSubmit: "Eliminar",
		                          bCancel: "Cancelar",
		                          bClose: "Close",
		                          msg:" <b>¿Desea Eliminar Bloque?</b><br>¡ Recordar <b>NO</b> se Podran Reversar los Cambios !",
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

