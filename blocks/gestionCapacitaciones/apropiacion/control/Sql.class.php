<?php
namespace gestionCapacitaciones\apropiacion;

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
            case 'consultarTipoActividad':
                $cadenaSql = " SELECT valor,valor||' - '||descripcion as descripcion";
                $cadenaSql .= " FROM parametros.generales";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND tipo='TipoActividad';";
                break;

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
                $cadenaSql .= " FROM logica.info_aprop;";
                break;

            case "registrarApropiacion":
                $cadenaSql = " INSERT INTO logica.info_aprop(";
                $cadenaSql .= " anio,";
                $cadenaSql .= " nit_operador, ";
                $cadenaSql .= " dane_centro_poblado, ";
                $cadenaSql .= " dane_departamento, ";
                $cadenaSql .= " dane_institucion, ";
                $cadenaSql .= " dane_municipio, ";
                $cadenaSql .= " tipo_actividad,";
                $cadenaSql .= " actividad, ";
                $cadenaSql .= " id_actividad, ";
                $cadenaSql .= " asistentes_actividad, ";
                $cadenaSql .= " fecha_actividad, ";
                $cadenaSql .= " personas_visitadas, ";
                $cadenaSql .= " id_beneficiario, ";
                $cadenaSql .= " numero_contrato, ";
                $cadenaSql .= " region, ";
                $cadenaSql .= " codigo_simona)";
                $cadenaSql .= " VALUES (";
                foreach ($variable as $key => $value) {
                    $cadenaSql .= "'" . $value . "',";
                }
                $cadenaSql .= ") RETURNING id_actividad;";
                $cadenaSql = str_replace(",)", ")", $cadenaSql);
                break;

        }

        return $cadenaSql;
    }
}
