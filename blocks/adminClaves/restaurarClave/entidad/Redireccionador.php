<?php

namespace cambioClave\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "index.php";
	exit ();
}
class Redireccionador {
	public static function redireccionar($opcion, $valor = "") {
		$miConfigurador = \Configurador::singleton ();
		
		switch ($opcion) {
			
			case "sucess" :
				$variable = 'pagina=restaurarClave';
				$variable .= '&mensaje=sucess';
				$variable .= '&valor=' . $valor;
				break;
			
			case "sucessCambio" :
				$variable = 'pagina=restaurarClave';
				$variable .= '&opcion=restaurar';
				$variable .= '&usuario=' . $valor ['usuario'];
				$variable .= '&token=' . $valor ['token'];
				$variable .= '&valor=' . $valor ['mensaje'];
				$variable .= '&mensaje=sucessCambio';
				break;
			
			case "error" :
				$variable = 'pagina=restaurarClave';
				$variable .= '&mensaje=error';
				break;
			
			case "errorCorreo" :
				$variable = 'pagina=restaurarClave';
				$variable .= '&mensaje=errorCorreo';
				break;
			
			case "errorCambio" :
				$variable = 'pagina=restaurarClave';
				$variable .= '&opcion=restaurar';
				$variable .= '&usuario=' . $valor ['usuario'];
				$variable .= '&token=' . $valor ['token'];
				$variable .= '&valor=' . $valor ['mensaje'];
				$variable .= '&mensaje=errorCambio';
				break;
		}
		
		foreach ( $_REQUEST as $clave => $valor ) {
			unset ( $_REQUEST [$clave] );
		}
		
		$url = $miConfigurador->configuracion ["host"] . $miConfigurador->configuracion ["site"] . "/index.php?";
		$enlace = $miConfigurador->configuracion ['enlace'];
		$variable = $miConfigurador->fabricaConexiones->crypto->codificar ( $variable );
		$_REQUEST [$enlace] = $enlace . '=' . $variable;
		$redireccion = $url . $_REQUEST [$enlace];
		echo "<script>location.replace('" . $redireccion . "')</script>";
		
		exit ();
	}
	public static function generarRedireccion($opcion, $valor = "") {
		$miConfigurador = \Configurador::singleton ();
		
		switch ($opcion) {
			
			case "restaurar" :
				$variable = 'pagina=restaurarClave';
				$variable .= '&opcion=restaurar';
				$variable .= '&token=' . $valor ['token'];
				$variable .= '&usuario=' . $valor ['usuario'];
				break;
		}
		
		foreach ( $_REQUEST as $clave => $valor ) {
			unset ( $_REQUEST [$clave] );
		}
		
		$url = $miConfigurador->configuracion ["host"] . $miConfigurador->configuracion ["site"] . "/index.php?";
		$enlace = $miConfigurador->configuracion ['enlace'];
		$variable = $miConfigurador->fabricaConexiones->crypto->codificar ( $variable );
		$_REQUEST [$enlace] = $enlace . '=' . $variable;
		$redireccion = $url . $_REQUEST [$enlace];
		return $redireccion;
	}
}
?>
