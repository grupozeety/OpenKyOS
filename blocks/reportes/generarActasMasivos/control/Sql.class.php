<?php
namespace reportes\generarActasMasivos;
if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
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
        $prefijo = $this->miConfigurador->getVariableConfiguracion("prefijo");
        $idSesion = $this->miConfigurador->getVariableConfiguracion("id_sesion");

        switch ($tipo) {

            /**
             * Clausulas específicas
             */

            //Validaciones

            case 'consultarExitenciaActaServicios':
                $cadenaSql = " SELECT aes.id_beneficiario,cn.numero_identificacion";
                $cadenaSql .= " FROM interoperacion.acta_entrega_servicios aes ";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=aes.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE cn.numero_identificacion='" . $variable . "'";
                $cadenaSql .= " AND aes.estado_registro='TRUE';";
                break;

            case 'consultarExitenciaActaPortatil':
                $cadenaSql = " SELECT ap.id_beneficiario,cn.numero_identificacion";
                $cadenaSql .= " FROM interoperacion.acta_entrega_portatil ap ";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=ap.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE cn.numero_identificacion='" . $variable . "'";
                $cadenaSql .= " AND ap.estado_registro='TRUE'";
                break;

            case 'consultarExitenciaIP':
                $cadenaSql = " SELECT aes.id_beneficiario,cn.numero_identificacion ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_servicios aes";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=aes.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE aes.estado_registro='TRUE'";
                $cadenaSql .= " AND aes.ip_esc='" . $variable . "';";
                break;

            case 'consultarExitenciaMac1':
                $cadenaSql = " SELECT aes.id_beneficiario,cn.numero_identificacion ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_servicios aes";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=aes.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE aes.estado_registro='TRUE'";
                $cadenaSql .= " AND aes.mac_esc='" . $variable . "';";
                break;

            case 'consultarExitenciaMac2':
                $cadenaSql = " SELECT aes.id_beneficiario,cn.numero_identificacion ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_servicios aes";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=aes.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE aes.estado_registro='TRUE'";
                $cadenaSql .= " AND aes.mac2_esc='" . $variable . "';";
                break;

            case 'consultarExitenciaSerialRegistrado':
                $cadenaSql = " SELECT id_equipo, serial";
                $cadenaSql .= " FROM interoperacion.politecnica_portatil ";
                $cadenaSql .= " WHERE serial='" . $variable . "';";
                break;

            case 'consultarExitenciaActa':
                $cadenaSql = " SELECT ep.id as identificador_acta, cn.numero_identificacion ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_portatil ep";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=ep.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE ep.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.numero_identificacion='" . $variable . "'";

                break;

            case 'consultarExitenciaSerialPortatil':
                $cadenaSql = " SELECT ep.id as identificador_acta, cn.numero_identificacion ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_portatil ep";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=ep.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE ep.estado_registro='TRUE'";
                $cadenaSql .= " AND ep.serial='" . $variable['serial_portatil'] . "'";
                //$cadenaSql .= " AND cn.numero_identificacion='" . $variable['identificacion_beneficiario'] . "'";
                break;

            case 'consultarExitenciaContrato':
                $cadenaSql = " SELECT id_beneficiario, numero_contrato,numero_identificacion";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND numero_identificacion='" . $variable . "';";
                break;

            case 'consultarExitenciaBeneficiario':
                $cadenaSql = " SELECT id_beneficiario";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND identificacion='" . $variable . "';";
                break;

            case 'consultarInformacionBeneficiario':
                $cadenaSql = " SELECT bp.* ";
                $cadenaSql .= " FROM interoperacion.contrato bp";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE' ";
                $cadenaSql .= " AND bp.numero_identificacion='" . $variable . "';";

                break;
            //Registros
            case 'registrarActaServicios':

                if ($_REQUEST['funcionalidad'] == '3') {
                    $cadenaSql = " UPDATE interoperacion.acta_entrega_servicios";
                    $cadenaSql .= " SET estado_registro='FALSE'";
                    $cadenaSql .= " WHERE id_beneficiario='" . $variable['id_beneficiario'] . "';";
                    $cadenaSql .= " INSERT INTO interoperacion.acta_entrega_servicios(";
                } else {
                    $cadenaSql = " INSERT INTO interoperacion.acta_entrega_servicios(";
                }

                $cadenaSql .= " id_beneficiario,";
                $cadenaSql .= " mac_esc, ";
                $cadenaSql .= " serial_esc, ";
                $cadenaSql .= " marca_esc, ";
                $cadenaSql .= " cant_esc, ";
                $cadenaSql .= " ip_esc, ";
                $cadenaSql .= " mac2_esc)";
                $cadenaSql .= " VALUES (";
                foreach ($variable as $key => $value) {

                    if ($key == 'mac_esc' && $value == 'Sin MAC 1') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'mac_esc2' && $value == 'Sin MAC 2') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'ip_esc' && $value == 'Sin IP') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'marca_esc' && $value == 'Sin Marca Esclavo') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'serial_esc' && $value == 'Sin Serial Esclavo') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'cant_esc' && $value == 'Sin Cantidad') {
                        $cadenaSql .= "NULL,";
                    } else {

                        $cadenaSql .= "'" . $value . "',";

                    }

                }

                $cadenaSql .= ")RETURNING id_beneficiario;";
                break;

            case 'registrarActaPortatil':

                if ($_REQUEST['funcionalidad'] == '3') {

                    $cadenaSql = " UPDATE interoperacion.acta_entrega_portatil";
                    $cadenaSql .= " SET estado_registro='FALSE'";
                    $cadenaSql .= " WHERE id_beneficiario='" . $variable['id_beneficiario'] . "';";
                    $cadenaSql .= " INSERT INTO interoperacion.acta_entrega_portatil(";
                } else {
                    $cadenaSql = " INSERT INTO interoperacion.acta_entrega_portatil(";
                }
                $cadenaSql .= " id_beneficiario,";
                $cadenaSql .= " fecha_entrega,";
                $cadenaSql .= " serial)";
                $cadenaSql .= " VALUES (";
                foreach ($variable as $key => $value) {

                    if ($key == 'serial' && $value == 'Sin Serial Portatil') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'fecha_entrega' && $value == 'Sin Fecha') {
                        $cadenaSql .= "NULL,";
                    } else {

                        $cadenaSql .= "'" . $value . "',";

                    }

                }

                $cadenaSql .= ")RETURNING id_beneficiario;";
                break;
            // Registro Procesos
            case 'registrarProceso':
                $cadenaSql = " INSERT INTO parametros.procesos_masivos(";
                $cadenaSql .= " descripcion,";
                $cadenaSql .= " estado,";
                $cadenaSql .= " nombre_archivo,";
                $cadenaSql .= " parametro_inicio,";
                $cadenaSql .= " parametro_fin,datos_adicionales,urbanizaciones )";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= " 'Actas',";
                $cadenaSql .= " 'No Iniciado',";
                $cadenaSql .= " '" . $variable['nombre'] . "',";
                $cadenaSql .= " '" . $variable['inicio'] . "',";
                $cadenaSql .= " '" . $variable['final'] . "',";
                $cadenaSql .= " '" . $variable['datos_adicionales'] . "',";
                $cadenaSql .= " '" . $variable['urbanizaciones'] . "'";
                $cadenaSql .= " )RETURNING id_proceso;";
                break;

            case 'consultarProceso':
                $cadenaSql = " SELECT * ";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE descripcion='Actas'";
                $cadenaSql .= " AND  estado_registro='TRUE' ";
                $cadenaSql .= " ORDER BY id_proceso DESC;";
                break;

            case 'consultarProcesoParticular':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE id_proceso=(";
                $cadenaSql .= " SELECT MIN(id_proceso) ";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE estado_registro='TRUE' ";
                $cadenaSql .= " AND estado='No Iniciado'";
                $cadenaSql .= " AND descripcion='Actas'";
                $cadenaSql .= " );";

                break;

            case 'actualizarProceso':
                $cadenaSql = " UPDATE parametros.procesos_masivos";
                $cadenaSql .= " SET estado='En Proceso'";
                $cadenaSql .= " WHERE id_proceso='" . $variable . "';";
                break;

            case 'finalizarProceso':
                $cadenaSql = " UPDATE parametros.procesos_masivos";
                $cadenaSql .= " SET estado='Finalizado',";
                $cadenaSql .= " ruta_archivo='" . $variable['ruta_archivo'] . "',";
                $cadenaSql .= " nombre_ruta_archivo='" . $variable['nombre_archivo'] . "',";
                $cadenaSql .= " peso_archivo='" . $variable['tamanio_archivo'] . "'";
                $cadenaSql .= " WHERE id_proceso='" . $variable['id_proceso'] . "';";
                break;
            // Crear Documenntos Contrato
            case 'ConsultaBeneficiarios':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE numero_contrato >=" . $variable['Inicio'] . " ";
                $cadenaSql .= " AND numero_contrato<=" . $variable['Fin'] . " ";
                $cadenaSql .= " ORDER BY numero_contrato ;";
                break;

            case 'consultarTipoDocumento':
                $cadenaSql = " SELECT pr.id_parametro,pr.codigo, pr.descripcion ";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Tipo de Documento'";
                $cadenaSql .= " AND pr.descripcion='" . $variable . "' ";
                $cadenaSql .= " AND rl.estado_registro=TRUE ";
                break;

            case 'consultarParametroParticular':
                $cadenaSql = " SELECT descripcion ";
                $cadenaSql .= " FROM parametros.parametros";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_parametro='" . $variable . "';";
                break;

            case 'ConsultaBeneficiariosActaServicio':
                $cadenaSql = " SELECT";
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
                $cadenaSql .= " cn.barrio,";
                $cadenaSql .= " cn.torre,";
                $cadenaSql .= " cn.casa_apartamento,";
                $cadenaSql .= " cn.interior,";
                $cadenaSql .= " cn.lote,";
                $cadenaSql .= " cn.piso,";
                $cadenaSql .= " cn.estrato_socioeconomico,";
                $cadenaSql .= " cn.tipo_tecnologia as tipo_tecnologia_con,";
                $cadenaSql .= " ";
                $cadenaSql .= " '' AS serial,";
                $cadenaSql .= " mp.serial_esc,";
                $cadenaSql .= " mp.mac_esc as mac1_esc,";
                $cadenaSql .= " mp.mac2_esc,";
                $cadenaSql .= " mp.marca_esc,";
                $cadenaSql .= " mp.cant_esc as cantidad_esc,";
                $cadenaSql .= " mp.ip_esc,";
                $cadenaSql .= " '' hora_prueba,";
                $cadenaSql .= " '' as hora_prueba_vs,";
                $cadenaSql .= " '' resultado_vs ,";
                $cadenaSql .= " 'Mbps' as unidad_vs ,";
                $cadenaSql .= " '' as observaciones_vs ,";
                $cadenaSql .= " '' as hora_prueba_vb ,";
                $cadenaSql .= " '' resultado_vb ,";
                $cadenaSql .= " 'Mbps' as unidad_vb ,";
                $cadenaSql .= " '' as observaciones_vb ,";
                $cadenaSql .= " '' as hora_prueba_p1 ,";
                $cadenaSql .= " '' resultado_p1 ,";
                $cadenaSql .= " 'ms' as unidad_p1 ,";
                $cadenaSql .= " 'www.mintic.gov.co' as observaciones_p1 ,";
                $cadenaSql .= " '' as hora_prueba_p2 ,";
                $cadenaSql .= " '' resultado_p2 ,";
                $cadenaSql .= " 'ms' as unidad_p2 ,";
                $cadenaSql .= " 'http://www.louvre.fr/en' as observaciones_p2 ,";
                $cadenaSql .= " '' as hora_prueba_p3 ,";
                $cadenaSql .= " '' resultado_p3 ,";
                $cadenaSql .= " 'ms' as unidad_p3 ,";
                $cadenaSql .= " 'https://www.wikipedia.org/' as observaciones_p3 ,";
                $cadenaSql .= " '' as hora_prueba_tr1 ,";
                $cadenaSql .= " '' resultado_tr1 ,";
                $cadenaSql .= " 'estado conexión' as unidad_tr1 ,";
                $cadenaSql .= " 'https://www.sivirtual.gov.co/' as observaciones_tr1 ,";
                $cadenaSql .= " '' as hora_prueba_tr2 ,";
                $cadenaSql .= " '' resultado_tr2 ,";
                $cadenaSql .= " 'Pasa por el NAP Colombia' as unidad_tr2 ,";
                $cadenaSql .= " 'https://www.sivirtual.gov.co' as observaciones_tr2,";
                $cadenaSql .= " cn.tipo_tecnologia as tecnologia,";
                $cadenaSql .= " pr.descripcion as tipo_tecnologia";
                $cadenaSql .= " FROM interoperacion.contrato AS cn";
                $cadenaSql .= " JOIN interoperacion.acta_entrega_servicios AS mp ON mp.id_beneficiario=cn.id_beneficiario AND mp.estado_registro='TRUE' ";
                $cadenaSql .= " FULL JOIN parametros.parametros pr ON cn.tipo_tecnologia=pr.id_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " cn.estado_registro=TRUE ";
                $cadenaSql .= " AND cn.id_beneficiario IN (" . $variable . ");";
                break;

            case 'ConsultaBeneficiariosActaPortatil':
                $cadenaSql = " SELECT ep.*, ";
                $cadenaSql .= " cn.numero_contrato,";
                $cadenaSql .= " cn.estrato as tp_beneficiario,";
                $cadenaSql .= " cn.direccion_domicilio,";
                $cadenaSql .= " cn.manzana,";
                $cadenaSql .= " cn.bloque,";
                $cadenaSql .= " cn.torre,";
                $cadenaSql .= " cn.casa_apartamento,";
                $cadenaSql .= " cn.interior,";
                $cadenaSql .= " cn.lote,";
                $cadenaSql .= " cn.piso,";
                $cadenaSql .= " bn.municipio as codigo_municipio, ";
                $cadenaSql .= " cn.nombres as nombre_contrato,";
                $cadenaSql .= " cn.primer_apellido as primer_apellido_contrato,";
                $cadenaSql .= " cn.segundo_apellido as segundo_apellido_contrato,";
                $cadenaSql .= " cn.tipo_documento as tipo_documento_contrato,";
                $cadenaSql .= " cn.numero_identificacion as numero_identificacion_contrato,";
                $cadenaSql .= " cn.estrato as tipo_beneficiario_contrato, ";
                $cadenaSql .= " cn.estrato_socioeconomico as estrato_socioeconomico_contrato,";
                $cadenaSql .= " cn.urbanizacion as nombre_urbanizacion,";
                $cadenaSql .= " cn.departamento as nombre_departamento,";
                $cadenaSql .= " cn.municipio as nombre_municipio,";
                $cadenaSql .= " cn.barrio as barrio_contrato, cn.numero_identificacion,cn.nombres ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_portatil AS ep";
                $cadenaSql .= " JOIN interoperacion.contrato as cn ON cn.id_beneficiario=ep.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial as bn ON bn.id_beneficiario=ep.id_beneficiario AND bn.estado_registro='TRUE' ";
                $cadenaSql .= " WHERE  ep.estado_registro='TRUE' ";
                $cadenaSql .= " AND ep.id_beneficiario IN (" . $variable . ");";

                break;

            case 'consultarEstadoProceso':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_proceso='" . $_REQUEST['id_proceso'] . "' ";
                $cadenaSql .= " AND estado IN ('No Iniciado','Finalizado'); ";
                break;

            case 'eliminarProceso':
                $cadenaSql = " UPDATE parametros.procesos_masivos";
                $cadenaSql .= " SET estado_registro='FALSE'";
                $cadenaSql .= " WHERE id_proceso='" . $_REQUEST['id_proceso'] . "'; ";
                break;

        }

        return $cadenaSql;
    }
}
?>

