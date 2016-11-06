<?php

namespace registroBeneficiario\funcion;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "index.php";
	exit ();
}
class redireccion {
	public static function redireccionar($opcion, $valor = "") {
		$miConfigurador = \Configurador::singleton ();
		$miPaginaActual = $miConfigurador->getVariableConfiguracion ( "pagina" );
		
		switch ($opcion) {
			
			case "noExisteBeneficiario" :
				$variable = "pagina=consultarBeneficiario";
				$variable .= "&mensaje=errorBeneficiario";
				break;
			
			case "multipleBeneficiario" :
				$variable = "pagina=consultarBeneficiario";
				$variable .= "&mensaje=errorMultipleBeneficiario";
				$variable .= "&informacion=" . $valor;
				break;
			 
			case "inserto" :
				$variable = "pagina=consultarBeneficiario";
				// $variable .= "&opcion=mensaje";
				$variable .= "&mensaje=confirma";
				break;
			 
			case "noAlfresco" :
				$variable = "pagina=consultarBeneficiario";
				// $variable .= "&opcion=mensaje";
				$variable .= "&mensaje=noAlfresco";
				break;
			
			case "noInserto" :
				$variable = "pagina=consultarBeneficiario";
				// $variable .= "&opcion=mensaje";
				$variable .= "&mensaje=error";
				break;
			
			case "noItems" :
				$variable = "pagina=consultarBeneficiario";
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=otros";
				$variable .= "&errores=noItems";
				break;
			
			case "noDatos" :
				$variable = "pagina=consultarBeneficiario";
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=otros";
				$variable .= "&errores=noDatos";
				break;
			
			case "regresar" :
				$variable = "pagina=consultarBeneficiario";
				break;
			
			case "actualizo" :
				$variable = "pagina=consultarBeneficiario";
				$variable .= "&mensaje=confirmaAct";
				break;
			
			case "noActualizo" :
				$variable = "pagina=consultarBeneficiario";
				$variable .= "&mensaje=errorAct";
				break;
			
			case "registrar" :
				$variable = "pagina=consultarBeneficiario";
				$variable .= "&opcion=asociarActa";
				break;
			
			case "paginaPrincipal" :
				$variable = "pagina=consultarBeneficiario";
				break;
			
			case "paginaConsulta" :
				$variable = "pagina=consultarBeneficiario";
				$variable .= "&opcion=consultar";
				$variable .= "&id_variable=" . $valor [0];
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