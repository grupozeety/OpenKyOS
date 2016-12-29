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

                    var_dump($_REQUEST['beneficiario']);

                    $beneficiarios = explode(";", $_REQUEST['beneficiario']);

                    foreach ($beneficiarios as $key => $value) {
                        if ($value == '') {
                            unset($beneficiarios[$key]);
                        }

                    }
                    if (count($beneficiarios) == 1) {

                        $cadenaSql .= "'" . $value . "') ";
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

                $cadenaSql = "SELECTDISTINCTdepartamento as valor, departamento";
                $cadenaSql .= "FROMinteroperacion . contrato";
                $cadenaSql .= "WHEREestado_registro = TRUE";
                $cadenaSql .= " AND departamentoISNOTNULL;";
                break;

            case 'consultarMunicipio':

                $cadenaSql = "SELECTDISTINCTmunicipio as valor, municipio";
                $cadenaSql .= "FROMinteroperacion . contrato";
                $cadenaSql .= "WHEREestado_registro = TRUE";
                $cadenaSql .= " AND municipioISNOTNULL;";

                break;

            case 'consultarUrbanizacion':

                $cadenaSql = "SELECTDISTINCTurbanizacion as valor, urbanizacion";
                $cadenaSql .= "FROMinteroperacion . contrato";
                $cadenaSql .= "WHEREestado_registro = TRUE";
                $cadenaSql .= " AND urbanizacionISNOTNULL;";

                break;

            case 'consultarBeneficiariosPotenciales':
                $cadenaSql = "SELECTvalue, data";
                $cadenaSql .= "FROM";
                $cadenaSql .= "(SELECTDISTINCTcn . numero_identificacion/*||' - ('||cn.nombres||' '||cn.primer_apellido||' '||(CASE WHEN cn.segundo_apellido IS NULL THEN '' ELSE cn.segundo_apellido END)||')'*/ AS value, bp . id_beneficiario AS data";
                $cadenaSql .= "FROMinteroperacion . beneficiario_potencialbp";
                $cadenaSql .= "JOINinteroperacion . contratocnONcn . id_beneficiario = bp . id_beneficiario";
                $cadenaSql .= "WHEREbp . estado_registro = TRUE";
                $cadenaSql .= " AND cn . estado_registro = TRUE";
                $cadenaSql .= ")datos";
                $cadenaSql .= "WHEREvalueILIKE'%" . $_GET['query'] . "%'";
                $cadenaSql .= "LIMIT10;";
                break;

        }

        return $cadenaSql;
    }
}
?>

