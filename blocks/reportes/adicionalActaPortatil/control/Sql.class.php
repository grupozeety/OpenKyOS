<?php

namespace reportes\adicionalActaPortatil;

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

            case 'consultaInformacionDocumentos':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM interoperacion.documentos_contrato";
                $cadenaSql .= " WHERE tipologia_documento='131'";
                $cadenaSql .= " AND estado_registro='TRUE';";
                break;

            case 'consultaInformacionBeneficiario':

                $cadenaSql = " SELECT cn.*, bn.tipo_beneficiario,dp.departamento as nombre_departamento,mn.municipio as nombre_municipio, pm.proyecto as proyecto_urbanizacion ";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial bn ON bn.id_beneficiario=cn.id_beneficiario AND bn.estado_registro='TRUE'";
                $cadenaSql .= " JOIN parametros.departamento dp ON dp.codigo_dep=bn.departamento ";
                $cadenaSql .= " JOIN parametros.municipio mn ON mn.codigo_mun=bn.municipio ";
                $cadenaSql .= " JOIN parametros.proyectos_metas pm ON pm.id_proyecto=bn.id_proyecto ";
                $cadenaSql .= " WHERE cn.id_beneficiario='" . $variable . "' ";
                $cadenaSql .= " AND cn.estado_registro=TRUE;";

                break;

        }

        return $cadenaSql;
    }
}
