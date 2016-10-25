//Deshabilitar el comportamiento predeterminado de los botones 
$(function() {
	$("btn").button().click(function(event) {
		console.log("Hola boton");
		event.preventDefault();
	});
});

