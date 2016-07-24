<?php

namespace usuarios\crearUsuario\funcion;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("index.php");
	exit ();
}
class redireccion {
	public static function redireccionar($opcion, $valor = "") {
		$miConfigurador = \Configurador::singleton ();
		$miPaginaActual = $miConfigurador->getVariableConfiguracion ( "pagina" );
		
		switch ($opcion) {
			case "inserto" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=confirma";
				$variable .= "&usuario=" . $valor;
				break;
			
			case "noInserto" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=error";
				$variable .= "&usuario=" . $valor;
				break;
			
			case "noItems" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=otros";
				$variable .= "&errores=noItems";
				break;
			
			case "noDatos" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=otros";
				$variable .= "&errores=noDatos";
				break;
			
			case "regresar" :
				$variable = "pagina=" . $miPaginaActual;
				break;
				
			case "continuar" :
				$variable = "pagina=" . $miPaginaActual;
				break;
				
			case "actualizo" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=actualizo";
				$variable .= "&docente=" . $valor;
				break;
			
			case "noActualizo" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=noActualizo";
				$variable .= "&docente=" . $valor;
				break;
			
			case "registrar" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=asociarActa";
				break;
			
			case "paginaPrincipal" :
				$variable = "pagina=" . $miPaginaActual;
				break;
			
			case "paginaConsulta" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=consultar";
				$variable .= "&id_docente=".$valor[0];
				$variable .= "&facultad=".$valor[1];
				$variable .= "&proyectoCurricular=".$valor[2];
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
		
	}
}

?>