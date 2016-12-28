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

            case 'consultaInformacionBeneficiario':
                $cadenaSql = " SELECT cn.* , pm.descripcion_metas";
                $cadenaSql .= " FROM interoperacion.contrato AS cn ";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial AS bn ON bn.id_beneficiario =cn.id_beneficiario";
                $cadenaSql .= " JOIN parametros.proyectos_metas AS pm ON pm.id_proyecto =bn.id_proyecto";
                $cadenaSql .= " WHERE cn.estado_registro='TRUE' ";
                $cadenaSql .= " ORDER BY cn.numero_contrato;";
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

