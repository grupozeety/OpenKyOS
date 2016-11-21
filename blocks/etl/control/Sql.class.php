<?php

namespace reportes\instalacionesGenerales;

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
	function getCadenaSql($tipo, $variable = '') {
		
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
			case 'consultarBloques' :
				
				$cadenaSql = " SELECT id_bloque, nombre, descripcion, grupo ";
				$cadenaSql .= " FROM " . $prefijo . "bloque;";
				
				break;
			
			case 'insertarBloque' :
				$cadenaSql = 'INSERT INTO ';
				$cadenaSql .= $prefijo . 'bloque ';
				$cadenaSql .= '( ';
				$cadenaSql .= 'nombre,';
				$cadenaSql .= 'descripcion,';
				$cadenaSql .= 'grupo';
				$cadenaSql .= ') ';
				$cadenaSql .= 'VALUES ';
				$cadenaSql .= '( ';
				$cadenaSql .= '\'' . $_REQUEST ['nombre'] . '\', ';
				$cadenaSql .= '\'' . $_REQUEST ['descripcion'] . '\', ';
				$cadenaSql .= '\'' . $_REQUEST ['grupo'] . '\' ';
				$cadenaSql .= '); ';
				break;
			
			case 'registrarProyectosAlmacen' :
				
				$cadenaSql = "";
				$cont = 0;
				
				foreach ( $variable as $valor ) {
					
					if ($cont == 0) {
						
						$cadenaSql = "INSERT INTO public.reporte_semanal(";
						
						foreach ( $valor as $key => $value ) {
							$cadenaSql .= "" . $key . ",";
						}
						
						$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
						
						$cadenaSql .= ") VALUES ";
					}
					
					$cadenaSql .= "(";
					
					foreach ( $valor as $key => $value ) {
						
						$cadenaSql .= "'" . $value . "',";
					}
					
					$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
					
					$cadenaSql .= "),";
					
					$cont ++;
				}
				
				$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
				
				break;
			
			case 'actualizarProyectosAlmacen' :
				
				$cadenaSql = "UPDATE public.reporte_semanal ";
				$cadenaSql .= "SET ";
				$cadenaSql .= "estado_registro=FALSE ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "fecha_registro::timestamp::date=(SELECT current_timestamp::timestamp::date)";
				
				break;
			
			case 'consultarProyectosParametrizados' :
				
				$cadenaSql = " SELECT DISTINCT tipo_proyecto, id_proyecto";
				$cadenaSql .= " FROM parametros.parametrizacion_reporte";
				$cadenaSql .= " WHERE estado_registro= TRUE";
				$cadenaSql .= " ORDER BY id_proyecto;";
				break;
			
			case 'consultarCamposParametrizados' :
				
				$cadenaSql = " SELECT DISTINCT pr.campo, pr.valor_campo, ";
				$cadenaSql .= " pr.valor_actividad, pr.info_hijos, cr.tipo,cr.sub_tipo,cr.nombre_formulario ";
				$cadenaSql .= " FROM parametros.parametrizacion_reporte AS pr ";
				$cadenaSql .= " JOIN parametros.campos_reporte as cr ON cr.campo=pr.campo";
				$cadenaSql .= " WHERE pr.estado_registro= TRUE";
				$cadenaSql .= " AND pr.id_proyecto='" . $variable . "'";
				break;
			
			case 'consultarInformacionCore' :
				$cadenaSql = " SELECT DISTINCT tipo_proyecto, id_proyecto";
				$cadenaSql .= " FROM parametros.parametrizacion_reporte";
				$cadenaSql .= " WHERE estado_registro= TRUE";
				$cadenaSql .= " AND tipo_proyecto= 'core' ";
				$cadenaSql .= " ORDER BY id_proyecto;";
				break;
			
			case 'consultarInformacionCabecera' :
				$cadenaSql = " SELECT DISTINCT tipo_proyecto, id_proyecto";
				$cadenaSql .= " FROM parametros.parametrizacion_reporte";
				$cadenaSql .= " WHERE estado_registro= TRUE";
				$cadenaSql .= " AND tipo_proyecto= 'cabecera' ";
				$cadenaSql .= " ORDER BY id_proyecto;";
				break;
			
			case 'consultarInformacionHFC' :
				$cadenaSql = " SELECT DISTINCT tipo_proyecto, id_proyecto";
				$cadenaSql .= " FROM parametros.parametrizacion_reporte";
				$cadenaSql .= " WHERE estado_registro= TRUE";
				$cadenaSql .= " AND tipo_proyecto= 'hfc' ";
				$cadenaSql .= " ORDER BY id_proyecto;";
				break;
			
			case 'consultarInformacionWman' :
				$cadenaSql = " SELECT DISTINCT tipo_proyecto, id_proyecto";
				$cadenaSql .= " FROM parametros.parametrizacion_reporte";
				$cadenaSql .= " WHERE estado_registro= TRUE";
				$cadenaSql .= " AND tipo_proyecto= 'wman' ";
				$cadenaSql .= " ORDER BY id_proyecto;";
				break;
		}
		
		return $cadenaSql;
	}
}
?>
