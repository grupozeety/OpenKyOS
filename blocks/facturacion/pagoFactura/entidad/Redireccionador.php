<?php

namespace facturacion\pagoFactura\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "index.php";
	exit ();
}
class Redireccionador {
	public static function redireccionar($opcion, $valor = "") {
		$miConfigurador = \Configurador::singleton ();
		
		switch ($opcion) {
			
			case "InsertoInformacion" :
				$variable = 'pagina=pagoFactura';
				$variable .= '&mensajeModal=exitoInformacion';
				break;
			
			case "UpdateInformacion" :
				$variable = 'pagina=pagoFactura';
				$variable .= '&mensajeModal=exitoActualizacion';
				break;
			
			case "NoUpdateInformacion" :
				$variable = 'pagina=pagoFactura';
				$variable .= '&mensajeModal=errorActualizacion';
				break;
			
			case "ErrorValor" :
				$variable = 'pagina=pagoFactura';
				$variable .= '&mensajeModal=errorValor';
				break;
			
			case "ErrorPago" :
				$variable = 'pagina=pagoFactura';
				$variable .= '&mensajeModal=errorCreacion';
				break;
			
			case "ErrorUpdate" :
				$variable = 'pagina=pagoFactura';
				$variable .= '&mensajeModal=errorActualizacion';
				break;
			
			case "EstadoCuenta" :

				$variable = 'pagina=estadoCuenta';
				$variable .= '&opcion=verFacturas';
				$variable .= '&id_beneficiario='.$valor['id_beneficiario'];
				$variable .= '&contenido='.$valor['pagina'];
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
