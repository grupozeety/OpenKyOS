<?php

if (!isset($GLOBALS["autorizado"])) {
    include("../index.php");
    exit;
}

include_once("core/manager/Configurador.class.php");
include_once("core/connection/Sql.class.php");

//Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
//en camel case precedida por la palabra sql

class SqlindexPres extends sql {

    var $miConfigurador;

    function __construct() {
        $this->miConfigurador = Configurador::singleton();
    }

    function cadena_sql($tipo, $variable = "") {
        /**
         * 1. Revisar las variables para evitar SQL Injection
         *
         */
        $prefijo = $this->miConfigurador->getVariableConfiguracion("prefijo");
        $idSesion = $this->miConfigurador->getVariableConfiguracion("id_sesion");

        switch ($tipo) {

            /**
             * Clausulas específicas
             */
            
            case "datosUsuario":
                $cadena_sql =" SELECT";
                $cadena_sql.=" id_usuario ID,";
                $cadena_sql.=" nombre NOMBRE,";
                $cadena_sql.=" apellido APELLIDO,";
                $cadena_sql.=" correo CORREO,";
                $cadena_sql.=" imagen IMAGEN";
                $cadena_sql.=" FROM ".$prefijo."usuario";
                $cadena_sql.=" WHERE id_usuario='" . $variable . "' ";                
                break;
				
        }

        return $cadena_sql;
    }

}

?>
