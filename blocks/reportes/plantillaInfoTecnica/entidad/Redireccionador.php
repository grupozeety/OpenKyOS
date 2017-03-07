<?php

namespace reportes\plantillaInfoTecnica\entidad;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "index.php";
	exit ();
}
class Redireccionador {
	public static function redireccionar($opcion, $valor = "") {
		$miConfigurador = \Configurador::singleton ();
		
		switch ($opcion) {
			
			case "ErrorFormatoArchivo" :
				$variable = 'pagina=plantillaInfoTecnica';
				$variable .= '&mensajeModal=errorFormatoArchivo';
				break;
			
			case "ErrorArchivoNoValido" :
				$variable = 'pagina=plantillaInfoTecnica';
				$variable .= '&mensajeModal=errorArchivoNoValido';
				break;
			
			case "ErrorCargarArchivo" :
				$variable = 'pagina=plantillaInfoTecnica';
				$variable .= '&mensajeModal=errorCargarArchivo';
				break;
			
			case "ErrorNoCargaInformacionHojaCalculo" :
				$variable = 'pagina=plantillaInfoTecnica';
				$variable .= '&mensajeModal=errorCargarInformacion';
				break;
			
			case "ErrorInformacionCargar" :
				$variable = 'pagina=plantillaInfoTecnica';
				$variable .= '&mensajeModal=errorInformacionCargar';
				$variable .= '&log=' . $valor;
				break;
			
			case "ErrorTecnologia" :
				$variable = 'pagina=plantillaInfoTecnica';
				$variable .= '&mensajeModal=errorTecnologia';
				
				break;
			
			case "ExitoInformacion" :
				$variable = 'pagina=plantillaInfoTecnica';
				$variable .= '&mensajeModal=exitoInformacion';
				break;
			
			case "ErrorCreacionContratos" :
				$variable = 'pagina=plantillaInfoTecnica';
				$variable .= '&mensajeModal=errorCreacionContratos';
				break;
			
			case "ExitoRegistroProceso" :
				$variable = 'pagina=plantillaInfoTecnica';
				$variable .= '&mensajeModal=exitoRegistroProceso';
				break;
			
			case "ErrorRegistroProceso" :
				$variable = 'pagina=plantillaInfoTecnica';
				$variable .= '&mensajeModal=errorRegistroProceso';
				break;
			
			case "ErrorActualizacion" :
				$variable = 'pagina=plantillaInfoTecnica';
				$variable .= '&mensajeModal=errorActualizacion';
				break;
			
			case "ErrorActualizacioncabecera" :
				$variable = 'pagina=plantillaInfoTecnica';
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
