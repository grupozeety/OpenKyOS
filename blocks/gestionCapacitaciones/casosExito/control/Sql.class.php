<?php
namespace gestionCapacitaciones\casosExito;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

require_once "core/manager/Configurador.class.php";
require_once "core/connection/Sql.class.php";

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql
{
    public $miConfigurador;
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

            case 'consultaDepartamento':
                $cadenaSql = " SELECT codigo_dep,codigo_dep ||' - '||departamento as departamento";
                $cadenaSql .= " FROM parametros.departamento;";
                break;

            case 'consultaMunicipio':
                $cadenaSql = " SELECT codigo_mun,codigo_mun||' - '||municipio as municipio";
                $cadenaSql .= " FROM parametros.municipio;";
                break;

            case 'consultarBeneficiariosPotenciales':
                $cadenaSql = " SELECT value , data ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "(SELECT DISTINCT bp.identificacion ||' - ('||bp.nombre||' '||bp.primer_apellido||' '||bp.segundo_apellido||')' AS  value, bp.id_beneficiario  AS data ";
                $cadenaSql .= " FROM  interoperacion.beneficiario_potencial bp ";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=bp.id_beneficiario ";
                $cadenaSql .= " WHERE bp.estado_registro=TRUE ";
                $cadenaSql .= " AND cn.estado_registro=TRUE ";
                $cadenaSql .= $variable;
                $cadenaSql .= "     ) datos ";
                $cadenaSql .= "WHERE value ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

            case 'consultarPerteneciaEtnica':
                $cadenaSql = " SELECT valor,valor||' - '||descripcion as descripcion";
                $cadenaSql .= " FROM parametros.generales";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND tipo='PertenenciaEtnica';";
                break;

            case 'consultarOcupacion':
                $cadenaSql = " SELECT valor,valor||' - '||descripcion as descripcion";
                $cadenaSql .= " FROM parametros.generales";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND tipo='Ocupacion';";
                break;

            case 'consultarNivelEducativo':
                $cadenaSql = " SELECT valor,valor||' - '||descripcion as descripcion";
                $cadenaSql .= " FROM parametros.generales";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND tipo='NivelEducativo';";
                break;

            case 'consultarServicio':
                $cadenaSql = " SELECT valor,valor||' - '||descripcion as descripcion";
                $cadenaSql .= " FROM parametros.generales";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND tipo='ServicioCapacitacion';";
                break;

            case 'consultarDetalleServicio':
                $cadenaSql = " SELECT valor,valor||' - '||descripcion as descripcion";
                $cadenaSql .= " FROM parametros.generales";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND tipo='DetalleServicio';";
                break;

            case "registroCompetencia":

                $cadenaSql = " INSERT INTO logica.info_compe(";
                $cadenaSql .= " anio, ";
                $cadenaSql .= " nit_operador, ";
                $cadenaSql .= " id_capacitado, ";
                $cadenaSql .= " dane_centro_poblado, ";
                $cadenaSql .= " dane_departamento, ";
                $cadenaSql .= " dane_institucion, ";
                $cadenaSql .= " dane_municipio, ";
                $cadenaSql .= " nombre_capacitado, ";
                $cadenaSql .= " correo_capacitado, ";
                $cadenaSql .= " telefono_contacto,";
                $cadenaSql .= " genero, ";
                $cadenaSql .= " pertenecia_etnica, ";
                $cadenaSql .= " nivel_educativo, ";
                $cadenaSql .= " servicio_capacitacion, ";
                $cadenaSql .= " detalle_servicio, ";
                $cadenaSql .= " ocupacion, ";
                $cadenaSql .= " edad, ";
                $cadenaSql .= " estrato, ";
                $cadenaSql .= " deserto, ";
                $cadenaSql .= " fecha_capacitacion, ";
                $cadenaSql .= " horas_capacitacion, ";
                $cadenaSql .= " id_actividad, ";
                $cadenaSql .= " actividad, ";
                $cadenaSql .= " id_beneficiario, ";
                $cadenaSql .= " numero_contrato, ";
                $cadenaSql .= " codigo_simona, ";
                $cadenaSql .= " region)";
                $cadenaSql .= " VALUES (";
                foreach ($variable as $key => $value) {

                    $cadenaSql .= "'" . $value . "',";

                }

                $cadenaSql .= ") RETURNING id_actividad;";

                $cadenaSql = str_replace(",)", ")", $cadenaSql);

                break;

            case 'consultarInformacionBeneficiario':
                $cadenaSql = " SELECT";
                $cadenaSql .= " cn.id_beneficiario,";
                $cadenaSql .= " cn.nombres||' '||cn.primer_apellido||' '||(CASE WHEN cn.segundo_apellido IS NOT NULL THEN cn.segundo_apellido ELSE '' END) as nombre_beneficiario,";
                $cadenaSql .= " cn.numero_identificacion,";
                $cadenaSql .= " (CASE WHEN bp.edad > 0 THEN bp.edad ELSE null END) as edad,";
                $cadenaSql .= " (CASE WHEN bp.correo='NA' THEN null WHEN bp.correo IS NOT NULL THEN bp.correo ELSE null END) as correo,";
                $cadenaSql .= " (CASE WHEN bp.celular IS NOT NULL THEN replace( bp.celular, ' ', '') ELSE null END) as telefono,";
                $cadenaSql .= " (CASE WHEN bp.genero = 2 THEN 'M' WHEN bp.genero= 1 THEN 'F' ELSE null END) as genero,";
                $cadenaSql .= " bp.municipio,";
                $cadenaSql .= " bp.departamento,";
                $cadenaSql .= " cn.estrato_socioeconomico as estrato,";
                $cadenaSql .= " nv.codigo_homologacion as nivel_estudio,";
                $cadenaSql .= " op.codigo_homologacion as ocupacion,";
                $cadenaSql .= " pe.codigo_homologacion as pertencia_etnica";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=bp.id_beneficiario AND cn.estado_registro='TRUE'";
                $cadenaSql .= " LEFT JOIN parametros.parametros nv ON nv.codigo::int=bp.nivel_estudio AND nv.rel_parametro='3' AND nv.estado_registro='TRUE'";
                $cadenaSql .= " LEFT JOIN parametros.parametros op ON op.codigo::int=bp.ocupacion AND op.rel_parametro='9' AND op.estado_registro='TRUE'";
                $cadenaSql .= " LEFT JOIN parametros.parametros pe ON pe.codigo::int=bp.pertenencia_etnica AND pe.rel_parametro='8' AND pe.estado_registro='TRUE'";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE'";
                $cadenaSql .= " AND cn.numero_identificacion is not null";
                $cadenaSql .= " AND bp.id_beneficiario='" . $variable . "';";

                break;

            case 'consultarIdentificadorActividad':
                $cadenaSql = " SELECT max(id_actividad) ";
                $cadenaSql .= " FROM logica.info_compe;";
                break;

            case 'consultarActividad':
                $cadenaSql = " SELECT DISTINCT id_actividad as data, actividad as value";
                $cadenaSql .= " FROM logica.info_compe";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND actividad ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= " LIMIT 10; ";

                break;

            case 'consultarInformacionCapacitacion':
                $cadenaSql = " SELECT DISTINCT";
                $cadenaSql .= " id_info_compe,";
                $cadenaSql .= " servicio_capacitacion,";
                $cadenaSql .= " detalle_servicio,";
                $cadenaSql .= " fecha_capacitacion,";
                $cadenaSql .= " horas_capacitacion,";
                $cadenaSql .= " actividad,";
                $cadenaSql .= " id_actividad";
                $cadenaSql .= " FROM logica.info_compe";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_actividad='" . $variable . "'";
                $cadenaSql .= " ORDER BY id_info_compe ASC";
                $cadenaSql .= " LIMIT 1;";
                break;

            case 'consultarCodigoParametro':
                $cadenaSql = " SELECT codigo";
                $cadenaSql .= " FROM parametros.parametros";
                $cadenaSql .= " WHERE codigo_homologacion='" . $variable . "';";
                break;

            case 'actualizarBeneficiario':
                $cadenaSql = " UPDATE interoperacion.beneficiario_potencial";
                $cadenaSql .= " SET genero='" . $variable['genero'] . "', ";
                $cadenaSql .= " nivel_estudio='" . $variable['nivelEducacion'] . "',";
                $cadenaSql .= " pertenencia_etnica='" . $variable['pertenenciaEtnica'] . "', ";
                $cadenaSql .= " ocupacion='" . $variable['ocupacion'] . "',";
                $cadenaSql .= " correo='" . $variable['correo'] . "',";
                $cadenaSql .= " edad='" . $variable['edad'] . "',";
                $cadenaSql .= " celular='" . $variable['telefono'] . "'";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable['idBeneficiario'] . "';";
                break;

            case 'actualizarContrato':
                $cadenaSql = " UPDATE interoperacion.contrato";
                $cadenaSql .= " SET estrato_socioeconomico='" . $variable['estrato'] . "' ";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_beneficiario='" . $variable['idBeneficiario'] . "';";
                break;

            case 'consultarActividadValidar':
                $cadenaSql = " SELECT  id_actividad,id_capacitado";
                $cadenaSql .= " FROM logica.info_compe";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND id_actividad='" . $variable['id_actividad'] . "'";
                $cadenaSql .= " AND id_capacitado='" . $variable['identificacion'] . "';";

                break;

        }

        return $cadenaSql;
    }
}
