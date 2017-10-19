<?php

namespace gestionBeneficiarios\gestionFirmaDocumentos;

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
                $cadenaSql = " SELECT value , data ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "(SELECT DISTINCT bp.identificacion ||' - ('||bp.nombre||' '||bp.primer_apellido||' '||bp.segundo_apellido||')' AS  value, bp.id_beneficiario  AS data ";
                $cadenaSql .= " FROM  interoperacion.beneficiario_potencial bp ";
                $cadenaSql .= " LEFT JOIN interoperacion.agendamiento_comisionamiento ac on ac.id_beneficiario=bp.id_beneficiario ";
                $cadenaSql .= " JOIN interoperacion.beneficiario_alfresco ba ON bp.id_beneficiario=ba.id_beneficiario ";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=bp.id_beneficiario ";
                $cadenaSql .= " WHERE bp.estado_registro=TRUE ";
                $cadenaSql .= " AND ba.estado_registro=TRUE ";
                $cadenaSql .= " AND ba.carpeta_creada=TRUE ";
                $cadenaSql .= $variable;
                $cadenaSql .= "     ) datos ";
                $cadenaSql .= "WHERE value ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

            case 'gestionarFirma':
                $cadenaSql = " UPDATE interoperacion.firma_beneficiario";
                $cadenaSql .= " SET estado_registro=FALSE";
                $cadenaSql .= " WHERE id_beneficiario='" . $variable['id_beneficiario'] . "';";
                $cadenaSql .= " INSERT INTO interoperacion.firma_beneficiario(id_beneficiario, nombre_archivo, ruta_archivo)";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= " '" . $variable['id_beneficiario'] . "',";
                $cadenaSql .= " '" . $variable['nombre_archivo'] . "',";
                $cadenaSql .= " '" . $variable['ruta_archivo'] . "');";
                break;

            case 'consultarFirma':
                $cadenaSql = " SELECT nombre_archivo, ruta_archivo";
                $cadenaSql .= " FROM interoperacion.firma_beneficiario";
                $cadenaSql .= " WHERE estado_registro = TRUE";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "';";
                break;

        }

        return $cadenaSql;
    }
}
