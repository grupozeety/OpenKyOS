<?php

namespace reportes\reporteQuincenal;

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
			case 'consultarProyectosParametrizados' :
				$cadenaSql = " SELECT DISTINCT tipo_proyecto, id_proyecto";
				$cadenaSql .= " FROM parametros.parametrizacion_reporte";
				$cadenaSql .= " WHERE estado_registro= TRUE AND tipo_proyecto <>'core' ";
				$cadenaSql .= " ORDER BY id_proyecto;";
				break;
			
			case 'consultarCamposParametrizados' :
				$cadenaSql = " SELECT DISTINCT pr.campo, pr.valor_campo, ";
				$cadenaSql .= " pr.valor_actividad, pr.info_hijos, cr.tipo,cr.sub_tipo,cr.nombre_formulario ";
				$cadenaSql .= " FROM parametros.parametrizacion_reporte AS pr ";
				$cadenaSql .= " JOIN parametros.campos_reporte as cr ON cr.identificador_campo=pr.campo";
				$cadenaSql .= " WHERE pr.estado_registro= TRUE";
				$cadenaSql .= " AND pr.id_proyecto='" . $variable . "'";
				break;
			
			case 'consultarInformacionReporte' :
				$cadenaSql = " SELECT *";
				$cadenaSql .= " FROM public.reporte_semanal";
				$cadenaSql .= " WHERE estado_registro=TRUE";
				$cadenaSql .= " AND ( fecha_registro BETWEEN '" . $_REQUEST ['fecha_inicio'] . " 00:00:00'::timestamp AND '" . $_REQUEST ['fecha_final'] . " 23:59:59'::timestamp);";
				break;
			
			case 'consultarInformacionCore' :
				$cadenaSql = " SELECT DISTINCT tipo_proyecto, id_proyecto";
				$cadenaSql .= " FROM parametros.parametrizacion_reporte";
				$cadenaSql .= " WHERE estado_registro= TRUE";
				$cadenaSql .= " AND tipo_proyecto= 'core' ";
				$cadenaSql .= " ORDER BY id_proyecto;";
				break;
			
			// ----------------------
			
			case 'consultarSeguimiento' :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " `Nombre Elemento` as a, ";
				$cadenaSql .= " `Identificacion  o referencia del elemento` as b, ";
				$cadenaSql .= " `Descripción  detallada del elemento` as c, ";
				$cadenaSql .= " `Destino Instalación Nivel Red` as d, ";
				$cadenaSql .= " `Estado de compra` as e,";
				$cadenaSql .= " `Número OC  o Contrato` as f, ";
				$cadenaSql .= " `Nombre Proveedor` as g, ";
				$cadenaSql .= " `Cantidad o capacidad Comprada o a adquirir` as h, ";
				$cadenaSql .= " `Unidad` as i, ";
				$cadenaSql .= " `Unidad` as l, ";
				$cadenaSql .= " `Marca o fabricante elemento` as j, ";
				$cadenaSql .= " `Cantidad o capacidad requerida en el proyecto` as k, ";
				$cadenaSql .= " CASE WHEN `Ubicación Actual` IS NULL THEN 'Bodega Sincelejo - CPNDC'  ";
				$cadenaSql .= "  ELSE `Ubicación Actual`  END  ";
				$cadenaSql .= "  as m, ";
				$cadenaSql .= " `Fecha Entrada`  as n, ";
				$cadenaSql .= " `Cantidad en Bodega` as o, ";
				$cadenaSql .= " `Fecha Prevista de Entrega en Bodega` as p, ";
				$cadenaSql .= " `Sitio de Instalación` as q, ";
				$cadenaSql .= " `Fecha Entrega en Sitio de Instalación` as r, ";
				$cadenaSql .= " `Cantidad en sitio de Instalación` as s, ";
				$cadenaSql .= " `Fecha Prevista en Sitio de Instalación` as t, ";
				$cadenaSql .= " `Cantidad Requerida en sitio de Instalación` as u, ";
				$cadenaSql .= " `Estado actual` as v, ";
				$cadenaSql .= " `Fecha Inicio` as w, ";
				$cadenaSql .= " `Fecha Terminación` as x, ";
				$cadenaSql .= " `Fecha Prevista Inicio PI&PS` as y, ";
				$cadenaSql .= " `Fecha Prevista Terminación PI&PS` as z, ";
				$cadenaSql .= " `Proyecto/Municipio Destino Instalación` as aa, ";
				$cadenaSql .= " `Fecha Prevista Inicio Instalación Proyecto/Municipio` as ab, ";
				$cadenaSql .= " `Fecha Prevista Fin Instalación Proyecto/Municipio` as ac";
				$cadenaSql .= " FROM ( ";
				$cadenaSql .= " SELECT DISTINCT ";
				$cadenaSql .= "
`tabProductos a Proyectar`.`cantidad_devolucion` as cantidad_devolucion,
 `tabItem`.codificacion as \"Codificación\",
`tabItem`.`item_group` as \"Nombre Elemento\",
`tabItem`.`referencia_elemento` as \"Identificacion  o referencia del elemento\", 
`tabItem`.`item_code` as \"Descripción  detallada del elemento\",
`tabProductos a Proyectar`.`destino_instalacion` as \"Destino Instalación Nivel Red\",
item_contrato.estado_contrato as \"Estado de compra\",
item_contrato.num_contrato as \"Número OC  o Contrato\",
item_contrato.proveedor as \"Nombre Proveedor\", 
\"\" as \"Fecha Contratacion\", 
item_contrato.cantidad as  \"Cantidad o capacidad Comprada o a adquirir\", 
`tabItem`.`stock_uom` as \"Unidad\",
`tabItem`.`brand` as \"Marca o fabricante elemento\",
`tabProductos a Proyectar`.`item_proyeccion` as \"Cantidad o capacidad requerida en el proyecto\",
`tabProductos a Proyectar`.`cantidad_adicional` as \"Cantidad Adicionada\", 
`tabProductos a Proyectar`.`cantidad_devolucion` as \"Cantidad Devuelta\",
`tabItem`.`stock_uom` as \"Unidad Medida\",
CASE WHEN stock_detail.qty IS NULL THEN purchase_items.t_warehouse
ELSE stock_detail.t_warehouse  END 
as \"Ubicación Actual\",
CASE WHEN stock_detail.qty IS NULL THEN qty_entrada
ELSE qty_entrada-stock_detail.qty- COALESCE(qty_entradaso,0) END 
as \"Cantidad en Bodega\",
`tabProject`.`fecha_prevista_bodega` as \"Fecha Prevista de Entrega en Bodega\",
stock_detail.fechas_salidas  as \"Fecha Entrega en Sitio de Instalación\",
purchase_items.fechas_entradas as \"Fecha Entrada\",
stock_detail.`project`  as \"Sitio de Instalación\",
(stock_detail.qty -`tabProductos a Proyectar`.`cantidad_devolucion`)  as \"Cantidad en sitio de Instalación\",
`tabProductos a Proyectar`.`item_proyeccion`  as \"Cantidad Requerida en sitio de Instalación\",
`tabProject`.`fecha_prevista_sitio` as \"Fecha Prevista en Sitio de Instalación\",
`tabProject`.`estado_isp` as \"Estado actual\",
`tabProject`.`expected_start_date` as \"Fecha Inicio\",
`tabProject`.`expected_end_date` as \"Fecha Terminación\",
`tabProject`.`fecha_prevista_inicio_pips` as \"Fecha Prevista Inicio PI&PS\",
`tabProject`.`fecha_prevista_fin_pips` as \"Fecha Prevista Terminación PI&PS\",`tabProductos a Proyectar`.`parent` as \"Proyecto/Municipio Destino Instalación\",
`tabProject`.`fecha_prevista_inicio_instalacion_proyecto` as \"Fecha Prevista Inicio Instalación Proyecto/Municipio\",
`tabProject`.`fecha_prevista_fin_instalacion_proyecto` as \"Fecha Prevista Fin Instalación Proyecto/Municipio\"
FROM `tabItem`
JOIN `tabProductos a Proyectar` on `tabProductos a Proyectar`.`item`=`tabItem`.`item_code`
LEFT JOIN `tabProject` on `tabProject`.`name`=`tabProductos a Proyectar`.`parent`
LEFT JOIN (SELECT destino.tipo_almacen,destino.project, `tabStock Entry Detail`.`parent`,`item_code`, sum(qty) as qty_entrada,GROUP_CONCAT(`posting_date` SEPARATOR ', ') as fechas_entradas, `t_warehouse`,s_warehouse, origen.`tipo_almacen` tipo_origen,`uom` FROM `tabStock Entry Detail` JOIN `tabStock Entry` on `tabStock Entry`.`name`=`tabStock Entry Detail`.`parent` JOIN `tabWarehouse` as origen on origen.`name`=s_warehouse JOIN `tabWarehouse` as destino on destino.`name`=t_warehouse WHERE `tabStock Entry Detail`.`docstatus`!=2 AND destino.tipo_almacen!='Salida' AND posting_date<= '".$variable."' GROUP BY item_code,`t_warehouse`, project ORDER BY `tabStock Entry Detail`.`t_warehouse` ASC ) as purchase_items on purchase_items.project=`tabProductos a Proyectar`.`parent` AND purchase_items.item_code=`tabItem`.`item_code` 
LEFT JOIN (SELECT destino.tipo_almacen,origen.project, `tabStock Entry Detail`.`parent`,`item_code`, sum(qty) as qty_entradaso, `t_warehouse`,s_warehouse, origen.`tipo_almacen` tipo_origen,`uom`FROM `tabStock Entry Detail` JOIN `tabStock Entry` on `tabStock Entry`.`name`=`tabStock Entry Detail`.`parent` JOIN `tabWarehouse` as origen on origen.`name`=s_warehouse JOIN `tabWarehouse` as destino on destino.`name`=t_warehouse WHERE `tabStock Entry Detail`.`docstatus`!=2 AND origen.tipo_almacen='Entrada' AND destino.tipo_almacen='Entrada' AND posting_date<= '".$variable."'  GROUP BY item_code, s_warehouse ORDER BY `tabStock Entry Detail`.`t_warehouse` ) as entradaso_items on entradaso_items.project=`tabProductos a Proyectar`.`parent` AND entradaso_items.item_code=`tabItem`.`item_code` 
LEFT JOIN `tabPurchase Receipt Item` on `tabPurchase Receipt Item`.`item_code`=`tabItem`.`item_code` AND `tabPurchase Receipt Item`.creation<= '".$variable."' 
LEFT JOIN `tabPurchase Receipt` on `tabPurchase Receipt`.`name`=`tabPurchase Receipt Item`.`parent` AND `tabPurchase Receipt`.posting_date<= '".$variable."' 
LEFT JOIN 
(SELECT DISTINCT item_code,`tabRegistro de Contrato`.`proveedor`, `tabRegistro de Contrato`.`num_contrato`, `tabRegistro de Contrato`.`estado_contrato`, project, cantidad
FROM `tabRegistro de Contrato Item` 
JOIN `tabRegistro de Contrato` ON `tabRegistro de Contrato`.`name`=`tabRegistro de Contrato Item`.`parent` 
WHERE 1 ORDER BY item_code ASC ) as item_contrato on item_contrato.item_code=`tabItem`.item_code  AND item_contrato.project=`tabProductos a Proyectar`.`parent`
LEFT JOIN (
SELECT destino.tipo_almacen,origen.project, `tabStock Entry Detail`.`parent`,`item_code`, sum(qty) as qty,GROUP_CONCAT(`posting_date` SEPARATOR ', ') as fechas_salidas, `t_warehouse`,s_warehouse, origen.`tipo_almacen` tipo_origen,`uom`FROM `tabStock Entry Detail` JOIN `tabStock Entry` on `tabStock Entry`.`name`=`tabStock Entry Detail`.`parent` JOIN `tabWarehouse` as origen on origen.`name`=s_warehouse JOIN `tabWarehouse` as destino on destino.`name`=t_warehouse WHERE `tabStock Entry Detail`.`docstatus`!=2 AND destino.tipo_almacen='Salida' AND origen.tipo_almacen='Entrada' AND posting_date<= '".$variable."' GROUP BY item_code,`s_warehouse`, project ORDER BY `tabStock Entry Detail`.`s_warehouse` ASC ) as stock_detail on stock_detail.item_code=`tabItem`.`item_code` AND stock_detail.project=`tabProductos a Proyectar`.`parent`
ORDER By `tabItem`.`item_code`ASC
) as resultado
		
GROUP BY `Proyecto/Municipio Destino Instalación`,`Descripción  detallada del elemento`
ORDER BY `Ubicación Actual`";
				$cadenaSql." LIMIT 50";
				break;
		}
		
		return $cadenaSql;
	}
}
?>

