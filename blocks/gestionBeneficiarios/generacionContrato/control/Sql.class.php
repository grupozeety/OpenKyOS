<?php
namespace gestionBeneficiarios\generacionContrato;
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
            case 'consultarBeneficiariosPotenciales':
                $cadenaSql = " SELECT identificacion ||' - ('||nombre||')' AS  value, id  AS data  ";
                $cadenaSql .= " FROM  interoperacion.beneficiario_potencial ";
                $cadenaSql .= "WHERE estado_registro=TRUE ";
                $cadenaSql .= "AND  cast(identificacion  as text) ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "OR nombre ILIKE '%" . $_GET['query'] . "%' LIMIT 10; ";

                break;
            case 'consultaInformacionBeneficiario':
                $cadenaSql = " SELECT bn.*,pr.descripcion as descripcion_tipo  ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bn ";
                $cadenaSql .= " JOIN parametros.parametros pr ON pr.id_parametro= bn.tipo";
                $cadenaSql .= " WHERE bn.estado_registro = TRUE ";
                $cadenaSql .= " AND pr.estado_registro = TRUE ";
                $cadenaSql .= " AND id= '" . $_REQUEST['id_beneficiario'] . "';";
                break;

            case 'registrarDocumentos':
                $cadenaSql = " INSERT INTO interoperacion.documentos_contrato(";
                $cadenaSql .= " id_beneficiario, ";
                $cadenaSql .= " tipologia_documento,";
                $cadenaSql .= " nombre_documento,";
                $cadenaSql .= " ruta_relativa,";
                $cadenaSql .= " usuario )";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= " '" . $_REQUEST['id_beneficiario'] . "',";
                $cadenaSql .= " '" . $variable['tipo_documento'] . "',";
                $cadenaSql .= " '" . $variable['nombre_archivo'] . "',";
                $cadenaSql .= " '" . $variable['ruta_archivo'] . "',";
                $cadenaSql .= " '" . $info_usuario['uid'][0] . "' ";
                $cadenaSql .= " );";
                break;

            case 'registrarContrato':
                $cadenaSql = " INSERT INTO interoperacion.contrato(";
                $cadenaSql .= " id_beneficiario,";
                $cadenaSql .= " estado_contrato, ";
                $cadenaSql .= " usuario )";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= " '" . $_REQUEST['id_beneficiario'] . "',";
                $cadenaSql .= " (SELECT pr.id_parametro";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE pr.descripcion='Borrador'";
                $cadenaSql .= " AND pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Estado Contrato'";
                $cadenaSql .= " AND rl.estado_registro=TRUE ";
                $cadenaSql .= " ),";
                $cadenaSql .= " '" . $info_usuario['uid'][0] . "') ";
                $cadenaSql .= "  RETURNING contrato.id, contrato.numero_contrato;  ";
                break;

            case 'registrarServicio':
                $cadenaSql = " INSERT INTO interoperacion.servicio(";
                $cadenaSql .= " id_contrato, ";
                $cadenaSql .= " descripcion_servicio,";
                $cadenaSql .= " estado_servicio,";
                $cadenaSql .= " usuario)";
                $cadenaSql .= " VALUES ('" . $variable . "',";
                $cadenaSql .= " 'ServicioporDefinir',";
                $cadenaSql .= " (SELECT pr.id_parametro";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE pr.descripcion='Borrador'";
                $cadenaSql .= " AND pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Estado Servicio'";
                $cadenaSql .= " AND rl.estado_registro=TRUE ";
                $cadenaSql .= " ),";
                $cadenaSql .= " '" . $info_usuario['uid'][0] . "'); ";

                break;

        }

        return $cadenaSql;
    }
}
?>

