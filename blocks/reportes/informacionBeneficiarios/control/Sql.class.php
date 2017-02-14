<?php
namespace reportes\informacionBeneficiarios;

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

            case 'finalizarProceso':
                $cadenaSql = " UPDATE parametros.procesos_accesos";
                $cadenaSql .= " SET estado='Finalizado',";
                $cadenaSql .= " porcentaje_estado='100', ";
                $cadenaSql .= " tamanio_archivo='" . $variable['tamanio_archivo'] . "',";
                $cadenaSql .= " nombre_archivo='" . $variable['nombre_archivo'] . "',";
                $cadenaSql .= " ruta_relativa_archivo='" . $variable['rutaUrl'] . "'";
                $cadenaSql .= " WHERE id_proceso='" . $variable['proceso'] . "' ";
                $cadenaSql .= " AND estado_registro='TRUE' ;";
                break;

            case 'actualizarProcesoParticularEstado':
                $cadenaSql = " UPDATE parametros.procesos_accesos";
                $cadenaSql .= " SET estado='En Proceso' ";
                //$cadenaSql .= " porcentaje_estado=?";
                $cadenaSql .= " WHERE id_proceso='" . $variable . "' ";
                $cadenaSql .= " AND estado_registro='TRUE' ;";
                break;

            case 'actualizarProcesoParticularAvance':
                $cadenaSql = " UPDATE parametros.procesos_accesos";
                $cadenaSql .= " SET porcentaje_estado='" . $variable['avance'] . "'";
                $cadenaSql .= " WHERE id_proceso='" . $variable['proceso'] . "' ";
                $cadenaSql .= " AND estado_registro='TRUE' ;";

                break;

            case 'crearProceso':
                $cadenaSql = " INSERT INTO parametros.procesos_accesos(descripcion, parametros, estado, porcentaje_estado)";
                $cadenaSql .= " VALUES ('" . $variable['descripcion'] . "',";
                $cadenaSql .= " '" . $variable['parametros'] . "',";
                $cadenaSql .= " 'No Iniciado',";
                $cadenaSql .= " 0)";
                $cadenaSql .= " RETURNING id_proceso;";

                break;

            case 'consultarEstadoProceso':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM parametros.procesos_accesos";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_proceso='" . $_REQUEST['id_proceso'] . "' ";
                $cadenaSql .= " AND estado IN ('No Iniciado','Finalizado');";
                break;

            case 'eliminarProceso':
                $cadenaSql = " UPDATE parametros.procesos_accesos";
                $cadenaSql .= " SET estado_registro='FALSE'";
                $cadenaSql .= " WHERE id_proceso='" . $_REQUEST['id_proceso'] . "'; ";
                break;

            case 'eliminarProcesoVencido':
                $cadenaSql = " UPDATE parametros.procesos_accesos";
                $cadenaSql .= " SET estado_registro='FALSE'";
                $cadenaSql .= " WHERE nombre_archivo='" . $variable . "'; ";
                break;

            /**
             * Clausulas específicas
             */

            case 'consultarProceso':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM parametros.procesos_accesos";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " ORDER BY id_proceso DESC;";
                break;

            case 'consultarProcesoParticular':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM parametros.procesos_accesos";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND estado='No Iniciado'";
                $cadenaSql .= " ORDER BY id_proceso ASC LIMIT 1;";
                break;

            case 'consultaDocumentosBeneficiarios':
                $cadenaSql = " SELECT nombre_documento, ruta_relativa";
                $cadenaSql .= " FROM interoperacion.documentos_contrato";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "';";
                break;

            case 'consultaGeneralInformacion':
                $cadenaSql = " SELECT DISTINCT cn.departamento,cn.municipio,cn.urbanizacion ";
                $cadenaSql .= " FROM interoperacion.contrato AS cn ";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial AS bn ON bn.id_beneficiario =cn.id_beneficiario";
                $cadenaSql .= " JOIN parametros.proyectos_metas AS pm ON pm.id_proyecto =bn.id_proyecto";
                $cadenaSql .= " JOIN parametros.parametros AS pmr ON pmr.id_parametro =cn.tipo_tecnologia";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_servicios AS aes ON aes.id_beneficiario=cn.id_beneficiario AND aes.estado_registro='TRUE'";

                if (isset($_REQUEST['estado_beneficiario']) && $_REQUEST['estado_beneficiario'] == '1') {
                    $cadenaSql .= " JOIN interoperacion.documentos_contrato dr  ON dr.id_beneficiario=cn.id_beneficiario AND dr.estado_registro='TRUE' AND dr.tipologia_documento='132' ";
                }

                $cadenaSql .= " WHERE cn.estado_registro='TRUE' ";

                if (isset($_REQUEST['estado_beneficiario']) && $_REQUEST['estado_beneficiario'] == '1') {
                    $cadenaSql .= " AND cn.nombre_documento_contrato IS NOT NULL ";

                }

                if (isset($_REQUEST['estado_beneficiario']) && $_REQUEST['estado_beneficiario'] == '3') {
                    $cadenaSql .= " AND bn.estado_beneficiario='APROBADO INTERVENTORIA' ";

                }

                if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                    $cadenaSql .= " AND cn.municipio='" . $_REQUEST['municipio'] . "'";
                }
                if (isset($_REQUEST['departamento']) && $_REQUEST['departamento'] != '') {

                    $cadenaSql .= " AND cn.departamento='" . $_REQUEST['departamento'] . "'";
                }

                if (isset($_REQUEST['urbanizacion']) && $_REQUEST['urbanizacion'] != '') {
                    $cadenaSql .= " AND cn.urbanizacion='" . $_REQUEST['urbanizacion'] . "'";
                }

                if (isset($_REQUEST['beneficiario']) && $_REQUEST['beneficiario'] != '') {

                    $cadenaSql .= " AND cn.numero_identificacion IN(";

                    $beneficiarios = explode(";", $_REQUEST['beneficiario']);

                    foreach ($beneficiarios as $key => $value) {
                        if ($value == '') {
                            unset($beneficiarios[$key]);
                        }

                    }
                    if (count($beneficiarios) == 1) {

                        $cadenaSql .= "'" . $beneficiarios[0] . "') ";
                    } else {
                        foreach ($beneficiarios as $key => $value) {
                            $cadenaSql .= "'" . $value . "',";
                        }

                        $cadenaSql .= ") ";

                    }
                }

                $cadenaSql .= " AND cn.departamento IS NOT NULL ";
                $cadenaSql .= " AND cn.municipio IS NOT NULL ";
                $cadenaSql .= " AND cn.urbanizacion IS NOT NULL ";

                $cadenaSql = str_replace("',)", "')", $cadenaSql);

                break;

            case 'consultaInformacionBeneficiario':
                $cadenaSql = " SELECT DISTINCT ";
                $cadenaSql .= " cn.* , pm.meta,pmr.descripcion as descripcion_tipo_tegnologia, ";
                $cadenaSql .= " aes.resultado_vs as velocidad_subida, aes.resultado_vb as velocidad_bajada,";
                $cadenaSql .= " ip_olt,mac_olt, nd.port_olt,nombre_olt, puerto_olt,";     //Cabecera
                $cadenaSql .= " ip_celda,mac_celda,nombre_nodo,nombre_sectorial,ip_switch_celda,ip_sm_celda,";     //Nodo
                $cadenaSql .= " mac_sm_celda,mac_cpe_celda,";     //Nodo
                $cadenaSql .= " mac_master_eoc,ip_master_eoc,ip_onu_eoc,mac_onu_eoc,ip_hub_eoc,mac_hub_eoc,mac_cpe_eoc,";     //Nodo HCF
                $cadenaSql .= " aes.fecha_instalacion,aes.ip_esc,aes.mac_esc, aes.resultado_p1,aes.resultado_tr1, ";     //Nodo HCF
                $cadenaSql .= " aes.resultado_tr2, aes.reporte_fallos, aes.acceso_reportando ,aes.fecha_comisionamiento, ";
                $cadenaSql .= " CASE WHEN aes.id=NULL  THEN ''  ELSE 'www.mintic.gov.co;https://www.sivirtual.gov.co;https://www.wikipedia.org/'  END  AS paginas_visitadas";
                $cadenaSql .= " FROM interoperacion.contrato AS cn ";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial AS bn ON bn.id_beneficiario =cn.id_beneficiario AND bn.estado_registro='TRUE'";
                $cadenaSql .= " JOIN parametros.proyectos_metas AS pm ON pm.id_proyecto =bn.id_proyecto";
                $cadenaSql .= " JOIN parametros.parametros AS pmr ON pmr.id_parametro =cn.tipo_tecnologia";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_servicios AS aes ON aes.id_beneficiario=cn.id_beneficiario AND aes.estado_registro='TRUE'";

                $cadenaSql .= " LEFT JOIN interoperacion.nodo AS nd ON nd.macesclavo1=aes.mac_esc AND nd.estado_registro='TRUE'";

                $cadenaSql .= " LEFT JOIN interoperacion.cabecera AS cab ON cab.codigo_cabecera=nd.codigo_cabecera AND cab.estado_registro='TRUE'";

                if (isset($_REQUEST['estado_beneficiario']) && $_REQUEST['estado_beneficiario'] == '1') {
                    $cadenaSql .= " JOIN interoperacion.documentos_contrato dr  ON dr.id_beneficiario=cn.id_beneficiario AND dr.estado_registro='TRUE' AND dr.tipologia_documento='132' ";
                }

                $cadenaSql .= " WHERE cn.estado_registro='TRUE' ";

                if (isset($_REQUEST['estado_beneficiario']) && $_REQUEST['estado_beneficiario'] == '1') {
                    $cadenaSql .= " AND cn.nombre_documento_contrato IS NOT NULL ";

                }

                if (isset($_REQUEST['estado_beneficiario']) && $_REQUEST['estado_beneficiario'] == '3') {
                    $cadenaSql .= " AND bn.estado_beneficiario='APROBADO INTERVENTORIA' ";

                }

                if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                    $cadenaSql .= " AND cn.municipio='" . $_REQUEST['municipio'] . "'";
                }
                if (isset($_REQUEST['departamento']) && $_REQUEST['departamento'] != '') {

                    $cadenaSql .= " AND cn.departamento='" . $_REQUEST['departamento'] . "'";
                }

                if (isset($_REQUEST['urbanizacion']) && $_REQUEST['urbanizacion'] != '') {
                    $cadenaSql .= " AND cn.urbanizacion='" . $_REQUEST['urbanizacion'] . "'";
                }

                if (isset($_REQUEST['beneficiario']) && $_REQUEST['beneficiario'] != '') {

                    $cadenaSql .= " AND cn.numero_identificacion IN(";

                    $beneficiarios = explode(";", $_REQUEST['beneficiario']);

                    foreach ($beneficiarios as $key => $value) {
                        if ($value == '') {
                            unset($beneficiarios[$key]);
                        }

                    }
                    if (count($beneficiarios) == 1) {

                        $cadenaSql .= "'" . $beneficiarios[0] . "') ";
                    } else {
                        foreach ($beneficiarios as $key => $value) {
                            $cadenaSql .= "'" . $value . "',";
                        }

                        $cadenaSql .= ") ";

                    }
                }

                $cadenaSql .= " AND cn.departamento IS NOT NULL ";
                $cadenaSql .= " AND cn.municipio IS NOT NULL ";
                $cadenaSql .= " AND cn.urbanizacion IS NOT NULL ";
                $cadenaSql .= "ORDER BY cn . numero_contrato;";

                $cadenaSql = str_replace("',)", "')", $cadenaSql);

                break;

            /**
             * Clausulas específicas
             */
            case 'consultarDepartamento':

                $cadenaSql = " SELECT DISTINCT departamento as valor, departamento";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " AND departamento IS NOT NULL;";
                break;

            case 'consultarMunicipio':

                $cadenaSql = " SELECT DISTINCT municipio as valor, municipio ";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " AND municipio IS NOT NULL;";

                break;

            case 'consultarUrbanizacion':

                $cadenaSql = " SELECT DISTINCT urbanizacion as valor, urbanizacion";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " AND urbanizacion IS NOT NULL;";

                break;

            case 'consultarBeneficiariosPotenciales':
                $cadenaSql = " SELECT value,  data ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "(SELECT DISTINCT cn.numero_identificacion /*||' - ('||cn.nombres||' '||cn.primer_apellido||' '||(CASE WHEN cn.segundo_apellido IS NULL THEN '' ELSE cn.segundo_apellido END)||')'*/ AS value, bp.id_beneficiario AS data ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp ";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=bp.id_beneficiario ";
                $cadenaSql .= " WHERE bp.estado_registro=TRUE ";
                $cadenaSql .= " AND cn.estado_registro=TRUE ";
                $cadenaSql .= "     ) datos ";
                $cadenaSql .= "WHERE value ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

            // Consultas Particulares

            case 'consultaCantidadMujeresHogar':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND genero_familiar='1';";
                break;

            case 'consultaCantidadMasculinoHogar':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND genero_familiar='2';";
                break;

            case 'consultaCantidadMenores18':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND edad_familiar::int < 18 ;";
                break;

            case 'consultaCantidad18y25':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND edad_familiar::int >= 18 ";
                $cadenaSql .= " AND edad_familiar::int <= 25 ;";
                break;

            case 'consultaCantidad26y30':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND edad_familiar::int >= 26 ";
                $cadenaSql .= " AND edad_familiar::int <= 30 ;";
                break;

            case 'consultaCantidad31y40':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND edad_familiar::int >= 31 ";
                $cadenaSql .= " AND edad_familiar::int <= 40 ;";
                break;

            case 'consultaCantidad41y65':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND edad_familiar::int >= 41 ";
                $cadenaSql .= " AND edad_familiar::int <= 65 ;";
                break;

            case 'consultaCantidadMayor65':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND edad_familiar::int > 65 ;";
                break;

            case 'consultaCantidadEmpleado':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='1';";
                break;

            case 'consultaCantidadTrabajoInformal':    // Corregir
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='1';";
                break;

            case 'consultaCantidadEstudiante':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='4';";
                break;

            case 'consultaCantidadTrabajoIndependiente':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='13';";
                break;

            case 'consultaCantidadHogarDomestico':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='20';";
                break;

            case 'consultaCantidadHogarDomesticoCasa':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='14';";
                break;

            case 'consultaCantidadNoTrabaja':    //Corregir
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='14';";
                break;

            case 'consultaCantidadOtro':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='30';";
                break;

            case 'verificarDocumentos':
                $cadenaSql = " SELECT count(*)";
                $cadenaSql .= " FROM";
                $cadenaSql .= " (";
                $cadenaSql .= " SELECT DISTINCT tipologia_documento";
                $cadenaSql .= " FROM interoperacion.documentos_requisitos dr";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " UNION";
                $cadenaSql .= " SELECT (CASE WHEN nombre_documento_contrato IS NULL THEN '0' ELSE '128' END)::int AS tipologia_documento";
                $cadenaSql .= " FROM interoperacion.contrato ";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND (CASE WHEN nombre_documento_contrato IS NULL THEN '0' ELSE '128' END)::int <> 0";
                $cadenaSql .= " AND supervisor='TRUE'";
                $cadenaSql .= " ) as requisitos";
                $cadenaSql .= " JOIN interoperacion.documentos_contrato dc ON dc.tipologia_documento=requisitos.tipologia_documento ";
                $cadenaSql .= " AND dc.estado_registro='TRUE'";
                $cadenaSql .= " AND dc.id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND dc.supervisor='TRUE'";
                break;
        }

        return $cadenaSql;
    }
}
