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
$valor = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$valor .= "&procesarAjax=true";
$valor .= "&action=index.php";
$valor .= "&bloqueNombre=" . $esteBloque ["nombre"];
$valor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$valor .= "&funcion=guardarColor";
$valor .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $valor, $enlace );

// URL definitiva
$urlGuardarColor = $url . $cadena;
?>

<script type='text/javascript'>

function guardarColor(){
		
	$.ajax({
		url: "<?php echo $urlGuardarColor?>",
		dataType: "json",
		data: { existeColor: estado, color1: color1, color2: color2, color3: color3},
		success: function(data){
			
		}
	});
};


var menu = $('.navbar-nav li a');
var well = $('.superior');
var panel = $('.panel-primary');
var activo = $('.navbar-nav > .active > a');

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

$("#bgcolor").ColorPickerSliders({
    color: color2,
    previewontriggerelement: false,
    title: 'Personalizar color de fondo',
    order: {
        rgb: 1,
        preview: 2
    },
    onchange: function(container, color) {

    	color1 = oscurecerColor(color.tiny.toHexString(),60);
        color2 = color.tiny.toHexString();

        if (color.cielch.l < 60) {
        	color3 = "#FFFFFF";
        }
        else {
        	color3 = "#000000";
        }
        
        menu.css("background-color", color2);
        menu.css("color", color3);

        well.css("background-color", color2);
        well.css("color", color3);
        
        panel.css("border-color", color2);

        activo.css("background-color", color1);
        activo.css("color", color3);
        
        guardarColor();

        estado = true;
    }
});

function oscurecerColor(color, cant){
	 //voy a extraer las tres partes del color
	 var rojo = color.substr(1,2);
	 var verd = color.substr(3,2);
	 var azul = color.substr(5,2);
	 
	 //voy a convertir a enteros los string, que tengo en hexadecimal
	 var introjo = parseInt(rojo,16);
	 var intverd = parseInt(verd,16);
	 var intazul = parseInt(azul,16);
	 
	 //ahora verifico que no quede como negativo y resto
	 if (introjo-cant>=0) introjo = introjo-cant;
	 if (intverd-cant>=0) intverd = intverd-cant;
	 if (intazul-cant>=0) intazul = intazul-cant;
	 
	 //voy a convertir a hexadecimal, lo que tengo en enteros
	 rojo = introjo.toString(16);
	 verd = intverd.toString(16);
	 azul = intazul.toString(16);
	 
	 //voy a validar que los string hexadecimales tengan dos caracteres
	 if (rojo.length<2) rojo = "0"+rojo;
	 if (verd.length<2) verd = "0"+verd;
	 if (azul.length<2) azul = "0"+azul;
	 
	 //voy a construir el color hexadecimal
	 var oscuridad = "#"+rojo+verd+azul;
	 //la función devuelve el valor del color hexadecimal resultante
	 return oscuridad;
	}

function hora(){  
    var hora=fecha.getHours();
    var minutos=fecha.getMinutes();
    var segundos=fecha.getSeconds();
    if(hora<10){ hora='0'+hora;}
    if(minutos<10){minutos='0'+minutos; }
    if(segundos<10){ segundos='0'+segundos; }     
    fecha.setSeconds(fecha.getSeconds()+1);
    var fech = "<b>Fecha: " + fecha.getFullYear() + "/" + (fecha.getMonth() + 1) + "/" + fecha.getDate() + " <br> Hora: " + hora +":"+minutos+":"+segundos + "</b>";       
    
    $('#<?php echo ('bannerReloj') ?>').text( "Hora: " + hora + ":" + minutos + ":" + segundos );
    setTimeout("hora()",1000);
}
fecha = new Date(); 
hora();

</script>
