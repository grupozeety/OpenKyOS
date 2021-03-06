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
class Sql extends \Sql
{
    public $miConfigurador;
    public function getCadenaSql($tipo, $variable = '')
    {

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
                $cadenaSql = " SELECT aes.id_beneficiario,cn.numero_identificacion,cn.urbanizacion ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_servicios aes";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=aes.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE aes.estado_registro='TRUE'";
                $cadenaSql .= " AND aes.mac_esc='" . $variable . "';";
                break;

            case 'consultarExitenciaMac2':
                $cadenaSql = " SELECT aes.id_beneficiario,cn.numero_identificacion,cn.urbanizacion ";
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

                $cadenaSql = " INSERT INTO interoperacion.acta_entrega_servicios(";
                $cadenaSql .= " id_beneficiario,";
                $cadenaSql .= " mac_esc, ";
                $cadenaSql .= " serial_esc, ";
                $cadenaSql .= " marca_esc, ";
                $cadenaSql .= " cant_esc, ";
                $cadenaSql .= " ip_esc, ";
                $cadenaSql .= " mac2_esc,";
                $cadenaSql .= " fecha_instalacion)";
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
                    } else if ($key == 'fecha_instalacion' && $value == 'Sin Fecha') {
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

                $cadenaSql = " INSERT INTO interoperacion.acta_entrega_portatil(";
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

            case 'actualizarActaPortatil':
                $cadenaSql = " UPDATE interoperacion.acta_entrega_portatil";
                $cadenaSql .= " SET";

                if (!is_null($variable['fecha_entrega'])) {

                    $cadenaSql .= " fecha_entrega='" . $variable['fecha_entrega'] . "',";
                }

                if (!is_null($variable['serial'])) {

                    $cadenaSql .= " serial='" . $variable['serial'] . "',";
                }

                $cadenaSql .= " WHERE id_beneficiario='" . $variable['id_beneficiario'] . "'";
                $cadenaSql .= " AND estado_registro='TRUE';";

                $cadenaSql = str_replace("', W", "' W", $cadenaSql);

                break;

            case 'actualizarActaServicios':
                $cadenaSql = " UPDATE interoperacion.acta_entrega_servicios";
                $cadenaSql .= " SET";

                if (!is_null($variable['mac_esc'])) {
                    $cadenaSql .= " mac_esc='" . $variable['mac_esc'] . "',";
                }

                if (!is_null($variable['serial_esc'])) {
                    $cadenaSql .= " serial_esc='" . $variable['serial_esc'] . "',";
                }

                if (!is_null($variable['marca_esc'])) {
                    $cadenaSql .= " marca_esc='" . $variable['marca_esc'] . "',";
                }
                if (!is_null($variable['cant_esc'])) {
                    $cadenaSql .= " cant_esc='" . $variable['cant_esc'] . "',";
                }

                if (!is_null($variable['ip_esc'])) {
                    $cadenaSql .= " ip_esc='" . $variable['ip_esc'] . "',";
                }

                if (!is_null($variable['mac_esc2'])) {
                    $cadenaSql .= " mac2_esc='" . $variable['mac_esc2'] . "',";
                }

                if (!is_null($variable['fecha_instalacion'])) {
                    $cadenaSql .= " fecha_instalacion='" . $variable['fecha_instalacion'] . "',";
                }

                $cadenaSql .= " WHERE id_beneficiario='" . $variable['id_beneficiario'] . "'";
                $cadenaSql .= " AND estado_registro='TRUE';";

                $cadenaSql = str_replace("', W", "' W", $cadenaSql);

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
                $cadenaSql = " SELECT DISTINCT";
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
                $cadenaSql .= " JOIN interoperacion.acta_entrega_servicios AS mp ON mp.id_beneficiario=cn.id_beneficiario  ";
                $cadenaSql .= " FULL JOIN parametros.parametros pr ON cn.tipo_tecnologia=pr.id_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " cn.estado_registro=TRUE AND mp.estado_registro='TRUE' ";
                $cadenaSql .= " AND cn.id_beneficiario IN (" . $variable . ");";

                break;

            case 'ConsultaBeneficiariosActaPortatil':
                $cadenaSql = " SELECT DISTINCT ep.*, ";
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
                $cadenaSql .= " cn.barrio,";
                $cadenaSql .= " bn.municipio as codigo_municipio, ";
                $cadenaSql .= " bn.departamento as codigo_departamento, ";
                $cadenaSql .= " cn.nombres as nombre_contrato,";
                $cadenaSql .= " cn.primer_apellido as primer_apellido_contrato,";
                $cadenaSql .= " cn.segundo_apellido as segundo_apellido_contrato,";
                $cadenaSql .= " cn.tipo_documento as tipo_documento_contrato,";
                $cadenaSql .= " cn.numero_identificacion as numero_identificacion_contrato,";
                $cadenaSql .= " cn.estrato as tipo_beneficiario_contrato, ";
                $cadenaSql .= " cn.estrato_socioeconomico as estrato_socioeconomico_contrato,";
                $cadenaSql .= " cn.urbanizacion as nombre_urbanizacion,";
                $cadenaSql .= " cn.departamento as nombre_departamento,";
                $cadenaSql .= " mn.municipio as nombre_municipio,";
                $cadenaSql .= " cn.barrio as barrio_contrato, cn.numero_identificacion,cn.nombres ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_portatil AS ep";
                $cadenaSql .= " JOIN interoperacion.contrato as cn ON cn.id_beneficiario=ep.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial as bn ON bn.id_beneficiario=ep.id_beneficiario AND bn.estado_registro='TRUE' ";
                $cadenaSql .= " JOIN parametros.municipio as mn ON mn.codigo_mun=bn.municipio";
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

            case 'consultarInformacionEquipoSerial':
                $cadenaSql = " SELECT";
                $cadenaSql .= " camara_tipo ||' '||camara_formato||' '||camara_funcionalidad as camara,";
                $cadenaSql .= " mouse_tipo,";
                $cadenaSql .= " sistema_operativo,";
                $cadenaSql .= " 'Incorporados' as targeta_audio_video,";
                $cadenaSql .= " substr(disco_capacidad,0,4)||' GB velocidad de '||disco_velocidad as disco_duro,";
                $cadenaSql .= " 'Mín. Cuatro horas – 6 celdas' as autonomia,";
                $cadenaSql .= " '('||puerto_usb2_total||')Usb 2.0 y ('||puerto_usb3_total||') Ubs 3.0' as puerto_usb,";
                $cadenaSql .= " alimentacion_voltaje ||' - '||alimentacion_frecuencia as voltaje,";
                $cadenaSql .= " slot_expansion_tipo as targeta_memoria,";
                $cadenaSql .= " 'VGA '||puerto_vga_total ||' y HMDI '||puerto_vga_total as salida_video,";
                $cadenaSql .= " alimentacion_dispositivo||' '||alimentacion_voltaje as cargador, ";
                $cadenaSql .= " 'Recargable '|| bateria_tipo as bateria_tipo,";
                $cadenaSql .= " teclado_idioma||'(Internacional)' as teclado,";
                $cadenaSql .= " marca, ";
                $cadenaSql .= " modelo, ";
                $cadenaSql .= " substr(cpu_version,0,12) ||' '|| cpu_velocidad ||' cores '||(substr(cpu_velocidad,0,5)::float / 1000)||' GHz' as procesador,";
                $cadenaSql .= " cpu_bits||' Bits' as arquitectura,";
                $cadenaSql .= " memoria_tipo||' '||memoria_capacidad as memoria_ram,";
                $cadenaSql .= " 'PAE, NX, y SSE 4.x' as compatibilidad_memoria_ram,";
                $cadenaSql .= " memoria_tipo as tecnologia_memoria_ram,";
                $cadenaSql .= " antivirus,";
                $cadenaSql .= " 'N/A' as disco_anti_impacto,";
                $cadenaSql .= " serial,";
                $cadenaSql .= " parlantes_tipo||' '||audio_tipo as audio,";
                $cadenaSql .= " substr(bateria_autonomia,0,10) as bateria, ";
                $cadenaSql .= " 'Integrada' as targeta_red_alambrica,";
                $cadenaSql .= " 'Integrada' as targeta_red_inalambrica,";
                $cadenaSql .= " substr(pantalla_tipo ,0,20)||substr(pantalla_tipo ,35,50)||substr(pantalla_tamanno ,0,5)as pantalla";
                $cadenaSql .= " FROM interoperacion.politecnica_portatil";
                $cadenaSql .= " WHERE serial='" . $variable . "';";

                break;

        }

        return $cadenaSql;
    }
}
