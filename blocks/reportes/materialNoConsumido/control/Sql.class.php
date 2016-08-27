<?php
namespace reportes\materialNoConsumido;
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

            case 'elementosConsumidos':

                $cadenaSql = " SELECT nombre, ";
                $cadenaSql .= " cantidad_asignada, consumo ";
                $cadenaSql .= " FROM interoperacion.consumo_material";
                $cadenaSql .= " WHERE proyecto ='" . $_REQUEST['proyecto'] . "' ";
                $cadenaSql .= " AND estado_registro='TRUE';";

                break;

        }

        return $cadenaSql;
    }
}
?>

