<?php

namespace reportes\plantillaResultadoCom;

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
			
			// Validaciones
			
			case 'consultarExitenciaActa' :
				$cadenaSql = " SELECT ep.id as identificador_acta, cn.numero_identificacion ";
				$cadenaSql .= " FROM interoperacion.acta_entrega_portatil ep";
				$cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=ep.id_beneficiario AND cn.estado_registro='TRUE'";
				$cadenaSql .= " WHERE ep.estado_registro='TRUE'";
				$cadenaSql .= " AND ep.serial='" . $variable ['serial_portatil'] . "'";
				// $cadenaSql .= " AND cn.numero_identificacion='" . $variable['identificacion_beneficiario'] . "'";
				break;
			
			case 'consultarExitenciaSerialPortatil' :
				$cadenaSql = " SELECT ep.id as identificador_acta, cn.numero_identificacion ";
				$cadenaSql .= " FROM interoperacion.acta_entrega_portatil ep";
				$cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=ep.id_beneficiario AND cn.estado_registro='TRUE'";
				$cadenaSql .= " WHERE ep.estado_registro='TRUE'";
				$cadenaSql .= " AND ep.serial='" . $variable ['serial_portatil'] . "'";
				// $cadenaSql .= " AND cn.numero_identificacion='" . $variable['identificacion_beneficiario'] . "'";
				break;
			
			case 'consultarExitenciaContrato' :
				$cadenaSql = " SELECT id_beneficiario, numero_contrato,numero_identificacion";
				$cadenaSql .= " FROM interoperacion.contrato";
				$cadenaSql .= " WHERE estado_registro='TRUE'";
				$cadenaSql .= " AND numero_identificacion='" . $variable . "';";
				break;
			
			case 'consultarExitenciaBeneficiario' :
				$cadenaSql = " SELECT id_beneficiario";
				$cadenaSql .= " FROM interoperacion.beneficiario_potencial";
				$cadenaSql .= " WHERE estado_registro='TRUE'";
				$cadenaSql .= " AND identificacion='" . $variable . "';";
				break;
			
			case 'consultarExistenciaServicio' :
				$cadenaSql = " SELECT bp.id_beneficiario ";
				$cadenaSql .= " FROM interoperacion.beneficiario_potencial bp ";
				$cadenaSql .= " JOIN interoperacion.acta_entrega_servicios aes ON aes.id_beneficiario=bp.id_beneficiario";
				$cadenaSql .= " WHERE bp.estado_registro='TRUE'";
				$cadenaSql .= " AND bp.identificacion='" . $variable . "';";
				break;
			
			case 'consultarInformacionBeneficiario' :
				$cadenaSql = " SELECT bp.* ,mn.municipio as nombre_municipio,dp.departamento as nombre_departamento";
				$cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
				$cadenaSql .= " JOIN parametros.municipio mn ON mn.codigo_mun=bp.municipio";
				$cadenaSql .= " JOIN parametros.departamento dp ON dp.codigo_dep=bp.departamento";
				$cadenaSql .= " WHERE bp.estado_registro='TRUE' ";
				$cadenaSql .= " AND bp.identificacion='" . $variable . "';";
				
				break;
			
			// Registro Procesos
			case 'registrarProceso' :
				$cadenaSql = " INSERT INTO parametros.procesos_masivos(";
				$cadenaSql .= " descripcion,";
				$cadenaSql .= " estado,";
				$cadenaSql .= " nombre_archivo, ruta_archivo,";
				$cadenaSql .= " parametro_inicio,";
				$cadenaSql .= " parametro_fin)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= " 'Actualizar Servicio - Pruebas Comision',";
				$cadenaSql .= " 'Actualizar',";
				$cadenaSql .= " '" . $variable ['nombre_archivo'] . "',";
				$cadenaSql .= " '" . $variable ['nombre_archivo'] . "',";
				$cadenaSql .= " 0,";
				$cadenaSql .= " 0";
				$cadenaSql .= " )RETURNING id_proceso;";
				break;
			
			case 'consultarProceso' :
				$cadenaSql = " SELECT * ";
				$cadenaSql .= " FROM parametros.procesos_masivos";
				$cadenaSql .= " WHERE descripcion='Contratos'";
				$cadenaSql .= " ORDER BY id_proceso DESC;";
				break;
			
			case 'consultarProcesoParticular' :
				$cadenaSql = " SELECT *";
				$cadenaSql .= " FROM parametros.procesos_masivos";
				$cadenaSql .= " WHERE id_proceso=(";
				$cadenaSql .= " SELECT MIN(id_proceso) ";
				$cadenaSql .= " FROM parametros.procesos_masivos";
				$cadenaSql .= " WHERE estado_registro='TRUE' ";
				$cadenaSql .= " AND estado='No Iniciado'";
				$cadenaSql .= " );";
				break;
			
			case 'actualizarProceso' :
				$cadenaSql = " UPDATE parametros.procesos_masivos";
				$cadenaSql .= " SET estado='En Proceso'";
				$cadenaSql .= " WHERE id_proceso='" . $variable . "';";
				break;
			
			case 'finalizarProceso' :
				$cadenaSql = " UPDATE parametros.procesos_masivos";
				$cadenaSql .= " SET estado='Finalizado',";
				$cadenaSql .= " ruta_archivo='" . $variable ['ruta_archivo'] . "',";
				$cadenaSql .= " nombre_ruta_archivo='" . $variable ['nombre_archivo'] . "'";
				$cadenaSql .= " WHERE id_proceso='" . $variable ['id_proceso'] . "';";
				break;
			// Crear Documenntos Contrato
			case 'ConsultaBeneficiarios' :
				$cadenaSql = " SELECT *";
				$cadenaSql .= " FROM interoperacion.contrato";
				$cadenaSql .= " WHERE numero_contrato >=" . $variable ['Inicio'] . " ";
				$cadenaSql .= " AND numero_contrato<=" . $variable ['Fin'] . " ";
				$cadenaSql .= " ORDER BY numero_contrato ;";
				break;
			
			case 'consultarTipoDocumento' :
				$cadenaSql = " SELECT pr.id_parametro,pr.codigo, pr.descripcion ";
				$cadenaSql .= " FROM parametros.parametros pr";
				$cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " pr.estado_registro=TRUE ";
				$cadenaSql .= " AND rl.descripcion='Tipo de Documento'";
				$cadenaSql .= " AND pr.descripcion='" . $variable . "' ";
				$cadenaSql .= " AND rl.estado_registro=TRUE ";
				break;
			
			case 'consultarParametroParticular' :
				$cadenaSql = " SELECT descripcion ";
				$cadenaSql .= " FROM parametros.parametros";
				$cadenaSql .= " WHERE estado_registro='TRUE'";
				$cadenaSql .= " AND id_parametro='" . $variable . "';";
				break;
			
			case 'actualizarServicio' :
				$cadenaSql = " UPDATE interoperacion.acta_entrega_servicios ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " resultado_vs='" . $variable ['resultado_vs'] . "', ";
				$cadenaSql .= " resultado_vb='" . $variable ['resultado_vb'] . "', ";
				$cadenaSql .= " resultado_p1='" . $variable ['resultado_p1'] . "', ";
				$cadenaSql .= " observaciones_p1='" . $variable ['observaciones_p1'] . "', ";
				$cadenaSql .= " resultado_tr2='" . $variable ['resultado_tr2'] . "', ";
				$cadenaSql .= " resultado_tr1='" . $variable ['resultado_tr1'] . "', ";
				$cadenaSql .= " reporte_fallos='Fecha Comisionamiento " . $variable ['fecha'] . ". " . $variable ['reporte_fallos'] . "', ";
				$cadenaSql .= " acceso_reportando='" . $variable ['acceso_reportando'] . "', ";
				$cadenaSql .= " hora_prueba_vs='" . $variable ['fecha'] . "', ";
				$cadenaSql .= " hora_prueba_vb='" . $variable ['fecha'] . "', ";
				$cadenaSql .= " hora_prueba_p1='" . $variable ['fecha'] . "', ";
				$cadenaSql .= " hora_prueba_tr2='" . $variable ['fecha'] . "', ";
				$cadenaSql .= " hora_prueba_tr1='" . $variable ['fecha'] . "', ";
				$cadenaSql .= " paginas_visitadas='" . $variable ['paginas_visitadas'] . "' ";
				$cadenaSql .= " WHERE id_beneficiario='" . $variable ['id_beneficiario'] . "' ;";
				break;
		}
		
		return $cadenaSql;
	}
}
?>

