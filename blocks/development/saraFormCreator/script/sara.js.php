/**
 * @author Jorge Ulises Useche Cuellar
 */

<?php
$rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );

if (! isset ( $esteBloque ["grupo"] ) || $esteBloque ["grupo"] == "") {
	$rutaURL .= "/blocks/" . $esteBloque ["nombre"] . "/";
} else {
	$rutaURL .= "/blocks/" . $esteBloque ["grupo"] . "/" . $esteBloque ["nombre"] . "/";
}
?>

// Menu Toggle Function
$(".menu-toggle").click(function(e) {
	e.preventDefault();
    $("#wrapper").toggleClass("toggled");
    // $("#editor").css("height",$(window).innerHeight()-40+"px");
});

/**
 * Seinicia el editor con las variables consideradas 
 */
var editor = ace.edit("editor");
editor.setTheme("ace/theme/eclipse");
editor.getSession().setMode("ace/mode/php");
$("#editor").css("height",$(window).innerHeight()-40+"px");
// $("#sidebar-wrapper").css("width",$(window).innerWidth()*0.4+"px");

/*
 * Se carga un ejemplo de un formulario en el editor de código
 */
$.ajax({
  url: "<?php echo $rutaURL ?>/script/code/form.php.txt",
  beforeSend: function( xhr ) {
    xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
  }
})
.done(function( data ) {
  //console.log(data);
  editor.insert(data);
});

/*
 * Se carga los contenedores de componentes que se pueden arrastrar
 * en este caso los del panel elementos en página
 */
$("ol.nested_with_drop").sortable({
	group : 'nested',
	handle : 'i.icon-move',
	onDragStart : function(item, container, _super) {
		// console.log(container,_super);
		// Duplicate items of the no drop area
		// console.log(item);
		if (!container.options.drop) {
			var itemclone = item.clone(false,false);
			//itemclone.find('.icon-config')[0].valores = item.find('.icon-config')[0].valores;
			itemclone.insertAfter(item);
			var iconconfigclone = itemclone.find('.icon-config')[0];
			addClickEvent(iconconfigclone);
			var valores = JSON.parse(JSON.stringify(item.find('.icon-config')[0].valores));
			iconconfigclone.valores = valores;
			item.find('.icon-remove').attr('removable','true');
			addClickEvent2(item.find('.icon-remove')[0]);
			//alert();
		}
		_super(item);
	}
});

/*
 * Se carga los contenedores de componentes que no se pueden arrastrar
 * en este caso los del panel de componentes
 */
$("ol.nested_with_no_drop").sortable({
	group : 'nested',
	drop : false
});

/*
 * Con el nombre del elemento se busca un archivo plantilla del mismo nombre en formato JSON
 */
function crearAtributos (elem,value){
	$.getJSON("<?php echo $rutaURL ?>/script/elements/"+value+".json", function(result){
//		console.log(value+".json")
		result = replaceAttributesDefaultData(result);
	    elem.valores=result;
	    console.log(result);
	    addClickEvent(elem);
	});	
}

/*
 * A partir del formato JSON se cargan los valores que se necesitan en los componentes
 * todos los valores se guardan en el DOM en su respectivo elemento como atributos
 */
function replaceAttributesDefaultData(result){
	var atributos = result.default.$atributos;
	$.each(atributos,function (i,v){
//		console.log(i,v);
		if(result.options.$atributos[i] && result.options.$atributos[i][v]){
//			console.log(result.default.$atributos[i], result.options.$atributos[i][v])
			result.default.$atributos[i] = result.options.$atributos[i][v];
		}
	});
	return result;
}

/*
 * Evento relacionado al botón de configuración del componente
 */
function addClickEvent(elem){
	$(elem).click(function(){
    	openDialog(elem);
    });
}

/*
 * Evento relacionado al botón de eliminar el componente
 */
function addClickEvent2(elem){
	$(elem).click(function(){
		if($(this).attr('removable')=='true'){
			$(this).parent('li').remove();
		}
	});
}

/**
 * a partir de un tipo de elemento con valores, retorna el nodo con la estructura definida
 * en este caso el nodo son varias tablas con parámetros de configuración
 * @param {Object} elemento del DOM exactamente de la clase .icon-config.
 */

function createConfigNode(elem){
	var arreglo = elem.valores.default;
	var opciones = elem.valores.options;
	
	var table1 = $("<table>").addClass("table-striped table1").css("width","100%");
	var thead = $("<thead>").html("<tr>"+
	    "<th>Atributo</th>"+
	    "<th>Valor</th>"+
	    "</tr>");
	var tbody = $("<tbody>");
	$.each(arreglo.$atributos,function(key,value){
		if(opciones.$atributos[key]){
			var td = crearTdKeyValueOptions(key,value,opciones.$atributos[key]);
		} else {
			var td = crearTdKeyValue(key,value);
		}		
		var tr = $("<tr>");
		tr.append(td[0]);
		tr.append(td[1]);
		tbody.append(tr);
	});
	table1.append(thead);
	table1.append(tbody);
	
	var table2 = $("<table>").addClass("table-striped table2").css("width","100%");
	var thead = $("<thead>").html("<tr>"+
	    "<th>Atributo</th>"+
	    "<th>Valor</th>"+
	    "</tr>");
	var tbody = $("<tbody>");
	var attr = "$esteCampo";
	var tr = $("<tr>").append(crearTdKeyValue(attr,arreglo[attr]));
	tbody.append(tr);
	var attr = "header1";
	var tr = $("<tr>").append(crearTdTextAreaKeyValue(attr,arreglo[attr]));
	tbody.append(tr);
	var attr = "footer1";
	var tr = $("<tr>").append(crearTdTextAreaKeyValue(attr,arreglo[attr]));
	tbody.append(tr);
	var attr = "footer2";
	if(arreglo[attr]){
		var tr = $("<tr>").append(crearTdTextAreaKeyValue(attr,arreglo[attr]));
		tbody.append(tr);	
	}
	table2.append(thead);
	table2.append(tbody);
	
	var div = $('<div>').append(table1).append(table2);
	return div;
}

/**
 * Permite generar un arreglo de elementos <td> (celdas) con inputs que contienen los valores que se
 * le ingresan con sus repectivas opciones y demás. Simulando el comportamiento de un menú SELECT con INPUTS
 * @param key String con la llave del atributo
 * @param value String con el valor predeterminado del atributo
 * @param options Array con los valores que puede tener el atributo
 * @returns Array con nodos <td>
 */
function crearTdKeyValueOptions(key,value,options){
	var td1 = $("<td>").html(key);
	var input = $("<input>").addClass("form-control").attr("key",key).attr("value",value);
	var div = $("<div>").addClass("glyphicon glyphicon-triangle-bottom select-editable-narrow");
	div[0].options = options;
	div.click(clickInInputOptions);
	var td2 = $("<td>").css("position","relative").append(input).append(div);
	return [td1,td2];
}

/**
 * Evento relacionado a el click en los inputs que se presentan en la ventana de configuración de elementos (MODAL Boostrap) 
 */
function clickInInputOptions(a){
	var elem = a.target;
	if ($(elem).parent().children("select")[0]){
		$(".select-options").remove();
	} else {
		var select = $("<select>").attr("multiple","").addClass("form-control")
		$.each(elem.options,function (k,v){
			var option = $("<option>").html(v);
			select.append(option);
		});
		var parent = $(elem).parent();
		select.addClass("select-options");
		select.css("width", parent[0].offsetWidth + 'px');
//		select.css("top",parent[0].offsetTop + 'px');
		select.css("position","absolute");
		select.css("z-index","1000");
		select.mouseleave(function (a){
			var sel = a.target;
			$(sel).remove();
		});
		select.change(function (a){
			var sel = a.target;
			parent.children(".form-control").val(sel.value);
			$(sel).remove();
		});
		parent.append(select);
	}
}

/**
 * Permite generar un arreglo de elementos <td> (celdas) con input (tiene una opción predeterminada)
 */
function crearTdKeyValue(key,value){
	var td1 = $("<td>").html(key);
	var input = $("<input>").addClass("form-control").attr("key",key).attr("value",value);
	var td2 = $("<td>").append(input);
	return [td1,td2];
}

/**
 * Permite generar un arreglo de elementos <td> (celdas) con TEXTAREA (areas de edición de texto)
 * con un valor predeterminado que puede ser modificado
 */
function crearTdTextAreaKeyValue(key,arreglo){
	var valor = new String();
	$.each(arreglo,function(i,v){
		valor += v + "\n";
	});
	var td1 = $("<td>").html(key);
	var input = $("<textarea>")
		.addClass("form-control")
		.attr("key",key)
		.attr("rows",arreglo.length)
		.html(valor);
	var td2 = $("<td>").append(input);
	return [td1,td2];
}

/**
 * Lee los parámetros configurados en las tablas que están en el diálogo de configuración del componente
 * y los guarda en el nodo del DOM que representa al componente
 */
function saveDataInNode(){
	$(".table1 tr>td:nth-child(2)").children("input,textarea").each(function(i,v){
		var key = $(v).attr("key");
		var valor = new String();
		if(v.tagName=="INPUT"){
			valor = v.value;
		} else if(v.tagName=="TEXTAREA"){
			valor = $(v).html().split('\n');
		}
		elementoActual.valores.default.$atributos[key] = valor;
	});
	$(".table2 tr>td:nth-child(2)").children("input,textarea").each(function(i,v){
		var key = $(v).attr("key");
		var valor = new String();
		if(v.tagName=="INPUT"){
			valor = v.value;
		} else if(v.tagName=="TEXTAREA"){
			valor = $(v).html().split('\n');
		}
		// console.log(key,valor);
		elementoActual.valores.default[key] = valor;
	});
	$('#myModal').modal('hide');
}

/*
 * Abre el diálogo de configuración almacenando el elemento que fue invocado en una variable global
 */
function openDialog(elem){
	elementoActual = elem;
	var tabla = createConfigNode(elem);
	$("#myModal .modal-body").empty();
	$("#myModal .modal-body").append(tabla);
	$('#myModal').modal('show');
}

/*
 * Todos los elementos que tienen esta clase (en este caso todos los elementos creados y con archivo de configuración JSON)
 * se configuran poniendoles sus atributos respectivos (del archivo JSON al nodo en el DOM)
 */
$(".nested_with_no_drop .icon-config").each(function( index ) {
  crearAtributos(this,$(this).attr('value'));
});

/*
 * Actualiza el código en el editor de PHP
 */
function updateCode(){
	var contenido = $('#contenidoFormulario').children();
	var texto = searchChildComponents(contenido);
	editor.gotoLine(53);
	editor.insert(texto);
}

/*
 * Guarda el código del editor en un archivo de texto
 */
function saveCode(){
	var textToWrite = editor.getValue();
	var textFileAsBlob = new Blob([textToWrite], {type:'text/plain'});
	var fileNameToSaveAs = "Form.php";//document.getElementById("inputFileNameToSaveAs").value;

	var downloadLink = document.createElement("a");
	downloadLink.download = fileNameToSaveAs;
	downloadLink.innerHTML = "Download File";
	if (window.webkitURL != null){
		// Chrome allows the link to be clicked
		// without actually adding it to the DOM.
		downloadLink.href = window.webkitURL.createObjectURL(textFileAsBlob);
	} else {
		// Firefox requires the link to be added to the DOM
		// before it can be clicked.
		downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
		downloadLink.onclick = destroyClickedElement;
		downloadLink.style.display = "none";
		document.body.appendChild(downloadLink);
	}	
	downloadLink.click();
}

/*
 * Es el evento que elimina los nodos del DOM para el botón de eliminar componente
 */
function destroyClickedElement(event){
	document.body.removeChild(event.target);
}

/*
 * Busca los componentes y los convierte de la representación en objetos de javascript a código PHP
 */
function searchChildComponents(contenido){
	var texto = new String();
	$.each(contenido,function(i,v){
		texto = convertirJSON2PHP(v);
	});
	return texto;
}

/*
 * Convierte los objetos del DOM .icon-config y los convierte a su representación en PHP 
 */
function convertirJSON2PHP(contenidonodo){
	var nodo = $(contenidonodo).find('.icon-config')[0];
	var valores = nodo.valores.default;
	var texto = new String();
	$.each(valores.header1,function(i,v){
		texto += v + "\n";
	});
	texto += "$esteCampo = " + valores.$esteCampo + ";\n";
	$.each(valores.$atributos,function(i,v){
		texto += "$atributos ['" + i + "'] = " + v + ";\n";
	});
	$.each(valores.footer1,function(i,v){
		texto += v + "\n";
	});
	if(valores.content==true){
		var contenido = $(contenidonodo).children("ol").children();
		console.log(contenido);
		$.each(contenido,function(i,v){
			texto += convertirJSON2PHP(v);
		});		
	}
	if(valores.footer2){
		$.each(valores.footer2,function(i,v){
			texto += v + "\n";
		});
	}
	return texto;
}
