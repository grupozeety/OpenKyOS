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
        $info_usuario = $this->miSesionSso->getParametrosSesionAbierta();

        foreach ($info_usuario['description'] as $key => $rol) {

            $info_usuario['rol'][] = $rol;
        }

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

            case 'consultarBeneficiario':
                $cadenaSql = " SELECT";
                $cadenaSql .= " cn.nombres||' '||cn.primer_apellido||' '||(CASE WHEN cn.segundo_apellido IS NOT NULL THEN cn.segundo_apellido ELSE '' END) as nombre_beneficiario,";
                $cadenaSql .= " cn.numero_identificacion,";
                $cadenaSql .= " cn.direccion_domicilio||";
                $cadenaSql .= " (CASE WHEN cn.manzana <> '0' THEN ' Manzana # '||cn.manzana ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.torre <> '0' THEN ' Torre # '||cn.manzana ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.casa_apartamento <>'0' THEN ' Casa/Apartamento # '||cn.casa_apartamento ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.interior <>'0' THEN ' Interior # '||cn.interior ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.lote <>'0' THEN ' Lote # '||cn.lote ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.barrio IS NOT NULL THEN ' Barrio '||cn.barrio ELSE '' END)as direccion_beneficiario,";
                $cadenaSql .= " cn.municipio,";
                $cadenaSql .= " cn.departamento,";
                $cadenaSql .= " (CASE WHEN cn.estrato_socioeconomico::text IS NULL THEN 'No Caracterizado' ELSE cn.estrato_socioeconomico::text END) as estrato";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " WHERE cn.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.id_beneficiario='" . $variable . "';";

                break;

            case 'consultaInformacionFacturacion':
                $cadenaSql = " SELECT ";
                $cadenaSql .= " cn.numero_contrato, ";
                $cadenaSql .= " to_date(aes.fecha_instalacion, 'DD-MM-YYYY') as fecha_venta,";
                $cadenaSql .= " fc.estado_factura,";
                $cadenaSql .= " to_char(fc.fecha_registro, 'YYYY-MM-DD')as fecha_factura,";
                $cadenaSql .= " fc.total_factura,";
                $cadenaSql .= " fc.id_ciclo,";
                $cadenaSql .= " pb.municipio,";
                $cadenaSql .= " pb.departamento,";
                $cadenaSql .= " pb.id_beneficiario ";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial pb ON pb.id_beneficiario=cn.id_beneficiario AND pb.estado_registro='TRUE'";
                $cadenaSql .= " JOIN interoperacion.acta_entrega_servicios aes ON aes.id_beneficiario=cn.id_beneficiario AND aes.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.factura fc ON fc.id_beneficiario=cn.id_beneficiario AND fc.estado_registro='TRUE'";
                $cadenaSql .= " WHERE cn.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.id_beneficiario='" . $variable . "' ";
                $cadenaSql .= " ORDER BY fc.fecha_registro DESC";
                $cadenaSql .= " LIMIT 1;";
                break;

            case 'consultaValoresConceptos':
                $cadenaSql = " SELECT ";
                $cadenaSql .= " fc.id_factura,";
                $cadenaSql .= " cp.valor_calculado as valor_concepto,";
                $cadenaSql .= " rl.descripcion as concepto,";
                $cadenaSql .= "to_char(urp.inicio_periodo, 'YYYY-MM-DD')as inicio_periodo,";
                $cadenaSql .= "to_char(urp.fin_periodo, 'YYYY-MM-DD') as fin_periodo";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial pb ON pb.id_beneficiario=cn.id_beneficiario AND pb.estado_registro='TRUE'";
                $cadenaSql .= " JOIN interoperacion.acta_entrega_servicios aes ON aes.id_beneficiario=cn.id_beneficiario AND aes.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.factura fc ON fc.id_beneficiario=cn.id_beneficiario AND fc.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.conceptos cp ON cp.id_factura=fc.id_factura AND cp.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.regla rl ON rl.id_regla=cp.id_regla AND rl.estado_registro='TRUE'";
                $cadenaSql .= " JOIN facturacion.usuario_rol_periodo urp ON urp.id_usuario_rol_periodo=cp.id_usuario_rol_periodo AND urp.estado_registro='TRUE'";
                $cadenaSql .= " WHERE cn.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.id_beneficiario='" . $variable . "' ";
                $cadenaSql .= " AND fc.id_factura=";
                $cadenaSql .= " (";
                $cadenaSql .= " SELECT id_factura";
                $cadenaSql .= " FROM facturacion.factura";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "' ";
                $cadenaSql .= " ORDER BY fecha_registro DESC";
                $cadenaSql .= " LIMIT 1";
                $cadenaSql .= " );";
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
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM parametros.procesos_accesos";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " ORDER BY id_proceso DESC;";
                break;

        }

        return $cadenaSql;
    }
}
