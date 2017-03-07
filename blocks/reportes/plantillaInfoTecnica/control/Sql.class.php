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
				$cadenaSql = " SELECT id_nodo ";
				$cadenaSql .= " FROM interoperacion.nodo ";
				$cadenaSql .= " WHERE estado_registro='TRUE'";
				//$cadenaSql .= " AND codigo_nodo='" . $variable ['codigo_nodo'] . "' ";
				//$cadenaSql .= " AND codigo_cabecera='" . $variable ['codigo_cabecera'] . "' ";
				$cadenaSql .= " AND macesclavo1='" . $variable ['macesclavo1'] . "' ";
				break;
			
			case 'consultarExistenciaInfoWMAN' :
				$cadenaSql = " SELECT id_nodo ";
				$cadenaSql .= " FROM interoperacion.nodo ";
				$cadenaSql .= " WHERE estado_registro='TRUE'";
				//$cadenaSql .= " AND codigo_nodo='" . $variable ['codigo_nodo'] . "' ";
				//$cadenaSql .= " AND codigo_cabecera='" . $variable ['codigo_cabecera'] . "' ";
				$cadenaSql .= " AND macesclavo1='" . $variable ['macesclavo1'] . "' ";
				break;
			
			case 'consultarCabecera' :
				$cadenaSql = " SELECT id_cabecera ";
				$cadenaSql .= " FROM interoperacion.cabecera ";
				$cadenaSql .= " WHERE estado_registro='TRUE'";
				$cadenaSql .= " AND codigo_cabecera='" . $variable . "' ";
				break;
			
			case 'registrarCabecera' :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " interoperacion.cabecera (codigo_cabecera,descripcion, departamento, municipio,urbanizacion, id_urbanizacion) ";
				$cadenaSql .= " VALUES ( ";
				$cadenaSql .= " '".$variable['codigo_cabecera']."',";
				$cadenaSql .= " 'Cabecera ".$variable['urbanizacion']."',";
				$cadenaSql .= " '".$variable['departamento']."',";
				$cadenaSql .= " '".$variable['municipio']."',";
				$cadenaSql .= " '".$variable['urbanizacion']."',";
				$cadenaSql .= " '".$variable['id_urbanizacion']."'";
				$cadenaSql .= " ); ";
				break;
			
			case 'actualizarInformacion' :
				$cadenaSql = " UPDATE interoperacion.nodo ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " departamento='" . $variable ['departamento'] . "', ";
				$cadenaSql .= " municipio='" . $variable ['municipio'] . "', ";
				$cadenaSql .= " urbanizacion='" . $variable ['urbanizacion'] . "', ";
				$cadenaSql .= " id_urbanizacion='" . $variable ['id_urbanizacion'] . "', ";
				$cadenaSql .= " tipo_tecnologia='" . $variable ['tipo_tecnologia'] . "', ";
				$cadenaSql .= " mac_master_eoc='" . $variable ['mac_master_eoc'] . "', ";
				$cadenaSql .= " ip_master_eoc='" . $variable ['ip_master_eoc'] . "', ";
				$cadenaSql .= " mac_onu_eoc='" . $variable ['mac_onu_eoc'] . "', ";
				$cadenaSql .= " ip_onu_eoc='" . $variable ['ip_onu_eoc'] . "', ";
				$cadenaSql .= " mac_hub_eoc='" . $variable ['mac_hub_eoc'] . "', ";
				$cadenaSql .= " ip_hub_eoc='" . $variable ['ip_hub_eoc'] . "', ";
				$cadenaSql .= " mac_cpe_eoc='" . $variable ['mac_cpe_eoc'] . "', ";
				$cadenaSql .= " mac_celda='" . $variable ['mac_celda'] . "', ";
				$cadenaSql .= " ip_celda='" . $variable ['ip_celda'] . "', ";
				$cadenaSql .= " nombre_nodo='" . $variable ['nombre_nodo'] . "', ";
				$cadenaSql .= " nombre_sectorial='" . $variable ['nombre_sectorial'] . "', ";
				$cadenaSql .= " ip_switch_celda='" . $variable ['ip_switch_celda'] . "', ";
				$cadenaSql .= " mac_sm_celda='" . $variable ['mac_sm_celda'] . "', ";
				$cadenaSql .= " ip_sm_celda='" . $variable ['ip_sm_celda'] . "', ";
				$cadenaSql .= " mac_cpe_celda='" . $variable ['mac_cpe_celda'] . "', ";
				//$cadenaSql .= " estado_registro='" . $variable ['estado_registro'] . "', ";
				$cadenaSql .= " latitud='" . $variable ['latitud'] . "', ";
				$cadenaSql .= " longitud='" . $variable ['longitud'] . "', ";
				$cadenaSql .= " macesclavo1='" . $variable ['macesclavo1'] . "', ";
				$cadenaSql .= " port_olt='" . $variable ['port_olt'] . "' ";
				$cadenaSql .= " WHERE macesclavo1='" . $variable ['macesclavo1'] . "' ";
				$cadenaSql .= " AND codigo_nodo='" . $variable ['codigo_nodo'] . "' ";
				$cadenaSql .= " AND codigo_cabecera='" . $variable ['codigo_cabecera'] . "'; ";
				break;
			
			case 'registrarNodo' :
				$cadenaSql = " INSERT INTO interoperacion.nodo ( ";
				$cadenaSql .= "codigo_nodo,codigo_cabecera,departamento,municipio, urbanizacion, id_urbanizacion, ";
				$cadenaSql .= "tipo_tecnologia, mac_master_eoc,ip_master_eoc,mac_onu_eoc,ip_onu_eoc, mac_hub_eoc,ip_hub_eoc, ";
				$cadenaSql .= "mac_cpe_eoc,mac_celda,ip_celda,nombre_nodo, nombre_sectorial,ip_switch_celda, ";
				$cadenaSql .= "mac_sm_celda,ip_sm_celda, mac_cpe_celda, latitud, longitud, macesclavo1,port_olt) VALUES (";
				$cadenaSql .= " '" . $variable ['codigo_nodo'] . "', ";
				$cadenaSql .= " '" . $variable ['codigo_cabecera'] . "', ";
				$cadenaSql .= " '" . $variable ['departamento'] . "', ";
				$cadenaSql .= " '" . $variable ['municipio'] . "', ";
				$cadenaSql .= " '" . $variable ['urbanizacion'] . "', ";
				$cadenaSql .= " '" . $variable ['id_urbanizacion'] . "', ";
				$cadenaSql .= " '" . $variable ['tipo_tecnologia'] . "', ";
				$cadenaSql .= " '" . $variable ['mac_master_eoc'] . "', ";
				$cadenaSql .= " '" . $variable ['ip_master_eoc'] . "', ";
				$cadenaSql .= " '" . $variable ['mac_onu_eoc'] . "', ";
				$cadenaSql .= " '" . $variable ['ip_onu_eoc'] . "', ";
				$cadenaSql .= " '" . $variable ['mac_hub_eoc'] . "', ";
				$cadenaSql .= " '" . $variable ['ip_hub_eoc'] . "', ";
				$cadenaSql .= " '" . $variable ['mac_cpe_eoc'] . "', ";
				$cadenaSql .= " '" . $variable ['mac_celda'] . "', ";
				$cadenaSql .= " '" . $variable ['ip_celda'] . "', ";
				$cadenaSql .= " '" . $variable ['nombre_nodo'] . "', ";
				$cadenaSql .= " '" . $variable ['nombre_sectorial'] . "', ";
				$cadenaSql .= " '" . $variable ['ip_switch_celda'] . "', ";
				$cadenaSql .= " '" . $variable ['mac_sm_celda'] . "', ";
				$cadenaSql .= " '" . $variable ['ip_sm_celda'] . "', ";
				$cadenaSql .= " '" . $variable ['mac_cpe_celda'] . "', ";
				$cadenaSql .= " '" . $variable ['latitud'] . "', ";
				$cadenaSql .= " '" . $variable ['longitud'] . "', ";
				$cadenaSql .= " '" . $variable ['macesclavo1'] . "', ";
				$cadenaSql .= " '" . $variable ['port_olt'] . "') ";
			break;
		}
		
		return $cadenaSql;
	}
}
?>

