<?php

namespace facturacion\estadoCuenta;

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
			
			case 'consultarBeneficiariosPotenciales' :
				$cadenaSql = " SELECT value , data ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "(SELECT DISTINCT identificacion ||' - ('||nombre||' '||primer_apellido||' '||segundo_apellido||')' AS  value, bp.id_beneficiario  AS data ";
				$cadenaSql .= " FROM  interoperacion.beneficiario_potencial bp ";
				$cadenaSql .= " JOIN interoperacion.documentos_contrato ac on ac.id_beneficiario=bp.id_beneficiario ";
				$cadenaSql .= " JOIN facturacion.usuario_rol ur on ur.id_beneficiario=bp.id_beneficiario ";
				$cadenaSql .= " WHERE bp.estado_registro=TRUE ";
				$cadenaSql .= " AND ac.estado_registro=TRUE ";
				$cadenaSql .= " AND ur.estado_registro=TRUE ";
				$cadenaSql .= " AND ac.tipologia_documento=132 ";
				$cadenaSql .= "     ) datos ";
				$cadenaSql .= "WHERE value ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "LIMIT 10; ";
				break;
			
			case 'consultarFactura' :
				$cadenaSql = " SELECT id_factura, total_factura, estado_factura, id_ciclo, fac.id_beneficiario, identificacion||' - '|| nombre ||' '|| primer_apellido ||' '||segundo_apellido as nombres ";
				$cadenaSql .= " FROM facturacion.factura fac";
				$cadenaSql .= " JOIN interoperacion.beneficiario_potencial bp on bp.id_beneficiario=fac.id_beneficiario";
				$cadenaSql .= " WHERE 1=1";
				$cadenaSql .= " AND fac.estado_registro=TRUE";
				$cadenaSql .= " AND fac.id_beneficiario='" . $variable . "' ";
				// $cadenaSql .= " AND fac.estado_factura='Aprobado' ";
				$cadenaSql .= " AND bp.estado_registro=TRUE ORDER BY id_factura ASC ";
				break;
			
			case 'consultarFactura_especifico' :
				$cadenaSql = " SELECT fac.id_factura, total_factura, estado_factura, fac.id_ciclo, fac.id_beneficiario, identificacion||' - '|| nombre ||' '|| primer_apellido ||' '||segundo_apellido as nombres ,  ";
				$cadenaSql .= " regla.descripcion, conceptos.valor_calculado ";
				$cadenaSql .= " FROM facturacion.factura fac ";
				$cadenaSql .= " JOIN interoperacion.beneficiario_potencial bp on bp.id_beneficiario=fac.id_beneficiario ";
				$cadenaSql .= " JOIN facturacion.conceptos on conceptos.id_factura=fac.id_factura AND conceptos.estado_registro=TRUE ";
				$cadenaSql .= " JOIN facturacion.regla on regla.id_regla=conceptos.id_regla AND regla.estado_registro=TRUE ";
				$cadenaSql .= " WHERE 1=1 ";
				$cadenaSql .= " AND fac.estado_registro=TRUE ";
				$cadenaSql .= " AND bp.estado_registro=TRUE ";
				$cadenaSql .= " AND fac.id_factura='" . $variable . "' ";
				break;
			
			case 'consultarConceptos_especifico' :
				$cadenaSql = " SELECT fac.id_factura,fac.id_beneficiario,regla.descripcion regla, conceptos.valor_calculado, rol.descripcion, conceptos.id_usuario_rol_periodo, inicio_periodo, fin_periodo ";
				$cadenaSql .= " FROM facturacion.factura fac ";
				$cadenaSql .= " JOIN interoperacion.beneficiario_potencial bp on bp.id_beneficiario=fac.id_beneficiario ";
				$cadenaSql .= " JOIN facturacion.conceptos on conceptos.id_factura=fac.id_factura AND conceptos.estado_registro=TRUE ";
				$cadenaSql .= " JOIN facturacion.usuario_rol_periodo urp on conceptos.id_usuario_rol_periodo=urp.id_usuario_rol_periodo ";
				$cadenaSql .= " JOIN facturacion.regla on regla.id_regla=conceptos.id_regla AND regla.estado_registro=TRUE ";
				$cadenaSql .= " JOIN facturacion.usuario_rol on usuario_rol.id_beneficiario=fac.id_beneficiario AND usuario_rol.estado_registro=TRUE AND usuario_rol.id_usuario_rol=urp.id_usuario_rol ";
				$cadenaSql .= " JOIN facturacion.rol on rol.id_rol=usuario_rol.id_rol and rol.estado_registro=TRUE ";
				$cadenaSql .= " WHERE 1=1 ";
				$cadenaSql .= " AND fac.estado_registro=TRUE ";
				$cadenaSql .= " AND bp.estado_registro=TRUE ";
				$cadenaSql .= " AND fac.id_factura='" . $variable . "' ORDER BY rol.descripcion ASC, regla.descripcion ASC ";
				break;
			
			case 'contratoBeneficiario' :
				$cadenaSql = " SELECT bp.id_beneficiario, nombre ||' ' || bp.primer_apellido ||' '|| bp.segundo_apellido as nombre, numero_contrato, fecha_contrato, estado_contrato, parametros.descripcion as descr_contrato, ";
				$cadenaSql .= " servicio.estado_servicio, parametros2.descripcion as descr_servicio, valor_mensual*15 as valor_total ";
				$cadenaSql .= " FROM interoperacion.beneficiario_potencial bp ";
				$cadenaSql .= " JOIN interoperacion.contrato on bp.id_beneficiario=contrato.id_beneficiario ";
				$cadenaSql .= " LEFT JOIN interoperacion.servicio on contrato.id=servicio.id_contrato ";
				$cadenaSql .= " JOIN parametros.parametros on contrato.estado_contrato=parametros.id_parametro and parametros.estado_registro=TRUE ";
				$cadenaSql .= " LEFT JOIN parametros.parametros as parametros2 on servicio.estado_servicio=parametros2.id_parametro and parametros2.estado_registro=TRUE ";
				$cadenaSql .= " WHERE 1=1 ";
				$cadenaSql .= " AND bp.estado_registro=TRUE ";
				$cadenaSql .= " AND contrato.estado_registro=TRUE ";
				$cadenaSql .= " AND bp.id_beneficiario='" . $variable . "' ";
				break;
			
			case 'consultarPagos' :
				$cadenaSql = " SELECT id_pago, pago_factura.id_factura, pago_factura.fecha_registro, usuario_recibe as cajero, valor_pagado, id_beneficiario, abono_adicional , abono_adicional+valor_pagado as total_pagado ";
				$cadenaSql .= " FROM facturacion.pago_factura ";
				$cadenaSql .= " JOIN facturacion.factura ON factura.id_factura=pago_factura.id_factura ";
				$cadenaSql .= " WHERE 1=1 ";
				$cadenaSql .= " AND pago_factura.estado_registro=TRUE ";
				$cadenaSql .= " AND factura.estado_registro=TRUE ";
				$cadenaSql .= " AND id_beneficiario='" . $variable . "' ";
				break;
			
			case 'totalPagado' :
				$cadenaSql = " SELECT  id_beneficiario, sum(valor_pagado + abono_adicional) pagado ";
				$cadenaSql .= " FROM facturacion.pago_factura ";
				$cadenaSql .= " JOIN facturacion.factura ON factura.id_factura=pago_factura.id_factura ";
				$cadenaSql .= " WHERE 1=1 ";
				$cadenaSql .= " AND pago_factura.estado_registro=TRUE ";
				$cadenaSql .= " AND factura.estado_registro=TRUE ";
				$cadenaSql .= " AND id_beneficiario='" . $variable . "' ";
				$cadenaSql .= " GROUP BY   id_beneficiario ";
				break;
			
			case 'estadoServicio' :
				$cadenaSql = " SELECT estado_servicio , descripcion_parametro ";
				$cadenaSql .= " FROM logica.info_avan_oper iao ";
				$cadenaSql .= " JOIN parametros.parametros par ON estado_servicio=codigo_parametro ";
				$cadenaSql .= " WHERE iao.estado_registro=TRUE ";
				$cadenaSql .= " AND par.estado_registro=TRUE AND id_beneficiario='" . $variable . "' ;";
				break;
			
			case 'imagenServicio' :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " CASE count(estado_factura)";
				$cadenaSql .= " WHEN 0 THEN 'Activo.png'";
				$cadenaSql .= " WHEN 1 THEN 'Mora1.png'";
				$cadenaSql .= " WHEN 2 THEN 'Mora2.png'";
				$cadenaSql .= " WHEN 3 THEN 'Inactivo.png'";
				$cadenaSql .= " END";
				$cadenaSql .= " FROM facturacion.factura";
				$cadenaSql .= " WHERE 1=1";
				$cadenaSql .= " AND estado_registro=TRUE";
				$cadenaSql .= " AND estado_factura='Mora'";
				$cadenaSql .= " AND id_beneficiario='" . $variable . "' ";
				$cadenaSql .= " GROUP BY estado_factura ";
				break;
		}
		
		return $cadenaSql;
	}
}
?>

