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

                $cadenaSql = " SELECT DISTINCT identificacion ||' - ('||nombre||' '||primer_apellido||' '||segundo_apellido||')' AS  value, bn.id_beneficiario  AS data  ";
                $cadenaSql .= " FROM  interoperacion.beneficiario_potencial bn ";
                $cadenaSql .= " JOIN interoperacion.beneficiario_alfresco ba ON bn.id_beneficiario=ba.id_beneficiario ";
                $cadenaSql .= "WHERE bn.estado_registro=TRUE ";
                $cadenaSql .= "AND ba.estado_registro=TRUE  ";
                $cadenaSql .= "AND ba.carpeta_creada=TRUE ";
                $cadenaSql .= "AND  (cast(identificacion  as text) ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "OR nombre ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "OR primer_apellido ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "OR segundo_apellido ILIKE '%" . $_GET['query'] . "%') ";
                $cadenaSql .= "LIMIT 10; ";

                break;

            case 'consultaInformacionBeneficiario':
                $cadenaSql = " SELECT bn.*,pr.descripcion as descripcion_tipo , cn.id id_contrato, cn.numero_contrato  ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bn ";
                $cadenaSql .= " JOIN parametros.parametros pr ON pr.codigo= bn.tipo_beneficiario::text ";
                $cadenaSql .= "JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro= pr.rel_parametro AND rl.descripcion='Tipo de Beneficario o Cliente' ";
                $cadenaSql .= " LEFT JOIN interoperacion.contrato cn ON cn.id_beneficiario= bn.id_beneficiario AND cn.estado_registro=TRUE ";
                $cadenaSql .= " WHERE bn.estado_registro = TRUE ";
                $cadenaSql .= " AND pr.estado_registro = TRUE ";
                $cadenaSql .= " AND bn.id_beneficiario= '" . $_REQUEST['id_beneficiario'] . "';";
                break;

            case 'consultaInformacionAprobacion':
                $cadenaSql = " SELECT cd.id,";
                $cadenaSql .= " cd.id_beneficiario,";
                $cadenaSql .= " pr.codigo codigo_requisito,";
                $cadenaSql .= " cd.supervisor,";
                $cadenaSql .= " cd.comisionador,";
                $cadenaSql .= " cd.analista, cd.tipologia_documento, cd.ruta_relativa";
                $cadenaSql .= " FROM interoperacion.documentos_contrato cd";
                $cadenaSql .= " JOIN parametros.parametros pr ON pr.id_parametro=cd.tipologia_documento";
                $cadenaSql .= " WHERE cd.estado_registro=TRUE";
                $cadenaSql .= " AND pr.estado_registro=TRUE";
                $cadenaSql .= " AND pr.estado_registro=TRUE";
                $cadenaSql .= " AND cd.id_beneficiario = '" . $_REQUEST['id_beneficiario'] . "'";
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
            /**
             * ********************************************************************************
             */
            // Las siguientes son para incluir el contrato para ser gestionado por verificar
            case 'consultarContratoExistente':
                $cadenaSql = " SELECT id, id_beneficiario, tipologia_documento,nombre_documento, ruta_relativa, tipo_requisito, codigo_requisito ";
                $cadenaSql .= " FROM (SELECT";
                $cadenaSql .= " cn.id,";
                $cadenaSql .= " cn.id_beneficiario,";
                $cadenaSql .= " cn.nombre_documento_contrato as nombre_documento,";
                $cadenaSql .= " cn.ruta_documento_contrato as ruta_relativa";
                $cadenaSql .= " FROM interoperacion.contrato cn";
                $cadenaSql .= " WHERE cn.estado_registro=TRUE";
                $cadenaSql .= " AND cn.id_beneficiario='" . $_REQUEST['id_beneficiario'] . "') as table1,(SELECT";
                $cadenaSql .= " pr.descripcion tipo_requisito,";
                $cadenaSql .= " pr.codigo codigo_requisito,";
                $cadenaSql .= " pr.id_parametro as tipologia_documento";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " WHERE pr.estado_registro=TRUE";
                $cadenaSql .= " AND pr.id_parametro=128) as table2";
                break;

            case "consultaRequisitosContrato":
                $cadenaSql = "SELECT ";
                $cadenaSql .= " pr.descripcion, ";
                $cadenaSql .= " pr.codigo codigo, ";
                $cadenaSql .= " pr.id_parametro as tipologia_documento, ";
                $cadenaSql .= " 0 as obligatoriedad ";
                $cadenaSql .= " FROM parametros.parametros pr ";
                $cadenaSql .= " WHERE pr.estado_registro=TRUE ";
                $cadenaSql .= " AND pr.id_parametro=128";
                break;

            case 'consultaInformacionAprobacionContrato':
                $cadenaSql = " SELECT id, ";
                $cadenaSql .= " id_beneficiario,  ";
                $cadenaSql .= " codigo_requisito,  ";
                $cadenaSql .= " supervisor , ";
                $cadenaSql .= " comisionador, ";
                $cadenaSql .= " analista, ";
                $cadenaSql .= " tipologia_documento, ";
                $cadenaSql .= " ruta_relativa ";
                $cadenaSql .= " FROM (SELECT  ";
                $cadenaSql .= " cn.id, ";
                $cadenaSql .= " cn.id_beneficiario, ";
                $cadenaSql .= " cn.nombre_documento_contrato as nombre_documento, ";
                $cadenaSql .= " cn.ruta_documento_contrato as ruta_relativa, ";
                $cadenaSql .= " supervisor , ";
                $cadenaSql .= " comisionador, ";
                $cadenaSql .= " analista ";
                $cadenaSql .= " FROM interoperacion.contrato cn ";
                $cadenaSql .= " WHERE cn.estado_registro=TRUE AND ruta_documento_contrato!=NULL  ";
                $cadenaSql .= " AND cn.id_beneficiario='" . $_REQUEST['id_beneficiario'] . "') as table1,(SELECT ";
                $cadenaSql .= " pr.descripcion tipo_requisito, ";
                $cadenaSql .= " pr.codigo codigo_requisito, ";
                $cadenaSql .= " pr.id_parametro as tipologia_documento ";
                $cadenaSql .= " FROM parametros.parametros pr ";
                $cadenaSql .= " WHERE pr.estado_registro=TRUE ";
                $cadenaSql .= " AND pr.id_parametro=128) as table2";
                break;

            case "consultaRequisitosEspecificos":
                $cadenaSql = " SELECT tipologia_documento,codigo, descripcion,obligatoriedad ";
                $cadenaSql .= " FROM interoperacion.documentos_requisitos ";
                $cadenaSql .= " JOIN parametros.parametros ON id_parametro=tipologia_documento ";
                $cadenaSql .= " WHERE perfil=" . $variable['tipo'] . "";
                $cadenaSql .= " AND documentos_requisitos.estado_registro=TRUE";
                $cadenaSql .= " AND proceso=116";
                $cadenaSql .= " AND tipologia_documento='" . $variable['codigo'] . "'";
                break;

            /**
             * ********************************************************************************
             */

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
                $cadenaSql = " SELECT pr.id_parametro, pr.descripcion, pr.codigo ";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Tipologia Archivo'";
                $cadenaSql .= " AND pr.codigo='" . $variable . "' ";
                $cadenaSql .= " AND rl.estado_registro=TRUE ";

                break;

            case 'consultarParametroContrato':
                $cadenaSql = " SELECT pr.id_parametro, pr.descripcion, pr.codigo ";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Carpeta Contrato'";
                $cadenaSql .= " AND pr.codigo='" . $variable . "' ";
                $cadenaSql .= " AND rl.estado_registro=TRUE ";
                break;

            case 'consultarParametroCodigo':
                $cadenaSql = " SELECT pr.id_parametro, pr.codigo ";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Tipologia Archivo'";
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

            case 'registrarInformacionContrato':
                $cadenaSql = " UPDATE interoperacion.contrato";
                $cadenaSql .= " SET ";
                $cadenaSql .= " nombres='" . $variable['nombres'] . "',";
                $cadenaSql .= " primer_apellido='" . $variable['primer_apellido'] . "', ";
                $cadenaSql .= " segundo_apellido='" . $variable['segundo_apellido'] . "',";
                $cadenaSql .= " tipo_documento='" . $variable['tipo_documento'] . "',";
                $cadenaSql .= " numero_identificacion='" . $variable['numero_identificacion'] . "', ";
                $cadenaSql .= " fecha_expedicion='" . $variable['fecha_expedicion'] . "', ";
                $cadenaSql .= " direccion_domicilio='" . $variable['direccion_domicilio'] . "',";
            /* $cadenaSql .= " direccion_instalacion='" . $variable['direccion_instalacion'] . "', "; */
                $cadenaSql .= " departamento='" . $variable['departamento'] . "',";
                $cadenaSql .= " municipio='" . $variable['municipio'] . "', ";
                $cadenaSql .= " urbanizacion='" . $variable['urbanizacion'] . "', ";
                $cadenaSql .= " estrato='" . $variable['estrato'] . "', ";
            /*$cadenaSql .= " barrio='" . $variable['barrio'] . "', ";*/
                $cadenaSql .= " telefono='" . $variable['telefono'] . "',";
                $cadenaSql .= " celular='" . $variable['celular'] . "',";
                $cadenaSql .= " correo='" . $variable['correo'] . "',";
                $cadenaSql .= " manzana='" . $variable['manzana'] . "',";
                $cadenaSql .= " bloque='" . $variable['bloque'] . "',";
                $cadenaSql .= " torre='" . $variable['torre'] . "',";
                $cadenaSql .= " casa_apartamento='" . $variable['casa_apartamento'] . "',";
                $cadenaSql .= " tipo_tecnologia='" . $variable['tipo_tecnologia'] . "',";
                $cadenaSql .= " valor_tarificacion='" . $variable['valor_tarificacion'] . "',";
                $cadenaSql .= " medio_pago='" . $variable['medio_pago'] . "',";
                /*
             * $cadenaSql .= " cuenta_suscriptor='" . $variable ['cuenta_suscriptor'] . "', ";
             * $cadenaSql .= " velocidad_internet='" . $variable ['velocidad_internet'] . "', ";
             * $cadenaSql .= " fecha_inicio_vigencia_servicio='" . $variable ['fecha_inicio_vigencia_servicio'] . "',";
             * $cadenaSql .= " fecha_fin_vigencia_servicio='" . $variable ['fecha_fin_vigencia_servicio'] . "', ";
             * $cadenaSql .= " valor_mensual='" . $variable ['valor_mensual'] . "',";
             * $cadenaSql .= " marca='" . $variable ['marca'] . "',";
             * $cadenaSql .= " modelo='" . $variable ['modelo'] . "',";
             * $cadenaSql .= " serial='" . $variable ['serial'] . "', ";
             * $cadenaSql .= " tecnologia='" . $variable ['tecnologia'] . "',";
             * $cadenaSql .= " estado='" . $variable ['estado'] . "', ";
             */
                $cadenaSql .= " clausulas='" . $variable['clausulas'] . "', ";
                $cadenaSql .= " url_firma_beneficiarios='" . $variable['url_firma_beneficiario'] . "' ";
                //$cadenaSql .= " url_firma_contratista='" . $variable['url_firma_contratista'] . "' ";
                $cadenaSql .= " WHERE id_beneficiario='" . $_REQUEST['id_beneficiario'] . "' ";
                $cadenaSql .= " AND numero_contrato='" . $_REQUEST['numero_contrato'] . "' ";
                $cadenaSql .= " AND estado_registro=TRUE;";
                break;

            case 'consultaInformacionContrato':

                $cadenaSql = " SELECT bn.*, dp.departamento nombre_departamento,mn.municipio nombre_municipio   ";
                $cadenaSql .= " FROM interoperacion.contrato bn";
                $cadenaSql .= " LEFT JOIN parametros.departamento dp ON dp.codigo_dep= bn.departamento";
                $cadenaSql .= " LEFT JOIN parametros.municipio mn ON mn.codigo_mun= bn.municipio";
                $cadenaSql .= " WHERE id_beneficiario='" . $_REQUEST['id_beneficiario'] . "' ";
                // $cadenaSql .= " AND numero_contrato='" . $_REQUEST['numero_contrato'] . "' ";
                $cadenaSql .= " AND estado_registro=TRUE;";

                break;

            case 'consultaNombreProyecto':
                $cadenaSql = " SELECT  name as nombre";
                $cadenaSql .= " FROM public.projects";
                $cadenaSql .= " WHERE id='" . $variable . "';";
                break;

            // ----------------------- Verificación de archivos Alfresco

            // ----------------------- Verificación de archivos Alfresco

            case 'verificarArchivo':
                $cadenaSql = " UPDATE interoperacion.documentos_contrato SET ";
                if ($variable['rol'] == 7) {
                    $cadenaSql .= " comisionador=TRUE";
                }
                if ($variable['rol'] == 9) {
                    $cadenaSql .= " analista=TRUE";
                }
                if ($variable['rol'] == 10) {
                    $cadenaSql .= " supervisor=TRUE";
                }
                $cadenaSql .= " WHERE id='" . $variable['archivo'] . "';";
                break;

            case 'verificarArchivoContrato':
                $cadenaSql = " UPDATE interoperacion.contrato SET ";
                if ($variable['rol'] == 7) {
                    $cadenaSql .= " comisionador=TRUE";
                }
                if ($variable['rol'] == 9) {
                    $cadenaSql .= " analista=TRUE";
                }
                if ($variable['rol'] == 10) {
                    $cadenaSql .= " supervisor=TRUE";
                }
                $cadenaSql .= " WHERE id='" . $variable['archivo'] . "';";
                break;

            case "alfrescoUser":
                $cadenaSql = " SELECT DISTINCT id_beneficiario, nombre_carpeta_dep as padre, nombre_carpeta_mun as hijo, site_alfresco as site ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial ";
                $cadenaSql .= " INNER JOIN interoperacion.carpeta_alfresco on beneficiario_potencial.departamento=cast(carpeta_alfresco.cod_departamento as integer) ";
                $cadenaSql .= " WHERE cast(cod_municipio as integer)=municipio ";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "' ";
                break;

            case "alfrescoCarpetas":
                $cadenaSql = "SELECT parametros.codigo, parametros.descripcion ";
                $cadenaSql .= " FROM parametros.parametros ";
                $cadenaSql .= " JOIN parametros.relacion_parametro ON relacion_parametro.id_rel_parametro=parametros.rel_parametro ";
                $cadenaSql .= " WHERE parametros.estado_registro=TRUE AND relacion_parametro.descripcion='Alfresco Folders' ";
                break;

            case "alfrescoDirectorio":
                $cadenaSql = "SELECT parametros.descripcion ";
                $cadenaSql .= " FROM parametros.parametros ";
                $cadenaSql .= " JOIN parametros.relacion_parametro ON relacion_parametro.id_rel_parametro=parametros.rel_parametro ";
                $cadenaSql .= " WHERE parametros.estado_registro=TRUE AND relacion_parametro.descripcion='Directorio Alfresco Site' ";
                break;

            case "alfrescoLog":
                $cadenaSql = "SELECT host, usuario, password ";
                $cadenaSql .= " FROM parametros.api_data ";
                $cadenaSql .= " WHERE componente='alfresco' ";
                break;

            case "consultarCarpetaSoportes":
                $cadenaSql = " SELECT pr.id_parametro, pr.descripcion ";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Alfresco Folders'";
                $cadenaSql .= " AND pr.codigo='" . $variable . "' ";
                $cadenaSql .= " AND rl.estado_registro=TRUE ";
                break;

            case "actualizarLocal":
                $cadenaSql = "UPDATE interoperacion.documentos_contrato SET";
                $cadenaSql .= " nombre_documento='" . $variable['nombre_archivo'] . "',";
                $cadenaSql .= " ruta_relativa='" . $variable['ruta_archivo'] . "',";
                $cadenaSql .= " supervisor=FALSE,";
                $cadenaSql .= " comisionador=FALSE,";
                $cadenaSql .= " analista=FALSE ";
                $cadenaSql .= " WHERE id='" . $variable['id_archivo'] . "'";
                break;

            case "actualizarLocalContrato":
                $cadenaSql = "UPDATE interoperacion.contrato SET";
                $cadenaSql .= " nombre_documento_contrato='" . $variable['nombre_archivo'] . "',";
                $cadenaSql .= " ruta_documento_contrato='" . $variable['ruta_archivo'] . "',";
                $cadenaSql .= " supervisor=FALSE,";
                $cadenaSql .= " comisionador=FALSE,";
                $cadenaSql .= " analista=FALSE ";
                $cadenaSql .= " WHERE id='" . $variable['id_archivo'] . "'";
                break;

            case "pruebas":
                $cadenaSql = " SELECT pr.descripcion ";
                $cadenaSql .= " FROM parametros.parametros pr";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pr.rel_parametro";
                $cadenaSql .= " WHERE ";
                $cadenaSql .= " pr.estado_registro=TRUE ";
                $cadenaSql .= " AND rl.descripcion='Pruebas Rol Comision'";
                $cadenaSql .= " AND rl.estado_registro=TRUE ";
                break;

            case "consultaRequisitos":
                $cadenaSql = " SELECT tipologia_documento,codigo, descripcion,obligatoriedad ";
                $cadenaSql .= " FROM interoperacion.documentos_requisitos ";
                $cadenaSql .= " JOIN parametros.parametros ON id_parametro=tipologia_documento ";
                $cadenaSql .= " WHERE perfil=" . $variable . "";
                $cadenaSql .= " AND documentos_requisitos.estado_registro=TRUE";
                $cadenaSql .= " AND proceso=116";
                break;

            case "consultaRequisitosEspecificos":
                $cadenaSql = " SELECT tipologia_documento,codigo, descripcion,obligatoriedad ";
                $cadenaSql .= " FROM interoperacion.documentos_requisitos ";
                $cadenaSql .= " JOIN parametros.parametros ON id_parametro=tipologia_documento ";
                $cadenaSql .= " WHERE perfil=" . $variable['tipo'] . "";
                $cadenaSql .= " AND documentos_requisitos.estado_registro=TRUE";
                $cadenaSql .= " AND proceso=116";
                $cadenaSql .= " AND tipologia_documento='" . $variable['codigo'] . "'";
                break;

            case 'consultarValidacionRequisitos':
                $cadenaSql = " SELECT dr.perfil, dr.tipologia_documento, dr.obligatoriedad, dr.proceso, ";
                $cadenaSql .= " dc.nombre_documento, pr.descripcion nombre_requisitos ";
                $cadenaSql .= " FROM interoperacion.documentos_requisitos AS dr";
                $cadenaSql .= " JOIN  parametros.parametros AS pr ON pr.id_parametro= dr.tipologia_documento ";
                $cadenaSql .= " LEFT JOIN interoperacion.documentos_contrato AS dc ON dc.tipologia_documento= dr.tipologia_documento AND dc.id_beneficiario='" . $variable['id_beneficiario'] . "'";
                $cadenaSql .= " WHERE dr.estado_registro='TRUE'";
                $cadenaSql .= " AND dr.proceso='116'";
                $cadenaSql .= " AND dr.perfil='" . $variable['perfil_beneficiario'] . "';";

                break;

            case 'consultarTipoTecnologia':
                $cadenaSql = " SELECT pm.id_parametro, pm.descripcion ";
                $cadenaSql .= " FROM parametros.parametros pm";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pm.rel_parametro AND pm.estado_registro=TRUE AND rl.descripcion='Tipo Tecnologia'";
                $cadenaSql .= " WHERE pm.estado_registro=TRUE;";

                break;

            case 'consultarMedioPago':
                $cadenaSql = " SELECT pm.id_parametro, pm.descripcion ";
                $cadenaSql .= " FROM parametros.parametros pm";
                $cadenaSql .= " JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro=pm.rel_parametro AND pm.estado_registro=TRUE AND rl.descripcion='Medio Pago'";
                $cadenaSql .= " WHERE pm.estado_registro=TRUE;";

                break;

            case 'registrarDocumentoContrato':
                $cadenaSql = " UPDATE interoperacion.contrato";
                $cadenaSql .= " SET ";
                $cadenaSql .= " nombre_documento_contrato='" . $variable['nombre_contrato'] . "', ";
                $cadenaSql .= " ruta_documento_contrato='" . $variable['ruta_contrato'] . "' , ";
                $cadenaSql .= " estado_contrato='83'  ";
                $cadenaSql .= " WHERE id_beneficiario='" . $_REQUEST['id_beneficiario'] . "' ";
                $cadenaSql .= " AND numero_contrato='" . $_REQUEST['numero_contrato'] . "' ";
                $cadenaSql .= " AND estado_registro=TRUE ";
                $cadenaSql .= " RETURNING id ;";

                break;

            case 'buscarRol':
                $cadenaSql = " SELECT id_rol ";
                $cadenaSql .= " FROM gestion_menu.rol ";
                $cadenaSql .= " WHERE rol='" . $variable . "'; ";

                break;

            case 'actualizarServicio':
                $cadenaSql = " UPDATE interoperacion.servicio";
                $cadenaSql .= " SET estado_servicio='86'  ";
                $cadenaSql .= " WHERE id_contrato= '" . $variable . "'";
                $cadenaSql .= " AND estado_registro=TRUE ;";
                break;

        }

        return $cadenaSql;
    }

}
?>
