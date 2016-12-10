<?php

namespace llamarApi\funcion;

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
    public function __construct() {
        $this->miConfigurador = \Configurador::singleton();
    }
    public function getCadenaSql($tipo, $variable = "") {

        /**
         * 1.
         * Revisar las variables para evitar SQL Injection
         */
        $prefijo = $this->miConfigurador->getVariableConfiguracion("prefijo");
        $idSesion = $this->miConfigurador->getVariableConfiguracion("id_sesion");

        switch ($tipo) {

            /**
             * Clausulas genéricas.
             * se espera que estén en todos los formularios
             * que utilicen esta plantilla
             */
            case 'consultarInformacionApi':
                $cadenaSql = " SELECT componente, host, usuario, password, token_codificado, ruta_cookie ";
                $cadenaSql .= " FROM parametros.api_data";
                $cadenaSql .= " WHERE componente ='" . $variable . "';";
                break;

        }

        return $cadenaSql;
    }
}

?>
