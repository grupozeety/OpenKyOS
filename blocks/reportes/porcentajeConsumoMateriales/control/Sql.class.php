<?php
namespace reportes\porcentajeConsumoMateriales;
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

            case 'porcentajeConsumo':

                $cadenaSql = " SELECT distinct proyecto, ";
                $cadenaSql .= " orden_trabajo, descripcion, porcentaje_consumo ";
                $cadenaSql .= " FROM interoperacion.consumo_material";
                $cadenaSql .= " WHERE proyecto ='" . $variable . "' ";
                $cadenaSql .= " AND estado_registro='TRUE';";

                break;
                
            case 'porcentajeConsumoTodos':
                
                $cadenaSql = " SELECT distinct proyecto, ";
                $cadenaSql .= " orden_trabajo, descripcion, porcentaje_consumo ";
                $cadenaSql .= " FROM interoperacion.consumo_material";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " estado_registro='TRUE' ";
                $cadenaSql .= " AND ";
                $cadenaSql .= "proyecto ";
                $cadenaSql .= "IN ";
                $cadenaSql .= "(";

                foreach ($variable as $clave => $valor) {

                    $cadenaSql .= "'" . $valor . "',";

                }

                $cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));
                $cadenaSql .= ")";
                
                
                break;
                
            case 'obtenerProyectos':
                
                $cadenaSql = " SELECT distinct proyecto";
                $cadenaSql .= " FROM interoperacion.consumo_material";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " estado_registro='TRUE';";
                break;

        }

        return $cadenaSql;
    }
}
?>

