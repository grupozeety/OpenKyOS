<?php

namespace gestionComisionamiento\archivosAlfresco;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include "../index.php";
	exit ();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	public $miConfigurador;
	public function getCadenaSql($tipo, $variable = '') {
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		switch ($tipo) {
			
			/**
			 * Clausulas específicas
			 */
			
			// Sincronización Comisionamiento
			case "alfrescoUser" :
				$cadenaSql = " SELECT id_beneficiario, nombre_carpeta_dep as padre, nombre_carpeta_mun as hijo, site_alfresco as site ";
				$cadenaSql .= " FROM interoperacion.beneficiario_potencial ";
				$cadenaSql .= " INNER JOIN interoperacion.carpeta_alfresco on beneficiario_potencial.departamento=cast(carpeta_alfresco.cod_departamento as integer) ";
				$cadenaSql .= " WHERE cast(cod_municipio as integer)=municipio ";
				$cadenaSql .= " AND identificacion='" . $variable . "' ";
				break;
			
			case "alfrescoCarpetas" :
				$cadenaSql = "SELECT parametros.codigo, parametros.descripcion ";
				$cadenaSql .= " FROM parametros.parametros ";
				$cadenaSql .= " JOIN parametros.relacion_parametro ON relacion_parametro.id_rel_parametro=parametros.rel_parametro ";
				$cadenaSql .= " WHERE parametros.estado_registro=TRUE AND relacion_parametro.descripcion='Alfresco Folders' ";
				break;
			
			case "alfrescoDirectorio" :
				$cadenaSql = "SELECT parametros.descripcion ";
				$cadenaSql .= " FROM parametros.parametros ";
				$cadenaSql .= " JOIN parametros.relacion_parametro ON relacion_parametro.id_rel_parametro=parametros.rel_parametro ";
				$cadenaSql .= " WHERE parametros.estado_registro=TRUE AND relacion_parametro.descripcion='Directorio Alfresco Site' ";
				break;
			
			case "alfrescoLog" :
				$cadenaSql = "SELECT host, usuario, password ";
				$cadenaSql .= " FROM parametros.api_data ";
				$cadenaSql .= " WHERE componente='alfresco' ";
				break;
			
            case 'consultarBeneficiariosPotenciales':
                $cadenaSql = " SELECT DISTINCT identificacion ||' - ('||nombre||' '||primer_apellido||' '||segundo_apellido||')' AS  value, id_beneficiario  AS data  ";
                $cadenaSql .= " FROM  interoperacion.beneficiario_potencial ";
                $cadenaSql .= "WHERE estado_registro=TRUE ";
                $cadenaSql .= "AND  cast(identificacion  as text) ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "OR nombre ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "OR primer_apellido ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "OR segundo_apellido ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;
				
		}
		
		return $cadenaSql;
	}
}
?>

