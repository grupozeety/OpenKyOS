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
	public $miSesionSso;
	public $message;
	
	public function __construct($sql) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->miSql = $sql;
		$this->rutaURL = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" );
		$this->miSesionSso = \SesionSso::singleton ();
		
		$info_usuario = $this->miSesionSso->getParametrosSesionAbierta ();
		
		// Conexion a Base de Datos
		$conexion = "interoperacion";
		$this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$_REQUEST ['usuario'] = $info_usuario ['uid'] [0];
		
		$this->changePassword ( $_REQUEST ['usuario'], $_REQUEST ['contrasena_actual'], $_REQUEST ['contrasena_nueva'], $_REQUEST ['contrasena_nueva_val'] );
		
		if(isset($this->message['error']) && $this->message['error'] != ""){
			Redireccionador::redireccionar("error", $this->message['error']);
		}else if(isset($this->message['sucess']) && $this->message['sucess'] != ""){
			Redireccionador::redireccionar("sucess", $this->message['sucess']);
		}

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
	function changePassword($user, $oldPassword, $newPassword, $newPasswordCnf) {
		
		global $message;
		global $message_css;
		
		$cadenaSql = $this->miSql->getCadenaSql('consultarInformacionApi', 'ldap');
		$ldap = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];
		
		$server = $ldap['host'];
		$dn = $ldap['ruta_cookie'];
		
		$ldaprdn = $ldap['usuario'];
		$ldappass = $ldap['password'];
		
		error_reporting ( 0 );
		$con = ldap_connect ( $server );
		ldap_set_option ( $con, LDAP_OPT_PROTOCOL_VERSION, 3 );
		
		// bind anon and find user by uid
		$user_search = ldap_search ( $con, $dn, "(|(uid=$user)(mail=$user))" );
		$user_get = ldap_get_entries ( $con, $user_search );
		$user_entry = ldap_first_entry ( $con, $user_search );
		$user_dn = ldap_get_dn ( $con, $user_entry );
		$user_id = $user_get [0] ["uid"] [0];
		$user_givenName = $user_get [0] ["givenName"] [0];
		$user_search_arry = array (
				"*",
				"ou",
				"uid",
				"mail",
				"passwordRetryCount",
				"passwordhistory" 
		);
		$user_search_filter = "(|(uid=$user_id)(mail=$user))";
		$user_search_opt = ldap_search ( $con, $user_dn, $user_search_filter, $user_search_arry );
		$user_get_opt = ldap_get_entries ( $con, $user_search_opt );
		$passwordRetryCount = $user_get_opt [0] ["passwordRetryCount"] [0];
		$passwordhistory = $user_get_opt [0] ["passwordhistory"] [0];
		
		$testing = 0;
		/* Start the testing */
		if ($passwordRetryCount == 3) {
			$message ['error'] .= "Error E101 - Su cuenta está bloqueada!!!\n";
			$testing ++;
		}
		if (ldap_bind ( $con, $user_dn, $oldPassword ) === false) {
			$message ['error'] .= "Error E101 - Nombre de usuario actual o contraseña incorrecta.\n";
			$testing ++;
		}
		if ($newPassword != $newPasswordCnf) {
			$message ['error'] .= "Error E102 - Sus nuevas contraseñas no coinciden!\n";
			$testing ++;
		}
		$encoded_newPassword = "{SHA}" . base64_encode ( pack ( "H*", sha1 ( $newPassword ) ) );
		$history_arr = ldap_get_values ( $con, $user_dn, "passwordhistory" );
		if ($history_arr) {
			$message ['error'] .= "Error E102 - Su nueva contraseña coincide con una de las últimas 10 contraseñas que usó, debe crear una nueva contraseña.\n";
			$testing ++;
		}
		if (strlen ( $newPassword ) < 8) {
			$message ['error'] .= "Error E103 - Su nueva contraseña es demasiado corta. <br/> Su contraseña debe tener al menos 8 caracteres.\n";
			$testing ++;
		}
		if (! preg_match ( "/[0-9]/", $newPassword )) {
			$message ['error'] .= "Error E104 - Su nueva contraseña debe contener al menos un número.\n";
			$testing ++;
		}
		if (! preg_match ( "/[a-zA-Z]/", $newPassword )) {
			$message ['error'] .= "Error E105 - Su nueva contraseña debe contener al menos una letra.\n";
			$testing ++;
		}
		if (! preg_match ( "/[A-Z]/", $newPassword )) {
			$message ['error'] .= "Error E106 - Su nueva contraseña debe contener al menos una letra mayúscula.\n";
			$testing ++;
		}
		if (! preg_match ( "/[a-z]/", $newPassword )) {
			$message ['error'] .= "Error E107 - Su nueva contraseña debe contener al menos una letra minúscula.\n";
			$testing ++;
		}
		if (! $user_get) {
			$message ['error'] .= "Error E200 - No se puede conectar al servidor, no puede cambiar su contraseña en este momento, lo siento.\n";
			$testing ++;
		}
		
		if ($testing == 0) {
			
			$ldapbind = ldap_bind ( $con, $ldaprdn, $ldappass );
			
			$auth_entry = ldap_first_entry ( $con, $user_search );
			$mail_addresses = ldap_get_values ( $con, $auth_entry, "mail" );
			$given_names = ldap_get_values ( $con, $auth_entry, "givenName" );
			$password_history = ldap_get_values ( $con, $auth_entry, "passwordhistory" );
			$mail_address = $mail_addresses [0];
			$first_name = $given_names [0];
			
			/* And Finally, Change the password */
			$entry = array ();
			$entry ["userPassword"] = "$encoded_newPassword";
			
			if (ldap_modify ( $con, $user_dn, $entry ) === false) {
				$error = ldap_error ( $con );
				$errno = ldap_errno ( $con );
				
				var_dump ( $errno );
				$message ['error'] .= "E201 - Su contraseña no puede ser cambiada, por favor póngase en contacto con el administrador.\n";
				$message ['error'] .= "$errno - $error";
			} else {
				$message_css = "yes";
				$message ['sucess'] = "La contraseña para $user_id ha sido cambiada. \n Su nueva contraseña está completamente activa.\n";
			}
		}
		
		$this->message = $message;
	}
}
$miDocumento = new GenerarDocumento ( $this->sql );

?>
