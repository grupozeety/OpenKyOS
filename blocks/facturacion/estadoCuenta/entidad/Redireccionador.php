<?php

namespace facturacion\estadoCuenta\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "index.php";
	exit ();
}
class Redireccionador {
	public static function redireccionar($opcion, $valor = "") {
		$miConfigurador = \Configurador::singleton ();
		
		switch ($opcion) {
			
			case "InsertoInformacion" :
				$variable = 'pagina=estadoCuenta';
				$variable .= '&mensajeModal=exitoInformacion';
				break;
			
			case "UpdateInformacion" :
				$variable = 'pagina=estadoCuenta';
				$variable .= '&mensajeModal=exitoActualizacion';
				break;
			
			case "NoUpdateInformacion" :
				$variable = 'pagina=estadoCuenta';
				$variable .= '&mensajeModal=errorActualizacion';
				break;
			
			case "ErrorValor" :
				$variable = 'pagina=estadoCuenta';
				$variable .= '&mensajeModal=errorValor';
				break;
			
			case "ErrorPago" :
				$variable = 'pagina=estadoCuenta';
				$variable .= '&mensajeModal=errorCreacion';
				break;
			
			case "ErrorUpdate" :
				$variable = 'pagina=estadoCuenta';
				$variable .= '&mensajeModal=errorActualizacion';
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
}
?>
