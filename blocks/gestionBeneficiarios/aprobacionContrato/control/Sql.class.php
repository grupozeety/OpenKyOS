<?php
namespace gestionBeneficiarios\aprobacionContrato;
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
            case 'consultarContratos':
                $cadenaSql = " SELECT cn.id identificador_contrato, cn.numero_contrato, bn.nombre||' '||bn.primer_apellido||' '||bn.segundo_apellido nombre_beneficiario, bn.id identificador_beneficiario, bn.identificacion, bn.urbanizacion,pm.descripcion estado_contrato ";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN parametros.parametros pm ON pm.id_parametro=cn.estado_contrato AND pm.estado_registro=TRUE";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pm.rel_parametro AND rl.descripcion='Estado Contrato' AND rl.estado_registro=TRUE";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial bn ON bn.id=cn.id_beneficiario";
                $cadenaSql .= " WHERE cn.estado_registro=TRUE";
                $cadenaSql .= " AND bn.estado_registro=TRUE";

                break;

        }

        return $cadenaSql;
    }
}
?>

