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
        }

        return $cadenaSql;
    }
}
?>

