<?php

namespace reportes\actaEntregaServicios;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";
include_once "core/auth/SesionSso.class.php";

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql
{
    public $miConfigurador;
    public $miSesionSso;
    public function __construct()
    {
        $this->miConfigurador = \Configurador::singleton();
        //$this->miSesionSso = \SesionSso::singleton();
    }
    public function getCadenaSql($tipo, $variable = '')
    {
        /*$info_usuario = $this->miSesionSso->getParametrosSesionAbierta();

        foreach ($info_usuario['description'] as $key => $rol) {

        $info_usuario['rol'][] = $rol;
        }*/

        /**
         * 1.
         * Revisar las variables para evitar SQL Injection
         */
        $prefijo = $this->miConfigurador->getVariableConfiguracion("prefijo");
        $idSesion = $this->miConfigurador->getVariableConfiguracion("id_sesion");

        switch ($tipo) {

            /**
                 * Clausulas específicas
                 */

            case 'consultarFirma':
                $cadenaSql = " SELECT nombre_archivo, ruta_archivo";
                $cadenaSql .= " FROM interoperacion.firma_beneficiario";
                $cadenaSql .= " WHERE estado_registro = TRUE";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "';";
                break;

            case 'consultaInformacionBeneficiario':
                $cadenaSql = " SELECT ";
                $cadenaSql .= " cn.numero_contrato,";
                $cadenaSql .= " cn.nombres AS nombre,";
                $cadenaSql .= " cn.primer_apellido,";
                $cadenaSql .= " cn.segundo_apellido,";
                $cadenaSql .= " cn.tipo_documento,";
                $cadenaSql .= " cn.numero_identificacion AS identificacion,";
                $cadenaSql .= " cn.direccion_domicilio AS direccion,";
                $cadenaSql .= " cn.manzana AS manzana_contrato,";
                $cadenaSql .= " cn.bloque As bloque_contrato,";
                $cadenaSql .= " cn.torre AS torre_contrato,";
                $cadenaSql .= " cn.casa_apartamento AS casa_apto_contrato,";
                $cadenaSql .= " cn.interior AS interior_contrato,";
                $cadenaSql .= " cn.lote AS lote_contrato,";
                $cadenaSql .= " cn.piso AS piso_contrato,";
                $cadenaSql .= " cn.estrato AS tipo_beneficiario,";
                $cadenaSql .= " cn.estrato_socioeconomico,";
                $cadenaSql .= " cn.departamento AS nombre_departamento,";
                $cadenaSql .= " cn.municipio AS nombre_municipio,";
                $cadenaSql .= " cn.urbanizacion AS nombre_urbanizacion,";
                $cadenaSql .= " cn.tipo_tecnologia as tipo_tecnologia_int,";
                $cadenaSql .= " pr.descripcion AS tipo_tecnologia,";
                $cadenaSql .= " aes.geolocalizacion,";
                $cadenaSql .= " aes.mac_esc,";
                $cadenaSql .= " aes.mac2_esc,";
                $cadenaSql .= " aes.serial_esc,";
                $cadenaSql .= " aes.marca_esc,";
                $cadenaSql .= " aes.cant_esc,";
                $cadenaSql .= " aes.ip_esc,";
                $cadenaSql .= " aes.hora_prueba_vs,";
                $cadenaSql .= " aes.resultado_vs,";
                $cadenaSql .= " aes.unidad_vs,";
                $cadenaSql .= " aes.observaciones_vs,";
                $cadenaSql .= " aes.hora_prueba_vb,";
                $cadenaSql .= " aes.resultado_vb, ";
                $cadenaSql .= " aes.unidad_vb,";
                $cadenaSql .= " aes.observaciones_vb,";
                $cadenaSql .= " aes.hora_prueba_p1,";
                $cadenaSql .= " aes.resultado_p1,";
                $cadenaSql .= " aes.unidad_p1,";
                $cadenaSql .= " aes.observaciones_p1,";
                $cadenaSql .= " aes.hora_prueba_p2,";
                $cadenaSql .= " aes.resultado_p2,";
                $cadenaSql .= " aes.unidad_p2,";
                $cadenaSql .= " aes.observaciones_p2,";
                $cadenaSql .= " aes.hora_prueba_p3,";
                $cadenaSql .= " aes.resultado_p3,";
                $cadenaSql .= " aes.unidad_p3,";
                $cadenaSql .= " aes.observaciones_p3,";
                $cadenaSql .= " aes.hora_prueba_tr1,";
                $cadenaSql .= " aes.resultado_tr1,";
                $cadenaSql .= " aes.unidad_tr1,";
                $cadenaSql .= " aes.observaciones_tr1,";
                $cadenaSql .= " aes.hora_prueba_tr2,";
                $cadenaSql .= " aes.resultado_tr2,";
                $cadenaSql .= " aes.unidad_tr2,";
                $cadenaSql .= " aes.observaciones_tr2,";
                $cadenaSql .= " aes.fecha_instalacion,";
                $cadenaSql .= " aes.firmabeneficiario,";
                $cadenaSql .= " aes.ruta_documento,";
                $cadenaSql .= " aes.nombre_documento,";
                $cadenaSql .= " aes.reporte_fallos,";
                $cadenaSql .= " aes.acceso_reportando,";
                $cadenaSql .= " aes.paginas_visitadas,";
                $cadenaSql .= " aes.fecha_comisionamiento ";
                $cadenaSql .= " FROM interoperacion.contrato AS cn ";
                $cadenaSql .= " FULL JOIN interoperacion.acta_entrega_servicios aes";
                $cadenaSql .= " ON cn.id_beneficiario=aes.id_beneficiario AND aes.estado_registro='TRUE'";
                $cadenaSql .= " JOIN parametros.parametros pr";
                $cadenaSql .= " ON cn.tipo_tecnologia=pr.id_parametro";
                $cadenaSql .= " WHERE cn.id_beneficiario ='" . $_REQUEST['id_beneficiario'] . "'";
                break;

            case 'consultaInformacionBeneficiarioEditar':
                $cadenaSql = " SELECT ";
                $cadenaSql .= " cn.numero_contrato,";
                $cadenaSql .= " cn.nombres AS nombre,";
                $cadenaSql .= " cn.primer_apellido,";
                $cadenaSql .= " cn.segundo_apellido,";
                $cadenaSql .= " cn.tipo_documento,";
                $cadenaSql .= " cn.numero_identificacion AS identificacion,";
                $cadenaSql .= " cn.direccion_domicilio AS direccion,";
                $cadenaSql .= " cn.manzana AS manzana_contrato,";
                $cadenaSql .= " cn.bloque As bloque_contrato,";
                $cadenaSql .= " cn.torre AS torre_contrato,";
                $cadenaSql .= " cn.casa_apartamento AS casa_apto_contrato,";
                $cadenaSql .= " cn.interior AS interior_contrato,";
                $cadenaSql .= " cn.lote AS lote_contrato,";
                $cadenaSql .= " cn.piso AS piso_contrato,";
                $cadenaSql .= " cn.estrato AS tipo_beneficiario,";
                $cadenaSql .= " cn.estrato_socioeconomico,";
                $cadenaSql .= " cn.departamento AS nombre_departamento,";
                $cadenaSql .= " cn.municipio AS nombre_municipio,";
                $cadenaSql .= " cn.urbanizacion AS nombre_urbanizacion,";
                $cadenaSql .= " cn.tipo_tecnologia as tipo_tecnologia_int,";
                $cadenaSql .= " pr.descripcion AS tipo_tecnologia,";
                $cadenaSql .= " aes.geolocalizacion,";
                $cadenaSql .= " aes.mac_esc,";
                $cadenaSql .= " aes.mac2_esc,";
                $cadenaSql .= " aes.serial_esc,";
                $cadenaSql .= " aes.marca_esc,";
                $cadenaSql .= " aes.cant_esc,";
                $cadenaSql .= " aes.ip_esc,";
                $cadenaSql .= " aes.hora_prueba_vs,";
                $cadenaSql .= " aes.resultado_vs,";
                $cadenaSql .= " aes.unidad_vs,";
                $cadenaSql .= " aes.observaciones_vs,";
                $cadenaSql .= " aes.hora_prueba_vb,";
                $cadenaSql .= " aes.resultado_vb, ";
                $cadenaSql .= " aes.unidad_vb,";
                $cadenaSql .= " aes.observaciones_vb,";
                $cadenaSql .= " aes.hora_prueba_p1,";
                $cadenaSql .= " aes.resultado_p1,";
                $cadenaSql .= " aes.unidad_p1,";
                $cadenaSql .= " aes.observaciones_p1,";
                $cadenaSql .= " aes.hora_prueba_p2,";
                $cadenaSql .= " aes.resultado_p2,";
                $cadenaSql .= " aes.unidad_p2,";
                $cadenaSql .= " aes.observaciones_p2,";
                $cadenaSql .= " aes.hora_prueba_p3,";
                $cadenaSql .= " aes.resultado_p3,";
                $cadenaSql .= " aes.unidad_p3,";
                $cadenaSql .= " aes.observaciones_p3,";
                $cadenaSql .= " aes.hora_prueba_tr1,";
                $cadenaSql .= " aes.resultado_tr1,";
                $cadenaSql .= " aes.unidad_tr1,";
                $cadenaSql .= " aes.observaciones_tr1,";
                $cadenaSql .= " aes.hora_prueba_tr2,";
                $cadenaSql .= " aes.resultado_tr2,";
                $cadenaSql .= " aes.unidad_tr2,";
                $cadenaSql .= " aes.observaciones_tr2,";
                $cadenaSql .= " aes.fecha_instalacion,";
                $cadenaSql .= " aes.firmabeneficiario,";
                $cadenaSql .= " aes.ruta_documento,";
                $cadenaSql .= " aes.nombre_documento,";
                $cadenaSql .= " aes.verificacion_tracert,";
                $cadenaSql .= " aes.reporte_fallos,";
                $cadenaSql .= " aes.acceso_reportando,";
                $cadenaSql .= " aes.paginas_visitadas ,";
                $cadenaSql .= " aes.fecha_comisionamiento ";
                $cadenaSql .= " FROM interoperacion.contrato AS cn ";
                $cadenaSql .= " FULL JOIN interoperacion.acta_entrega_servicios aes";
                $cadenaSql .= " ON cn.id_beneficiario=aes.id_beneficiario";
                $cadenaSql .= " JOIN parametros.parametros pr";
                $cadenaSql .= " ON cn.tipo_tecnologia=pr.id_parametro";
                $cadenaSql .= " WHERE cn.id_beneficiario ='" . $_REQUEST['id_beneficiario'] . "'";
                $cadenaSql .= " AND aes.estado_registro=TRUE";
                break;

            case 'consultarBeneficiariosPotenciales':
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
                $cadenaSql .= "WHERE value ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

            case 'registrarActaEntrega':
                $cadenaSql = " UPDATE interoperacion.acta_entrega_servicios";
                $cadenaSql .= " SET estado_registro='FALSE'";
                $cadenaSql .= " WHERE id_beneficiario='" . $variable['id_beneficiario'] . "';";
                $cadenaSql .= " INSERT INTO interoperacion.acta_entrega_servicios(";
                $cadenaSql .= " id_beneficiario,";
                //$cadenaSql .= " nombre,";
                //$cadenaSql .= " primer_apellido,";
                //$cadenaSql .= " segundo_apellido,";
                //$cadenaSql .= " tipo_documento,";
                //$cadenaSql .= " identificacion, ";
                $cadenaSql .= " fecha_instalacion,";
                //$cadenaSql .= " tipo_beneficiario,";
                //$cadenaSql .= " estrato,";
                //$cadenaSql .= " direccion,";
                //$cadenaSql .= " urbanizacion,";
                //$cadenaSql .= " departamento,";
                //$cadenaSql .= " municipio,";
                //$cadenaSql .= " tipo_tecnologia,";
                $cadenaSql .= " geolocalizacion,";
                $cadenaSql .= " mac_esc,";
                $cadenaSql .= " mac2_esc,";
                $cadenaSql .= " serial_esc,";
                $cadenaSql .= " marca_esc,";
                $cadenaSql .= " cant_esc,";
                $cadenaSql .= " ip_esc,";
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
                $cadenaSql .= " firmaBeneficiario,";
                $cadenaSql .= " reporte_fallos,";
                $cadenaSql .= " acceso_reportando,";
                $cadenaSql .= " paginas_visitadas,";
                $cadenaSql .= " fecha_comisionamiento)";
                $cadenaSql .= " VALUES ('" . $variable['id_beneficiario'] . "',";
                //$cadenaSql .= " '" . $variable['nombres'] . "',";
                //$cadenaSql .= " '" . $variable['primer_apellido'] . "',";
                //$cadenaSql .= " '" . $variable['segundo_apellido'] . "',";
                //$cadenaSql .= " '" . $variable['tipo_documento'] . "', ";
                //$cadenaSql .= " '" . $variable['identificacion'] . "',";
                $cadenaSql .= " '" . $variable['fecha_instalacion'] . "', ";
                //$cadenaSql .= " '" . $variable['tipo_beneficiario'] . "', ";
                //$cadenaSql .= " '" . $variable['estrato'] . "', ";
                //$cadenaSql .= " '" . $variable['direccion'] . "', ";
                //$cadenaSql .= " '" . $variable['urbanizacion'] . "', ";
                //$cadenaSql .= " '" . $variable['departamento'] . "', ";
                //$cadenaSql .= " '" . $variable['municipio'] . "', ";
                //$cadenaSql .= " '" . $variable['tipo_tecnologia'] . "', ";
                $cadenaSql .= " '" . $variable['geolocalizacion'] . "', ";
                $cadenaSql .= " '" . $variable['mac_esc'] . "',";
                $cadenaSql .= " '" . $variable['mac2_esc'] . "',";
                $cadenaSql .= " '" . $variable['serial_esc'] . "',";
                $cadenaSql .= " '" . $variable['marca_esc'] . "',";
                $cadenaSql .= " '" . $variable['cant_esc'] . "',";
                $cadenaSql .= " '" . $variable['ip_esc'] . "',";
                $cadenaSql .= " '" . $variable['hora_prueba_vs'] . "',";
                $cadenaSql .= " '" . $variable['resultado_vs'] . "',";
                $cadenaSql .= " '" . $variable['unidad_vs'] . "',";
                $cadenaSql .= " '" . $variable['observaciones_vs'] . "',";
                $cadenaSql .= " '" . $variable['hora_prueba_vb'] . "',";
                $cadenaSql .= " '" . $variable['resultado_vb'] . "',";
                $cadenaSql .= " '" . $variable['unidad_vb'] . "',";
                $cadenaSql .= " '" . $variable['observaciones_vb'] . "',";
                $cadenaSql .= " '" . $variable['hora_prueba_p1'] . "',";
                $cadenaSql .= " '" . $variable['resultado_p1'] . "',";
                $cadenaSql .= " '" . $variable['unidad_p1'] . "',";
                $cadenaSql .= " '" . $variable['observaciones_p1'] . "',";
                $cadenaSql .= " '" . $variable['hora_prueba_p2'] . "',";
                $cadenaSql .= " '" . $variable['resultado_p2'] . "',";
                $cadenaSql .= " '" . $variable['unidad_p2'] . "',";
                $cadenaSql .= " '" . $variable['observaciones_p2'] . "',";
                $cadenaSql .= " '" . $variable['hora_prueba_p3'] . "',";
                $cadenaSql .= " '" . $variable['resultado_p3'] . "',";
                $cadenaSql .= " '" . $variable['unidad_p3'] . "',";
                $cadenaSql .= " '" . $variable['observaciones_p3'] . "',";
                $cadenaSql .= " '" . $variable['hora_prueba_tr1'] . "',";
                $cadenaSql .= " '" . $variable['resultado_tr1'] . "',";
                $cadenaSql .= " '" . $variable['unidad_tr1'] . "',";
                $cadenaSql .= " '" . $variable['observaciones_tr1'] . "',";
                $cadenaSql .= " '" . $variable['hora_prueba_tr2'] . "',";
                $cadenaSql .= " '" . $variable['resultado_tr2'] . "',";
                $cadenaSql .= " '" . $variable['unidad_tr2'] . "',";
                $cadenaSql .= " '" . $variable['observaciones_tr2'] . "',";
                $cadenaSql .= " '" . $variable['url_firma_beneficiario'] . "',";
                $cadenaSql .= " '" . $variable['reporte_fallos'] . "',";
                $cadenaSql .= " '" . $variable['acceso_reportando'] . "',";
                $cadenaSql .= " '" . $variable['paginas_visitadas'] . "',";
                $cadenaSql .= " '" . $variable['fecha_comisionamiento'] . "');";
                break;

            case 'consultarParametro':
                $cadenaSql = " SELECT pr.id_parametro, pr.descripcion, pr.codigo ";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Tipologia Archivo'";
                $cadenaSql .= " AND pr.codigo='" . $variable . "' ";
                $cadenaSql .= " AND rl.estado_registro=TRUE ";

                break;

            case 'registrarDocumentoCertificado':
                $cadenaSql = " UPDATE interoperacion.acta_entrega_servicios";
                $cadenaSql .= " SET nombre_documento='" . $variable['nombre_contrato'] . "', ruta_documento='" . $variable['ruta_contrato'] . "' ";
                $cadenaSql .= " WHERE id_beneficiario='" . $_REQUEST['id_beneficiario'] . "' AND estado_registro='TRUE';";
                break;

            case 'consultaInformacionCertificado':
                $cadenaSql = " SELECT ";
                $cadenaSql .= " cn.numero_contrato,";
                $cadenaSql .= " cn.nombres AS nombre,";
                $cadenaSql .= " cn.primer_apellido,";
                $cadenaSql .= " cn.segundo_apellido,";
                $cadenaSql .= " cn.tipo_documento,";
                $cadenaSql .= " cn.numero_identificacion AS identificacion,";
                $cadenaSql .= " cn.direccion_domicilio AS direccion,";
                $cadenaSql .= " cn.manzana,";
                $cadenaSql .= " cn.bloque,";
                $cadenaSql .= " cn.torre,";
                $cadenaSql .= " cn.casa_apartamento,";
                $cadenaSql .= " cn.interior,";
                $cadenaSql .= " cn.lote,";
                $cadenaSql .= " cn.piso,";
                $cadenaSql .= " cn.estrato AS estrato_socioeconomico,";
                $cadenaSql .= " cn.estrato_socioeconomico AS estrato,";
                $cadenaSql .= " cn.departamento,";
                $cadenaSql .= " cn.municipio,";
                $cadenaSql .= " cn.urbanizacion,";
                $cadenaSql .= " pr.descripcion AS tipo_tecnologia,";
                $cadenaSql .= " aes.geolocalizacion,";
                $cadenaSql .= " aes.mac_esc,";
                $cadenaSql .= " aes.mac2_esc,";
                $cadenaSql .= " aes.serial_esc,";
                $cadenaSql .= " aes.marca_esc,";
                $cadenaSql .= " aes.cant_esc,";
                $cadenaSql .= " aes.ip_esc,";
                $cadenaSql .= " aes.hora_prueba_vs,";
                $cadenaSql .= " aes.resultado_vs,";
                $cadenaSql .= " aes.unidad_vs,";
                $cadenaSql .= " aes.observaciones_vs,";
                $cadenaSql .= " aes.hora_prueba_vb,";
                $cadenaSql .= " aes.resultado_vb, ";
                $cadenaSql .= " aes.unidad_vb,";
                $cadenaSql .= " aes.observaciones_vb,";
                $cadenaSql .= " aes.hora_prueba_p1,";
                $cadenaSql .= " aes.resultado_p1,";
                $cadenaSql .= " aes.unidad_p1,";
                $cadenaSql .= " aes.observaciones_p1,";
                $cadenaSql .= " aes.hora_prueba_p2,";
                $cadenaSql .= " aes.resultado_p2,";
                $cadenaSql .= " aes.unidad_p2,";
                $cadenaSql .= " aes.observaciones_p2,";
                $cadenaSql .= " aes.hora_prueba_p3,";
                $cadenaSql .= " aes.resultado_p3,";
                $cadenaSql .= " aes.unidad_p3,";
                $cadenaSql .= " aes.observaciones_p3,";
                $cadenaSql .= " aes.hora_prueba_tr1,";
                $cadenaSql .= " aes.resultado_tr1,";
                $cadenaSql .= " aes.unidad_tr1,";
                $cadenaSql .= " aes.observaciones_tr1,";
                $cadenaSql .= " aes.hora_prueba_tr2,";
                $cadenaSql .= " aes.resultado_tr2,";
                $cadenaSql .= " aes.unidad_tr2,";
                $cadenaSql .= " aes.observaciones_tr2,";
                $cadenaSql .= " aes.fecha_instalacion,";
                $cadenaSql .= " aes.firmabeneficiario,";
                $cadenaSql .= " aes.ruta_documento,";
                $cadenaSql .= " aes.nombre_documento";
                $cadenaSql .= " FROM interoperacion.contrato AS cn ";
                $cadenaSql .= " JOIN interoperacion.acta_entrega_servicios aes";
                $cadenaSql .= " ON cn.id_beneficiario=aes.id_beneficiario";
                $cadenaSql .= " JOIN parametros.parametros pr";
                $cadenaSql .= " ON cn.tipo_tecnologia=pr.id_parametro";
                $cadenaSql .= " WHERE cn.id_beneficiario ='" . $_REQUEST['id_beneficiario'] . "'";
                $cadenaSql .= " AND aes.estado_registro=TRUE";
                break;

            case "parametroTipoVivienda":
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

            case "parametroDepartamento":
                $cadenaSql = "SELECT ";
                $cadenaSql .= "codigo_dep, ";
                $cadenaSql .= "departamento ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "parametros.departamento ";
                break;

            case "parametroMunicipio":
                $cadenaSql = "SELECT ";
                $cadenaSql .= "codigo_mun, ";
                $cadenaSql .= "municipio ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "parametros.municipio ";
                break;

            case "parametroTipoBeneficiario":
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

            case "parametroEstrato":
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

//             case "parametroTipoTecnologia" :
            //                 $cadenaSql = "SELECT        ";
            //                 $cadenaSql .= "codigo, ";
            //                 $cadenaSql .= "param.descripcion ";
            //                 $cadenaSql .= "FROM ";
            //                 $cadenaSql .= "parametros.parametros as param ";
            //                 $cadenaSql .= "INNER JOIN ";
            //                 $cadenaSql .= "parametros.relacion_parametro as rparam ";
            //                 $cadenaSql .= "ON ";
            //                 $cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
            //                 $cadenaSql .= "WHERE ";
            //                 $cadenaSql .= "rparam.descripcion = 'Tipo de Tecnología' ";
            //                 break;

            case 'parametroTipoTecnologia':
                $cadenaSql = " SELECT pm.id_parametro, pm.descripcion ";
                $cadenaSql .= " FROM parametros.parametros pm";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pm.rel_parametro AND pm.estado_registro=TRUE AND rl.descripcion='Tipo Tecnologia'";
                $cadenaSql .= " WHERE pm.estado_registro=TRUE;";

                break;
        }

        return $cadenaSql;
    }
}
