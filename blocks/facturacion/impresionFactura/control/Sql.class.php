<?php

namespace facturacion\impresionFactura;

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
        $this->miSesionSso = \SesionSso::singleton();
    }
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
                 * Clausulas específicas|
                 */

            case 'consultarInformacionApi':
                $cadenaSql = " SELECT componente, host, usuario, password, token_codificado, ruta_cookie ";
                $cadenaSql .= " FROM parametros.api_data";
                $cadenaSql .= " WHERE componente ='" . $variable . "';";
                break;

            case 'consultarBeneficiario':
                $cadenaSql = " SELECT";
                $cadenaSql .= " cn.nombres||' '||cn.primer_apellido||' '||(CASE WHEN cn.segundo_apellido IS NOT NULL THEN cn.segundo_apellido ELSE '' END) as nombre_beneficiario,";
                $cadenaSql .= " cn.numero_identificacion,";
                $cadenaSql .= " cn.direccion_domicilio||";
                $cadenaSql .= " (CASE WHEN cn.manzana <> '0' THEN ' Manzana # '||cn.manzana ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.bloque <> '0' THEN ' Bloque # '||cn.bloque ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.torre <> '0' THEN ' Torre # '||cn.manzana ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.casa_apartamento <>'0' THEN ' Casa/Apartamento # '||cn.casa_apartamento ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.interior <>'0' THEN ' Interior # '||cn.interior ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.lote <>'0' THEN ' Lote # '||cn.lote ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.piso <>'0' THEN ' Piso # '||cn.piso ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.barrio IS NOT NULL THEN ' Barrio '||cn.barrio ELSE '' END)as direccion_beneficiario,";
                $cadenaSql .= " cn.municipio,";
                $cadenaSql .= " cn.departamento,";
                $cadenaSql .= " upper(trim(replace(replace(urb.urbanizacion, 'URBANIZACIÓN', ''), 'URBANIZACION', ''))) as urbanizacion,";
                $cadenaSql .= " (CASE WHEN cn.estrato_socioeconomico::text IS NULL THEN 'No Caracterizado' ELSE cn.estrato_socioeconomico::text END) as estrato,";
                $cadenaSql .= " pb.id_beneficiario,(CASE WHEN  cn.celular='0' THEN NULL ELSE cn.celular END) as telefono ";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial pb ON pb.id_beneficiario=cn.id_beneficiario AND pb.estado_registro='TRUE'";

                $cadenaSql .= " JOIN parametros.urbanizacion urb ON urb.id_urbanizacion=pb.id_proyecto ";
                $cadenaSql .= " WHERE cn.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.id_beneficiario='" . $variable . "';";

                break;

            case 'consultaInformacionFacturacion':
                $cadenaSql = " SELECT fc.id_factura, ";
                $cadenaSql .= " cn.numero_contrato, ";
                $cadenaSql .= " to_date(aes.fecha_instalacion, 'DD-MM-YYYY') as fecha_venta,";
                $cadenaSql .= " fc.estado_factura,";
                $cadenaSql .= " to_char(fc.fecha_registro, 'YYYY-MM-DD')as fecha_factura,";
                $cadenaSql .= " fc.total_factura,";
                $cadenaSql .= " fc.id_ciclo,";
                $cadenaSql .= " pb.municipio,";
                $cadenaSql .= " pb.departamento,";
                $cadenaSql .= " pb.id_beneficiario, ";
                $cadenaSql .= " pb.correo_institucional, ";
                $cadenaSql .= " pb.correo,cn.numero_identificacion,fc.fecha_pago_oportuno, ";
                $cadenaSql .= " numeracion_facturacion,indice_facturacion,";
                $cadenaSql .= " tb.descripcion as tipo_beneficiario,";
                $cadenaSql .= " (cn.valor_tarificacion * 15) as valor_contrato";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial pb ON pb.id_beneficiario=cn.id_beneficiario AND pb.estado_registro='TRUE'";
                $cadenaSql .= " JOIN interoperacion.acta_entrega_servicios aes ON aes.id_beneficiario=cn.id_beneficiario AND aes.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.factura fc ON fc.id_beneficiario=cn.id_beneficiario AND fc.estado_registro='TRUE'";
                $cadenaSql .= " JOIN parametros.parametros tb ON tb.codigo::int=pb.tipo_beneficiario AND tb.estado_registro='TRUE' AND tb.rel_parametro='1' ";
                $cadenaSql .= " WHERE cn.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.id_beneficiario='" . $variable . "' ";
                $cadenaSql .= " ORDER BY fc.fecha_registro DESC";
                $cadenaSql .= " LIMIT 1;";
                break;

            case 'consultaInformacionFacturacionAnterior':
                $cadenaSql = " SELECT fc.id_factura, ";
                $cadenaSql .= " cn.numero_contrato, ";
                $cadenaSql .= " to_date(aes.fecha_instalacion, 'DD-MM-YYYY') as fecha_venta,";
                $cadenaSql .= " fc.estado_factura,";
                $cadenaSql .= " to_char(fc.fecha_registro, 'YYYY-MM-DD')as fecha_factura,";
                $cadenaSql .= " fc.total_factura,";
                $cadenaSql .= " fc.id_ciclo,";
                $cadenaSql .= " pb.municipio,";
                $cadenaSql .= " pb.departamento,";
                $cadenaSql .= " pb.id_beneficiario, ";
                $cadenaSql .= " pb.correo_institucional, ";
                $cadenaSql .= " pb.correo,cn.numero_identificacion,fc.fecha_pago_oportuno, ";
                $cadenaSql .= " numeracion_facturacion,indice_facturacion,";
                $cadenaSql .= " tb.descripcion as tipo_beneficiario,";
                $cadenaSql .= " (cn.valor_tarificacion * 15) as valor_contrato";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial pb ON pb.id_beneficiario=cn.id_beneficiario AND pb.estado_registro='TRUE'";
                $cadenaSql .= " JOIN interoperacion.acta_entrega_servicios aes ON aes.id_beneficiario=cn.id_beneficiario AND aes.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.factura fc ON fc.id_beneficiario=cn.id_beneficiario AND fc.estado_registro='TRUE'";
                $cadenaSql .= " JOIN parametros.parametros tb ON tb.codigo::int=pb.tipo_beneficiario AND tb.estado_registro='TRUE' AND tb.rel_parametro='1' ";
                $cadenaSql .= " WHERE cn.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.id_beneficiario='" . $variable . "' ";
                $cadenaSql .= " AND fc.estado_factura IN ('Mora','Pagada') ";
                $cadenaSql .= " ORDER BY fc.fecha_registro DESC";
                $cadenaSql .= " LIMIT 1;";
                break;

            case 'consultarFacturaMora':
                $cadenaSql = " SELECT fc.* ";
                $cadenaSql .= " FROM facturacion.factura fc ";
                $cadenaSql .= " WHERE fc.estado_registro='TRUE'";
                $cadenaSql .= " AND fc.id_factura='" . $variable . "' ;";
                break;

            case 'consultaValorPagado':
                $cadenaSql = " SELECT SUM(pg.valor_pagado + pg.abono_adicional) as valor_pagado";
                $cadenaSql .= " FROM facturacion.pago_factura pg";
                $cadenaSql .= " JOIN facturacion.factura fc ON fc.id_factura=pg.id_factura";
                $cadenaSql .= " WHERE pg.estado_registro='TRUE'";
                $cadenaSql .= " AND fc.estado_registro='TRUE'";
                $cadenaSql .= " AND fc.estado_factura='Pagada'";
                $cadenaSql .= " AND fc.id_beneficiario='" . $variable . "';";
                break;

            case 'consultaUltimoValorPagado':
                $cadenaSql = " SELECT pg.id_pago,(pg.valor_pagado + pg.abono_adicional) as ultimo_valor_pagado";
                $cadenaSql .= " FROM facturacion.pago_factura pg";
                $cadenaSql .= " JOIN facturacion.factura fc ON fc.id_factura=pg.id_factura";
                $cadenaSql .= " WHERE pg.estado_registro='TRUE'";
                $cadenaSql .= " AND fc.estado_registro='TRUE'";
                $cadenaSql .= " AND fc.estado_factura='Pagada'";
                $cadenaSql .= " AND fc.id_beneficiario='" . $variable . "'";
                $cadenaSql .= " ORDER BY pg.id_pago DESC ";
                $cadenaSql .= " LIMIT 1;";

                break;

            case 'consultarValorPagado':
                $cadenaSql = " SELECT fc.* ";
                $cadenaSql .= " FROM facturacion.factura fc ";
                $cadenaSql .= " WHERE fc.estado_registro='TRUE'";
                $cadenaSql .= " AND fc.id_factura='" . $variable . "' ;";
                break;

            case 'consultaValoresConceptos':

                $cadenaSql = " SELECT * FROM ";
                $cadenaSql .= " (select conceptos.id_factura factura_imprimir, conceptos.observacion as factura_mora, ";
                $cadenaSql .= " conceptos.id_usuario_rol_periodo, factura.id_ciclo,to_char( inicio_periodo ,'YYYY-MM-DD')as inicio_periodo,to_char( fin_periodo, 'YYYY-MM-DD') as fin_periodo, conceptos.valor_calculado as valor_concepto,rl.descripcion as concepto ";
                $cadenaSql .= " from facturacion.conceptos";
                $cadenaSql .= " left join facturacion.factura on conceptos.observacion=cast(factura.id_factura as character varying)";
                $cadenaSql .= " left JOIN facturacion.conceptos as conceptos2 on conceptos2.id_factura=factura.id_factura";
                $cadenaSql .= " left JOIN facturacion.usuario_rol_periodo urp on urp.id_usuario_rol_periodo=conceptos2.id_usuario_rol_periodo and conceptos2.estado_registro=TRUE ";
                $cadenaSql .= "JOIN facturacion.regla rl ON rl.id_regla=conceptos.id_regla AND rl.estado_registro='TRUE' ";
                $cadenaSql .= " WHERE conceptos.id_factura='" . $variable['id_factura'] . "'";
                $cadenaSql .= " and conceptos.observacion!=''";
                $cadenaSql .= " union";
                $cadenaSql .= " select conceptos.id_factura, conceptos.observacion, ";
                $cadenaSql .= " conceptos.id_usuario_rol_periodo, factura.id_ciclo, to_char( inicio_periodo ,'YYYY-MM-DD')as inicio_periodo,to_char( fin_periodo, 'YYYY-MM-DD') as fin_periodo, conceptos.valor_calculado as valor_concepto,rl.descripcion as concepto ";
                $cadenaSql .= " from facturacion.conceptos";
                $cadenaSql .= " left join facturacion.factura on conceptos.id_factura=factura.id_factura ";
                $cadenaSql .= " left JOIN facturacion.usuario_rol_periodo urp on urp.id_usuario_rol_periodo=conceptos.id_usuario_rol_periodo and conceptos.estado_registro=TRUE ";
                $cadenaSql .= "JOIN facturacion.regla rl ON rl.id_regla=conceptos.id_regla AND rl.estado_registro='TRUE' ";
                $cadenaSql .= " WHERE conceptos.id_factura='" . $variable['id_factura'] . "'";
                $cadenaSql .= " AND conceptos.observacion='') as consulta ORDER BY consulta.id_ciclo DESC;";

                /**$cadenaSql = " SELECT ";
                $cadenaSql .= " fc.id_factura,";
                $cadenaSql .= " cp.valor_calculado as valor_concepto,";
                $cadenaSql .= " rl.descripcion as concepto,";
                $cadenaSql .= "to_char(urp.inicio_periodo, 'YYYY-MM-DD')as inicio_periodo,";
                $cadenaSql .= "to_char(urp.fin_periodo, 'YYYY-MM-DD') as fin_periodo,";
                $cadenaSql .= "cp.observacion";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial pb ON pb.id_beneficiario=cn.id_beneficiario AND pb.estado_registro='TRUE'";
                $cadenaSql .= " JOIN interoperacion.acta_entrega_servicios aes ON aes.id_beneficiario=cn.id_beneficiario AND aes.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.factura fc ON fc.id_beneficiario=cn.id_beneficiario AND fc.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.conceptos cp ON cp.id_factura=fc.id_factura AND cp.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.regla rl ON rl.id_regla=cp.id_regla AND rl.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.usuario_rol_periodo urp ON urp.id_usuario_rol_periodo=cp.id_usuario_rol_periodo AND urp.estado_registro='TRUE'";
                $cadenaSql .= " WHERE cn.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.id_beneficiario='" . $variable['id_beneficiario'] . "' ";
                $cadenaSql .= " AND fc.id_factura='" . $variable['id_factura'] . "';";**/
                break;

            case 'consultaValoresConceptosMora':
                $cadenaSql = " SELECT ";
                $cadenaSql .= " fc.id_factura,";
                $cadenaSql .= " cp.valor_calculado as valor_concepto,";
                $cadenaSql .= " rl.descripcion as concepto,";
                $cadenaSql .= "to_char(urp.inicio_periodo, 'YYYY-MM-DD')as inicio_periodo,";
                $cadenaSql .= "to_char(urp.fin_periodo, 'YYYY-MM-DD') as fin_periodo,";
                $cadenaSql .= "cp.observacion";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial pb ON pb.id_beneficiario=cn.id_beneficiario AND pb.estado_registro='TRUE'";
                $cadenaSql .= " JOIN interoperacion.acta_entrega_servicios aes ON aes.id_beneficiario=cn.id_beneficiario AND aes.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.factura fc ON fc.id_beneficiario=cn.id_beneficiario AND fc.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.conceptos cp ON cp.id_factura=fc.id_factura AND cp.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.regla rl ON rl.id_regla=cp.id_regla AND rl.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.usuario_rol_periodo urp ON urp.id_usuario_rol_periodo=cp.id_usuario_rol_periodo AND urp.estado_registro='TRUE'";
                $cadenaSql .= " WHERE cn.estado_registro='TRUE'";
                $cadenaSql .= " AND fc.id_factura='" . $variable['id_factura'] . "';";
                break;

            case 'consultarDepartamento':

                $cadenaSql = " SELECT DISTINCT departamento as valor, departamento";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " AND departamento IS NOT NULL";
                $cadenaSql .= " AND departamento <> ''; ";
                break;

            case 'consultarMunicipio':

                $cadenaSql = " SELECT DISTINCT municipio as valor, municipio ";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " AND municipio IS NOT NULL ";
                $cadenaSql .= " AND municipio <> ''; ";

                break;

            case 'consultarUrbanizacion':

                $cadenaSql = " SELECT DISTINCT urbanizacion as valor, urbanizacion";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " AND urbanizacion IS NOT NULL";
                $cadenaSql .= " AND urbanizacion <> '' ";
                $cadenaSql .= " AND urbanizacion <> 'Seleccione .....' ;";

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

            case 'consultarProceso':
                $cadenaSql = " SELECT * ";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE descripcion='Facturas'";
                $cadenaSql .= " AND  estado_registro='TRUE' ";
                $cadenaSql .= " ORDER BY id_proceso DESC;";
                break;

            case 'consultaGeneralInformacion':
                $cadenaSql = " SELECT DISTINCT cn.id_beneficiario,bn.departamento ";
                $cadenaSql .= " FROM interoperacion.contrato AS cn ";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial AS bn ON bn.id_beneficiario =cn.id_beneficiario";
                $cadenaSql .= " JOIN parametros.proyectos_metas AS pm ON pm.id_proyecto =bn.id_proyecto";
                $cadenaSql .= " JOIN parametros.parametros AS pmr ON pmr.id_parametro =cn.tipo_tecnologia";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_servicios AS aes ON aes.id_beneficiario=cn.id_beneficiario AND aes.estado_registro='TRUE'";

                $cadenaSql .= " WHERE cn.estado_registro='TRUE' ";

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

            case 'consultaGeneralInformacionUrbanizaciones':

                $cadenaSql = " SELECT DISTINCT cn.urbanizacion ";
                $cadenaSql .= " FROM interoperacion.contrato AS cn ";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial AS bn ON bn.id_beneficiario =cn.id_beneficiario";
                $cadenaSql .= " JOIN parametros.proyectos_metas AS pm ON pm.id_proyecto =bn.id_proyecto";
                $cadenaSql .= " JOIN parametros.parametros AS pmr ON pmr.id_parametro =cn.tipo_tecnologia";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_servicios AS aes ON aes.id_beneficiario=cn.id_beneficiario AND aes.estado_registro='TRUE'";

                $cadenaSql .= " WHERE cn.estado_registro='TRUE' ";

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
                $cadenaSql .= " AND cn.urbanizacion IS NOT NULL LIMIT 40 ";

                $cadenaSql = str_replace("',)", "')", $cadenaSql);

                break;

            case 'registrarProceso':
                $cadenaSql = " INSERT INTO parametros.procesos_masivos(";
                $cadenaSql .= " descripcion,";
                $cadenaSql .= " estado,nombre_archivo,";
                $cadenaSql .= " parametro_inicio,";
                $cadenaSql .= " parametro_fin,datos_adicionales,urbanizaciones )";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= " 'Facturas',";
                $cadenaSql .= " 'No Iniciado','NOMBRE POR DEFECTO',";
                $cadenaSql .= " '" . $variable['inicio'] . "',";
                $cadenaSql .= " '" . $variable['final'] . "',";
                $cadenaSql .= " '" . $variable['datos_adicionales'] . "',";
                $cadenaSql .= " '" . $variable['urbanizaciones'] . "'";
                $cadenaSql .= " )RETURNING id_proceso;";
                break;

            case 'consultarProcesoParticular':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE id_proceso=(";
                $cadenaSql .= " SELECT MIN(id_proceso) ";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE estado_registro='TRUE' ";
                $cadenaSql .= " AND estado='No Iniciado'";
                $cadenaSql .= " AND descripcion='Facturas'";
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

            case 'actualizarFacturaBeneficiario':
                $cadenaSql = " UPDATE facturacion.factura ";
                $cadenaSql .= " SET estado_factura='Aprobado',";
                $cadenaSql .= "  fecha_pago_oportuno='" . $variable['fecha_oportuna_pago'] . "',";
                $cadenaSql .= "  indice_facturacion='" . $variable['indice_facturacion'] . "',";
                $cadenaSql .= "  numeracion_facturacion='" . $variable['numeracion_facturacion'] . "',";
                $cadenaSql .= "  codigo_barras='" . $variable['codigo_barras'] . "' ,";
                $cadenaSql .= "  factura_erpnext ='" . $variable['factura_erp'] . "' ";
                $cadenaSql .= " WHERE id_factura='" . $variable['id_factura'] . "';";
                break;

            case 'consultarNumeracionFactura':
                $cadenaSql = " SELECT max(numeracion_facturacion) as numeracion";
                $cadenaSql .= " FROM facturacion.factura";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND estado_factura IN ('Aprobado','Mora')";
                $cadenaSql .= " AND indice_facturacion='" . $variable . "';";
                break;

            case 'consultarNumeracionFacturaActual':
                $cadenaSql = " SELECT max(numeracion_facturacion) as numeracion";
                $cadenaSql .= " FROM facturacion.factura";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND estado_factura='Aprobado'";
                $cadenaSql .= " AND indice_facturacion='" . $variable . "';";
                break;

            //Variables Sincronizar ERP

            case 'parametrosGlobales':
                $cadenaSql = " SELECT descripcion , id_valor ";
                $cadenaSql .= " FROM  facturacion.parametros_generales ";
                $cadenaSql .= " WHERE estado_registro=TRUE ";
                break;

            case 'consultarInformacionBeneficiario':
                $cadenaSql = " SELECT value , data , urbanizacion ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "(SELECT DISTINCT identificacion ||' - ('||nombre||' '||primer_apellido||' '||segundo_apellido||')' AS  value, bp.id_beneficiario  AS data, proyecto as urbanizacion ";
                $cadenaSql .= " FROM  interoperacion.beneficiario_potencial bp ";
                $cadenaSql .= " JOIN interoperacion.documentos_contrato ac on ac.id_beneficiario=bp.id_beneficiario ";
                $cadenaSql .= " WHERE bp.estado_registro=TRUE ";
                $cadenaSql .= " AND ac.estado_registro=TRUE ";
                $cadenaSql .= " AND ac.tipologia_documento=132 ";
                $cadenaSql .= "     ) datos ";
                $cadenaSql .= "WHERE data='" . $variable . "' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

            case 'actualizarFechaPagoOportuno':
                $cadenaSql = " UPDATE facturacion.factura SET fecha_pago_oportuno='" . $variable['fechaOportuna'] . "' ";
                $cadenaSql .= " WHERE id_factura='" . $variable['id_factura'] . "'";
                break;

        }

        return $cadenaSql;

    }

}
