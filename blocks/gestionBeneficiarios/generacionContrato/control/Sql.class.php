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
                $cadenaSql = " SELECT DISTINCT identificacion ||' - ('||nombre||' '||primer_apellido||' '||segundo_apellido||')' AS  value, id_beneficiario  AS data  ";
                $cadenaSql .= " FROM  interoperacion.beneficiario_potencial ";
                $cadenaSql .= "WHERE estado_registro=TRUE ";
                $cadenaSql .= "AND  cast(identificacion  as text) ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "OR nombre ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "OR primer_apellido ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "OR segundo_apellido ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

            case 'consultaInformacionBeneficiario':
                $cadenaSql = " SELECT bn.*,pr.descripcion as descripcion_tipo , cn.id id_contrato, cn.numero_contrato ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bn ";
                $cadenaSql .= " JOIN parametros.parametros pr ON pr.codigo= bn.tipo_beneficiario::text ";
                $cadenaSql .= "JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro= pr.rel_parametro AND rl.descripcion='Tipo de Beneficario o Cliente' ";
                $cadenaSql .= " LEFT JOIN interoperacion.contrato cn ON cn.id_beneficiario= bn.id_beneficiario AND cn.estado_registro=TRUE ";
                $cadenaSql .= " WHERE bn.estado_registro = TRUE ";
                $cadenaSql .= " AND pr.estado_registro = TRUE ";
                $cadenaSql .= " AND bn.id_beneficiario= '" . $_REQUEST['id_beneficiario'] . "';";
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

            case 'consultaRequisitosVerificados':
                $cadenaSql = " SELECT cd.id,";
                $cadenaSql .= " cd.id_beneficiario,";
                $cadenaSql .= " cd.tipologia_documento,";
                $cadenaSql .= " cd.nombre_documento,";
                $cadenaSql .= " cd.ruta_relativa, ";
                $cadenaSql .= " pr.descripcion tipo_requisito,";
                $cadenaSql .= " pr.codigo codigo_requisito";
                $cadenaSql .= " FROM interoperacion.documentos_contrato cd";
                $cadenaSql .= " JOIN parametros.parametros pr ON pr.id_parametro=cd.tipologia_documento";
                $cadenaSql .= " WHERE cd.estado_registro=TRUE";
                $cadenaSql .= " AND pr.estado_registro=TRUE";
                $cadenaSql .= " AND pr.estado_registro=TRUE";
                $cadenaSql .= " AND cd.id_beneficiario = '" . $_REQUEST['id_beneficiario'] . "'";
                break;

            case 'consultarNumeralesContrato':
                $cadenaSql = " SELECT pr.id_parametro, pr.descripcion ";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Numerales Contrato'";
                $cadenaSql .= " AND rl.estado_registro=TRUE ";
                break;
            case 'consultarClausulas':
                $cadenaSql = " SELECT numeral,orden_general, contenido";
                $cadenaSql .= " FROM interoperacion.clausulas_contrato";
                $cadenaSql .= " WHERE estado_registro=TRUE ";
                $cadenaSql .= " AND numeral= '" . $variable . "'";
                $cadenaSql .= " ORDER BY orden ASC ;";
                break;

            case 'obtenerDatosBasicosBeneficiarios':
                $cadenaSql = " SELECT bn.*,pr.descripcion as descripcion_tipo , cn.id id_contrato, cn.numero_contrato,dp.departamento nombre_departamento,mn.municipio nombre_municipio  ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bn ";
                $cadenaSql .= " JOIN parametros.parametros pr ON pr.codigo= bn.tipo_beneficiario::text ";
                $cadenaSql .= " LEFT JOIN parametros.departamento dp ON dp.codigo_dep= bn.departamento";
                $cadenaSql .= " LEFT JOIN parametros.municipio mn ON mn.codigo_mun= bn.municipio";
                $cadenaSql .= " LEFT JOIN interoperacion.contrato cn ON cn.id_beneficiario= bn.id_beneficiario AND cn.estado_registro=TRUE ";
                $cadenaSql .= " WHERE bn.estado_registro = TRUE ";
                $cadenaSql .= " AND pr.estado_registro = TRUE ";
                $cadenaSql .= " AND bn.id_beneficiario= '" . $_REQUEST['id_beneficiario'] . "';";
                break;

            case 'consultarParametro':
                $cadenaSql = " SELECT pr.id_parametro, pr.descripcion ";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Tipologia Archivo'";
                $cadenaSql .= " AND pr.codigo='" . $variable . "' ";
                $cadenaSql .= " AND rl.estado_registro=TRUE ";

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

            case 'consultarDepartamento':
                $cadenaSql = " SELECT codigo_dep, departamento";
                $cadenaSql .= " FROM parametros.departamento;";
                break;

            case 'consultarMunicipio':
                $cadenaSql = " SELECT codigo_mun, municipio";
                $cadenaSql .= " FROM parametros.municipio;";
                break;

            case 'consultarProyectos':
                $cadenaSql = " SELECT id as id,name as nombre";
                $cadenaSql .= " FROM public.projects";
                $cadenaSql .= " WHERE description LIKE '%(Proyecto/Urbanizacion)%';";
                break;

        }

        return $cadenaSql;
    }
}
?>

