<?php
namespace reportes\certificadoNoInternet;
if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";
include_once "core/auth/SesionSso.class.php";

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
    public $miConfigurador;
    public $miSesionSso;
    public function __construct() {
        $this->miConfigurador = \Configurador::singleton();
        $this->miSesionSso = \SesionSso::singleton();
    }
    public function getCadenaSql($tipo, $variable = '') {

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

            case 'consultaInformacionBeneficiario':
                $cadenaSql = " SELECT bn.*,pr.descripcion as descripcion_tipo , cn.id id_contrato, cn.numero_contrato  ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bn ";
                $cadenaSql .= " JOIN parametros.parametros pr ON pr.codigo= bn.tipo_beneficiario::text ";
                $cadenaSql .= "JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro= pr.rel_parametro AND rl.descripcion='Tipo de Beneficario o Cliente' ";
                $cadenaSql .= " LEFT JOIN interoperacion.contrato cn ON cn.id_beneficiario= bn.id_beneficiario AND cn.estado_registro=TRUE ";
                $cadenaSql .= " WHERE bn.estado_registro = TRUE ";
                $cadenaSql .= " AND pr.estado_registro = TRUE ";
                $cadenaSql .= " AND bn.id_beneficiario= '" . $_REQUEST['id'] . "';";
                break;

            case 'consultarBeneficiariosPotenciales':
        		$cadenaSql = " SELECT value , data ";
				$cadenaSql .= "FROM ";
				$cadenaSql .= "(SELECT DISTINCT identificacion ||' - ('||nombre||' '||primer_apellido||' '||segundo_apellido||')' AS  value, bp.id_beneficiario  AS data ";
				$cadenaSql .= " FROM  interoperacion.beneficiario_potencial bp ";
				$cadenaSql .= " LEFT JOIN interoperacion.agendamiento_comisionamiento ac on ac.id_beneficiario=bp.id_beneficiario ";
				$cadenaSql .= " JOIN interoperacion.beneficiario_alfresco ba ON bp.id_beneficiario=ba.id_beneficiario ";
				$cadenaSql .= " WHERE bp.estado_registro=TRUE ";
				$cadenaSql .= " AND ba.estado_registro=TRUE ";
				$cadenaSql .= " AND ba.carpeta_creada=TRUE ";
				$cadenaSql .= $variable;
				$cadenaSql .= "		) datos ";
				$cadenaSql .= "WHERE value ILIKE '%" . $_GET ['query'] . "%' ";
				$cadenaSql .= "LIMIT 10; ";
                break;

            case 'registrarCertificacion':
                $cadenaSql = " UPDATE interoperacion.certificacion_no_internet";
                $cadenaSql .= " SET estado_registro='FALSE'";
                $cadenaSql .= " WHERE id_beneficiario='" . $variable['id_beneficiario'] . "';";
                $cadenaSql .= " INSERT INTO interoperacion.certificacion_no_internet(";
                $cadenaSql .= " id_beneficiario,";
                $cadenaSql .= " nombre,";
                $cadenaSql .= " primer_apellido,";
                $cadenaSql .= " segundo_apellido,";
                $cadenaSql .= " identificacion, ";
                $cadenaSql .= " celular,";
                $cadenaSql .= " ciudad_expedicion_identificacion,";
                $cadenaSql .= " ciudad_firma,";
                $cadenaSql .= " ruta_firma)";
                $cadenaSql .= " VALUES ('" . $variable['id_beneficiario'] . "',";
                $cadenaSql .= " '" . $variable['nombres'] . "',";
                $cadenaSql .= " '" . $variable['primer_apellido'] . "',";
                $cadenaSql .= " '" . $variable['segundo_apellido'] . "',";
                $cadenaSql .= " '" . $variable['identificacion'] . "',";
                $cadenaSql .= " '" . $variable['celular'] . "', ";
                $cadenaSql .= " '" . $variable['ciudad_expedicion_identificacion'] . "',";
                $cadenaSql .= " '" . $variable['ciudad_firma'] . "',";
                $cadenaSql .= " '" . $variable['ruta_firma'] . "');";
                break;

            case 'consultarParametro':
                $cadenaSql = " SELECT pr.id_parametro, pr.descripcion, pr.codigo ";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Tipologia Archivo'";
                $cadenaSql .= " AND pr.codigo='" . $variable . "' ";
                $cadenaSql .= " AND rl.estado_registro=TRUE ";

                break;

            case 'registrarDocumentoCertificado':
                $cadenaSql = " UPDATE interoperacion.certificacion_no_internet";
                $cadenaSql .= " SET nombre_documento='" . $variable['nombre_contrato'] . "', ruta_documento='" . $variable['ruta_contrato'] . "' ";
                $cadenaSql .= " WHERE id_beneficiario='" . $_REQUEST['id_beneficiario'] . "' AND estado_registro='TRUE';";
                break;

            case 'consultaInformacionCertificado':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM interoperacion.certificacion_no_internet";
                $cadenaSql .= " WHERE id_beneficiario ='" . $_REQUEST['id_beneficiario'] . "'";
                $cadenaSql .= " AND estado_registro='TRUE';";
                break;

            case 'registrarRequisito':
                $cadenaSql = " INSERT INTO interoperacion.documentos_contrato(";
                $cadenaSql .= " id_beneficiario, ";
                $cadenaSql .= " tipologia_documento,";
                $cadenaSql .= " nombre_documento, ";
                $cadenaSql .= " ruta_relativa, ";
                $cadenaSql .= " usuario)";
                $cadenaSql .= " VALUES ('" . $variable['id_beneficiario'] . "',";
                $cadenaSql .= " '" . $variable['tipologia'] . "',";
                $cadenaSql .= " '" . $variable['nombre_documento'] . "',";
                $cadenaSql .= " '" . $variable['ruta_relativa'] . "',";
                $cadenaSql .= " '" . $info_usuario['uid'][0] . "');";

                break;
        }

        return $cadenaSql;
    }
}
?>

