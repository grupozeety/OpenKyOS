<?php

namespace reportes\plantillaInfoTecnica;

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
			
			case 'consultarExistenciaInfoHFC' :
				$cadenaSql = " SELECT id ";
				$cadenaSql .= " FROM interoperacion.nodo ";
				$cadenaSql .= " WHERE estado_registro='TRUE'";
				$cadenaSql .= " AND codigo_nodo='" . $variable ['codigo_nodo'] . "' ";
				$cadenaSql .= " AND codigo_cabecera='" . $variable ['codigo_cabecera'] . "' ";
				$cadenaSql .= " AND macesclavo1='" . $variable ['macesclavo1'] . "' ";
				break;
			
			case 'consultarExistenciaInfoWMAN' :
				$cadenaSql = " SELECT id ";
				$cadenaSql .= " FROM interoperacion.nodo ";
				$cadenaSql .= " WHERE estado_registro='TRUE'";
				$cadenaSql .= " AND codigo_nodo='" . $variable ['codigo_nodo'] . "' ";
				$cadenaSql .= " AND codigo_cabecera='" . $variable ['codigo_cabecera'] . "' ";
				$cadenaSql .= " AND macesclavo1='" . $variable ['macesclavo1'] . "' ";
				break;
			
			case 'consultarInformacionBeneficiario' :
				$cadenaSql = " SELECT bp.* ,mn.municipio as nombre_municipio,dp.departamento as nombre_departamento";
				$cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
				$cadenaSql .= " JOIN parametros.municipio mn ON mn.codigo_mun=bp.municipio";
				$cadenaSql .= " JOIN parametros.departamento dp ON dp.codigo_dep=bp.departamento";
				$cadenaSql .= " WHERE bp.estado_registro='TRUE' ";
				$cadenaSql .= " AND bp.identificacion='" . $variable . "';";
				
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

