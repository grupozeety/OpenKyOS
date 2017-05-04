<?php

namespace facturacion\masivoCalFactura;

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
			
			case 'consultarBeneficiario' :
				$cadenaSql = " SELECT value , data , urbanizacion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "(SELECT DISTINCT identificacion ||' - ('||nombre||' '||primer_apellido||' '||segundo_apellido||')' AS  value, bp.id_beneficiario  AS data, proyecto as urbanizacion ";
				$cadenaSql .= " FROM  interoperacion.beneficiario_potencial bp ";
				$cadenaSql .= " JOIN interoperacion.documentos_contrato ac on ac.id_beneficiario=bp.id_beneficiario ";
				$cadenaSql .= " JOIN facturacion.usuario_rol ur on ur.id_beneficiario=bp.id_beneficiario ";
				$cadenaSql .= " WHERE bp.estado_registro=TRUE ";
				$cadenaSql .= " AND ac.estado_registro=TRUE ";
				$cadenaSql .= " AND ur.estado_registro=TRUE ";
				$cadenaSql .= " AND ac.tipologia_documento=132 ";
				$cadenaSql .= "     ) datos ";
				$cadenaSql .= "WHERE data='" . $variable . "' ";
				$cadenaSql .= "LIMIT 10; ";
				break;
			
			case 'consultarRolUsuario' :
				$cadenaSql = " SELECT ur.id_rol, rol.descripcion ";
				$cadenaSql .= " FROM facturacion.usuario_rol ur ";
				$cadenaSql .= " JOIN facturacion.rol rol on rol.id_rol=ur.id_rol ";
				$cadenaSql .= " WHERE id_beneficiario ='" . $variable . "'  ";
				$cadenaSql .= " AND ur.estado_registro=TRUE;";
				break;
			
			case 'parametroPeriodos' :
				$cadenaSql = " SELECT codigo, descripcion ";
				$cadenaSql .= " FROM parametros.parametros ";
				$cadenaSql .= " WHERE rel_parametro =27  ";
				$cadenaSql .= " AND estado_registro=TRUE;";
				break;
			
			case 'consultarReglas' :
				$cadenaSql = " SELECT identificador, formula,regla.id_regla ";
				$cadenaSql .= " FROM facturacion.metodos ";
				$cadenaSql .= " JOIN facturacion.regla ON regla.id_regla=metodos.id_regla ";
				$cadenaSql .= " WHERE id_rol=" . $variable . "";
				$cadenaSql .= " AND regla.estado_registro=TRUE ";
				$cadenaSql .= " AND metodos.estado_registro=TRUE ";
				break;
			
			case 'consultarContrato' :
				$cadenaSql = " SELECT valor_tarificacion as VM, fecha_contrato ";
				$cadenaSql .= " FROM interoperacion.contrato ";
				$cadenaSql .= " WHERE id_beneficiario='" . $variable . "'";
				$cadenaSql .= " AND estado_registro=TRUE ";
				break;
			
			case 'consultarContrato' :
				$cadenaSql = " SELECT valor_tarificacion as VM, fecha_contrato ";
				$cadenaSql .= " FROM interoperacion.contrato ";
				$cadenaSql .= " WHERE id_beneficiario='" . $variable . "'";
				$cadenaSql .= " AND estado_registro=TRUE ";
				break;
			
			case 'consultarUsuarioRol' :
				$cadenaSql = " SELECT  usuario_rol.id_rol, rol.descripcion, id_usuario_rol ";
				$cadenaSql .= " FROM facturacion.usuario_rol ";
				$cadenaSql .= " JOIN facturacion.rol ON usuario_rol.id_rol=rol.id_rol ";
				$cadenaSql .= " WHERE id_beneficiario='" . $variable . "'";
				$cadenaSql .= " AND usuario_rol.estado_registro=TRUE ";
				break;
			
			case 'consultarUsuarioRol_predeterminado' :
				$cadenaSql = " SELECT  id_valor as id_rol, rol.descripcion ";
				$cadenaSql .= " FROM facturacion.parametros_generales ";
				$cadenaSql .= " JOIN facturacion.rol ON id_rol=parametros_generales.id_valor ";
				$cadenaSql .= " WHERE parametros_generales.descripcion='rol'";
				$cadenaSql .= " AND parametros_generales.estado_registro=TRUE ";
				break;
			
			case 'registrarFactura' :
				$cadenaSql = " INSERT INTO facturacion.factura (id_beneficiario, total_factura, id_ciclo) ";
				$cadenaSql .= " VALUES( ";
				$cadenaSql .= " '" . $variable ['id_beneficiario'] . "',";
				$cadenaSql .= " '" . $variable ['total_factura'] . "',";
				$cadenaSql .= " '" . $variable ['id_ciclo'] . "' ) RETURNING id_factura;";
				break;
			
			case 'consultarReglaID' :
				$cadenaSql = " SELECT id_regla ";
				$cadenaSql .= " FROM facturacion.regla ";
				$cadenaSql .= " WHERE identificador='" . $variable . "'";
				$cadenaSql .= " AND estado_registro=TRUE ";
				break;
			
			case 'registrarConceptos' :
				$cadenaSql = " INSERT INTO facturacion.conceptos (id_factura, id_regla,valor_calculado,observacion, id_usuario_rol_periodo) ";
				$cadenaSql .= " VALUES( ";
				$cadenaSql .= " '" . $variable ['id_factura'] . "',";
				$cadenaSql .= " '" . $variable ['id_regla'] . "',";
				$cadenaSql .= " '" . $variable ['valor_calculado'] . "',";
				$cadenaSql .= " '" . $variable ['observacion'] . "',";
				$cadenaSql .= " '" . $variable ['id_usuario_rol_periodo'] . "' ) ;";
				break;
			
			case 'consultarPeriodo' :
				$cadenaSql = " SELECT valor ";
				$cadenaSql .= " FROM facturacion.periodo ";
				$cadenaSql .= " WHERE id_periodo='" . $variable . "'";
				$cadenaSql .= " AND estado_registro=TRUE ";
				break;
			
			case 'registrarPeriodoRolUsuario' :
				$cadenaSql = " INSERT INTO facturacion.usuario_rol_periodo (id_usuario_rol, id_periodo,inicio_periodo, fin_periodo, id_ciclo) ";
				$cadenaSql .= " VALUES( ";
				$cadenaSql .= " '" . $variable ['id_usuario_rol'] . "',";
				$cadenaSql .= " '" . $variable ['id_periodo'] . "',";
				$cadenaSql .= " '" . $variable ['inicio_periodo'] . "',";
				$cadenaSql .= " '" . $variable ['fin_periodo'] . "',";
				$cadenaSql .= " '" . $variable ['id_ciclo'] . "' )  RETURNING id_usuario_rol_periodo ;";
				break;
			
			/* Consultas Informativas */
			
			case 'consultarDepartamento' :
				$cadenaSql = " SELECT DISTINCT codigo_dep as valor, contrato.departamento";
				$cadenaSql .= " FROM interoperacion.contrato";
				$cadenaSql .= " JOIN parametros.departamento on departamento.departamento=contrato.departamento";
				$cadenaSql .= " WHERE estado_registro=TRUE";
				$cadenaSql .= " ORDER BY contrato.departamento ASC; ";
				break;
			
			case 'consultarMunicipio' :
				$cadenaSql = " SELECT DISTINCT codigo_mun as valor, contrato.municipio ";
				$cadenaSql .= " FROM interoperacion.contrato ";
				$cadenaSql .= " JOIN parametros.municipio ON municipio.municipio=contrato.municipio ";
				$cadenaSql .= " WHERE estado_registro=TRUE ";
				$cadenaSql .= " ORDER BY contrato.municipio ASC";
				break;
			
			case 'consultarUrbanizacion' :
				$cadenaSql = " SELECT DISTINCT id_urbanizacion as valor, contrato.urbanizacion ";
				$cadenaSql .= " FROM interoperacion.contrato  ";
				$cadenaSql .= " JOIN parametros.urbanizacion on urbanizacion.urbanizacion=contrato.urbanizacion  ";
				$cadenaSql .= " WHERE estado_registro=TRUE";
				$cadenaSql .= " ORDER BY contrato.urbanizacion ASC";
				break;
			
			case 'consultarBeneficiarios' :
				$cadenaSql = " SELECT bp.id_beneficiario, bp.identificacion ";
				$cadenaSql .= " FROM interoperacion.beneficiario_potencial bp ";
				$cadenaSql .= " JOIN interoperacion.contrato con ON con.id_beneficiario=bp.id_beneficiario ";
				$cadenaSql .= " WHERE con.estado_registro=TRUE ";
				$cadenaSql .= " AND bp.estado_registro=TRUE ";
				if (isset ( $variable ['urbanizacion'] )) {
					$cadenaSql .= " AND id_proyecto='" . $variable ['urbanizacion'] . "'";
				}
				if (isset ( $variable ['municipio'] )) {
					$cadenaSql .= " AND bp.municipio='" . $variable ['municipio'] . "' ";
				}
				if (isset ( $variable ['departamento'] )) {
					$cadenaSql .= " AND bp.deparamento='" . $variable ['urbanizacion'] . "' ";
				}
				
				$cadenaSql .= " LIMIT 25";
				break;
			
			case 'registrarAsociacion' :
				$cadenaSql = " INSERT INTO facturacion.usuario_rol (id_rol, id_beneficiario) ";
				$cadenaSql .= " VALUES( ";
				$cadenaSql .= " " . $variable ['id_rol'] . ",";
				$cadenaSql .= " '" . $variable ['id_beneficiario'] . "' );";
				break;
			
			case 'consultarUsuarioRolPeriodo' :
				$cadenaSql = " SELECT DISTINCT  fin_periodo,urp.id_usuario_rol_periodo , urp.id_usuario_rol, urp.id_ciclo , id_beneficiario ";
				$cadenaSql .= " FROM facturacion.usuario_rol_periodo urp ";
				$cadenaSql .= " JOIN facturacion.conceptos on urp.id_usuario_rol_periodo=conceptos.id_usuario_rol_periodo and conceptos.estado_registro=TRUE ";
				$cadenaSql .= " JOIN facturacion.factura ON factura.id_factura=conceptos.id_factura and factura.estado_registro=TRUE ";
				$cadenaSql .= " WHERE id_beneficiario='" . $variable . "' ";
				$cadenaSql .= " AND urp.estado_registro=TRUE ";
				$cadenaSql .= " ORDER BY urp.id_usuario_rol_periodo DESC ";
				break;
			
			case 'consultarFechaInicio' :
				$cadenaSql = " SELECT fecha_instalacion ";
				$cadenaSql .= " FROM interoperacion.acta_entrega_servicios ";
				$cadenaSql .= " WHERE id_beneficiario='" . $variable . "'";
				$cadenaSql .= " AND estado_registro=TRUE AND fecha_instalacion IS NOT NULL AND fecha_instalacion!='' ";
				break;
			
			case 'consultarFactura' :
				$cadenaSql = " SELECT DISTINCT urp.id_usuario_rol, urp.id_ciclo , id_beneficiario ";
				$cadenaSql .= " FROM facturacion.usuario_rol_periodo urp ";
				$cadenaSql .= " JOIN facturacion.conceptos on urp.id_usuario_rol_periodo=conceptos.id_usuario_rol_periodo and conceptos.estado_registro=TRUE ";
				$cadenaSql .= " JOIN facturacion.factura ON factura.id_factura=conceptos.id_factura and factura.estado_registro=TRUE ";
				$cadenaSql .= " WHERE id_beneficiario='" . $variable ['id_beneficiario'] . "' ";
				$cadenaSql .= " AND urp.estado_registro=TRUE ";
				$cadenaSql .= " AND urp.id_ciclo='" . $variable ['id_ciclo'] . "' ";
				break;
			
			case 'consultarMoras' :
				$cadenaSql = " SELECT DISTINCT urp.id_usuario_rol, inicio_periodo, fin_periodo, conceptos.id_factura, estado_factura , factura.id_ciclo, factura.total_factura ";
				$cadenaSql .= " FROM facturacion.usuario_rol_periodo urp ";
				$cadenaSql .= " JOIN facturacion.conceptos on urp.id_usuario_rol_periodo=conceptos.id_usuario_rol_periodo and conceptos.estado_registro=TRUE ";
				$cadenaSql .= " JOIN facturacion.factura on factura.id_factura=conceptos.id_factura AND factura.estado_registro=TRUE AND estado_factura='Mora' ";
				$cadenaSql .= " WHERE factura.id_beneficiario='" . $variable . "' ";
				$cadenaSql .= " AND urp.estado_registro=TRUE ORDER BY urp.id_usuario_rol ASC ";
				
				break;
			
			case 'revisarMora' :
				$cadenaSql = " UPDATE facturacion.factura SET estado_factura='Mora' WHERE id_factura IN (SELECT id_factura ";
				$cadenaSql .= " FROM facturacion.factura WHERE 1=1  ";
				$cadenaSql .= " AND estado_factura='Aprobado'  ";
				$cadenaSql .= " AND estado_registro=TRUE  ";
				$cadenaSql .= " AND factura.id_beneficiario='" . $variable . "' ";
				$cadenaSql .= " AND fecha_pago_oportuno::date < CURRENT_DATE) ";
				
				break;
			
			case 'updateestadoCliente' :
				$cadenaSql = " UPDATE interoperacion.beneficiario_alfresco SET cliente_creado=TRUE ";
				$cadenaSql .= " WHERE id_beneficiario='" . $variable . "'";
				break;
			
			case 'actualizarFactura' :
				$cadenaSql = " UPDATE facturacion.factura SET factura_erpnext='" . $variable ['invoice'] . "' ";
				$cadenaSql .= " WHERE id_factura='" . $variable ['id_factura'] . "'";
				break;
			
			case 'estadoCliente' :
				$cadenaSql = " SELECT cliente_creado FROM interoperacion.beneficiario_alfresco ";
				$cadenaSql .= " WHERE id_beneficiario='" . $variable . "'";
				break;
		}
		
		return $cadenaSql;
	}
}
?>

