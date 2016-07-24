<?php
/**
 * Este es sólo el código, la función se realiza desde form.php
 */

if ( $_REQUEST ['botonCodificar'] =='true') {


	$cadena= nl2br($_REQUEST['campoCadena']);

	$linea=explode('<br />',$cadena);

	foreach ($linea as $key => $value) {
		echo '$cadenaSql';
		if($key!=0)
		{echo '.';}
		echo '=" '.$value.'";<br>';
	}


}else{

	$cadena= nl2br($_REQUEST['campoCadena']);

	$linea=explode('<br />',$cadena);

	foreach ($linea as $key => $value) {
		$caracteres = array('$cadenaSql.="','$cadenaSql="','$cadenaSql .= "','$cadenaSql = "', '$cadenaSql .= ');
		$value = str_replace($caracteres,'',$value);
		$value = substr($value, 0, -2);
		echo $value.'<br>';
	}

}

?>