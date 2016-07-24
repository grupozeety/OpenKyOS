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
$cadenaACodificar .= $cadenaACodificar . "&funcion=tablaItems";
$cadenaACodificar .="&tiempo=".$_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );

// URL definitiva
$urlFinal = $url . $cadena;

// Variables
$cadenaACodificar2 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar2 .= "&procesarAjax=true";
$cadenaACodificar2 .= "&action=index.php";
$cadenaACodificar2 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar2 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar2 .= $cadenaACodificar . "&funcion=AgregarItem";
$cadenaACodificar2.="&tiempo=".$_REQUEST['tiempo'];

// Codificar las variables
$enlace2 = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena2 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar2, $enlace2 );

// URL definitiva
$urlFinal2 = $url . $cadena2;

// Variables
$cadenaACodificar3 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar3 .= "&procesarAjax=true";
$cadenaACodificar3 .= "&action=index.php";
$cadenaACodificar3 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar3 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar3 .= $cadenaACodificar . "&funcion=EliminarItem";
$cadenaACodificar3 .="&tiempo=".$_REQUEST['tiempo'];

// Codificar las variables
$enlace3 = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena3 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar3, $enlace3 );

// URL definitiva
$urlFinal3 = $url . $cadena3;




// echo $urlFinal;exit;
// echo $urlFinal2;
// echo $urlFinal3;

?>
<script type='text/javascript'>
$(function() {

    $("#tablaContenido").jqGrid({
        url: "<?php echo $urlFinal?>",
        datatype: "json",
        height: 200,
        width: 930,
        mtype: "GET",
        colNames: [ "Item", "Cantidad", "Descripción", "Valor Unitario","Valor Total"],
        colModel: [
            
            { name: "item", width: 90,align: "center", editable:true },
          
            { name: "cantidad", width: 80, align: "center" ,editable:true,editrules:{number:true},sorttype:'number',formatter:'number' },
            { name: "descripcion", width: 80, align: "center",editable:true },
            { name: "valor_unitario", width: 80, align: "center",editable:true,editrules:{number:true},sorttype:'number',formatter:'number' },
            { name: "valor_total", width: 80, align: "center",editable:true,editrules:{number:true},sorttype:'number',formatter:'number' },
            ],

        pager: "#barraNavegacion",
        rowNum: 10,
        rowList: [10, 20, 30],
        sortname: "id_items",
        sortorder: "desc",
        viewrecords: false,
        loadtext: "Cargando...",
        pgtext : "Pagina {0} de {1}",
        caption: "Detalle ",
        
        
               
    }).navGrid('#barraNavegacion',
    	    {	
	    add:true,
	    addtext:'Añadir Item',
		edit:false,	    		
		del:true ,
		deltext:'Eliminar Item',
		alertcap:"Alerta",
        alerttext:"Seleccione Item",
		search:false ,
		refresh:true,
		refreshstate: 'current',
		refreshtext:'Refrescar Items',
		},

    { },//edit
    {

        caption:"Añadir Item",
        addCaption: "Adicionar Item",
        width: 425, 
        height: 310,
        mtype:'GET',
        url:'<?php echo $urlFinal2?>',
        bSubmit: "Agregar",
        bCancel: "Cancelar",
        bClose: "Close",
        saveData: "Data has been changed! Save changes?",
        bYes : "Yes",
        bNo : "No",
        bExit : "Cancel",
        closeOnEscape:true,
        closeAfterAdd:true,
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
        } },//add
     {
			
             
            url:'<?php echo $urlFinal3?>',
            caption: "Eliminar Item",
            width: 425, 
            height: 150,
            mtype:'GET',
            bSubmit: "Eliminar",
            bCancel: "Cancelar",
            bClose: "Close",
            msg:"Desea Eliminar Item ?",
            bYes : "Yes",
            bNo : "No",
            bExit : "Cancel",
            closeOnEscape:true,
            closeAfterAdd:true,
            refresh:true,
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

</script>

