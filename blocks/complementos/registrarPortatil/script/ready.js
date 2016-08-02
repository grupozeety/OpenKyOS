$('#registrarPortatil').validator();

//Configurar el comportamiento del Tooltip en este bloque

$(function() {
	$(document).tooltip({
		position : {
			my : "left+15 center",
			at : "right center"
		}
	},
	{ hide: { duration: 800 } }
	);
});

//Deshabilitar el comportamiento predeterminado de los botones 

$(function() {
	$("button").button().click(function(event) {
		event.preventDefault();
	;
})
});