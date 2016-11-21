<?php

namespace gestionComisionamiento\agendaComisionador;

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
		
		// $this->miSesionSso = \SesionSso::singleton();
	}
	public function getCadenaSql($tipo, $variable = "") {
		
		// $info_usuario = $this->miSesionSso->getParametrosSesionAbierta();
		
		// foreach ($info_usuario['description'] as $key => $rol) {
		
		// $info_usuario['rol'][] = $rol;
		
		// }
		
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
			case "consultarAgendaVia" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "bp.id_beneficiario, ";
				$cadenaSql .= "ac.fecha_agendamiento::timestamp::date AS fecha, ";
				$cadenaSql .= "ac.consecutivo AS consecutivo, ";
				$cadenaSql .= "ac.id_agendamiento AS id_agendamiento, ";
				$cadenaSql .= "bp.proyecto AS urbanizacion, ";
				$cadenaSql .= "ta.descripcion AS tipo_agendamiento, ";
				$cadenaSql .= "bp.identificacion ||' - '|| bp.nombre ||' '||bp.primer_apellido||' '||bp.segundo_apellido AS beneficiario, ";
				$cadenaSql .= "CASE estado_contrato WHEN  82 THEN 0"; //0 falta algo
				$cadenaSql .= "WHEN 83 THEN 1 "; //1 todo correcto
				$cadenaSql .= "ELSE 2 "; //no inciado
				$cadenaSql .= "END as estado_agenda, ";
				$cadenaSql .= "CASE estado_contrato WHEN  82 THEN 'En Verificación'"; //0 falta algo
				$cadenaSql .= "WHEN 83 THEN 'Contrato Aprobado' "; //1 todo correcto
				$cadenaSql .= "ELSE 'No iniciado' "; //no inciado
				$cadenaSql .= "END as etiqueta_agenda ";
				$cadenaSql .= "FROM interoperacion.agendamiento_comisionamiento as ac ";
				$cadenaSql .= "LEFT JOIN interoperacion.contrato cn ON cn.id_beneficiario=ac.id_beneficiario ";
				$cadenaSql .= "LEFT JOIN parametros.parametros pr ON pr.id_parametro=estado_contrato ";
				$cadenaSql .= "JOIN interoperacion.beneficiario_potencial as bp ON ac.id_beneficiario=bp.id_beneficiario, ";
				$cadenaSql .= "(SELECT codigo, param.descripcion ";
				$cadenaSql .= "FROM parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN parametros.relacion_parametro as rparam ON (param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE rparam.descripcion = 'Tipo de Agendamiento' ";
				$cadenaSql .= "AND param.codigo=cast(2 as char) ) AS ta ";
				$cadenaSql .= "WHERE ac.estado_registro=true ";
				$cadenaSql .= "AND bp.estado_registro=true ";
				$cadenaSql .= "AND ta.codigo= cast(ac.tipo_agendamiento as char) ";
				$cadenaSql .= str_replace("\\","",$variable);

				break;
			
			case "consultarAgendaIns" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "bp.id_beneficiario,  ";
				$cadenaSql .= "ac.fecha_agendamiento::timestamp::date AS fecha, ";
				$cadenaSql .= "ac.consecutivo AS consecutivo, ";
				$cadenaSql .= "ac.id_agendamiento AS id_agendamiento, ";
				$cadenaSql .= "bp.proyecto AS urbanizacion, ";
				$cadenaSql .= "ta.descripcion AS tipo_agendamiento, ";
				$cadenaSql .= "bp.identificacion ||' - '|| bp.nombre ||' '||bp.primer_apellido||' '||bp.segundo_apellido AS beneficiario, ";
				$cadenaSql .= "CASE estado_servicio ";
				$cadenaSql .= "WHEN  85 THEN 0 ";
				$cadenaSql .= "WHEN  84 THEN 0 ";
				$cadenaSql .= "WHEN 129 THEN 1  ";
				$cadenaSql .= "ELSE 2  ";
				$cadenaSql .= "END as estado_agenda, ";
				$cadenaSql .= "CASE estado_servicio ";
				$cadenaSql .= "WHEN  85 THEN 'En Verificación' ";
				$cadenaSql .= "WHEN  84 THEN 'En Verificación' ";
				$cadenaSql .= "WHEN 129 THEN 'Completado' ";
				$cadenaSql .= "ELSE 'No iniciado'  ";
				$cadenaSql .= "END as etiqueta_agenda ";
				$cadenaSql .= "FROM interoperacion.agendamiento_comisionamiento as ac ";
				$cadenaSql .= "LEFT JOIN interoperacion.contrato cn ON cn.id_beneficiario=ac.id_beneficiario ";
				$cadenaSql .= "LEFT JOIN interoperacion.servicio sv on sv.id_contrato=cn.id ";
				$cadenaSql .= "LEFT JOIN parametros.parametros pr ON pr.id_parametro=estado_contrato ";
				$cadenaSql .= "JOIN interoperacion.beneficiario_potencial as bp ON ac.id_beneficiario=bp.id_beneficiario, ";
				$cadenaSql .= "(SELECT codigo, param.descripcion ";
				$cadenaSql .= "FROM parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN parametros.relacion_parametro as rparam ON (param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE rparam.descripcion = 'Tipo de Agendamiento' ";
				$cadenaSql .= "AND param.codigo=cast(1 as char) ) AS ta ";
				$cadenaSql .= "WHERE ac.estado_registro=true ";
				$cadenaSql .= "AND bp.estado_registro=true ";
				$cadenaSql .= "AND ta.codigo= cast(ac.tipo_agendamiento as char) ";
					$cadenaSql .= str_replace("\\","",$variable);
				break;
			
			case "agendamientosReporteViabilidad" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "ac.id_agendamiento AS id_agendamiento,  ";
				$cadenaSql .= "bp.proyecto AS urbanizacion,";
				$cadenaSql .= "ta.descripcion AS tipo_agendamiento,";
				$cadenaSql .= "ac.nombre_comisionador AS comisionador,";
				$cadenaSql .= "bp.identificacion AS identificacion_beneficiario,";
				$cadenaSql .= "bp.nombre ||' '||bp.primer_apellido||' '||segundo_apellido AS nombre_beneficiario,";
				$cadenaSql .= "bp.manzana AS manzana,";
				$cadenaSql .= "bp.torre AS torre,";
				$cadenaSql .= "bp.bloque AS bloque,";
				$cadenaSql .= "bp.apartamento AS apartamento,";
				$cadenaSql .= "ac.fecha_agendamiento::timestamp::date AS fecha ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "interoperacion.agendamiento_comisionamiento as ac join interoperacion.beneficiario_potencial as bp ON ";
				$cadenaSql .= "ac.id_beneficiario=bp.id_beneficiario, ";
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
				$cadenaSql .= "rparam.descripcion = 'Tipo de Agendamiento') AS ta ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "ac.estado_registro=true AND bp.estado_registro=true AND ta.codigo= cast(ac.tipo_agendamiento as char) ";
				$cadenaSql .= "AND ac.consecutivo IN " . "" . $variable . "";
				break;
			
			case "comisionador" :
				$cadenaSql = " SELECT usr.mail as identificador, usr.firstname||' '||lastname as nombre_usuario";
				$cadenaSql .= " FROM public.group_users as gu";
				$cadenaSql .= " JOIN public.users as usr ON usr.id=gu.user_id AND usr.status=1";
				$cadenaSql .= " WHERE group_id=(SELECT DISTINCT id";
				$cadenaSql .= " FROM public.users";
				$cadenaSql .= " WHERE lastname= 'Comisionadores'";
				$cadenaSql .= " AND TYPE= 'Group'";
				$cadenaSql .= " LIMIT 1);";
				break;
			
			case "parametroDepartamento" :
				$cadenaSql = "SELECT ";
				$cadenaSql .= "codigo_dep, ";
				$cadenaSql .= "departamento ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.departamento ";
				break;
			
			case "parametroMunicipio" :
				$cadenaSql = "SELECT ";
				$cadenaSql .= "codigo_mun, ";
				$cadenaSql .= "municipio ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.municipio ";
				break;
			
			case "parametroTipoAgendamiento" :
				$cadenaSql = "SELECT        ";
				$cadenaSql .= "codigo, ";
				$cadenaSql .= "param.descripcion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN ";
				$cadenaSql .= "parametros.relacion_parametro as rparam ";
				$cadenaSql .= "ON ";
				$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "rparam.descripcion = 'Tipo de Agendamiento' AND param.estado_registro=TRUE";
				break;
			
			case "parametroTipoTecnologia" :
				$cadenaSql = "SELECT        ";
				$cadenaSql .= "codigo, ";
				$cadenaSql .= "param.descripcion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN ";
				$cadenaSql .= "parametros.relacion_parametro as rparam ";
				$cadenaSql .= "ON ";
				$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "rparam.descripcion = 'Tipo de Tecnología' ";
				break;
			
			case "registrarAgendamiento" :
				
				$cadenaSql = "INSERT INTO interoperacion.agendamiento_comisionamiento(";
				$cadenaSql .= "id_agendamiento,";
				$cadenaSql .= "id_orden_trabajo,";
				$cadenaSql .= "descripcion_orden_trabajo,";
				$cadenaSql .= "id_urbanizacion,";
				$cadenaSql .= "descripcion_urbanizacion,";
				$cadenaSql .= "identificacion_beneficiario,";
				$cadenaSql .= "nombre_beneficiario,";
				$cadenaSql .= "tipo_agendamiento,";
				$cadenaSql .= "tipo_tecnologia,";
				$cadenaSql .= "id_comisionador,";
				$cadenaSql .= "fecha_agendamiento,";
				$cadenaSql .= "codigo_nodo,";
				$cadenaSql .= "manzana,";
				$cadenaSql .= "torre,";
				$cadenaSql .= "bloque,";
				$cadenaSql .= "apartamento";
				$cadenaSql .= ") VALUES ";
				
				foreach ( $variable as $clave => $valor ) {
					
					$cadenaSql .= "(";
					$cadenaSql .= "" . "(SELECT 'AG-' || MAX(consecutivo) + 1 from  interoperacion.consecutivo_agendamiento)" . ",";
					$cadenaSql .= "'" . $valor ['id_orden_trabajo'] . "',";
					$cadenaSql .= "'" . $valor ['descripcion_orden_trabajo'] . "',";
					$cadenaSql .= "'" . $valor ['id_urbanizacion'] . "',";
					$cadenaSql .= "'" . $valor ['descripcion_urbanizacion'] . "',";
					$cadenaSql .= "'" . $valor ['identificacion_beneficiario'] . "',";
					$cadenaSql .= "'" . $valor ['nombre_beneficiario'] . "',";
					$cadenaSql .= "'" . $valor ['tipo_agendamiento'] . "',";
					$cadenaSql .= "'" . $valor ['tipo_tecnologia'] . "',";
					$cadenaSql .= "'" . $valor ['id_comisionador'] . "',";
					$cadenaSql .= "'" . $valor ['fecha_agendamiento'] . "',";
					$cadenaSql .= "'" . $valor ['codigo_nodo'] . "',";
					$cadenaSql .= "'" . $valor ['manzana'] . "',";
					$cadenaSql .= "'" . $valor ['torre'] . "',";
					$cadenaSql .= "'" . $valor ['bloque'] . "',";
					$cadenaSql .= "'" . $valor ['apartamento'] . "'";
					$cadenaSql .= "),";
				}
				
				$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
				
				break;
			
			case "registrarConsecutivoAgendamiento" :
				
				$cadenaSql = "INSERT INTO interoperacion.consecutivo_agendamiento(";
				$cadenaSql .= "nombre_consecutivo";
				$cadenaSql .= ") VALUES ";
				$cadenaSql .= "(";
				$cadenaSql .= "(SELECT 'AG-' || MAX(consecutivo) + 1 from  interoperacion.consecutivo_agendamiento)";
				$cadenaSql .= ")";
				break;
			
			case "parametroTipoAgendamiento" :
				
				$cadenaSql = "SELECT        ";
				$cadenaSql .= "codigo, ";
				$cadenaSql .= "param.descripcion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN ";
				$cadenaSql .= "parametros.relacion_parametro as rparam ";
				$cadenaSql .= "ON ";
				$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "rparam.descripcion = 'Tipo de Agendamiento' ";
				$cadenaSql .= "AND param.estado_registro=TRUE ";
				break;
			
			case 'consultarComisionador' :
				$cadenaSql = " SELECT DISTINCT id_comisionador ||' - '||nombre_comisionador AS  value, id_comisionador  AS data  ";
				$cadenaSql .= " FROM  interoperacion.agendamiento_comisionamiento AS ac ";
				$cadenaSql .= "WHERE estado_registro=TRUE ";
				$cadenaSql .= "AND  ( cast(id_comisionador  as text) ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "OR nombre_comisionador ILIKE '%" . $_GET ['query'] . "%' ) ";
				$cadenaSql .= " order by id_comisionador asc ";
				$cadenaSql .= "LIMIT 10; ";
				break;
			
			case 'consultarUrbanizacion' :
				$cadenaSql = " SELECT DISTINCT id_proyecto ||' - '|| proyecto AS  value, id_proyecto  AS data  ";
				$cadenaSql .= " FROM  interoperacion.beneficiario_potencial ";
				$cadenaSql .= "WHERE estado_registro=TRUE ";
				$cadenaSql .= "AND  cast(id_proyecto  as text) ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "OR proyecto ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "order by id_proyecto asc ";
				$cadenaSql .= "LIMIT 10; ";
				break;
			
			case 'consultarManzana' :
				$cadenaSql = " SELECT DISTINCT manzana AS  value, manzana  AS data  ";
				$cadenaSql .= " FROM  interoperacion.beneficiario_potencial ";
				$cadenaSql .= "WHERE estado_registro=TRUE ";
				$cadenaSql .= "AND  cast(manzana  as text) ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "order by manzana asc ";
				$cadenaSql .= "LIMIT 10; ";
				break;
		}
		
		return $cadenaSql;
	}
}

?>
