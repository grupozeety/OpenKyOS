<?php

namespace usuarios\crearUsuario;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	var $miConfigurador;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	function getCadenaSql($tipo, $variable = "") {
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		switch ($tipo) {
			
			/**
			 * Clausulas genéricas.
			 * se espera que estén en todos los formularios
			 * que utilicen esta plantilla
			 */
			case "iniciarTransaccion" :
				$cadenaSql = "START TRANSACTION";
				break;
			
			case "finalizarTransaccion" :
				$cadenaSql = "COMMIT";
				break;
			
			case "cancelarTransaccion" :
				$cadenaSql = "ROLLBACK";
				break;
			
			case "eliminarTemp" :
				
				$cadenaSql = "DELETE ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_sesion = '" . $variable . "' ";
				break;
			
			case "insertarTemp" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "( ";
				$cadenaSql .= "id_sesion, ";
				$cadenaSql .= "formulario, ";
				$cadenaSql .= "campo, ";
				$cadenaSql .= "valor, ";
				$cadenaSql .= "fecha ";
				$cadenaSql .= ") ";
				$cadenaSql .= "VALUES ";
				
				foreach ( $_REQUEST as $clave => $valor ) {
					$cadenaSql .= "( ";
					$cadenaSql .= "'" . $idSesion . "', ";
					$cadenaSql .= "'" . $variable ['formulario'] . "', ";
					$cadenaSql .= "'" . $clave . "', ";
					$cadenaSql .= "'" . $valor . "', ";
					$cadenaSql .= "'" . $variable ['fecha'] . "' ";
					$cadenaSql .= "),";
				}
				
				$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
				break;
			
			case "rescatarTemp" :
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_sesion, ";
				$cadenaSql .= "formulario, ";
				$cadenaSql .= "campo, ";
				$cadenaSql .= "valor, ";
				$cadenaSql .= "fecha ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= $prefijo . "tempFormulario ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_sesion='" . $idSesion . "'";
				break;
			
			/* Consultas del desarrollo */
				
			case "roles" :
				$cadenaSql = "SELECT";
				$cadenaSql .= " id_rol,";
				$cadenaSql .= "	descripcion";
				$cadenaSql .= " FROM ";
				$cadenaSql .= " menu.community_rol";
				break;
				
			case "tipoDocumento" :
				$cadenaSql = "SELECT";
				$cadenaSql .= " idtipodoc,";
				$cadenaSql .= "	descripcion";
				$cadenaSql .= " FROM ";
				$cadenaSql .= " data.tipodocumento";
				$cadenaSql .= " WHERE estado_registro=true";
				break;
				
				
			case "registrarUsuario" :
				
				$cadenaSql="BEGIN; ";
				$cadenaSql.="WITH rows AS (";
				$cadenaSql.="INSERT INTO um_user";
				$cadenaSql.=" (";
				$cadenaSql.=" um_user_name,";
				$cadenaSql.=" um_user_password,";
				$cadenaSql.=" um_salt_value,";
				$cadenaSql.=" um_changed_time,";
				$cadenaSql.=" um_tenant_id";
				$cadenaSql.=" )";
				$cadenaSql.=" VALUES";
				$cadenaSql.=" (";
				$cadenaSql.=" '" . $variable['usuario']. "',";
				$cadenaSql.=" '" . $variable['contrasena']. "',";
				$cadenaSql.=" '" . $variable['salt']. "',";
				$cadenaSql.=" '" . $variable['timestamp']. "',";
				$cadenaSql.=" '" . $variable['tenant']. "'";
				$cadenaSql.=" ) RETURNING um_id) ";
				
				$cadenaSql.="INSERT INTO um_user_attribute";
				$cadenaSql.=" (";
				$cadenaSql.=" um_attr_name,";
				$cadenaSql.=" um_attr_value,";
				$cadenaSql.=" um_profile_id,";
				$cadenaSql.=" um_user_id,";
				$cadenaSql.=" um_tenant_id";
				$cadenaSql.=" )";
				$cadenaSql.=" VALUES";
				$cadenaSql.=" (";
				$cadenaSql.=" '" . "givenName" . "',";
				$cadenaSql.=" '" . $variable['usuario']. "',";
				$cadenaSql.=" '" . $variable['profile']. "',";
				$cadenaSql.=" " . "(SELECT um_id FROM rows)" . ",";
				$cadenaSql.=" '" . $variable['tenant']. "'";
				$cadenaSql.=" ); ";
				
				$cadenaSql.="INSERT INTO um_hybrid_user_role";
				$cadenaSql.=" (";
				$cadenaSql.=" um_user_name,";
				$cadenaSql.=" um_role_id,";
				$cadenaSql.=" um_tenant_id,";
				$cadenaSql.=" um_domain_id";
				$cadenaSql.=" )";
				$cadenaSql.=" VALUES";
				$cadenaSql.=" (";
				$cadenaSql.=" '" . $variable['usuario']. "',";
				$cadenaSql.=" '" . $variable['rol']. "',";
				$cadenaSql.=" '" . $variable['tenant']. "',";
				$cadenaSql.=" '" . $variable['domain']. "'";
				$cadenaSql.=" ); ";
				$cadenaSql.="COMMIT; ";
				break;
				
			case "registrarDatosBasicosUsuario" :
					
					$cadenaSql="INSERT INTO data.datosbasicos";
					$cadenaSql.=" (";
					$cadenaSql.=" idusuario,";
					$cadenaSql.=" tipodocumento,";
					$cadenaSql.=" nombreusuario,";
					$cadenaSql.=" apellidousuario,";
					$cadenaSql.=" direccion,";
					$cadenaSql.=" correo,";
					$cadenaSql.=" telefono,";
					$cadenaSql.=" celular,";
					$cadenaSql.=" estado_registro,";
					$cadenaSql.=" fecha_registro,";
					$cadenaSql.=" estrato";
					$cadenaSql.=" )";
					$cadenaSql.=" VALUES";
					$cadenaSql.=" (";
					$cadenaSql.=" '" . $variable['documentoUsuario']. "',";
					$cadenaSql.=" '" . $variable['tipoDocumento']. "',";
					$cadenaSql.=" '" . $variable['nombreUsuario']. "',";
					$cadenaSql.=" '" . $variable['apellidoUsuario']. "',";
					$cadenaSql.=" '" . $variable['direccion']. "',";
					$cadenaSql.=" '" . $variable['email']. "',";
					$cadenaSql.=" '" . $variable['telefono']. "',";
					$cadenaSql.=" '" . $variable['celular']. "',";
					$cadenaSql.=" '" . "TRUE". "',";
					$cadenaSql.=" '" . $variable['timestamp']. "',";
					$cadenaSql.=" '" . $variable['estratoUsuario']. "'";
					$cadenaSql.=");";
					break;
					
			case "errorRegistro":
				$cadenaSql="DELETE FROM data.datosbasicos WHERE idusuario ='". $variable['documentoUsuario']."';";
				break;
				
		}
		
		
		
		return $cadenaSql;
	}
}

?>
