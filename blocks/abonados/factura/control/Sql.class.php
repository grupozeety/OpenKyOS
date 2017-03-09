<?php

namespace cambioClave;

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

            case 'consultarBeneficiario':
                $cadenaSql = " SELECT ";
                $cadenaSql .= " id_beneficiario, ";
                $cadenaSql .= " identificacion, ";
                $cadenaSql .= " nombre, ";
                $cadenaSql .= " primer_apellido, ";
                $cadenaSql .= " segundo_apellido";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND identificacion='" . $info_usuario['uid'][0] . "';";
                break;

            case 'consultarContrato':
                $cadenaSql = " SELECT nombre_documento_contrato, ruta_documento_contrato ";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE numero_identificacion ='" . $info_usuario['uid'][0] . "';";
                break;

            case 'consultaInformacionFacturacion':
                $cadenaSql = " SELECT ";
                $cadenaSql .= " fc.estado_factura,";
                $cadenaSql .= " to_char(fc.fecha_registro, 'YYYY-MM-DD')as fecha_factura,";
                $cadenaSql .= " fc.total_factura,";
                $cadenaSql .= " fc.id_ciclo";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " JOIN facturacion.factura fc ON fc.id_beneficiario=cn.id_beneficiario AND fc.estado_registro='TRUE'";
                $cadenaSql .= " WHERE cn.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.id_beneficiario='" . $variable . "' ";
                $cadenaSql .= " AND fc.estado_factura='Aprobado' ";
                $cadenaSql .= " ORDER BY fc.fecha_registro DESC;";

                break;
        }

        return $cadenaSql;
    }
}
