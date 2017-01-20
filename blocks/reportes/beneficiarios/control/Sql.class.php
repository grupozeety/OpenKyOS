<?php
namespace reportes\beneficiarios;
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
                $cadenaSql .= " AND estado='No Iniciado' ";
                $cadenaSql .= " OR estado='Finalizado'; ";
                break;

            case 'eliminarProceso':
                $cadenaSql = " UPDATE parametros.procesos_accesos";
                $cadenaSql .= " SET estado_registro='FALSE'";
                $cadenaSql .= " WHERE id_proceso='" . $_REQUEST['id_proceso'] . "'; ";
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

            case 'consultaInformacionBeneficiario':
                $cadenaSql = " SELECT DISTINCT cn.numero_contrato,";
                $cadenaSql .= " bn.consecutivo,";
                $cadenaSql .= " bn.id_beneficiario,";
                $cadenaSql .= " tb.descripcion tipo_beneficiario,";
                $cadenaSql .= " td.descripcion tipo_documento, ";
                $cadenaSql .= " bn.identificacion,";
                $cadenaSql .= " bn.nombre,";
                $cadenaSql .= " bn.primer_apellido,";
                $cadenaSql .= " bn.segundo_apellido, ";
                $cadenaSql .= " gn.descripcion genero,";
                $cadenaSql .= " CASE WHEN bn.edad=0 THEN '' ELSE bn.edad::char END edad, ";
                $cadenaSql .= " ne.descripcion nivel_estudio, ";
                $cadenaSql .= " bn.correo, ";
                $cadenaSql .= " bn.direccion, ";
                $cadenaSql .= " bn.tipo_vivienda, ";
                $cadenaSql .= " bn.manzana, ";
                $cadenaSql .= " bn.bloque, ";
                $cadenaSql .= " bn.torre, ";
                $cadenaSql .= " bn.apartamento, ";
                $cadenaSql .= " bn.telefono, ";
                $cadenaSql .= " dp.departamento, ";
                $cadenaSql .= " mn.municipio, ";
                $cadenaSql .= " bn.id_proyecto, ";
                $cadenaSql .= " bn.proyecto, ";
                $cadenaSql .= " bn.estrato, ";
                $cadenaSql .= " CASE WHEN bn.minvi=TRUE THEN 'SI' ELSE 'NO' END minvi,";
                $cadenaSql .= " bn.lote,";
                $cadenaSql .= " bn.interior,";
                $cadenaSql .= " bn.piso,";
                $cadenaSql .= " bn.barrio ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial AS bn";
                $cadenaSql .= " JOIN parametros.departamento AS dp ON dp.codigo_dep = bn.departamento";
                $cadenaSql .= " JOIN parametros.municipio AS mn ON mn.codigo_mun = bn.municipio";
                $cadenaSql .= " JOIN parametros.parametros AS tb ON tb.codigo::int = bn.tipo_beneficiario AND tb.rel_parametro='1' AND tb.estado_registro='TRUE' ";
                $cadenaSql .= " JOIN parametros.parametros AS td ON td.codigo::int = bn.tipo_documento AND td.rel_parametro='11' AND td.estado_registro='TRUE' ";
                $cadenaSql .= " LEFT JOIN parametros.parametros AS gn ON gn.codigo::int = bn.genero AND gn.rel_parametro='2' AND gn.estado_registro='TRUE' ";
                $cadenaSql .= " LEFT JOIN parametros.parametros AS ne ON ne.codigo::int = bn.nivel_estudio AND ne.rel_parametro='3' AND ne.estado_registro='TRUE' ";

                if (isset($_REQUEST['estado_contrato']) && $_REQUEST['estado_contrato'] == '1') {

                    $cadenaSql .= " JOIN interoperacion.contrato AS cn ON cn.id_beneficiario = bn.id_beneficiario AND cn.estado_registro='TRUE' AND cn.id_beneficiario IS NOT NULL ";
                } else {
                    $cadenaSql .= "LEFT  JOIN interoperacion.contrato AS cn ON cn.id_beneficiario = bn.id_beneficiario AND cn.estado_registro='TRUE' ";

                }
                $cadenaSql .= " WHERE bn.estado_registro='TRUE'";

                if (isset($_REQUEST['estado_contrato']) && $_REQUEST['estado_contrato'] == '0') {

                    $cadenaSql .= " AND cn.id_beneficiario IS  NULL ";

                }

                if (isset($_REQUEST['estado_beneficiario']) && $_REQUEST['estado_beneficiario'] == '1') {
                    $cadenaSql .= " AND cn.nombre_documento_contrato IS NOT NULL ";

                }

                if (isset($_REQUEST['estado_beneficiario']) && $_REQUEST['estado_beneficiario'] == '3') {
                    $cadenaSql .= " AND bn.estado_beneficiario='APROBADO INTERVENTORIA' ";

                }
                if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                    $cadenaSql .= " AND bn.municipio='" . $_REQUEST['municipio'] . "'";
                }
                if (isset($_REQUEST['departamento']) && $_REQUEST['departamento'] != '') {

                    $cadenaSql .= " AND bn.departamento='" . $_REQUEST['departamento'] . "'";
                }

                if (isset($_REQUEST['urbanizacion']) && $_REQUEST['urbanizacion'] != '') {
                    $cadenaSql .= " AND bn.id_proyecto='" . $_REQUEST['urbanizacion'] . "'";
                }

                if (isset($_REQUEST['beneficiario']) && $_REQUEST['beneficiario'] != '') {

                    $cadenaSql .= " AND bn.identificacion IN(";

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

                $cadenaSql .= " AND bn.departamento IS NOT NULL ";
                $cadenaSql .= " AND bn.municipio IS NOT NULL ";
                $cadenaSql .= " AND bn.id_proyecto IS NOT NULL ";
                $cadenaSql .= "ORDER BY bn.consecutivo;";

                $cadenaSql = str_replace("',)", "')", $cadenaSql);

                break;

            /**
             * Clausulas específicas
             */
            case 'consultarDepartamento':

                $cadenaSql = " SELECT DISTINCT bn.departamento as valor,dp.departamento";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bn ";
                $cadenaSql .= " JOIN parametros.departamento AS dp ON dp.codigo_dep = bn.departamento";
                $cadenaSql .= " WHERE bn.estado_registro=TRUE";
                $cadenaSql .= " AND bn.departamento IS NOT NULL;";
                break;

            case 'consultarMunicipio':

                $cadenaSql = " SELECT DISTINCT bn.municipio as valor,mn.municipio ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bn";
                $cadenaSql .= " JOIN parametros.municipio AS mn ON mn.codigo_mun = bn.municipio";
                $cadenaSql .= " WHERE bn.estado_registro=TRUE";
                $cadenaSql .= " AND  bn.municipio IS NOT NULL;";

                break;

            case 'consultarUrbanizacion':

                $cadenaSql = " SELECT DISTINCT id_proyecto as valor, proyecto";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " AND proyecto IS NOT NULL ";
                $cadenaSql .= " AND id_proyecto IS NOT NULL;";

                break;

            case 'consultarBeneficiariosPotenciales':
                $cadenaSql = " SELECT value,  data ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "(SELECT DISTINCT bn.identificacion AS value, bn.identificacion AS data ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bn ";
                $cadenaSql .= " LEFT JOIN interoperacion.contrato cn ON cn.id_beneficiario=bn.id_beneficiario AND cn.estado_registro=TRUE ";
                $cadenaSql .= " WHERE bn.estado_registro=TRUE ";
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
?>

