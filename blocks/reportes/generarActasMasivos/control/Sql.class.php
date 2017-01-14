<?php
namespace reportes\generarActasMasivos;
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

            //Validaciones

            case 'consultarExitenciaActaServicios':
                $cadenaSql = " SELECT aes.id_beneficiario,cn.numero_identificacion";
                $cadenaSql .= " FROM interoperacion.acta_entrega_servicios aes ";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=aes.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE cn.numero_identificacion='" . $variable . "'";
                $cadenaSql .= " AND aes.estado_registro='TRUE';";
                break;

            case 'consultarExitenciaActaPortatil':
                $cadenaSql = " SELECT ap.id_beneficiario,cn.numero_identificacion";
                $cadenaSql .= " FROM interoperacion.acta_entrega_portatil ap ";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=ap.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE cn.numero_identificacion='" . $variable . "'";
                $cadenaSql .= " AND ap.estado_registro='TRUE'";
                break;

            case 'consultarExitenciaIP':
                $cadenaSql = " SELECT aes.id_beneficiario,cn.numero_identificacion ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_servicios aes";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=aes.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE aes.estado_registro='TRUE'";
                $cadenaSql .= " AND aes.ip_esc='" . $variable . "';";
                break;

            case 'consultarExitenciaMac1':
                $cadenaSql = " SELECT aes.id_beneficiario,cn.numero_identificacion ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_servicios aes";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=aes.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE aes.estado_registro='TRUE'";
                $cadenaSql .= " AND aes.mac_esc='" . $variable . "';";
                break;

            case 'consultarExitenciaMac2':
                $cadenaSql = " SELECT aes.id_beneficiario,cn.numero_identificacion ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_servicios aes";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=aes.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE aes.estado_registro='TRUE'";
                $cadenaSql .= " AND aes.mac2_esc='" . $variable . "';";
                break;

            case 'consultarExitenciaSerialRegistrado':
                $cadenaSql = " SELECT id_equipo, serial";
                $cadenaSql .= " FROM interoperacion.politecnica_portatil ";
                $cadenaSql .= " WHERE serial='" . $variable . "';";
                break;

            case 'consultarExitenciaActa':
                $cadenaSql = " SELECT ep.id as identificador_acta, cn.numero_identificacion ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_portatil ep";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=ep.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE ep.estado_registro='TRUE'";
                $cadenaSql .= " AND ep.serial='" . $variable['serial_portatil'] . "'";
                //$cadenaSql .= " AND cn.numero_identificacion='" . $variable['identificacion_beneficiario'] . "'";
                break;

            case 'consultarExitenciaSerialPortatil':
                $cadenaSql = " SELECT ep.id as identificador_acta, cn.numero_identificacion ";
                $cadenaSql .= " FROM interoperacion.acta_entrega_portatil ep";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=ep.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE ep.estado_registro='TRUE'";
                $cadenaSql .= " AND ep.serial='" . $variable['serial_portatil'] . "'";
                //$cadenaSql .= " AND cn.numero_identificacion='" . $variable['identificacion_beneficiario'] . "'";
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

            case 'consultarInformacionBeneficiario':
                $cadenaSql = " SELECT bp.* ";
                $cadenaSql .= " FROM interoperacion.contrato bp";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE' ";
                $cadenaSql .= " AND bp.numero_identificacion='" . $variable . "';";

                break;
            //Registros
            case 'registrarActaServicios':

                if ($_REQUEST['funcionalidad'] == '3') {
                    $cadenaSql = " UPDATE interoperacion.acta_entrega_servicios";
                    $cadenaSql .= " SET estado_registro='FALSE'";
                    $cadenaSql .= " WHERE id_beneficiario='" . $variable['id_beneficiario'] . "';";
                    $cadenaSql .= " INSERT INTO interoperacion.acta_entrega_servicios(";
                } else {
                    $cadenaSql = " INSERT INTO interoperacion.acta_entrega_servicios(";
                }

                $cadenaSql .= " id_beneficiario,";
                $cadenaSql .= " mac_esc, ";
                $cadenaSql .= " serial_esc, ";
                $cadenaSql .= " marca_esc, ";
                $cadenaSql .= " cant_esc, ";
                $cadenaSql .= " ip_esc, ";
                $cadenaSql .= " mac2_esc)";
                $cadenaSql .= " VALUES (";
                foreach ($variable as $key => $value) {

                    if ($key == 'mac_esc' && $value == 'Sin MAC 1') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'mac_esc2' && $value == 'Sin MAC 2') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'ip_esc' && $value == 'Sin IP') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'marca_esc' && $value == 'Sin Marca Esclavo') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'serial_esc' && $value == 'Sin Serial Esclavo') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'cant_esc' && $value == 'Sin Cantidad') {
                        $cadenaSql .= "NULL,";
                    } else {

                        $cadenaSql .= "'" . $value . "',";

                    }

                }

                $cadenaSql .= ")RETURNING id_beneficiario;";
                break;

            case 'registrarActaPortatil':

                if ($_REQUEST['funcionalidad'] == '3') {

                    $cadenaSql = " UPDATE interoperacion.acta_entrega_portatil";
                    $cadenaSql .= " SET estado_registro='FALSE'";
                    $cadenaSql .= " WHERE id_beneficiario='" . $variable['id_beneficiario'] . "';";
                    $cadenaSql .= " INSERT INTO interoperacion.acta_entrega_portatil(";
                } else {
                    $cadenaSql = " INSERT INTO interoperacion.acta_entrega_portatil(";
                }
                $cadenaSql .= " id_beneficiario,";
                $cadenaSql .= " fecha_entrega,";
                $cadenaSql .= " serial)";
                $cadenaSql .= " VALUES (";
                foreach ($variable as $key => $value) {

                    if ($key == 'serial' && $value == 'Sin Serial Portatil') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'fecha_entrega' && $value == 'Sin Fecha') {
                        $cadenaSql .= "NULL,";
                    } else {

                        $cadenaSql .= "'" . $value . "',";

                    }

                }

                $cadenaSql .= ")RETURNING id_beneficiario;";
                break;
            // Registro Procesos
            case 'registrarProceso':
                $cadenaSql = " INSERT INTO parametros.procesos_masivos(";
                $cadenaSql .= " descripcion,";
                $cadenaSql .= " estado,";
                $cadenaSql .= " nombre_archivo,";
                $cadenaSql .= " parametro_inicio,";
                $cadenaSql .= " parametro_fin)";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= " 'Actas',";
                $cadenaSql .= " 'No Iniciado',";
                $cadenaSql .= " '" . $variable['nombre'] . "',";
                $cadenaSql .= " '" . $variable['inicio'] . "',";
                $cadenaSql .= " '" . $variable['final'] . "'";
                $cadenaSql .= " )RETURNING id_proceso;";
                break;

            case 'consultarProceso':
                $cadenaSql = " SELECT * ";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE descripcion='Contratos'";
                $cadenaSql .= " ORDER BY id_proceso DESC;";
                break;

            case 'consultarProcesoParticular':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE id_proceso=(";
                $cadenaSql .= " SELECT MIN(id_proceso) ";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE estado_registro='TRUE' ";
                $cadenaSql .= " AND estado='No Iniciado'";
                $cadenaSql .= " );";
                break;

            case 'actualizarProceso':
                $cadenaSql = " UPDATE parametros.procesos_masivos";
                $cadenaSql .= " SET estado='En Proceso'";
                $cadenaSql .= " WHERE id_proceso='" . $variable . "';";
                break;

            case 'finalizarProceso':
                $cadenaSql = " UPDATE parametros.procesos_masivos";
                $cadenaSql .= " SET estado='Finalizado',";
                $cadenaSql .= " ruta_archivo='" . $variable['ruta_archivo'] . "',";
                $cadenaSql .= " nombre_ruta_archivo='" . $variable['nombre_archivo'] . "'";
                $cadenaSql .= " WHERE id_proceso='" . $variable['id_proceso'] . "';";
                break;
            // Crear Documenntos Contrato
            case 'ConsultaBeneficiarios':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE numero_contrato >=" . $variable['Inicio'] . " ";
                $cadenaSql .= " AND numero_contrato<=" . $variable['Fin'] . " ";
                $cadenaSql .= " ORDER BY numero_contrato ;";
                break;

            case 'consultarTipoDocumento':
                $cadenaSql = " SELECT pr.id_parametro,pr.codigo, pr.descripcion ";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Tipo de Documento'";
                $cadenaSql .= " AND pr.descripcion='" . $variable . "' ";
                $cadenaSql .= " AND rl.estado_registro=TRUE ";
                break;

            case 'consultarParametroParticular':
                $cadenaSql = " SELECT descripcion ";
                $cadenaSql .= " FROM parametros.parametros";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_parametro='" . $variable . "';";
                break;

        }

        return $cadenaSql;
    }
}
?>

