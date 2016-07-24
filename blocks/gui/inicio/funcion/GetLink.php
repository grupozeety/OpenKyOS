<?php
namespace reportico;
if (! isset ( $GLOBALS ['autorizado'] )) {
	include ('index.php');
	exit ();
}

namespace gui\menuPrincipal\funcion;
// //Se usa de la siguiente manera:

// include_once ('funcion/GetLink.php');
// use reportico\funcion\GetLink;
// 	GetLink::ir("idp");

class GetLink {
	public static function obtener($nombrePagina) {
		$miConfigurador = \Configurador::singleton ();
		$miPaginaActual = $miConfigurador->getVariableConfiguracion ( 'pagina' );
		$variable = 'pagina=' . $nombrePagina;		
		$url = $miConfigurador->configuracion ['host'] . $miConfigurador->configuracion ['site'] . '/index.php?';
		$enlace = $miConfigurador->configuracion ['enlace'];
		$variable = $miConfigurador->fabricaConexiones->crypto->codificar ( $variable );
		$_REQUEST [$enlace] = $enlace . '=' . $variable;
		$direccion = $url . $_REQUEST [$enlace];
		
		return $direccion;
	}
	public static function ir($nombrePagina) {
		$direccion = self::obtener($nombrePagina);
		echo "<script>location.replace('" . $direccion . "')</script>";
		return true;
	}
}
?>