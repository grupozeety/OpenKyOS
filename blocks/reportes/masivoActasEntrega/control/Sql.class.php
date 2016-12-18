<?php

namespace reportes\masivoActas;

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
		$this->miSesionSso = \SesionSso::singleton ();
	}
	public function getCadenaSql($tipo, $variable = '') {
		$info_usuario = $this->miSesionSso->getParametrosSesionAbierta ();
		
		foreach ( $info_usuario ['description'] as $key => $rol ) {
			
			$info_usuario ['rol'] [] = $rol;
		}
		
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
			
			case 'consultarInformacionActa' :
				$cadenaSql = " SELECT";
				// Atributos tabla contrato
				$cadenaSql .= " cn.id_beneficiario,";
				$cadenaSql .= " cn.nombres,";
				$cadenaSql .= " cn.primer_apellido,";
				$cadenaSql .= " cn.segundo_apellido,";
				$cadenaSql .= " cn.numero_identificacion,";
				$cadenaSql .= " cn.numero_contrato,";
				$cadenaSql .= " cn.direccion_domicilio,";
				$cadenaSql .= " cn.departamento,";
				$cadenaSql .= " cn.municipio,";
				$cadenaSql .= " cn.urbanizacion,";
				$cadenaSql .= " cn.estrato,";
				$cadenaSql .= " cn.manzana,";
				$cadenaSql .= " cn.bloque,";
				$cadenaSql .= " cn.torre,";
				$cadenaSql .= " cn.casa_apartamento,";
				$cadenaSql .= " cn.interior,";
				$cadenaSql .= " cn.lote,";
				$cadenaSql .= " cn.piso,";
				$cadenaSql .= " cn.estrato_socioeconomico,";
				$cadenaSql .= " cn.tipo_tecnologia as tipo_tecnologia_con,";
				// Atributos tabla masivo_kit (Kit Beneficiario)
				$cadenaSql .= " mk.serial_com AS serial,";
				$cadenaSql .= " mk.serial_esc,";
				$cadenaSql .= " mk.mac1_esc,";
				$cadenaSql .= " mk.mac2_esc,";
				$cadenaSql .= " mk.marca_esc,";
				$cadenaSql .= " '1' as cantidad_esc,";
				$cadenaSql .= " mk.ip_esc,";
				// Atributos tabla masivo_pruebas (Pruebas)
				$cadenaSql .= " mp.hora_prueba,";
				$cadenaSql .= " mp.hora_prueba as hora_prueba_vs,";
				$cadenaSql .= " mp.resultado_vs ,";
				$cadenaSql .= " 'Mbps' as unidad_vs ,";
				$cadenaSql .= " '' as observaciones_vs ,";
				$cadenaSql .= " mp.hora_prueba as hora_prueba_vb ,";
				$cadenaSql .= " mp.resultado_vb ,";
				$cadenaSql .= " 'Mbps' as unidad_vb ,";
				$cadenaSql .= " '' as observaciones_vb ,";
				$cadenaSql .= " mp.hora_prueba as hora_prueba_p1 ,";
				$cadenaSql .= " mp.resultado_p1 ,";
				$cadenaSql .= " 'ms' as unidad_p1 ,";
				$cadenaSql .= " 'www.mintic.gov.co' as observaciones_p1 ,";
				$cadenaSql .= " mp.hora_prueba as hora_prueba_p2 ,";
				$cadenaSql .= " mp.resultado_p2 ,";
				$cadenaSql .= " 'ms' as unidad_p2 ,";
				$cadenaSql .= " 'http://www.louvre.fr/en' as observaciones_p2 ,";
				$cadenaSql .= " mp.hora_prueba as hora_prueba_p3 ,";
				$cadenaSql .= " mp.resultado_p3 ,";
				$cadenaSql .= " 'ms' as unidad_p3 ,";
				$cadenaSql .= " 'https://www.wikipedia.org/' as observaciones_p3 ,";
				$cadenaSql .= " mp.hora_prueba as hora_prueba_tr1 ,";
				$cadenaSql .= " mp.resultado_tr1 ,";
				$cadenaSql .= " 'estado conexión' as unidad_tr1 ,";
				$cadenaSql .= " 'https://www.sivirtual.gov.co/' as observaciones_tr1 ,";
				$cadenaSql .= " mp.hora_prueba as hora_prueba_tr2 ,";
				$cadenaSql .= " mp.resultado_tr2 ,";
				$cadenaSql .= " 'Paso NAP' as unidad_tr2 ,";
				$cadenaSql .= " 'https://www.sivirtual.gov.co' as observaciones_tr2,";
				// Atributos tabla politecnica_portatil (Especificaciones Técnicas Computador)
				// $cadenaSql .= " pc.marca, pc.modelo, pc.cpu_version as procesador,";
				// $cadenaSql .= " pc.memoria_tipo ||' '|| pc.memoria_capacidad as memoria_ram,";
				// $cadenaSql .= " pc.disco_capacidad ||' - '|| pc.disco_serial as disco_duro,";
				// $cadenaSql .= " pc.sistema_operativo,";
				// $cadenaSql .= " pc.camara_tipo ||' '|| pc.camara_formato as camara,";
				// $cadenaSql .= " pc.parlantes_tipo||' '|| pc.audio_tipo as audio,";
				// $cadenaSql .= " pc.bateria_autonomia||' '|| pc.bateria_serial as bateria, ";
				// $cadenaSql .= " pc.red_serial as targeta_red_alambrica ,";
				// $cadenaSql .= " pc.wifi_serial as targeta_red_inalambrica,";
				// $cadenaSql .= " pc.alimentacion_dispositivo||' '|| pc.alimentacion_voltaje as cargador, ";
				// $cadenaSql .= " pc.pantalla_tipo||' '|| pc.pantalla_tamanno as pantalla,";
				// $cadenaSql .= " '' as web_soporte,";
				// $cadenaSql .= " '' as telefono_soporte,";
				// $cadenaSql .= " ";
				// Atributos tabla masivo_beneficiario (Beneficiario)
				$cadenaSql .= " mb.latitud,";
				$cadenaSql .= " mb.longitud,";
				$cadenaSql .= " mb.tipo_tecnologia as tecnologia,";
				$cadenaSql .= " pr.descripcion as tipo_tecnologia";
				
				$cadenaSql .= " FROM interoperacion.contrato AS cn";
				$cadenaSql .= " FULL JOIN interoperacion.masivo_kit AS mk";
				$cadenaSql .= " ON cn.numero_identificacion=mk.identificacion";
				$cadenaSql .= " FULL JOIN interoperacion.masivo_pruebas AS mp";
				$cadenaSql .= " ON cn.numero_identificacion=mp.identificacion";
				// $cadenaSql .= " FULL JOIN interoperacion.politecnica_portatil AS pc";
				// $cadenaSql .= " ON mk.serial_com=pc.serial";
				$cadenaSql .= " FULL JOIN interoperacion.masivo_beneficiario AS mb";
				$cadenaSql .= " ON cn.numero_identificacion=mb.identificacion";
				$cadenaSql .= " FULL JOIN parametros.parametros pr ON cn.tipo_tecnologia=pr.id_parametro";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " cn.estado_registro=TRUE AND numero_identificacion='" . $variable . "'";
				
				break;
			// case 'consultaInformacionBeneficiario' :
			// $cadenaSql = " SELECT bn.*,pr.descripcion as descripcion_tipo , cn.id id_contrato, cn.numero_contrato ";
			// $cadenaSql .= " FROM interoperacion.beneficiario_potencial bn ";
			// $cadenaSql .= " JOIN parametros.parametros pr ON pr.codigo= bn.tipo_beneficiario::text ";
			// $cadenaSql .= "JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro= pr.rel_parametro AND rl.descripcion='Tipo de Beneficario o Cliente' ";
			// $cadenaSql .= " LEFT JOIN interoperacion.contrato cn ON cn.id_beneficiario= bn.id_beneficiario AND cn.estado_registro=TRUE ";
			// $cadenaSql .= " WHERE bn.estado_registro = TRUE ";
			// $cadenaSql .= " AND pr.estado_registro = TRUE ";
			// $cadenaSql .= " AND bn.id_beneficiario= '" . $_REQUEST ['id'] . "';";
			// break;
			
			// case 'consultarBeneficiariosPotenciales' :
			// $cadenaSql = " SELECT DISTINCT identificacion ||' - ('||nombre||' '||primer_apellido||' '||segundo_apellido||')' AS value, id_beneficiario AS data ";
			// $cadenaSql .= " FROM interoperacion.beneficiario_potencial ";
			// $cadenaSql .= "WHERE estado_registro=TRUE ";
			// $cadenaSql .= "AND cast(identificacion as text) ILIKE '%" . $_GET ['query'] . "%' ";
			// $cadenaSql .= "OR nombre ILIKE '%" . $_GET ['query'] . "%' ";
			// $cadenaSql .= "OR primer_apellido ILIKE '%" . $_GET ['query'] . "%' ";
			// $cadenaSql .= "OR segundo_apellido ILIKE '%" . $_GET ['query'] . "%' ";
			// $cadenaSql .= "LIMIT 10; ";
			
			// break;
			
			case 'consultaInformacionBeneficiario' :
				$cadenaSql = " SELECT bn.*, dep.departamento as dep ,mun.municipio as mun,pr.descripcion as descripcion_tipo , cn.id id_contrato, cn.numero_contrato ,cn.urbanizacion as nombre_urbanizacion, cn.departamento as nombre_departamento, cn.municipio as nombre_municipio,cn.direccion_domicilio, cn.manzana as manzana_contrato, cn.bloque as bloque_contrato,
                cn.torre as torre_contrato,cn.casa_apartamento as casa_apto_contrato,cn.interior as interior_contrato,cn.lote as lote_contrato, cn.estrato_socioeconomico ";
				$cadenaSql .= " FROM interoperacion.beneficiario_potencial bn ";
				$cadenaSql .= " JOIN parametros.parametros pr ON pr.codigo= bn.tipo_beneficiario::text ";
				$cadenaSql .= "JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro= pr.rel_parametro AND rl.descripcion='Tipo de Beneficario o Cliente' ";
				$cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario= bn.id_beneficiario AND cn.estado_registro=TRUE ";
				$cadenaSql .= " JOIN parametros.departamento as dep ON dep.codigo_dep=bn.departamento";
				$cadenaSql .= " JOIN parametros.municipio as mun ON mun.codigo_mun=bn.municipio";
				$cadenaSql .= " WHERE bn.estado_registro = TRUE ";
				$cadenaSql .= " AND pr.estado_registro = TRUE ";
				$cadenaSql .= " AND bn.id_beneficiario= '" . $_REQUEST ['id'] . "';";
				break;
			
			case 'consultarBeneficiariosPotenciales' :
				$cadenaSql = " SELECT value , data ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "(SELECT DISTINCT bp.identificacion ||' - ('||bp.nombre||' '||bp.primer_apellido||' '||bp.segundo_apellido||')' AS  value, bp.id_beneficiario  AS data ";
				$cadenaSql .= " FROM  interoperacion.beneficiario_potencial bp ";
				$cadenaSql .= " LEFT JOIN interoperacion.agendamiento_comisionamiento ac on ac.id_beneficiario=bp.id_beneficiario ";
				$cadenaSql .= " JOIN interoperacion.beneficiario_alfresco ba ON bp.id_beneficiario=ba.id_beneficiario ";
				$cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=bp.id_beneficiario ";
				$cadenaSql .= " WHERE bp.estado_registro=TRUE ";
				$cadenaSql .= " AND ba.estado_registro=TRUE ";
				$cadenaSql .= " AND ba.carpeta_creada=TRUE ";
				$cadenaSql .= $variable;
				$cadenaSql .= "     ) datos ";
				$cadenaSql .= "WHERE value ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "LIMIT 10; ";
				break;
			
			case 'pruebaProduccion' :
				$cadenaSql = " select * from interoperacion.acta_entrega_servicios;";
				break;
			
			case 'registrarActaEntrega' :
				$cadenaSql = " UPDATE interoperacion.acta_entrega_servicios";
				$cadenaSql .= " SET estado_registro='FALSE'";
				$cadenaSql .= " WHERE id_beneficiario='" . $variable ['id_beneficiario'] . "';";
				$cadenaSql .= " INSERT INTO interoperacion.acta_entrega_servicios(";
				$cadenaSql .= " id_beneficiario,";
				$cadenaSql .= " nombre,";
				$cadenaSql .= " primer_apellido,";
				$cadenaSql .= " segundo_apellido,";
				$cadenaSql .= " tipo_documento,";
				$cadenaSql .= " identificacion, ";
				// $cadenaSql .= " correo, ";
				$cadenaSql .= " fecha_instalacion,";
				$cadenaSql .= " tipo_beneficiario,";
				$cadenaSql .= " estrato,";
				$cadenaSql .= " direccion,";
				$cadenaSql .= " urbanizacion,";
				// $cadenaSql .= " id_urbanizacion,";
				$cadenaSql .= " departamento,";
				$cadenaSql .= " municipio,";
				// $cadenaSql .= " codigo_dane,";
				// $cadenaSql .= " contacto,";
				// $cadenaSql .= " identificacion_cont,";
				// $cadenaSql .= " telefono,";
				// $cadenaSql .= " celular,";
				$cadenaSql .= " tipo_tecnologia,";
				$cadenaSql .= " geolocalizacion,";
				// $cadenaSql .= " producto,";
				// $cadenaSql .= " numero_act_esc,";
				$cadenaSql .= " mac_esc,";
				$cadenaSql .= " mac2_esc,";
				$cadenaSql .= " serial_esc,";
				$cadenaSql .= " marca_esc,";
				$cadenaSql .= " cant_esc,";
				$cadenaSql .= " ip_esc,";
				// $cadenaSql .= " numero_act_comp,";
				// $cadenaSql .= " mac_comp,";
				// $cadenaSql .= " serial_comp,";
				// $cadenaSql .= " marca_comp,";
				// $cadenaSql .= " cant_comp,";
				// $cadenaSql .= " ip_comp,";
				$cadenaSql .= " hora_prueba_vs,";
				$cadenaSql .= " resultado_vs,";
				$cadenaSql .= " unidad_vs,";
				$cadenaSql .= " observaciones_vs,";
				$cadenaSql .= " hora_prueba_vb,";
				$cadenaSql .= " resultado_vb,";
				$cadenaSql .= " unidad_vb,";
				$cadenaSql .= " observaciones_vb,";
				$cadenaSql .= " hora_prueba_p1,";
				$cadenaSql .= " resultado_p1,";
				$cadenaSql .= " unidad_p1,";
				$cadenaSql .= " observaciones_p1,";
				$cadenaSql .= " hora_prueba_p2,";
				$cadenaSql .= " resultado_p2,";
				$cadenaSql .= " unidad_p2,";
				$cadenaSql .= " observaciones_p2,";
				$cadenaSql .= " hora_prueba_p3,";
				$cadenaSql .= " resultado_p3,";
				$cadenaSql .= " unidad_p3,";
				$cadenaSql .= " observaciones_p3,";
				$cadenaSql .= " hora_prueba_tr1,";
				$cadenaSql .= " resultado_tr1,";
				$cadenaSql .= " unidad_tr1,";
				$cadenaSql .= " observaciones_tr1,";
				$cadenaSql .= " hora_prueba_tr2,";
				$cadenaSql .= " resultado_tr2,";
				$cadenaSql .= " unidad_tr2,";
				$cadenaSql .= " observaciones_tr2,";
				// $cadenaSql .= " ciudad_expedicion_identificacion,";
				// $cadenaSql .= " ciudad_firma,";
				// $cadenaSql .= " nombre_ins,";
				// $cadenaSql .= " identificacion_ins,";
				// $cadenaSql .= " celular_ins,";
				// $cadenaSql .= " firmaInstalador,";
				$cadenaSql .= " firmaBeneficiario)";
				// $cadenaSql .= " soporte)";
				$cadenaSql .= " VALUES ('" . $variable ['id_beneficiario'] . "',";
				$cadenaSql .= " '" . $variable ['nombres'] . "',";
				$cadenaSql .= " '" . $variable ['primer_apellido'] . "',";
				$cadenaSql .= " '" . $variable ['segundo_apellido'] . "',";
				$cadenaSql .= " '" . $variable ['tipo_documento'] . "', ";
				$cadenaSql .= " '" . $variable ['identificacion'] . "',";
				// $cadenaSql .= " '" . $variable ['correo'] . "', ";
				$cadenaSql .= " '" . $variable ['fecha_instalacion'] . "', ";
				$cadenaSql .= " '" . $variable ['tipo_beneficiario'] . "', ";
				$cadenaSql .= " '" . $variable ['estrato'] . "', ";
				$cadenaSql .= " '" . $variable ['direccion'] . "', ";
				// $cadenaSql .= " '" . $variable ['id_urbanizacion'] . "', ";
				$cadenaSql .= " '" . $variable ['urbanizacion'] . "', ";
				$cadenaSql .= " '" . $variable ['departamento'] . "', ";
				$cadenaSql .= " '" . $variable ['municipio'] . "', ";
				// $cadenaSql .= " '" . $variable ['codigo_dane'] . "', ";
				// $cadenaSql .= " '" . $variable ['contacto'] . "', ";
				// $cadenaSql .= " '" . $variable ['identificacion_cont'] . "', ";
				// $cadenaSql .= " '" . $variable ['telefono'] . "', ";
				// $cadenaSql .= " '" . $variable ['celular'] . "', ";
				$cadenaSql .= " '" . $variable ['tipo_tecnologia'] . "', ";
				$cadenaSql .= " '" . $variable ['geolocalizacion'] . "', ";
				// $cadenaSql .= " '" . $variable ['producto'] . "',";
				// $cadenaSql .= " '" . $variable ['numero_act_esc'] . "',";
				$cadenaSql .= " '" . $variable ['mac_esc'] . "',";
				$cadenaSql .= " '" . $variable ['mac2_esc'] . "',";
				$cadenaSql .= " '" . $variable ['serial_esc'] . "',";
				$cadenaSql .= " '" . $variable ['marca_esc'] . "',";
				$cadenaSql .= " '" . $variable ['cant_esc'] . "',";
				$cadenaSql .= " '" . $variable ['ip_esc'] . "',";
				// $cadenaSql .= " '" . $variable ['numero_act_comp'] . "',";
				// $cadenaSql .= " '" . $variable ['mac_comp'] . "',";
				// $cadenaSql .= " '" . $variable ['serial_comp'] . "',";
				// $cadenaSql .= " '" . $variable ['marca_comp'] . "',";
				// $cadenaSql .= " '" . $variable ['cant_comp'] . "',";
				// $cadenaSql .= " '" . $variable ['ip_comp'] . "',";
				$cadenaSql .= " '" . $variable ['hora_prueba_vs'] . "',";
				$cadenaSql .= " '" . $variable ['resultado_vs'] . "',";
				$cadenaSql .= " '" . $variable ['unidad_vs'] . "',";
				$cadenaSql .= " '" . $variable ['observaciones_vs'] . "',";
				$cadenaSql .= " '" . $variable ['hora_prueba_vb'] . "',";
				$cadenaSql .= " '" . $variable ['resultado_vb'] . "',";
				$cadenaSql .= " '" . $variable ['unidad_vb'] . "',";
				$cadenaSql .= " '" . $variable ['observaciones_vb'] . "',";
				$cadenaSql .= " '" . $variable ['hora_prueba_p1'] . "',";
				$cadenaSql .= " '" . $variable ['resultado_p1'] . "',";
				$cadenaSql .= " '" . $variable ['unidad_p1'] . "',";
				$cadenaSql .= " '" . $variable ['observaciones_p1'] . "',";
				$cadenaSql .= " '" . $variable ['hora_prueba_p2'] . "',";
				$cadenaSql .= " '" . $variable ['resultado_p2'] . "',";
				$cadenaSql .= " '" . $variable ['unidad_p2'] . "',";
				$cadenaSql .= " '" . $variable ['observaciones_p2'] . "',";
				$cadenaSql .= " '" . $variable ['hora_prueba_p3'] . "',";
				$cadenaSql .= " '" . $variable ['resultado_p3'] . "',";
				$cadenaSql .= " '" . $variable ['unidad_p3'] . "',";
				$cadenaSql .= " '" . $variable ['observaciones_p3'] . "',";
				$cadenaSql .= " '" . $variable ['hora_prueba_tr1'] . "',";
				$cadenaSql .= " '" . $variable ['resultado_tr1'] . "',";
				$cadenaSql .= " '" . $variable ['unidad_tr1'] . "',";
				$cadenaSql .= " '" . $variable ['observaciones_tr1'] . "',";
				$cadenaSql .= " '" . $variable ['hora_prueba_tr2'] . "',";
				$cadenaSql .= " '" . $variable ['resultado_tr2'] . "',";
				$cadenaSql .= " '" . $variable ['unidad_tr2'] . "',";
				$cadenaSql .= " '" . $variable ['observaciones_tr2'] . "',";
				// $cadenaSql .= " '" . $variable ['ciudad_expedicion_identificacion'] . "',";
				// $cadenaSql .= " '" . $variable ['ciudad_firma'] . "',";
				// $cadenaSql .= " '" . $variable ['nombre_ins'] . "', ";
				// $cadenaSql .= " '" . $variable ['identificacion_ins'] . "', ";
				// $cadenaSql .= " '" . $variable ['celular_ins'] . "', ";
				// $cadenaSql .= " '" . $variable ['url_firma_contratista'] . "',";
				$cadenaSql .= " '" . $variable ['url_firma_beneficiario'] . "')";
				// $cadenaSql .= " '" . $variable ['soporte'] . "');";
				break;
			
			case 'consultarParametro' :
				$cadenaSql = " SELECT pr.id_parametro, pr.descripcion, pr.codigo ";
				$cadenaSql .= " FROM parametros.parametros pr";
				$cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " pr.estado_registro=TRUE ";
				$cadenaSql .= " AND rl.descripcion='Tipologia Archivo'";
				$cadenaSql .= " AND pr.codigo='" . $variable . "' ";
				$cadenaSql .= " AND rl.estado_registro=TRUE ";
				
				break;
			
			case 'registrarDocumentoCertificado' :
				$cadenaSql = " UPDATE interoperacion.acta_entrega_portatil";
				$cadenaSql .= " SET nombre_documento_ps='" . $variable ['nombre_contrato'] . "', ruta_documento_ps='" . $variable ['ruta_contrato'] . "' ";
				$cadenaSql .= " WHERE id_beneficiario='" . $_REQUEST ['id_beneficiario'] . "' AND estado_registro='TRUE';";
				break;
			
			case 'consultaInformacionCertificado' :
				$cadenaSql = " SELECT aep.ruta_documento_ps, aep.nombre_documento_ps, aep.id, aep.id_beneficiario, aep.nombre, aep.primer_apellido, aep.segundo_apellido, aep.tipo_documento, aep.identificacion, aep.fecha_entrega, aep.tipo_beneficiario, aep.urbanizacion, aep.id_urbanizacion, aep.departamento, aep.municipio, aep.celular, aep.marca, aep.modelo, aep.serial, aep.procesador, aep.memoria_ram, aep.disco_duro, aep.sistema_operativo, aep.perifericos, aep.nombre_ins, aep.identificacion_ins, aep.celular_ins, aep.nombre_documento, aep.ruta_documento, aep.firmainstalador, aep.firmabeneficiario, aep.soporte, aep.fecha_registro, aep.camara, aep.audio, aep.bateria, aep.targeta_red_alambrica, aep.targeta_red_inalambrica, aep.cargador, aep.pantalla, aep.web_soporte, aep.direccion_general, aep.telefono_soporte,";
				$cadenaSql .= " aes.tipo_tecnologia, aes.fecha_instalacion, aes.estrato, aes.geolocalizacion, aes.producto, aes.mac_esc, aes.serial_esc, aes.marca_esc, aes.cant_esc, aes.ip_esc, aes.hora_prueba_vs, aes.resultado_vs, aes.unidad_vs, aes.observaciones_vs, aes.hora_prueba_vb, aes.resultado_vb, aes.unidad_vb, aes.observaciones_vb, aes.hora_prueba_p1, aes.resultado_p1, aes.unidad_p1, aes.observaciones_p1, aes.hora_prueba_p2, aes.resultado_p2, aes.unidad_p2, aes.observaciones_p2, aes.hora_prueba_p3, aes.resultado_p3, aes.unidad_p3, aes.observaciones_p3, aes.hora_prueba_tr1, aes.resultado_tr1, aes.unidad_tr1, aes.observaciones_tr1, aes.hora_prueba_tr2, aes.resultado_tr2, aes.unidad_tr2, aes.observaciones_tr2, aes.firmabeneficiario as firmabeneficiario_aes, aes.mac2_esc";
				$cadenaSql .= " FROM interoperacion.acta_entrega_servicios AS aes";
				$cadenaSql .= " JOIN interoperacion.acta_entrega_portatil AS aep";
				$cadenaSql .= " ON aes.id_beneficiario=aep.id_beneficiario";
				$cadenaSql .= " AND aep.id_beneficiario='" . $_REQUEST ['id_beneficiario'] . "'";
				$cadenaSql .= " AND aep.estado_registro='TRUE' AND aes.estado_registro='TRUE';";
				break;
			
			case 'registrarRequisito' :
				$cadenaSql = " INSERT INTO interoperacion.documentos_contrato(";
				$cadenaSql .= " id_beneficiario, ";
				$cadenaSql .= " tipologia_documento,";
				$cadenaSql .= " nombre_documento, ";
				$cadenaSql .= " ruta_relativa, ";
				$cadenaSql .= " usuario)";
				$cadenaSql .= " VALUES ('" . $variable ['id_beneficiario'] . "',";
				$cadenaSql .= " '" . $variable ['tipologia'] . "',";
				$cadenaSql .= " '" . $variable ['nombre_documento'] . "',";
				$cadenaSql .= " '" . $variable ['ruta_relativa'] . "',";
				$cadenaSql .= " '" . $info_usuario ['uid'] [0] . "');";
				
				break;
			
			case "parametroTipoVivienda" :
				$cadenaSql = "SELECT        ";
				$cadenaSql .= " codigo, ";
				$cadenaSql .= "param.descripcion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN ";
				$cadenaSql .= "parametros.relacion_parametro as rparam ";
				$cadenaSql .= "ON ";
				$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "rparam.descripcion = 'Tipo de Vivienda' ";
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
			
			case "parametroTipoBeneficiario" :
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
				$cadenaSql .= "rparam.descripcion = 'Tipo de Beneficario o Cliente' ";
				break;
			
			case "parametroEstrato" :
				$cadenaSql = "SELECT        ";
				$cadenaSql .= " codigo, ";
				$cadenaSql .= "param.descripcion ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "parametros.parametros as param ";
				$cadenaSql .= "INNER JOIN ";
				$cadenaSql .= "parametros.relacion_parametro as rparam ";
				$cadenaSql .= "ON ";
				$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "rparam.descripcion = 'Estrato' ";
				break;
			
			// case "parametroTipoTecnologia" :
			// $cadenaSql = "SELECT ";
			// $cadenaSql .= "codigo, ";
			// $cadenaSql .= "param.descripcion ";
			// $cadenaSql .= "FROM ";
			// $cadenaSql .= "parametros.parametros as param ";
			// $cadenaSql .= "INNER JOIN ";
			// $cadenaSql .= "parametros.relacion_parametro as rparam ";
			// $cadenaSql .= "ON ";
			// $cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
			// $cadenaSql .= "WHERE ";
			// $cadenaSql .= "rparam.descripcion = 'Tipo de Tecnología' ";
			// break;
			
			case 'parametroTipoTecnologia' :
				$cadenaSql = " SELECT pm.descripcion, pm.descripcion ";
				$cadenaSql .= " FROM parametros.parametros pm";
				$cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pm.rel_parametro AND pm.estado_registro=TRUE AND rl.descripcion='Tipo Tecnologia'";
				$cadenaSql .= " WHERE pm.estado_registro=TRUE;";
				
				break;
			
			// Sincronizar Alfresco
			case "consultarCarpetaSoportes" :
				$cadenaSql = " SELECT pr.id_parametro, pr.descripcion ";
				$cadenaSql .= " FROM parametros.parametros pr";
				$cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " pr.estado_registro=TRUE ";
				$cadenaSql .= " AND rl.descripcion='Alfresco Folders'";
				$cadenaSql .= " AND pr.codigo='" . $variable . "' ";
				$cadenaSql .= " AND rl.estado_registro=TRUE ";
				break;
			case "alfrescoDirectorio" :
				$cadenaSql = "SELECT parametros.descripcion ";
				$cadenaSql .= " FROM parametros.parametros ";
				$cadenaSql .= " JOIN parametros.relacion_parametro ON relacion_parametro.id_rel_parametro=parametros.rel_parametro ";
				$cadenaSql .= " WHERE parametros.estado_registro=TRUE AND relacion_parametro.descripcion='Directorio Alfresco Site' ";
				break;
			case "alfrescoUser" :
				$cadenaSql = " SELECT DISTINCT id_beneficiario, nombre_carpeta_dep as padre, nombre_carpeta_mun as hijo, site_alfresco as site ";
				$cadenaSql .= " FROM interoperacion.beneficiario_potencial ";
				$cadenaSql .= " INNER JOIN interoperacion.carpeta_alfresco on beneficiario_potencial.departamento=cast(carpeta_alfresco.cod_departamento as integer) ";
				$cadenaSql .= " WHERE cast(cod_municipio as integer)=municipio ";
				$cadenaSql .= " AND id_beneficiario='" . $variable . "' ";
				break;
			case "alfrescoLog" :
				$cadenaSql = "SELECT host, usuario, password ";
				$cadenaSql .= " FROM parametros.api_data ";
				$cadenaSql .= " WHERE componente='alfresco' ";
				break;
		}
		
		return $cadenaSql;
	}
}
?>

