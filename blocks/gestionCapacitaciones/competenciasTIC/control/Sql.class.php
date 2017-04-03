<?php
namespace gestionCapacitaciones\competenciasTIC;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

require_once "core/manager/Configurador.class.php";
require_once "core/connection/Sql.class.php";

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

            case 'consultaDepartamento':
                $cadenaSql = " SELECT codigo_dep,codigo_dep ||' - '||departamento as departamento";
                $cadenaSql .= " FROM parametros.departamento;";
                break;

            case 'consultaMunicipio':
                $cadenaSql = " SELECT codigo_mun,codigo_mun||' - '||municipio as municipio";
                $cadenaSql .= " FROM parametros.municipio;";
                break;

            case 'consultarBeneficiariosPotenciales':
                $cadenaSql = " SELECT value , data ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "(SELECT DISTINCT bp.identificacion ||' - ('||bp.nombre||' '||bp.primer_apellido||' '||bp.segundo_apellido||')' AS  value, bp.id_beneficiario  AS data ";
                $cadenaSql .= " FROM  interoperacion.beneficiario_potencial bp ";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=bp.id_beneficiario ";
                $cadenaSql .= " WHERE bp.estado_registro=TRUE ";
                $cadenaSql .= " AND cn.estado_registro=TRUE ";
                $cadenaSql .= $variable;
                $cadenaSql .= "     ) datos ";
                $cadenaSql .= "WHERE value ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

            //----------------------------------------------

            case 'consultaParticular':
                $cadenaSql = " SELECT p.id_periodo,p.valor,p.tipo_unidad, pm.descripcion";
                $cadenaSql .= " FROM facturacion.periodo p";
                $cadenaSql .= " JOIN facturacion.parametros_generales pm ON pm.id=p.tipo_unidad::int AND pm.estado_registro='TRUE' AND pm.id_valor=2";
                $cadenaSql .= " WHERE p.estado_registro='TRUE';";
                break;

            case 'consultaTipoUnidad':
                $cadenaSql = " SELECT id, descripcion";
                $cadenaSql .= " FROM facturacion.parametros_generales";
                $cadenaSql .= " WHERE id_valor='2'";
                $cadenaSql .= " AND estado_registro='TRUE';";
                break;

            case 'consultarPeriodoParticular':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM facturacion.periodo";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_periodo='" . $_REQUEST['id_periodo'] . "';";
                break;

            case 'registrarPeriodo':

                $cadenaSql = " INSERT INTO facturacion.periodo(";
                $cadenaSql .= " valor,";
                $cadenaSql .= " tipo_unidad)";
                $cadenaSql .= " VALUES ('" . $variable['valor'] . "', ";
                $cadenaSql .= " '" . $variable['unidad'] . "');";

                break;

            case 'actualizarPeriodo':
                $cadenaSql = " UPDATE facturacion.periodo";
                $cadenaSql .= " SET valor='" . $variable['valor'] . "', ";
                $cadenaSql .= " tipo_unidad='" . $variable['unidad'] . "'";
                $cadenaSql .= " WHERE id_periodo='" . $variable['id_periodo'] . "';";
                break;

            case 'eliminarPeriodo':
                $cadenaSql = " UPDATE facturacion.periodo";
                $cadenaSql .= " SET estado_registro='FALSE'";
                $cadenaSql .= " WHERE id_periodo='" . $_REQUEST['id_periodo'] . "';";
                break;
        }

        return $cadenaSql;
    }
}
