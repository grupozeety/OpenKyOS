<?php
namespace gestionBeneficiarios\generarContratosMasivos;
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
            case 'consultarBloques':

                $cadenaSql = " SELECT id_bloque, nombre, descripcion, grupo ";
                $cadenaSql .= " FROM " . $prefijo . "bloque;";

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

            case 'consultarExitenciaContrato':
                $cadenaSql = " SELECT id_beneficiario, numero_contrato,numero_identificacion";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND numero_identificacion='" . $variable . "';";
                break;

            case 'consultarExitenciaBeneficiario':
                $cadenaSql = " SELECT id_beneficiario";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND identificacion='" . $variable . "';";
                break;
        }

        return $cadenaSql;
    }
}
?>

