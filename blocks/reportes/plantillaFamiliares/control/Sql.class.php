<?php

namespace reportes\plantillaFamiliares;

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

            case 'consultarDuplicidadFamiliar':
                $cadenaSql = " SELECT bp.identificacion as identificacion_beneficiario ,fm.id_beneficiario, fm.identificacion_familiar ";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial fm";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial bp ON bp.id_beneficiario=fm.id_beneficiario AND bp.estado_registro='TRUE'";
                $cadenaSql .= " WHERE fm.estado_registro='TRUE'";
                $cadenaSql .= " AND fm.identificacion_familiar ='" . $variable . "';";
                break;

            case 'consultarExistenciaFamiliar':
                $cadenaSql = " SELECT bp.identificacion as identificacion_beneficiario ,fm.id_beneficiario, fm.identificacion_familiar ";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial fm";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial bp ON bp.id_beneficiario=fm.id_beneficiario AND bp.estado_registro='TRUE'";
                $cadenaSql .= " WHERE fm.estado_registro='TRUE'";
                $cadenaSql .= " AND fm.identificacion_familiar ='" . $variable['identificacion_familiar'] . "' ";
                $cadenaSql .= " AND bp.identificacion ='" . $variable['identificacion_beneficiario'] . "';";
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

            case "codificacion":
                $cadenaSql = "SELECT mc.abreviatura AS abr_mun, ur.abreviatura AS  abr_urb, ur.abreviatura_benef AS abr_benf ";
                $cadenaSql .= "FROM parametros.urbanizacion ur join parametros.municipio mc ON  ur.codigo_mun=mc.codigo_mun ";
                $cadenaSql .= "WHERE id_urbanizacion='" . $variable . "'";
                break;

            case 'registroFamiliares':

                if ($_REQUEST['funcionalidad'] == '3') {
                    $cadenaSql = " UPDATE interoperacion.familiar_beneficiario_potencial";
                    $cadenaSql .= " SET estado_registro='FALSE'";
                    $cadenaSql .= " WHERE id_beneficiario='" . $variable['id_beneficiario'] . "' ";
                    $cadenaSql .= " AND identificacion_familiar='" . $variable['identificacion_familiar'] . "';";
                    $cadenaSql .= " INSERT INTO interoperacion.familiar_beneficiario_potencial(";
                } else {
                    $cadenaSql = " INSERT INTO interoperacion.familiar_beneficiario_potencial(";
                }

                $cadenaSql .= " id_beneficiario, ";
                $cadenaSql .= " tipo_documento, ";
                $cadenaSql .= " identificacion_familiar, ";
                $cadenaSql .= " nombre_familiar, ";
                $cadenaSql .= " primer_apellido_familiar, ";
                $cadenaSql .= " segundo_apellido_familiar, ";
                $cadenaSql .= " parentesco, ";
                $cadenaSql .= " genero_familiar, ";
                $cadenaSql .= " edad_familiar, ";
                $cadenaSql .= " celular_familiar, ";
                $cadenaSql .= " nivel_estudio_familiar,";
                $cadenaSql .= " correo_familiar,";
                $cadenaSql .= " pertenencia_etnica_familiar, ";
                $cadenaSql .= " institucion_educativa_familiar, ";
                $cadenaSql .= " ocupacion_familiar)";
                $cadenaSql .= " VALUES (";
                foreach ($variable as $key => $value) {

                    if ($key == 'segundo_apellido_familiar' && $value == 'Sin Segundo Apellido') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'parentesco' && $value == 0) {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'genero_familiar' && $value == 0) {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'edad_familiar' && $value == 0) {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'celular_familiar' && $value == 0) {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'nivel_estudio_familiar' && $value == 0) {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'correo_familiar' && $value == 'Sin Correo') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'pertenencia_etnica_familiar' && $value == 0) {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'institucion_educativa_familiar' && $value == 'Sin Institucion') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'ocupacion_familiar' && $value == 0) {
                        $cadenaSql .= "NULL,";
                    } else {

                        $cadenaSql .= "'" . $value . "',";

                    }

                }

                $cadenaSql .= ")RETURNING id_beneficiario;";
                break;

        }

        return $cadenaSql;
    }
}
?>

