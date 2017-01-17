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

            case 'consultarInformacionBeneficiario':
                $cadenaSql = " SELECT bp.* ,mn.municipio as nombre_municipio,dp.departamento as nombre_departamento";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " JOIN parametros.municipio mn ON mn.codigo_mun=bp.municipio";
                $cadenaSql .= " JOIN parametros.departamento dp ON dp.codigo_dep=bp.departamento";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE' ";
                $cadenaSql .= " AND bp.identificacion='" . $variable . "';";

                break;

            case 'registrarContrato':

                $cadenaSql = " INSERT INTO interoperacion.contrato(";
                $cadenaSql .= " id_beneficiario,";
                $cadenaSql .= " estado_contrato, ";
                $cadenaSql .= " nombres, ";
                $cadenaSql .= " primer_apellido,";
                $cadenaSql .= " segundo_apellido,";
                $cadenaSql .= " tipo_documento,";
                $cadenaSql .= " numero_identificacion, ";
                $cadenaSql .= " direccion_domicilio,";
                $cadenaSql .= " direccion_instalacion, ";
                $cadenaSql .= " departamento,";
                $cadenaSql .= " municipio, ";
                $cadenaSql .= " urbanizacion,";
                $cadenaSql .= " estrato,";
                $cadenaSql .= " telefono, ";
                $cadenaSql .= " celular,";
                $cadenaSql .= " correo,";
                $cadenaSql .= " velocidad_internet, ";
                $cadenaSql .= " valor_mensual, ";
                $cadenaSql .= " tecnologia,";
                $cadenaSql .= " estado,";
                $cadenaSql .= " usuario,";
                $cadenaSql .= " manzana,";
                $cadenaSql .= " bloque,";
                $cadenaSql .= " torre, ";
                $cadenaSql .= " casa_apartamento,";
                $cadenaSql .= " tipo_tecnologia, ";
                $cadenaSql .= " valor_tarificacion, ";
                $cadenaSql .= " interior,";
                $cadenaSql .= " lote,";
                $cadenaSql .= " piso,";
                $cadenaSql .= " nombre_comisionador,";
                $cadenaSql .= " fecha_contrato,";
                $cadenaSql .= " estrato_socioeconomico,";
                $cadenaSql .= " barrio)";
                $cadenaSql .= " VALUES (";
                foreach ($variable as $key => $value) {

                    if ($key == 'correo' && $value == 'Sin Correo') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'nombre_comisionador' && $value == 'Sin Comisionador') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'barrio' && $value == 'Sin Barrio') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'estrato_socioeconomico' && $value == 'Estrato No Clasificado') {
                        $cadenaSql .= "NULL,";
                    } else {

                        $cadenaSql .= "'" . $value . "',";

                    }

                }

                $cadenaSql .= ")RETURNING numero_contrato;";

                break;

            case 'registrarProceso':
                $cadenaSql = " INSERT INTO parametros.procesos_masivos(";
                $cadenaSql .= " descripcion,";
                $cadenaSql .= " estado,";
                $cadenaSql .= " nombre_archivo,";
                $cadenaSql .= " parametro_inicio,";
                $cadenaSql .= " parametro_fin,urbanizaciones)";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= " 'Contratos',";
                $cadenaSql .= " 'No Iniciado',";
                $cadenaSql .= " '" . $variable['nombre_contrato'] . "',";
                $cadenaSql .= " '" . $variable['contrato_inicio'] . "',";
                $cadenaSql .= " '" . $variable['contrato_final'] . "',";
                $cadenaSql .= " '" . $variable['urbanizaciones'] . "'";
                $cadenaSql .= " )RETURNING id_proceso;";
                break;

            case 'consultarProceso':
                $cadenaSql = " SELECT * ";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE descripcion='Contratos'";
                $cadenaSql .= " AND  estado_registro='TRUE' ";
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
                $cadenaSql .= " AND descripcion='Contratos'";
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
                $cadenaSql .= " nombre_ruta_archivo='" . $variable['nombre_archivo'] . "',";
                $cadenaSql .= " peso_archivo='" . $variable['tamanio_archivo'] . "'";
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

            case 'consultarEstadoProceso':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_proceso='" . $_REQUEST['id_proceso'] . "' ";
                $cadenaSql .= " AND estado IN ('No Iniciado','Finalizado'); ";
                break;

            case 'eliminarProceso':
                $cadenaSql = " UPDATE parametros.procesos_masivos";
                $cadenaSql .= " SET estado_registro='FALSE'";
                $cadenaSql .= " WHERE id_proceso='" . $_REQUEST['id_proceso'] . "'; ";
                break;

        }

        return $cadenaSql;
    }
}
?>

