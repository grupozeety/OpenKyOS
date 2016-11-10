<?php

namespace registroBeneficiario;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";
include_once "core/auth/SesionSso.class.php";
// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	public $miConfigurador;
	public $miSesionSso;
	public function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	public function getCadenaSql($tipo, $variable = "") {
		
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
			
			case 'consultarBeneficiariosPotenciales' :
				$cadenaSql = " SELECT value , data ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "(SELECT DISTINCT identificacion ||' - ('||nombre||' '||primer_apellido||' '||segundo_apellido||')' AS  value, bp.id_beneficiario  AS data ";
				$cadenaSql .= "FROM  interoperacion.beneficiario_potencial bp ";
				$cadenaSql .= "LEFT JOIN interoperacion.agendamiento_comisionamiento ac on ac.id_beneficiario=bp.id_beneficiario ";
				$cadenaSql .= "WHERE bp.estado_registro=TRUE ";
				$cadenaSql .= $variable ;
				$cadenaSql .= "		) datos ";
				$cadenaSql .= "WHERE value ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "LIMIT 10; ";
				break;
			
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
			
			case 'consultarUrbanizacion' :
				
				$cadenaSql = " SELECT DISTINCT id_proyecto ||' - '|| proyecto AS  value, id_proyecto  AS data  ";
				$cadenaSql .= " FROM  interoperacion.beneficiario_potencial ";
				$cadenaSql .= "WHERE estado_registro=TRUE ";
				$cadenaSql .= "AND  cast(id_proyecto  as text) ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "OR proyecto ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "LIMIT 10; ";
				break;
			
			case 'consultarBloqueManzana' :
				
				$cadenaSql = " SELECT DISTINCT manzana AS  value, manzana  AS data  ";
				$cadenaSql .= " FROM  interoperacion.beneficiario_potencial ";
				$cadenaSql .= "WHERE estado_registro=TRUE ";
				$cadenaSql .= "AND  cast(manzana  as text) ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "LIMIT 10; ";
				break;
			
			case 'consultarCasaAparta' :
				
				$cadenaSql = " SELECT DISTINCT apartamento AS  value, apartamento  AS data  ";
				$cadenaSql .= " FROM  interoperacion.beneficiario_potencial ";
				$cadenaSql .= "WHERE estado_registro=TRUE ";
				$cadenaSql .= "AND  cast(apartamento  as text) ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "LIMIT 10; ";
				break;
			
			case "consultarBeneficiario" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "proyecto  AS urbanizacion,";
				$cadenaSql .= "(nombre ||' '|| primer_apellido ||' '|| segundo_apellido) as nombre,";
				$cadenaSql .= "identificacion,";
				$cadenaSql .= "tipoben.descripcion as tipo_beneficiario, ";
				$cadenaSql .= "id_beneficiario ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "interoperacion.beneficiario_potencial, ";
				$cadenaSql .= "(SELECT        ";
				$cadenaSql .= "codigo, ";
				$cadenaSql .= "param.descripcion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN ";
				$cadenaSql .= "parametros.relacion_parametro as rparam ";
				$cadenaSql .= "ON ";
				$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "rparam.descripcion = 'Tipo de Beneficario o Cliente') AS tipoben ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "tipo_beneficiario=cast ( tipoben.codigo as int8) ";
				$cadenaSql .= "AND ";
				$cadenaSql .= "estado_registro=true ";
				
				break;
			
			case "parametroTipoBeneficiario" :
				
				break;
			
			case "inhabilitarBeneficiario" :
				
				$cadenaSql = "UPDATE interoperacion.beneficiario_potencial ";
				$cadenaSql .= "SET ";
				$cadenaSql .= "estado_registro=FALSE ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_beneficiario=" . "'" . $variable . "'";
				break;
		}
		
		return $cadenaSql;
	}
}

?>
