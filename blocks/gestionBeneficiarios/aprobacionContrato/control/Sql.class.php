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
                $cadenaSql = " SELECT cn.id identificador_contrato, cn.numero_contrato, bn.nombre||' '||bn.primer_apellido||' '||bn.segundo_apellido nombre_beneficiario,";
                $cadenaSql .= " bn.id_beneficiario identificador_beneficiario, bn.identificacion, bn.proyecto,pm.descripcion estado_contrato, ";
                $cadenaSql .= " cn.nombre_documento_contrato , cn.ruta_documento_contrato ";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN parametros.parametros pm ON pm.id_parametro=cn.estado_contrato AND pm.estado_registro=TRUE";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pm.rel_parametro AND rl.descripcion='Estado Contrato' AND rl.estado_registro=TRUE";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial bn ON bn.id_beneficiario=cn.id_beneficiario";
                $cadenaSql .= " WHERE cn.estado_registro=TRUE";
                $cadenaSql .= " AND bn.estado_registro=TRUE";

                break;

            case 'consultarContratoEspecifico':
                $cadenaSql = " SELECT cn.*, pm.descripcion est_contrato,pm.id_parametro id_est_contrato, bn.id_proyecto, bn.id_beneficiario as identificador_beneficiario ";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN parametros.parametros pm ON pm.id_parametro=cn.estado_contrato AND pm.estado_registro=TRUE";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pm.rel_parametro AND rl.descripcion='Estado Contrato' AND rl.estado_registro=TRUE";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial bn ON bn.id_beneficiario=cn.id_beneficiario AND bn.estado_registro=TRUE ";
                $cadenaSql .= " WHERE cn.estado_registro=TRUE";
                $cadenaSql .= " AND cn.id='" . $_REQUEST['id_contrato'] . "';";

                break;

            case 'consultarEstadoAprobado':
                $cadenaSql = " SELECT  pm.descripcion est_contrato,pm.id_parametro id_est_contrato ";
                $cadenaSql .= " FROM parametros.parametros pm  ";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pm.rel_parametro AND rl.descripcion='Estado Contrato' AND rl.estado_registro=TRUE";
                $cadenaSql .= " WHERE pm.estado_registro=TRUE AND pm.descripcion='Aprobado'";

                break;

            case 'consultarMedioPago':
                $cadenaSql = " SELECT pm.id_parametro, pm.descripcion ";
                $cadenaSql .= " FROM parametros.parametros pm";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pm.rel_parametro AND pm.estado_registro=TRUE AND rl.descripcion='Medio Pago'";
                $cadenaSql .= " WHERE pm.estado_registro=TRUE;";

                break;

            case 'consultarTipoTecnologia':
                $cadenaSql = " SELECT pm.id_parametro, pm.descripcion ";
                $cadenaSql .= " FROM parametros.parametros pm";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pm.rel_parametro AND pm.estado_registro=TRUE AND rl.descripcion='Tipo Tecnologia'";
                $cadenaSql .= " WHERE pm.estado_registro=TRUE;";

                break;

            case 'actualizarContrato':
                $cadenaSql = " UPDATE interoperacion.contrato";
                $cadenaSql .= " SET fecha_aprobacion='" . date('Y-m-d') . "', ";
                $cadenaSql .= " estado_contrato='" . $variable['id_est_contrato'] . "', ";
                $cadenaSql .= " nombre_documento_contrato='" . $variable['nombre_archivo'] . "',";
                $cadenaSql .= " ruta_documento_contrato='" . $variable['ruta_archivo'] . "'";
                $cadenaSql .= " WHERE id='" . $_REQUEST['id_contrato'] . "' ";
                $cadenaSql .= " AND estado_registro=TRUE ;";
                break;

            case 'consultarEstadoInstalarAgendar':
                $cadenaSql = " SELECT  pm.descripcion est_contrato,pm.id_parametro id_est_servicio ";
                $cadenaSql .= " FROM parametros.parametros pm  ";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pm.rel_parametro AND rl.descripcion='Estado Servicio' AND rl.estado_registro=TRUE";
                $cadenaSql .= " WHERE pm.estado_registro=TRUE AND pm.descripcion='Por Instalar y Agendar'";
                break;

            case 'actualizarServicio':
                $cadenaSql = " UPDATE interoperacion.servicio";
                $cadenaSql .= " SET estado_servicio='" . $variable['id_est_servicio'] . "',";
                $cadenaSql .= " tipo_tecnologia='" . $_REQUEST['tipo_tecnologia'] . "', ";
                $cadenaSql .= " medio_pago='" . $_REQUEST['medio_pago'] . "',";
                $cadenaSql .= " valor_tarificacion='" . $_REQUEST['valor_tarificacion'] . "' ";
                $cadenaSql .= " WHERE id_contrato= '" . $_REQUEST['id_contrato'] . "'";
                $cadenaSql .= " AND estado_registro=TRUE ;";
                break;

            case 'registrarOrdenTrabajo':
                $cadenaSql = " UPDATE interoperacion.beneficiario_potencial ";
                $cadenaSql .= " SET orden_trabajo='" . $variable['id_orden'] . "'";
                $cadenaSql .= " WHERE id_beneficiario='" . $variable['identificador_beneficiario'] . "'  ";
                $cadenaSql .= " AND estado_registro=TRUE ;";

                break;

        }

        return $cadenaSql;
    }
}
?>

