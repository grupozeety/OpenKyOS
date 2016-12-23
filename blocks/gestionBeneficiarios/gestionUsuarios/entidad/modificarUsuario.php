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
		
		if(isset($_REQUEST ['estado_cuenta']) & $_REQUEST ['estado_cuenta'] == 2){
			$_REQUEST ['rol'] = "inactivo";
		}
		
		if($_REQUEST['telefono']==""){
			$_REQUEST['telefono'] = "0";
		}
			
		$this->changePassword ( $_REQUEST ['nombre_completo'], $_REQUEST ['user'], $_REQUEST ['correo_electronico'], $_REQUEST ['telefono'], $_REQUEST['rol']);
		
		$datos = array('nombre_completo' => $_REQUEST ['nombre_completo'], 'nombre_usuario' => $_REQUEST ['user'], 'correo_electronico' => $_REQUEST ['correo_electronico'], 'telefono' => $_REQUEST ['telefono'], 'rol' => $_REQUEST['rol'], 'mensaje' =>  $this->message['error']);
		
		if(isset($this->message['error']) && $this->message['error'] != ""){
			Redireccionador::redireccionar("errorModificar", $datos);
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
	function changePassword($nombre, $user, $correo, $telefono, $rol) {
		
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
		$user_search = ldap_search ( $con, $dn, "(|(uid=$user))" );
		
		$user_get = ldap_get_entries ( $con, $user_search );

		$rolAnt = $user_get[0]['description'][0];
		
		$testing = 0;
		
		if($user_get[0]['mail'][0] != $correo){
			
			$user_search = ldap_search ( $con, $dn, "(|(mail=$correo))" );
			
			$user_get2 = ldap_get_entries ( $con, $user_search );
			
			if ($user_get2['count'] > 0) {
				$message ['error'] .= "Error E100 - Otro usuario tiene registrado el correo ingresado.\n";
				$testing ++;
			}
		}
		
		if (! $user_get) {
			$message ['error'] .= "Error E200 - No se puede conectar al servidor, no se ha podido modificar el usuario en este momento, lo sentimos.\n";
			$testing ++;
		}
		
		if ($testing == 0) {
			
			$ldapbind = ldap_bind ( $con, $ldaprdn, $ldappass );
			
			$dnUser = 'uid=' . $user . ',ou=' . $rolAnt . ',' . $dn;
			$ldaprecord['description'] = $rol;
			$ldaprecord['ou'] = 'ou=' . $rol . ',' . $dn;
			$ldaprecord['telephoneNumber'] = $telefono;
			$ldaprecord['mail'] = $correo;
			$ldaprecord['givenName'] = $nombre;
				
			$result = ldap_modify($con, $dnUser, $ldaprecord);
			 
			if ($result === false) {
				$error = ldap_error ( $con );
				$errno = ldap_errno ( $con );
				$message ['error'] .= "E201 - No se ha podido modificar el usuario, por favor póngase en contacto con el administrador.\n";
				$message ['error'] .= "$errno - $error";
			} else {
				
				if($rolAnt != $rol){
					$dnUserNew = 'uid=' . $user . ',ou=' . $rol . ',' . $dn;
					$result = ldap_rename($con, $dnUser, "uid=".$user, 'ou=' . $rol . ',' . $dn, true);
				}
				
				if ($result === false) {
					$error = ldap_error ( $con );
					$errno = ldap_errno ( $con );
					$message ['error'] .= "E201 - No se ha podido modificar el usuario, por favor póngase en contacto con el administrador.\n";
					$message ['error'] .= "$errno - $error";
				} else {
					$message_css = "yes";
					$message ['sucess'] = "El usuario $nombre ($user) ha sido modificado.";
				}
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
