<?php

namespace facturacion\impresionFactura;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";
include_once "core/auth/SesionSso.class.php";

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql
{
    public $miConfigurador;
    public $miSesionSso;
    public function __construct()
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->miSesionSso = \SesionSso::singleton();
    }
    public function getCadenaSql($tipo, $variable = '')
    {
        $info_usuario = $this->miSesionSso->getParametrosSesionAbierta();

        foreach ($info_usuario['description'] as $key => $rol) {

            $info_usuario['rol'][] = $rol;
        }

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

            case 'consultarBeneficiario':
                $cadenaSql = " SELECT";
                $cadenaSql .= " cn.nombres||' '||cn.primer_apellido||' '||(CASE WHEN cn.segundo_apellido IS NOT NULL THEN cn.segundo_apellido ELSE '' END) as nombre_beneficiario,";
                $cadenaSql .= " cn.numero_identificacion,";
                $cadenaSql .= " cn.direccion_domicilio||";
                $cadenaSql .= " (CASE WHEN cn.manzana <> '0' THEN ' Manzana # '||cn.manzana ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.torre <> '0' THEN ' Torre # '||cn.manzana ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.casa_apartamento <>'0' THEN ' Casa/Apartamento # '||cn.casa_apartamento ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.interior <>'0' THEN ' Interior # '||cn.interior ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.lote <>'0' THEN ' Lote # '||cn.lote ELSE '' END)||";
                $cadenaSql .= " (CASE WHEN cn.barrio IS NOT NULL THEN ' Barrio '||cn.barrio ELSE '' END)as direccion_beneficiario,";
                $cadenaSql .= " cn.municipio,";
                $cadenaSql .= " cn.departamento,";
                $cadenaSql .= " (CASE WHEN cn.estrato_socioeconomico::text IS NULL THEN 'No Caracterizado' ELSE cn.estrato_socioeconomico::text END) as estrato";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " WHERE cn.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.id_beneficiario='" . $variable . "';";

                break;

        }

        return $cadenaSql;
    }
}
