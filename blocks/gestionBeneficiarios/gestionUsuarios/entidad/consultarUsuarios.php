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
	public $users;
	
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
		
		$this->consultarUsuarios ();
		
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
	function consultarUsuarios() {
		
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
		
    	$filter="(objectClass=person)";
    	
    	$justthese = array("uid", "givenName", "mail", "description", "telephonenumber");

    	$sr=ldap_search($con, $dn, $filter, $justthese);

    	$this->users = ldap_get_entries($con, $sr);
    	
    	$miConfigurador = \Configurador::singleton ();
    	 
    	foreach ($this->users as $key => $user){
    		 
    		$variable = 'pagina=gestionUsuarios';
    		$variable .= '&opcion=editarUsuario';
    		$variable .= '&nombre_usuario=' . $user['uid'][0];
    		$variable .= '&rol=' . $user['description'][0];
    		$variable .= '&nombre_completo=' . $user['givenname'][0];
    		$variable .= '&correo_electronico=' . $user['mail'][0];
    		$variable .= '&telefono=' . $user['telephonenumber'][0];
    		 
    		$url = $miConfigurador->configuracion ["host"] . $miConfigurador->configuracion ["site"] . "/index.php?";
    		$enlace = $miConfigurador->configuracion ['enlace'];
    		$variable = $miConfigurador->fabricaConexiones->crypto->codificar ( $variable );
    		$_REQUEST [$enlace] = $enlace . '=' . $variable;
    		$redireccion = $url . $_REQUEST [$enlace];
    		if($key != "count"){
    			$infoUser[$key-1]['uid'] = "<a href='$redireccion'>" . $user['uid'][0] . "</a>";
    			$infoUser[$key-1]['description'] = $user['description'][0];
    			$infoUser[$key-1]['mail'] = $user['mail'][0];
    			$infoUser[$key-1]['givenname'] = $user['givenname'][0];
    			$infoUser[$key-1]['telephonenumber'] = $user['telephonenumber'][0];
    		}
    	}
    	
    	$total = count ( $infoUser );
    	
    	$resultado = json_encode ( $infoUser );
    	
    	$resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
				"data":' . $resultado . '}';
    	
    	echo $resultado;
		
	}
}
$miDocumento = new GenerarDocumento ( $this->sql );

?>
