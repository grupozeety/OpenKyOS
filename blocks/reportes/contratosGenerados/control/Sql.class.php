<?php

namespace reportes\contratosGenerados;

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

            case 'consultarInformacion':
                $cadenaSql = " SELECT id_beneficiario,";
                $cadenaSql .= " numero_contrato,";
                $cadenaSql .= " numero_identificacion ,";
                $cadenaSql .= " nombres||' '||primer_apellido||' '|| CASE WHEN segundo_apellido IS NULL THEN ' ' ELSE segundo_apellido END as \"NombreBeneficiario\" ,";
                $cadenaSql .= " departamento,";
                $cadenaSql .= " municipio,";
                $cadenaSql .= " urbanizacion,";
                $cadenaSql .= " direccion_domicilio as direccion,";
                $cadenaSql .= " manzana,";
                $cadenaSql .= " torre,";
                $cadenaSql .= " bloque,";
                $cadenaSql .= " interior,";
                $cadenaSql .= " lote,";
                $cadenaSql .= " piso,";
                $cadenaSql .= " casa_apartamento";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro='TRUE'";

                if (isset($_REQUEST['tipo_firma']) && $_REQUEST['tipo_firma'] == '2') {
                    $cadenaSql .= " AND nombre_documento_contrato IS NOT NULL ";

                } elseif (isset($_REQUEST['tipo_firma']) && $_REQUEST['tipo_firma'] == '1') {

                    $cadenaSql .= " AND nombre_documento_contrato IS  NULL";

                }

                if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                    $cadenaSql .= " AND municipio='" . $_REQUEST['municipio'] . "'";
                }

                if (isset($_REQUEST['departamento']) && $_REQUEST['departamento'] != '') {

                    $cadenaSql .= " AND departamento='" . $_REQUEST['departamento'] . "'";
                }

                if (isset($_REQUEST['urbanizacion']) && $_REQUEST['urbanizacion'] != '') {
                    $cadenaSql .= " AND urbanizacion='" . $_REQUEST['urbanizacion'] . "'";
                }

                if (isset($_REQUEST['id_beneficiario']) && $_REQUEST['id_beneficiario'] != '') {
                    $cadenaSql .= " AND id_beneficiario='" . $_REQUEST['id_beneficiario'] . "'";
                }

                $cadenaSql .= " ORDER BY numero_contrato ";
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

