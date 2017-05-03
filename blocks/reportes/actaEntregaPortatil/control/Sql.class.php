<?php

namespace reportes\actaEntregaPortatil;

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

            case 'consultaInformacionBeneficiario':
                $cadenaSql = " SELECT bn.*,pr.descripcion as descripcion_tipo , cn.id id_contrato, cn.numero_contrato ,cn.urbanizacion as nombre_urbanizacion, cn.departamento as nombre_departamento, cn.municipio as nombre_municipio,cn.direccion_domicilio, cn.manzana as manzana_contrato, cn.bloque as bloque_contrato,
                cn.torre as torre_contrato,cn.casa_apartamento as casa_apto_contrato,cn.interior as interior_contrato,cn.lote as lote_contrato,cn.piso as piso_contrato, cn.estrato_socioeconomico,
                cn.nombres as nombre_contrato,
                    cn.primer_apellido as primer_apellido_contrato,
                    cn.segundo_apellido as segundo_apellido_contrato,
                    cn.tipo_documento as tipo_documento_contrato,
                    cn.numero_identificacion as numero_identificacion_contrato,
                    cn.celular as celular_contrato
                             ";
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
                $cadenaSql .= " fecha_entrega,";
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
                $cadenaSql .= " firmainstalador,";
                $cadenaSql .= " firmaBeneficiario)";
                $cadenaSql .= " VALUES ('" . $variable['id_beneficiario'] . "',";
                $cadenaSql .= (is_null($variable['fecha_entrega'])) ? "NULL," : " '" . $variable['fecha_entrega'] . "', ";
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
                $cadenaSql .= " '" . $variable['url_firma_instalador'] . "', ";
                $cadenaSql .= " '" . $variable['url_firma_beneficiario'] . "');";
                echo $cadenaSql;exit;

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

                $cadenaSql = " SELECT pr.*,";
                $cadenaSql .= " cn.direccion_domicilio as direccion,";
                $cadenaSql .= " cn.manzana,";
                $cadenaSql .= " cn.bloque,";
                $cadenaSql .= " cn.torre,";
                $cadenaSql .= " cn.casa_apartamento,";
                $cadenaSql .= " cn.interior,";
                $cadenaSql .= " cn.lote,";
                $cadenaSql .= " cn.piso,";
                $cadenaSql .= " cn.nombres as nombre_contrato,";
                $cadenaSql .= " cn.primer_apellido as primer_apellido_contrato,";
                $cadenaSql .= " cn.segundo_apellido as segundo_apellido_contrato,";
                $cadenaSql .= " cn.tipo_documento as tipo_documento_contrato,";
                $cadenaSql .= " cn.numero_identificacion as numero_identificacion_contrato,";
                $cadenaSql .= " cn.estrato as tipo_beneficiario_contrato, ";
                $cadenaSql .= " cn.estrato_socioeconomico as estrato_socioeconomico_contrato,";
                $cadenaSql .= " cn.urbanizacion as nombre_urbanizacion,";
                $cadenaSql .= " cn.departamento as nombre_departamento,";
                $cadenaSql .= " cn.municipio as nombre_municipio,";
                $cadenaSql .= " bp.departamento as codigo_departamento,";
                $cadenaSql .= " bp.municipio as codigo_municipio";
                $cadenaSql .= " FROM interoperacion.acta_entrega_portatil pr";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=pr.id_beneficiario AND cn.estado_registro='TRUE' ";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial bp ON bp.id_beneficiario=cn.id_beneficiario AND bp.estado_registro='TRUE' ";
                $cadenaSql .= " WHERE pr.id_beneficiario ='" . $_REQUEST['id_beneficiario'] . "'";
                $cadenaSql .= " AND pr.estado_registro='TRUE' ";
                $cadenaSql .= " /*AND pr.serial IS NOT NULL*/ ";
                $cadenaSql .= " /*AND pr.marca IS NOT NULL */";

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

                $cadenaSql = " SELECT pp.id_equipo as data ";
                $cadenaSql .= " , pp.serial as value";
                $cadenaSql .= " FROM interoperacion.politecnica_portatil pp";
                $cadenaSql .= " WHERE pp.serial ILIKE  '%" . $variable . "%'";
                $cadenaSql .= " AND pp.serial NOT IN (SELECT DISTINCT serial from interoperacion.acta_entrega_portatil WHERE estado_registro='TRUE' AND serial IS NOT NULL )";
                $cadenaSql .= " LIMIT 10";
                break;

            case 'consultarInformacionEquipo':
                $cadenaSql = " SELECT marca, modelo,  substr(cpu_version,0,12) ||' 4 cores 2.2 GHz'as procesador,";
                $cadenaSql .= " memoria_tipo ||' '||memoria_capacidad as memoria_ram,";
                $cadenaSql .= " substr(disco_capacidad,0,4)||' GB' as disco_duro,";
                $cadenaSql .= " sistema_operativo,";
                $cadenaSql .= " camara_tipo ||' '||camara_formato as camara,";
                $cadenaSql .= " parlantes_tipo||' '||audio_tipo as audio,";
                $cadenaSql .= " substr(bateria_autonomia,0,10) as bateria, ";
                $cadenaSql .= " 'Integrada' as red_alamnbrica,";
                $cadenaSql .= " 'Integrada' as red_inalambrica,";
                $cadenaSql .= " alimentacion_dispositivo||' '||alimentacion_voltaje as cargador, ";
                $cadenaSql .= " substr(pantalla_tipo ,0,20)||substr(pantalla_tipo ,35,50)||substr(pantalla_tamanno ,0,5)as pantalla";
                $cadenaSql .= " FROM interoperacion.politecnica_portatil";
                $cadenaSql .= " WHERE id_equipo='" . $variable . "';";
                break;

            case 'consultarInformacionEquipoSerial':
                $cadenaSql = " SELECT";
                $cadenaSql .= " camara_tipo ||' '||camara_formato||' '||camara_funcionalidad as camara,";
                $cadenaSql .= " mouse_tipo,";
                $cadenaSql .= " sistema_operativo,";
                $cadenaSql .= " 'Incorporados' as targeta_audio_video,";
                $cadenaSql .= " substr(disco_capacidad,0,4)||' GB velocidad de '||disco_velocidad as disco_duro,";
                $cadenaSql .= " 'Mín. Cuatro horas – 6 celdas' as autonomia,";
                $cadenaSql .= " '('||puerto_usb2_total||')Usb 2.0 y ('||puerto_usb3_total||') Ubs 3.0' as puerto_usb,";
                $cadenaSql .= " alimentacion_voltaje ||' - '||alimentacion_frecuencia as voltaje,";
                $cadenaSql .= " slot_expansion_tipo as targeta_memoria,";
                $cadenaSql .= " 'VGA '||puerto_vga_total ||' y HMDI '||puerto_vga_total as salida_video,";
                $cadenaSql .= " alimentacion_dispositivo||' '||alimentacion_voltaje as cargador, ";
                $cadenaSql .= " 'Recargable '|| bateria_tipo as bateria_tipo,";
                $cadenaSql .= " teclado_idioma||'(Internacional)' as teclado,";
                $cadenaSql .= " marca, ";
                $cadenaSql .= " modelo, ";
                $cadenaSql .= " substr(cpu_version,0,12) ||' '|| cpu_velocidad ||' cores '||(substr(cpu_velocidad,0,5)::float / 1000)||' GHz' as procesador,";
                $cadenaSql .= " cpu_bits||' Bits' as arquitectura,";
                $cadenaSql .= " memoria_tipo||' '||memoria_capacidad as memoria_ram,";
                $cadenaSql .= " 'PAE, NX, y SSE 4.x' as compatibilidad_memoria_ram,";
                $cadenaSql .= " memoria_tipo as tecnologia_memoria_ram,";
                $cadenaSql .= " antivirus,";
                $cadenaSql .= " 'N/A' as disco_anti_impacto,";
                $cadenaSql .= " serial,";
                $cadenaSql .= " parlantes_tipo||' '||audio_tipo as audio,";
                $cadenaSql .= " substr(bateria_autonomia,0,10) as bateria, ";
                $cadenaSql .= " 'Integrada' as targeta_red_alambrica,";
                $cadenaSql .= " 'Integrada' as targeta_red_inalambrica,";
                $cadenaSql .= " substr(pantalla_tipo ,0,20)||substr(pantalla_tipo ,35,50)||substr(pantalla_tamanno ,0,5)as pantalla";
                $cadenaSql .= " FROM interoperacion.politecnica_portatil";
                $cadenaSql .= " WHERE serial='" . $variable . "';";

                break;

            case 'consultaInformacionCertificacion':
                $cadenaSql = " SELECT serial";
                $cadenaSql .= " FROM interoperacion.acta_entrega_portatil";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "'";
                break;

        }

        return $cadenaSql;
    }
}
