<?php

namespace facturacion\calculoFactura\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "index.php";
	exit ();
}
class Redireccionador {
	public static function redireccionar($opcion, $valor = "") {
		$miConfigurador = \Configurador::singleton ();

		switch ($opcion) {
			
			case "ErrorFormatoArchivo" :
				$variable = 'pagina=calculoFactura';
				$variable .= '&mensajeModal=errorFormatoArchivo';
				break;
			
			case "ErrorArchivoNoValido" :
				$variable = 'pagina=calculoFactura';
				$variable .= '&mensajeModal=errorArchivoNoValido';
				break;
			
			case "ErrorCargarArchivo" :
				$variable = 'pagina=calculoFactura';
				$variable .= '&mensajeModal=errorCargarArchivo';
				break;
			
			case "ErrorNoCargaInformacionHojaCalculo" :
				$variable = 'pagina=calculoFactura';
				$variable .= '&mensajeModal=errorCargarInformacion';
				break;
			
			case "ErrorInformacionCargar" :
				$variable = 'pagina=calculoFactura';
				$variable .= '&mensajeModal=errorInformacionCargar';
				$variable .= '&log=' . $valor;
				break;
			
			case "ExitoInformacion" :
				$variable = 'pagina=calculoFactura';
				$variable .= '&mensajeModal=exitoInformacion';
				break;
			
			case "ErrorCreacionContratos" :
				$variable = 'pagina=calculoFactura';
				$variable .= '&mensajeModal=errorCreacionContratos';
				break;
			
			case "ExitoRegistroProceso" :
				$variable = 'pagina=calculoFactura';
				$variable .= '&mensajeModal=exitoRegistroProceso';
				break;
			
			case "ErrorRegistroProceso" :
				$variable = 'pagina=calculoFactura';
				$variable .= '&mensajeModal=errorRegistroProceso';
				break;
			
			case "ErrorActualizacion" :
				$variable = 'pagina=calculoFactura';
				$variable .= '&mensajeModal=errorActualizacion';
				break;
			
			case "ErrorActualizacioncabecera" :
				$variable = 'pagina=calculoFactura';
				$variable .= '&mensajeModal=errorActualizacionCab';
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
