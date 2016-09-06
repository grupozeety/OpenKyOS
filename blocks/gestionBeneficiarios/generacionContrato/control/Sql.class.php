<?php
namespace gestionBeneficiarios\generacionContrato;
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
            case 'consultarBeneficiariosPotenciales':
                $cadenaSql = " SELECT identificacion ||' - ('||nombre||')' AS  value, id  AS data  ";
                $cadenaSql .= " FROM  interoperacion.beneficiario_potencial ";
                $cadenaSql .= "WHERE estado_registro=TRUE ";
                $cadenaSql .= "AND  cast(identificacion  as text) ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "OR nombre ILIKE '%" . $_GET['query'] . "%' LIMIT 10; ";

                break;

            case 'insertarBloque':
                $cadenaSql = 'INSERT INTO ';
                $cadenaSql .= $prefijo . 'bloque ';
                $cadenaSql .= '( ';
                $cadenaSql .= 'nombre,';
                $cadenaSql .= 'descripcion,';
                $cadenaSql .= 'grupo';
                $cadenaSql .= ') ';
                $cadenaSql .= 'VALUES ';
                $cadenaSql .= '( ';
                $cadenaSql .= '\'' . $_REQUEST['nombre'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST['descripcion'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST['grupo'] . '\' ';
                $cadenaSql .= '); ';
                break;
        }

        return $cadenaSql;
    }
}
?>

