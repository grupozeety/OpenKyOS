<?php

namespace reportes\actasGeneradas;

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

            case 'consultarInformacionPortatil':
                $cadenaSql = " SELECT cn.id_beneficiario, cn.numero_identificacion,";
                $cadenaSql .= " cn.nombres||' '||cn.primer_apellido||' '|| CASE WHEN cn.segundo_apellido IS NULL THEN ' 'ELSE cn.segundo_apellido END as \"NombreBeneficiario\",";
                $cadenaSql .= " cn.departamento, cn.municipio, cn.urbanizacion,";
                $cadenaSql .= " cn.direccion_domicilio as direccion,";
                $cadenaSql .= " cn.manzana,";
                $cadenaSql .= " cn.torre,";
                $cadenaSql .= " cn.bloque,";
                $cadenaSql .= " cn.interior,";
                $cadenaSql .= " cn.lote,";
                $cadenaSql .= " cn.piso,";
                $cadenaSql .= " cn.casa_apartamento, ";
                $cadenaSql .= " cn.numero_contrato ";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN interoperacion.acta_entrega_portatil ep ON ep.id_beneficiario=cn.id_beneficiario AND ep.estado_registro='TRUE'";
                $cadenaSql .= " WHERE cn.numero_identificacion IS NOT NULL";
                $cadenaSql .= " AND cn.estado_registro='TRUE'";

                if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                    $cadenaSql .= " AND cn.municipio='" . $_REQUEST['municipio'] . "'";
                }
                if (isset($_REQUEST['departamento']) && $_REQUEST['departamento'] != '') {

                    $cadenaSql .= " AND cn.departamento='" . $_REQUEST['departamento'] . "'";
                }

                if (isset($_REQUEST['urbanizacion']) && $_REQUEST['urbanizacion'] != '') {
                    $cadenaSql .= " AND cn.urbanizacion='" . $_REQUEST['urbanizacion'] . "'";
                }

                if (isset($_REQUEST['id_beneficiario']) && $_REQUEST['id_beneficiario'] != '') {
                    $cadenaSql .= " AND cn.id_beneficiario='" . $_REQUEST['id_beneficiario'] . "'";
                }

                $cadenaSql .= " ORDER BY cn.numero_contrato ";

                break;

            case 'consultarInformacionServicios':

                $cadenaSql = " SELECT cn.id_beneficiario, cn.numero_identificacion ,";
                $cadenaSql .= " cn.nombres||' '||cn.primer_apellido||' '|| CASE WHEN cn.segundo_apellido IS NULL THEN ' 'ELSE cn.segundo_apellido END as \"NombreBeneficiario\",";
                $cadenaSql .= " cn.departamento, cn.municipio, cn.urbanizacion,";
                $cadenaSql .= " cn.direccion_domicilio as direccion,";
                $cadenaSql .= " cn.manzana,";
                $cadenaSql .= " cn.torre,";
                $cadenaSql .= " cn.bloque,";
                $cadenaSql .= " cn.interior,";
                $cadenaSql .= " cn.lote,";
                $cadenaSql .= " cn.piso,";
                $cadenaSql .= " cn.casa_apartamento , ";
                $cadenaSql .= " cn.numero_contrato ";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN interoperacion.acta_entrega_servicios ep ON ep.id_beneficiario=cn.id_beneficiario AND ep.estado_registro='TRUE'";
                $cadenaSql .= " WHERE cn.numero_identificacion IS NOT NULL";
                $cadenaSql .= " AND cn.estado_registro='TRUE'";

                if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                    $cadenaSql .= " AND cn.municipio='" . $_REQUEST['municipio'] . "'";
                }
                if (isset($_REQUEST['departamento']) && $_REQUEST['departamento'] != '') {

                    $cadenaSql .= " AND cn.departamento='" . $_REQUEST['departamento'] . "'";
                }

                if (isset($_REQUEST['urbanizacion']) && $_REQUEST['urbanizacion'] != '') {
                    $cadenaSql .= " AND cn.urbanizacion='" . $_REQUEST['urbanizacion'] . "'";
                }

                if (isset($_REQUEST['id_beneficiario']) && $_REQUEST['id_beneficiario'] != '') {
                    $cadenaSql .= " AND cn.id_beneficiario='" . $_REQUEST['id_beneficiario'] . "'";
                }

                $cadenaSql .= " ORDER BY cn.numero_contrato ";

                break;

            case 'consultarBeneficiariosPotenciales':
                $cadenaSql = " SELECT value , data ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "(SELECT DISTINCT cn.numero_identificacion ||' - ('||cn.nombres||' '||cn.primer_apellido||' '||(CASE WHEN cn.segundo_apellido IS NULL THEN '' ELSE cn.segundo_apellido END)||')' AS value, bp.id_beneficiario AS data ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp ";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=bp.id_beneficiario ";
                $cadenaSql .= " WHERE bp.estado_registro=TRUE ";
                $cadenaSql .= " AND cn.estado_registro=TRUE ";
                $cadenaSql .= "     ) datos ";
                $cadenaSql .= "WHERE value ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

        }

        return $cadenaSql;
    }
}
?>

