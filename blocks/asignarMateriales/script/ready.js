//Deshabilitar el comportamiento predeterminado de los botones 

$(function() {
	$("button").button().click(function(event) {
		event.preventDefault();
});
});

$(".readonly").keydown(function(e){
    e.preventDefault();
});
