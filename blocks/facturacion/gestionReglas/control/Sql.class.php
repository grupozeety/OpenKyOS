<?php
namespace facturacion\gestionReglas;

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

            case 'consultaParticular':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM facturacion.regla";
                $cadenaSql .= " WHERE estado_registro='TRUE';";
                break;

            case 'consultarReglaParticular':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM facturacion.regla";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_regla='" . $_REQUEST['id_regla'] . "';";
                break;

            case 'registrarActualizarRegla':
                if ($_REQUEST['opcion'] == 'actualizarReglaParticular') {
                    $cadenaSql = " UPDATE facturacion.regla";
                    $cadenaSql .= " SET estado_registro='FALSE'";
                    $cadenaSql .= " WHERE id_regla='" . $variable['id_regla'] . "';";
                    $cadenaSql .= " INSERT INTO facturacion.regla(";
                } else {
                    $cadenaSql = " INSERT INTO facturacion.regla(";
                }
                $cadenaSql .= " descripcion,";
                $cadenaSql .= " formula, ";
                $cadenaSql .= " identificador)";
                $cadenaSql .= " VALUES ('" . $variable['descricion'] . "', ";
                $cadenaSql .= " '" . $variable['formula'] . "',";
                $cadenaSql .= " '" . $variable['identificador'] . "');";
                break;

            case 'eliminarRegla':
                $cadenaSql = " UPDATE facturacion.regla";
                $cadenaSql .= " SET estado_registro='FALSE'";
                $cadenaSql .= " WHERE id_regla='" . $_REQUEST['id_regla'] . "';";
                break;
        }

        return $cadenaSql;
    }
}
