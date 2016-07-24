<?php

namespace usuarios\crearUsuario\funcion;

use usuarios\crearUsuario\funcion\redireccionar;

include_once ('redireccionar.php');
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class Registrar {
	
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSql;
	var $conexion;
	
	function __construct($lenguaje, $sql, $funcion) {
		
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miFuncion = $funcion;
	}
	function procesarFormulario() {
		$conexion = "usuario";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/usuarios/crearUsuario/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/usuarios/crearUsuario/" . $esteBloque ['nombre'];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'validarContrasena', $_REQUEST['usuario'] );
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$_REQUEST['contrasena'] = base64_encode(pack('H*',hash('sha256', $_REQUEST['contrasenaAntigua'])));
		$_REQUEST['contrasenaNueva'] = base64_encode(pack('H*',hash('sha256', $_REQUEST['contrasenaNueva'])));
		$time = explode(" ",microtime());
		$_REQUEST['timestamp'] = date("y-m-d H:i:s",$time[1]).substr((string)$time[0],1,4);
		
		if(count($resultado)>0 && ($resultado[0]['um_user_password']==$_REQUEST['contrasena'])){
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarContrasena', $_REQUEST);
			$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "actualizar" );
		}
		
		if ($resultado) {
			redireccion::redireccionar ( 'actualizo');
			exit ();
		} else {
			redireccion::redireccionar ( 'noActualizo');
			exit ();
		}
	}
		
	function resetForm() {
		foreach ( $_REQUEST as $clave => $valor ) {
			
			if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
				unset ( $_REQUEST [$clave] );
			}
		}
	}
}

$miRegistrador = new Registrar ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();

?>
