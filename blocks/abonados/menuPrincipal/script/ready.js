function htmlbodyHeightUpdate() {
	var height3 = $(window).height()
	var height1 = $('.nav').height() + 50
	height2 = $('.main').height()
	if (height2 > height3) {
		$('html').height(Math.max(height1, height3, height2) + 10);
		$('body').height(Math.max(height1, height3, height2) + 10);
	} else {
		$('html').height(Math.max(height1, height3, height2));
		$('body').height(Math.max(height1, height3, height2));
	}

}
$(document).ready(function() {
	htmlbodyHeightUpdate()
	$(window).resize(function() {
		htmlbodyHeightUpdate()
	});
	$(window).scroll(function() {
		height2 = $('.main').height()
		htmlbodyHeightUpdate()
	});
});


var menu = $('.navbar-nav li a');
var well = $('.superior');
var panel = $('.panel-primary');
var activo = $('.navbar-nav > .active > a');
var boton = $('.btn');

var estado = false;

if ($("#<?php echo $this->campoSeguro('color1')?>").length > 0 ){

	var color1 =  $("#<?php echo $this->campoSeguro("color1")?>").val();
	var color2 =  $("#<?php echo $this->campoSeguro("color2")?>").val();
	var color3 =  $("#<?php echo $this->campoSeguro("color3")?>").val();

	estado = true;

}else{
	var color1 = "#d9d9d9"; 
	var color2 = "#ffffff";;
	var color3 = "#000000";
}

menu.css("background-color", color2);
menu.css("color", color3);

well.css("background-color", color2);
well.css("color", color3);
		
panel.css("border-color", color2);

activo.css("background-color", color1);
activo.css("color", color3);

boton.css("background-color", color2);
boton.css("color", color3);
