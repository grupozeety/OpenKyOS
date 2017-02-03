<?php

namespace gestionBeneficiarios\gestionEstadoBeneficiarios;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql
{
    public $miConfigurador;
    public function getCadenaSql($tipo, $variable = '')
    {

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
                $cadenaSql = " SELECT value,  data ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "(SELECT DISTINCT identificacion AS value, id_beneficiario AS data ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial  ";
                $cadenaSql .= " WHERE nombre IS NOT NULL";
                $cadenaSql.=" AND id_beneficiario IS NOT NULL";
                $cadenaSql.=" AND identificacion IS NOT NULL";
                $cadenaSql .= " ) datos ";
                $cadenaSql .= "WHERE value ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

            case 'consultarBeneficiarios':
                $cadenaSql=" SELECT id_beneficiario,";
                $cadenaSql.=" identificacion,";
                $cadenaSql.=" nombre||' '||primer_apellido||' '||(CASE WHEN segundo_apellido IS NOT NULL THEN segundo_apellido ELSE '' END) as nombre_beneficiario,";
                $cadenaSql.=" (CASE WHEN estado_registro ='false' THEN 'Inactivo' ELSE 'Activo' END) estado_beneficiario,";
                $cadenaSql.=" estado_registro";
                $cadenaSql.=" FROM interoperacion.beneficiario_potencial";
                $cadenaSql.=" WHERE nombre IS NOT NULL";
                $cadenaSql.=" AND id_beneficiario IS NOT NULL";
                $cadenaSql.=" AND identificacion IS NOT NULL;";
                break;

            // Consultas Particulares

            case 'consultaCantidadMujeresHogar':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND genero_familiar='1';";
                break;

            case 'consultaCantidadMasculinoHogar':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND genero_familiar='2';";
                break;

            case 'consultaCantidadMenores18':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND edad_familiar::int < 18 ;";
                break;

            case 'consultaCantidad18y25':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND edad_familiar::int >= 18 ";
                $cadenaSql .= " AND edad_familiar::int <= 25 ;";
                break;

            case 'consultaCantidad26y30':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND edad_familiar::int >= 26 ";
                $cadenaSql .= " AND edad_familiar::int <= 30 ;";
                break;

            case 'consultaCantidad31y40':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND edad_familiar::int >= 31 ";
                $cadenaSql .= " AND edad_familiar::int <= 40 ;";
                break;

            case 'consultaCantidad41y65':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND edad_familiar::int >= 41 ";
                $cadenaSql .= " AND edad_familiar::int <= 65 ;";
                break;

            case 'consultaCantidadMayor65':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND edad_familiar::int > 65 ;";
                break;

            case 'consultaCantidadEmpleado':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='1';";
                break;

            case 'consultaCantidadTrabajoInformal':    // Corregir
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='1';";
                break;

            case 'consultaCantidadEstudiante':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='4';";
                break;

            case 'consultaCantidadTrabajoIndependiente':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='13';";
                break;

            case 'consultaCantidadHogarDomestico':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='20';";
                break;

            case 'consultaCantidadHogarDomesticoCasa':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='14';";
                break;

            case 'consultaCantidadNoTrabaja':    //Corregir
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='14';";
                break;

            case 'consultaCantidadOtro':
                $cadenaSql = " SELECT COUNT(consecutivo)";
                $cadenaSql .= " FROM interoperacion.familiar_beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND ocupacion_familiar='30';";
                break;

            case 'verificarDocumentos':
                $cadenaSql = " SELECT count(*)";
                $cadenaSql .= " FROM";
                $cadenaSql .= " (";
                $cadenaSql .= " SELECT DISTINCT tipologia_documento";
                $cadenaSql .= " FROM interoperacion.documentos_requisitos dr";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " UNION";
                $cadenaSql .= " SELECT (CASE WHEN nombre_documento_contrato IS NULL THEN '0' ELSE '128' END)::int AS tipologia_documento";
                $cadenaSql .= " FROM interoperacion.contrato ";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND (CASE WHEN nombre_documento_contrato IS NULL THEN '0' ELSE '128' END)::int <> 0";
                $cadenaSql .= " AND supervisor='TRUE'";
                $cadenaSql .= " ) as requisitos";
                $cadenaSql .= " JOIN interoperacion.documentos_contrato dc ON dc.tipologia_documento=requisitos.tipologia_documento ";
                $cadenaSql .= " AND dc.estado_registro='TRUE'";
                $cadenaSql .= " AND dc.id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND dc.supervisor='TRUE'";
                break;
        }

        return $cadenaSql;
    }
}
