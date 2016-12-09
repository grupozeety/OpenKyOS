<?php

namespace cambioClave\entidad;

use cambioClave\entidad\Redireccionador;

include_once 'Redireccionador.php';

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

$ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/plugin/html2pfd/";

class GenerarDocumento {
	public $miConfigurador;
	public $miSql;
	public $conexion;
	public $rutaURL;
	public $esteRecursoDB;
	public $rutaAbsoluta;
	public $message;
	
	public function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		
		// Conexion a Base de Datos
		$conexion = "interoperacion";
		$this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		

		$this->changePassword ( $_REQUEST ['usuario']);
		
	}
	
	/**
	 * LDAP PHP Change Password Webpage
	 *
	 * @author : Matt Rude <http://mattrude.com>
	 *         @website: http://technology.mattrude.com/2010/11/ldap-php-change-password-webpage/
	 *        
	 *        
	 *         GNU GENERAL PUBLIC LICENSE
	 *         Version 2, June 1991
	 *        
	 *         Copyright (C) 1989, 1991 Free Software Foundation, Inc.,
	 *         51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
	 *         Everyone is permitted to copy and distribute verbatim copies
	 *         of this license document, but changing it is not allowed.
	 */
	function changePassword($user) {
		
		global $message;
		global $message_css;
		
		$cadenaSql = $this->miSql->getCadenaSql('consultarInformacionApi', 'ldap');
		$ldap = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
		
		$server = $ldap['host'];
		$dn = $ldap['ruta_cookie'];
		
		error_reporting ( 0 );
		$con = ldap_connect ( $server );
		ldap_set_option ( $con, LDAP_OPT_PROTOCOL_VERSION, 3 );
		
		$user_search = ldap_search ( $con, $dn, "(|(uid=$user)(mail=$user))" );
		$auth_entry = ldap_first_entry ( $con, $user_search );
		$mail_addresses = ldap_get_values ( $con, $auth_entry, "mail" );
		$user = ldap_get_values ( $con, $auth_entry, "uid" );
		
		if($mail_addresses){
			$token = str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789".uniqid());
			$datos = array("usuario" => $user[0], "token" => $token);
			$cadenaSql = $this->miSql->getCadenaSql('registrarRecuperacionClave', $datos);
			$resultado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");
			
			if($resultado){
				$redireccion = Redireccionador::generarRedireccion("restaurar", $datos);
				
				$this->urlApiCorreos = $this->crearUrlEnviarCorreos($mail_addresses[0], $redireccion, $usuario[0]);
				$this->enviarCorreo();
				
				if($this->estadoCorreo != "Message sent!"){
					Redireccionador::redireccionar("errorCorreo");
				}
				
			}

			Redireccionador::redireccionar("sucess", $mail_addresses[0]);
		}else {
			Redireccionador::redireccionar("error");
		}
	}
	
	public function crearUrlEnviarCorreos($var = '', $link='', $usuario='') {
	
		// URL base
		$url = $this->miConfigurador->getVariableConfiguracion("host");
		$url .= $this->miConfigurador->getVariableConfiguracion("site");
		$url .= "/index.php?";
		// Variables
		$variable = "pagina=enviarCorreos";
		$variable .= "&procesarAjax=true";
		$variable .= "&action=index.php";
		$variable .= "&bloqueNombre=" . "enviarCorreos";
		$variable .= "&bloqueGrupo=" . "";
		$variable .= "&tiempo=" . $_REQUEST['tiempo'];
		$variable .= "&metodo=recuperarClave";
		$variable .= "&destinatario=" . $var;
		$variable .= "&destinatario=" . $var;
		$variable .= "&usuario=" . $usuario;
		$variable .= "&link=" . $link;
		// Codificar las variables
		$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
		$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);
	
		// URL definitiva
		$urlApi = $url . $cadena;
	
		return $urlApi;
	}
	
	public function enviarCorreo() {
		$mensaje = file_get_contents($this->urlApiCorreos);
		$this->estadoCorreo = $mensaje;
	}
}
$miDocumento = new GenerarDocumento ( $this->sql );

?>
