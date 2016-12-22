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
		
		$this->changePassword ( $_REQUEST ['nombre_completo'], $_REQUEST ['nombre_usuario'], $_REQUEST ['contrasena'], $_REQUEST ['contrasena_val'], $_REQUEST ['correo_electronico'], $_REQUEST ['telefono'], $_REQUEST['rol']);
		
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
	function changePassword($nombre, $user, $Password, $PasswordCnf, $correo, $telefono, $rol) {
		
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
		$user_search = ldap_search ( $con, $dn, "(|(uid=$user)(mail=$correo))" );
		
		$user_get = ldap_get_entries ( $con, $user_search );

		$largestUID = $this->findLargestUidNumber($con, $dn);
		$largestGID = $this->findLargestGidNumber($con, $dn);
		
		$testing = 0;
		
    	//$filter="(objectClass=person)";
    	
    	//$justthese = array("uid", "sn", "mail");

    	//$sr=ldap_search($con, $dn, $filter, $justthese);

    	//$info = ldap_get_entries($con, $sr);
		
		/* Start the testing */
		
		if ($user_get['count'] > 0) {
			$message ['error'] .= "Error E100 - Ya existe un usuario registrado con el correo y/o usuario ingresado.\n";
			$testing ++;
		}
		if ($Password != $PasswordCnf) {
			$message ['error'] .= "Error E102 - Las contraseñas no coinciden!\n";
			$testing ++;
		}
		$encoded_newPassword = "{SHA}" . base64_encode ( pack ( "H*", sha1 ( $Password ) ) );
		if (strlen ( $Password ) < 8) {
			$message ['error'] .= "Error E103 - La contraseña es demasiado corta. <br/> Su contraseña debe tener al menos 8 caracteres.\n";
			$testing ++;
		}
		if (! preg_match ( "/[0-9]/", $Password )) {
			$message ['error'] .= "Error E104 - La contraseña debe contener al menos un número.\n";
			$testing ++;
		}
		if (! preg_match ( "/[a-zA-Z]/", $Password )) {
			$message ['error'] .= "Error E105 - La contraseña debe contener al menos una letra.\n";
			$testing ++;
		}
		if (! preg_match ( "/[A-Z]/", $Password )) {
			$message ['error'] .= "Error E106 - La contraseña debe contener al menos una letra mayúscula.\n";
			$testing ++;
		}
		if (! preg_match ( "/[a-z]/", $Password )) {
			$message ['error'] .= "Error E107 - La contraseña debe contener al menos una letra minúscula.\n";
			$testing ++;
		}
		if (! $user_get) {
			$message ['error'] .= "Error E200 - No se puede conectar al servidor, no puede crear el usuario en este momento, lo siento.\n";
			$testing ++;
		}
		
		if ($testing == 0) {
			
			$ldapbind = ldap_bind ( $con, $ldaprdn, $ldappass );
			
			$dnUser = 'uid=' . $user . ',ou=' . $rol . ',' . $dn;
			$ldaprecord['objectclass'][0] = "top";
			$ldaprecord['objectclass'][1] = "person";
			$ldaprecord['objectclass'][2] = "organizationalPerson";
			$ldaprecord['objectclass'][3] = "inetOrgPerson";
			$ldaprecord['objectclass'][4] = "posixAccount";
			$ldaprecord['uid'] = $user;
			$ldaprecord['cn'] = $user;
			$ldaprecord['sn'] = $user;
			$ldaprecord['userPassword'] = $encoded_newPassword;
			$ldaprecord['loginShell'] = '/bin/bash';
			$ldaprecord['uidNumber'] = $largestUID + 1;
			$ldaprecord['gidNumber'] = $largestGID + 1;
			$ldaprecord['homeDirectory'] = '/home/' . $user;
			$ldaprecord['description'] = $rol;
			$ldaprecord['ou'] = 'ou=' . $rol . ',' . $dn;
			$ldaprecord['telephoneNumber'] = $telefono;
			$ldaprecord['mail'] = $correo;
			$ldaprecord['givenName'] = $nombre;
			$ldaprecord['superAdmin'] = 'false';
				
			$result = ldap_add($con, $dnUser, $ldaprecord);
			 
			if ($result === false) {
				$error = ldap_error ( $con );
				$errno = ldap_errno ( $con );
				$message ['error'] .= "E201 - No se ha podido crear el usuario, por favor póngase en contacto con el administrador.\n";
				$message ['error'] .= "$errno - $error";
			} else {
				$message_css = "yes";
				$message ['sucess'] = "El usuario $nombre ($user) ha sido creado. \n Está completamente activo.\n";
			}
			
		}
		
		$this->message = $message;
	}
	
	function findLargestUidNumber($ds, $dn)
	{
		$s = ldap_search($ds, $dn, 'uidnumber=*');
		if ($s)
		{
			// there must be a better way to get the largest uidnumber, but I can't find a way to reverse sort.
			ldap_sort($ds, $s, "uidnumber");
	
			$result = ldap_get_entries($ds, $s);
			$count = $result['count'];
			$biguid = $result[$count-1]['uidnumber'][0];
			return $biguid;
		}
		return null;
	}
	
	function findLargestGidNumber($ds, $dn)
	{
		$s = ldap_search($ds, $dn, 'gidnumber=*');
		if ($s)
		{
			// there must be a better way to get the largest uidnumber, but I can't find a way to reverse sort.
			ldap_sort($ds, $s, "gidnumber");
	
			$result = ldap_get_entries($ds, $s);
			$count = $result['count'];
			$bigid = $result[$count-1]['gidnumber'][0];
			return $bigid;
		}
		return null;
	}
	
}
$miDocumento = new GenerarDocumento ( $this->sql );

?>
