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
                $cadenaSql = " SELECT bn.*,pr.descripcion as descripcion_tipo , cn.id id_contrato, cn.numero_contrato ,cn.urbanizacion as nombre_urbanizacion, cn.departamento as nombre_departamento, cn.municipio as nombre_municipio,cn.direccion_domicilio, cn.manzana as manzana_contrato, cn.bloque as bloque_contrato,
                cn.torre as torre_contrato,cn.casa_apartamento as casa_apto_contrato,cn.interior as interior_contrato,cn.lote as lote_contrato, cn.estrato_socioeconomico "    ;
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bn ";
                $cadenaSql .= " JOIN parametros.parametros pr ON pr.codigo= bn.tipo_beneficiario::text ";
                $cadenaSql .= "JOIN parametros.relacion_parametro rl ON rl.id_rel_parametro= pr.rel_parametro AND rl.descripcion='Tipo de Beneficario o Cliente' ";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario= bn.id_beneficiario AND cn.estado_registro=TRUE ";
                $cadenaSql .= " WHERE bn.estado_registro = TRUE ";
                $cadenaSql .= " AND pr.estado_registro = TRUE ";
                $cadenaSql .= " AND bn.id_beneficiario= '" . $_REQUEST['id'] . "';";
                break;

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

            case 'registrarActaEntrega':
                $cadenaSql = " UPDATE interoperacion.acta_entrega_portatil";
                $cadenaSql .= " SET estado_registro='FALSE'";
                $cadenaSql .= " WHERE id_beneficiario='" . $variable['id_beneficiario'] . "';";
                $cadenaSql .= " INSERT INTO interoperacion.acta_entrega_portatil(";
                $cadenaSql .= " id_beneficiario,";
                $cadenaSql .= " nombre,";
                $cadenaSql .= " primer_apellido,";
                $cadenaSql .= " segundo_apellido,";
                $cadenaSql .= " tipo_documento,";
                $cadenaSql .= " identificacion, ";
                $cadenaSql .= " fecha_entrega,";
                $cadenaSql .= " tipo_beneficiario,";
                $cadenaSql .= " urbanizacion,";
                //$cadenaSql .= " id_urbanizacion,";
                $cadenaSql .= " departamento,";
                $cadenaSql .= " municipio,";
                $cadenaSql .= " celular,";
                $cadenaSql .= " marca,";
                $cadenaSql .= " modelo,";
                $cadenaSql .= " serial,";
                $cadenaSql .= " procesador,";
                $cadenaSql .= " memoria_ram,";
                $cadenaSql .= " disco_duro,";
                $cadenaSql .= " sistema_operativo,";
                $cadenaSql .= " camara,";
                $cadenaSql .= " audio,";
                $cadenaSql .= " bateria,";
                $cadenaSql .= " targeta_red_alambrica,";
                $cadenaSql .= " targeta_red_inalambrica,";
                $cadenaSql .= " cargador,";
                $cadenaSql .= " pantalla,";
                $cadenaSql .= " web_soporte,";
                $cadenaSql .= " telefono_soporte,";
                $cadenaSql .= " direccion_general,";
                //$cadenaSql .= " perifericos,";
                //$cadenaSql .= " nombre_ins,";
                //$cadenaSql .= " identificacion_ins,";
                //$cadenaSql .= " celular_ins,";
                //$cadenaSql .= " firmaInstalador,";
                $cadenaSql .= " firmaBeneficiario)";
                $cadenaSql .= " VALUES ('" . $variable['id_beneficiario'] . "',";
                $cadenaSql .= " '" . $variable['nombre'] . "',";
                $cadenaSql .= " '" . $variable['primer_apellido'] . "',";
                $cadenaSql .= " '" . $variable['segundo_apellido'] . "',";
                $cadenaSql .= " '" . $variable['tipo_documento'] . "', ";
                $cadenaSql .= " '" . $variable['identificacion'] . "',";
                $cadenaSql .= " '" . $variable['fecha_entrega'] . "', ";
                $cadenaSql .= " '" . $variable['tipo_beneficiario'] . "', ";
                //$cadenaSql .= " '" . $variable['id_urbanizacion'] . "', ";
                $cadenaSql .= " '" . $variable['urbanizacion'] . "', ";
                $cadenaSql .= " '" . $variable['departamento'] . "', ";
                $cadenaSql .= " '" . $variable['municipio'] . "', ";
                $cadenaSql .= " '" . $variable['celular'] . "', ";
                $cadenaSql .= " '" . $variable['marca'] . "', ";
                $cadenaSql .= " '" . $variable['modelo'] . "', ";
                $cadenaSql .= " '" . $variable['serial'] . "', ";
                $cadenaSql .= " '" . $variable['procesador'] . "', ";
                $cadenaSql .= " '" . $variable['memoria_ram'] . "', ";
                $cadenaSql .= " '" . $variable['disco_duro'] . "', ";
                $cadenaSql .= " '" . $variable['sistema_operativo'] . "', ";
                $cadenaSql .= " '" . $variable['camara'] . "', ";
                $cadenaSql .= " '" . $variable['audio'] . "', ";
                $cadenaSql .= " '" . $variable['bateria'] . "', ";
                $cadenaSql .= " '" . $variable['targeta_red_alambrica'] . "', ";
                $cadenaSql .= " '" . $variable['targeta_red_inalambrica'] . "', ";
                $cadenaSql .= " '" . $variable['cargador'] . "', ";
                $cadenaSql .= " '" . $variable['pantalla'] . "', ";
                $cadenaSql .= " '" . $variable['web_soporte'] . "', ";
                $cadenaSql .= " '" . $variable['telefono_soporte'] . "', ";
                $cadenaSql .= " '" . $variable['direccion'] . "', ";
                //$cadenaSql .= " '" . $variable['perifericos'] . "', ";
                //$cadenaSql .= " '" . $variable['nombre_ins'] . "', ";
                //$cadenaSql .= " '" . $variable['identificacion_ins'] . "', ";
                //$cadenaSql .= " '" . $variable['celular_ins'] . "', ";
                //$cadenaSql .= " '" . $variable['url_firma_contratista'] . "',";
                $cadenaSql .= " '" . $variable['url_firma_beneficiario'] . "');";
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
                $cadenaSql = " UPDATE interoperacion.acta_entrega_portatil";
                $cadenaSql .= " SET nombre_documento='" . $variable['nombre_contrato'] . "', ruta_documento='" . $variable['ruta_contrato'] . "' ";
                $cadenaSql .= " WHERE id_beneficiario='" . $_REQUEST['id_beneficiario'] . "' AND estado_registro='TRUE';";
                break;

            case 'consultaInformacionCertificado':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM interoperacion.acta_entrega_portatil";
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

            case "parametroTipoVivienda":
                $cadenaSql = "SELECT        ";
                $cadenaSql .= " codigo, ";
                $cadenaSql .= "param.descripcion ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "parametros.parametros as param ";
                $cadenaSql .= "INNER JOIN ";
                $cadenaSql .= "parametros.relacion_parametro as rparam ";
                $cadenaSql .= "ON ";
                $cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "rparam.descripcion = 'Tipo de Vivienda' ";
                break;

            case "parametroDepartamento":
                $cadenaSql = "SELECT ";
                $cadenaSql .= "codigo_dep, ";
                $cadenaSql .= "departamento ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "parametros.departamento ";
                break;

            case "parametroMunicipio":
                $cadenaSql = "SELECT ";
                $cadenaSql .= "codigo_mun, ";
                $cadenaSql .= "municipio ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "parametros.municipio ";
                break;

            case "parametroTipoBeneficiario":
                $cadenaSql = "SELECT        ";
                $cadenaSql .= "codigo, ";
                $cadenaSql .= "param.descripcion ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "parametros.parametros as param ";
                $cadenaSql .= "INNER JOIN ";
                $cadenaSql .= "parametros.relacion_parametro as rparam ";
                $cadenaSql .= "ON ";
                $cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "rparam.descripcion = 'Tipo de Beneficario o Cliente' ";
                break;

            case "parametroEstrato":
                $cadenaSql = "SELECT        ";
                $cadenaSql .= " codigo, ";
                $cadenaSql .= "param.descripcion ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "parametros.parametros as param ";
                $cadenaSql .= "INNER JOIN ";
                $cadenaSql .= "parametros.relacion_parametro as rparam ";
                $cadenaSql .= "ON ";
                $cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "rparam.descripcion = 'Estrato' ";
                break;

            case "parametroTipoTecnologia":
                $cadenaSql = "SELECT        ";
                $cadenaSql .= "codigo, ";
                $cadenaSql .= "param.descripcion ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "parametros.parametros as param ";
                $cadenaSql .= "INNER JOIN ";
                $cadenaSql .= "parametros.relacion_parametro as rparam ";
                $cadenaSql .= "ON ";
                $cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "rparam.descripcion = 'Tipo de Tecnología' ";
                break;

            case 'consultarEquipo':
                $cadenaSql = " SELECT id_equipo as data , serial  as value";
                $cadenaSql .= " FROM interoperacion.politecnica_portatil";
                $cadenaSql .= " WHERE serial ILIKE '%" . $variable . "%'";
                $cadenaSql .= " LIMIT 10;";
                break;

            case 'consultarInformacionEquipo':
                $cadenaSql = " SELECT marca, modelo, cpu_version as procesador,";
                $cadenaSql .= " memoria_tipo ||' '||memoria_capacidad as memoria_ram,";
                $cadenaSql .= " disco_capacidad ||' - '||disco_serial as disco_duro,";
                $cadenaSql .= " sistema_operativo,";
                $cadenaSql .= " camara_tipo ||' '||camara_formato as camara,";
                $cadenaSql .= " parlantes_tipo||' '||audio_tipo as audio,";
                $cadenaSql .= " bateria_autonomia||' '||bateria_serial as bateria, ";
                $cadenaSql .= " red_serial as red_alamnbrica,";
                $cadenaSql .= " wifi_serial as red_inalambrica,";
                $cadenaSql .= " alimentacion_dispositivo||' '||alimentacion_voltaje as cargador, ";
                $cadenaSql .= " pantalla_tipo||' '|| pantalla_tamanno as pantalla";
                $cadenaSql .= " FROM interoperacion.politecnica_portatil";
                $cadenaSql .= " WHERE id_equipo='" . $variable . "';";
                break;

            case 'consultaInformacionCertificador':
                $cadenaSql = " SELECT ";
                $cadenaSql .= " cn.numero_contrato,";
                $cadenaSql .= " cn.estrato as tp_beneficiario,";
                $cadenaSql .= " cn.direccion_domicilio,";
                $cadenaSql .= " cn.manzana,";
                $cadenaSql .= " cn.bloque,";
                $cadenaSql .= " cn.torre,";
                $cadenaSql .= " cn.casa_apartamento,";
                $cadenaSql .= " cn.interior,";
                $cadenaSql .= " cn.lote,";
                $cadenaSql .= " cn.piso,";
                $cadenaSql .= " bn.municipio as codigo_municipio, ";
                $cadenaSql .= " cn.nombres as nombre_contrato,";
                $cadenaSql .= " cn.primer_apellido as primer_apellido_contrato,";
                $cadenaSql .= " cn.segundo_apellido as segundo_apellido_contrato,";
                $cadenaSql .= " cn.tipo_documento as tipo_documento_contrato,";
                $cadenaSql .= " cn.numero_identificacion as numero_identificacion_contrato,";
                $cadenaSql .= " cn.estrato as tipo_beneficiario_contrato, ";
                $cadenaSql .= " cn.estrato_socioeconomico as estrato_socioeconomico_contrato,";
                $cadenaSql .= " cn.urbanizacion as nombre_urbanizacion,";
                $cadenaSql .= " cn.departamento as nombre_departamento,";
                $cadenaSql .= " cn.municipio as nombre_municipio";
                $cadenaSql .= " FROM interoperacion.contrato as cn ";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial as bn ON bn.id_beneficiario=cn.id_beneficiario";
                $cadenaSql .= " WHERE cn.id_beneficiario ='" . $_REQUEST['id'] . "';";
                break;
            // Sincronizar Alfresco
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

            case "alfrescoDirectorio":
                $cadenaSql = "SELECT parametros.descripcion ";
                $cadenaSql .= " FROM parametros.parametros ";
                $cadenaSql .= " JOIN parametros.relacion_parametro ON relacion_parametro.id_rel_parametro=parametros.rel_parametro ";
                $cadenaSql .= " WHERE parametros.estado_registro=TRUE AND relacion_parametro.descripcion='Directorio Alfresco Site' ";
                break;

            case "alfrescoUser":
                $cadenaSql = " SELECT DISTINCT id_beneficiario, nombre_carpeta_dep as padre, nombre_carpeta_mun as hijo, site_alfresco as site ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial ";
                $cadenaSql .= " INNER JOIN interoperacion.carpeta_alfresco on beneficiario_potencial.departamento=cast(carpeta_alfresco.cod_departamento as integer) ";
                $cadenaSql .= " WHERE cast(cod_municipio as integer)=municipio ";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "' ";
                break;

            case "alfrescoLog":
                $cadenaSql = "SELECT host, usuario, password ";
                $cadenaSql .= " FROM parametros.api_data ";
                $cadenaSql .= " WHERE componente='alfresco' ";
                break;

        }

        return $cadenaSql;
    }
}
?>

