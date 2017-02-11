<?php

namespace gestionNotificacionesCorreo;

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

            case 'consultarProyectos':
                $cadenaSql = " SELECT id_proyecto, proyecto, meta, num_beneficiarios";
                $cadenaSql .= " FROM parametros.proyectos_metas ";
                $cadenaSql .= " ORDER BY meta;";
                break;

            case 'cantidadBeneficiarios':
                $cadenaSql = " SELECT count(id_beneficiario) cant_beneficiarios";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_proyecto='" . $variable . "';";
                break;

            case 'cantidadSinContrato':
                $cadenaSql = " SELECT count(bp.id_beneficiario) cant_beneficiarios";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.contrato cn ON cn.id_beneficiario=bp.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.id_beneficiario IS NULL";
                $cadenaSql .= " AND bp.id_proyecto='" . $variable . "';";
                break;

            case 'cantidadSinFamiliares':
                $cadenaSql = " SELECT count(bp.id_beneficiario) cant_beneficiarios";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.familiar_beneficiario_potencial fm ON fm.id_beneficiario=bp.id_beneficiario AND fm.estado_registro='TRUE'";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE'";
                $cadenaSql .= " AND fm.id_beneficiario IS NULL";
                $cadenaSql .= " AND bp.id_proyecto='" . $variable . "';";
                break;

            case 'cantidadSinFamiliaresActualizados':
                $cadenaSql = " SELECT count(bp.id_beneficiario) cant_beneficiarios";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.familiar_beneficiario_potencial fm ON fm.id_beneficiario=bp.id_beneficiario AND fm.estado_registro='TRUE'";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE'";
                $cadenaSql .= " AND fm.id_beneficiario IS NOT NULL";
                $cadenaSql .= " AND fm.edad_familiar IS NULL";
                $cadenaSql .= " AND fm.parentesco IS NULL";
                $cadenaSql .= " AND bp.id_proyecto='" . $variable . "';";
                break;

            case 'cantidadSinActaPortatil':
                $cadenaSql = " SELECT count(bp.id_beneficiario) cant_beneficiarios";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_portatil ac ON ac.id_beneficiario=bp.id_beneficiario AND ac.estado_registro='TRUE'";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE'";
                $cadenaSql .= " AND ac.id_beneficiario IS NULL";
                $cadenaSql .= " AND bp.id_proyecto='" . $variable . "';";
                break;

            case 'cantidadSinActaServicios':
                $cadenaSql = " SELECT count(bp.id_beneficiario) cant_beneficiarios";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_servicios acs ON acs.id_beneficiario=bp.id_beneficiario AND acs.estado_registro='TRUE'";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE'";
                $cadenaSql .= " AND acs.id_beneficiario IS NULL";
                $cadenaSql .= " AND bp.id_proyecto='" . $variable . "';";
                break;

            case 'cantidadSinPortatilAsociado':
                $cadenaSql = " SELECT count(bp.id_beneficiario) cant_beneficiarios";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.contrato cn ON cn.id_beneficiario=bp.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_portatil ac ON ac.id_beneficiario=bp.id_beneficiario AND ac.estado_registro='TRUE'";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.id_beneficiario IS NOT NULL";
                $cadenaSql .= " AND ac.serial IS NULL";
                $cadenaSql .= " AND bp.id_proyecto='" . $variable . "';";
                break;

            case 'cantidadSinEsclavoAsociado':
                $cadenaSql = " SELECT count(bp.id_beneficiario) cant_beneficiarios";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.contrato cn ON cn.id_beneficiario=bp.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_servicios acs ON acs.id_beneficiario=bp.id_beneficiario AND acs.estado_registro='TRUE'";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.id_beneficiario IS NOT NULL";
                $cadenaSql .= " AND acs.mac_esc IS NULL";
                $cadenaSql .= " AND bp.id_proyecto='" . $variable . "';";
                break;

            case 'cantidadBeneficiariariosDocumentosContratacion':
                $cadenaSql = " SELECT COUNT(beneficiarios) as cant_beneficiarios";
                $cadenaSql .= " FROM (";
                $cadenaSql .= " SELECT DISTINCT (bp.id_beneficiario) beneficiarios, dc.id_beneficiario documentos";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.documentos_contrato dc ON dc.id_beneficiario=bp.id_beneficiario AND dc.estado_registro='TRUE'";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE'";
                $cadenaSql .= " AND dc.id_beneficiario IS NOT NULL";
                $cadenaSql .= " AND dc.tipologia_documento IN ('99','100','101','102','103','104','105','124','130')";
                $cadenaSql .= " AND bp.id_proyecto='" . $variable . "' ";
                $cadenaSql .= " ) as documentos;";
                break;

            case 'cantidadBeneficiariariosDocumentosComisionamiento':
                $cadenaSql = " SELECT COUNT(beneficiarios) as cant_beneficiarios";
                $cadenaSql .= " FROM (";
                $cadenaSql .= " SELECT DISTINCT (bp.id_beneficiario) beneficiarios, dc.id_beneficiario documentos";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.documentos_contrato dc ON dc.id_beneficiario=bp.id_beneficiario AND dc.estado_registro='TRUE'";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE'";
                $cadenaSql .= " AND dc.id_beneficiario IS NOT NULL";
                $cadenaSql .= " AND dc.tipologia_documento IN ('131','132','133','134','135','136','137','138','139','140','141','142')";
                $cadenaSql .= " AND bp.id_proyecto='" . $variable . "' ";
                $cadenaSql .= " ) as documentos;";
                break;

            case 'cantidadSinInformacionTecnica':
                $cadenaSql = " SELECT count(bp.id_beneficiario) cant_beneficiarios";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_servicios acs ON acs.id_beneficiario=bp.id_beneficiario AND acs.estado_registro='TRUE'";
                $cadenaSql .= " LEFT JOIN interoperacion.nodo nd ON nd.macesclavo1=acs.mac_esc AND nd.estado_registro='TRUE'";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE'";
                $cadenaSql .= " AND acs.id_beneficiario IS NOT NULL";
                $cadenaSql .= " AND acs.mac_esc IS NOT NULL";
                $cadenaSql .= " AND nd.macesclavo1 IS NULL";
                $cadenaSql .= " AND bp.id_proyecto='" . $variable . "';";

                break;

            case 'cantidadSinPruebasAsociadas':
                $cadenaSql = " SELECT count(bp.id_beneficiario) cant_beneficiarios";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_servicios acs ON acs.id_beneficiario=bp.id_beneficiario AND acs.estado_registro='TRUE'";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE'";
                $cadenaSql .= " AND acs.id_beneficiario IS NOT NULL";
                $cadenaSql .= " AND  acs.resultado_vs IS NULL";
                $cadenaSql .= " AND  acs.resultado_vb IS NULL";
                $cadenaSql .= " AND bp.id_proyecto='" . $variable . "';";

                break;

            case 'consultarInformacionApi':
                $cadenaSql = " SELECT componente, host, usuario, password, token_codificado, ruta_cookie ";
                $cadenaSql .= " FROM parametros.api_data";
                $cadenaSql .= " WHERE componente ='" . $variable . "';";
                break;

            //-------------------------------------------

            case 'consultarInformacionApi':
                $cadenaSql = " SELECT componente, host, usuario, password, ruta_cookie ";
                $cadenaSql .= " FROM parametros.api_data";
                $cadenaSql .= " WHERE componente ='" . $variable . "';";
                break;

            case "rol":
                $cadenaSql = "SELECT        ";
                $cadenaSql .= "rol, ";
                $cadenaSql .= "descripcion ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "gestion_menu.rol";
                break;

        }

        return $cadenaSql;
    }
}
