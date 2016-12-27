<?php

namespace gestionComisionamiento\gestionRequisitos\entidad;

include_once 'Redireccionador.php';

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

$ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );
$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/plugin/html2pfd/";

class CrearUsuario {
	public $miConfigurador;
	public $miSql;
	public $conexion;
	public $rutaURL;
	public $esteRecursoDB;
	public $rutaAbsoluta;
	public $miSesionSso;
	public $message;
	
	public function iniciar($sql) {
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
		
		if($_REQUEST['telefono']==""){
			$_REQUEST['telefono'] = "0";
		}
		
		$this->createUser ( $_REQUEST ['nombre_completo'], $_REQUEST ['nombre_usuario'], $_REQUEST ['contrasena'], $_REQUEST ['correo_electronico'], $_REQUEST ['telefono'], $_REQUEST['rol']);
		
		return $this->message;
		
	}
	

	function createUser($nombre, $user, $Password, $correo, $telefono, $rol) {
		
		$this->message = array();
		
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
		
		/* Start the testing */
		
		if ($user_get['count'] > 0) {
			$message ['error'] .= "Error E100 - Ya existe un usuario registrado con el correo y/o usuario ingresado.\n";
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
				
				$this->urlApiCorreos = $this->crearUrlEnviarCorreos($correo, $user, $Password, $this->rutaURL, $rol);
				//$this->enviarCorreo();
				
				if($this->estadoCorreo != "Message sent!"){
					$message ['sucess'] .= "<br> <b>!No ha sido posible enviar el correo con la información de la nueva cuenta al usuario, por favor verifique la cuenta de correo y/o envíele uno con el nombre de usuario para que restaure la contraseña!</b> \n";
				}
			}
			
		}
		
		$this->message = $message;
	}
	
	public function crearUrlEnviarCorreos($var = '', $usuario='', $contrasena='',$link='', $rol='') {
	
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
		$variable .= "&metodo=crearUsuario";
		$variable .= "&destinatario=" . $var;
		$variable .= "&usuario=" . $usuario;
		$variable .= "&contrasena=" . $contrasena;
		$variable .= "&link=" . $link;
		$variable .= "&rol=" . $rol;
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

?>
