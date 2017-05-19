<?php

namespace reportes\tiempoMaxRespuesta;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
    public $miConfigurador;
    public function getCadenaSql($tipo, $variable = '') {

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
            case 'consultarDepartamento':

                $cadenaSql = " SELECT DISTINCT departamento as valor, departamento";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " AND departamento IS NOT NULL AND departamento!='';";
                break;

            case 'consultarMunicipio':

            $cadenaSql = " SELECT DISTINCT m.codigo_mun as valor, c.municipio ";
            $cadenaSql .= " FROM interoperacion.contrato c,";
            $cadenaSql .= " parametros.municipio m";
            $cadenaSql .= " WHERE c.estado_registro=TRUE";
            $cadenaSql .= " AND c.municipio = m.municipio";
            $cadenaSql .= " AND c.municipio IS NOT NULL AND c.municipio!='';";

                break;

            case 'consultarUrbanizacion':

                $cadenaSql = " SELECT DISTINCT urbanizacion as valor, urbanizacion";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " AND urbanizacion IS NOT NULL AND urbanizacion!='';";

                break;

                case 'consultarInformacion':
                  $cadenaSql=" SELECT id_info_llam, extract(month from t1) as id_mes, cast(extract(year from t1) as character varying) as annio, generales.descripcion,";
                  $cadenaSql.=" to_char( t1, 'dd/MM/yyyy hh:mi:ss') as t1 ,";
                  $cadenaSql.=" to_char( t2, 'dd/MM/yyyy hh:mi:ss') as t2 ,";
                  $cadenaSql.=" to_char( t3 , 'dd/MM/yyyy hh:mi:ss') as t3 ,";
                  $cadenaSql.=" extract(EPOCH from (t3-t2)) as tiempo_respuesta,";
                  $cadenaSql.=" CASE condicion_obtenida";
                  $cadenaSql.=" WHEN TRUE then 'Exito'";
                  $cadenaSql.=" WHEN FALSE then 'Fallido' END as condicion_obtenida";
                  $cadenaSql.=" FROM logica.info_llam";
                  $cadenaSql.=" JOIN parametros.generales ON generales.valor=cast(extract(month from t1) as character varying) and generales.estado_registro=TRUE and tipo='Mes'";
                  $cadenaSql.=" WHERE info_llam.estado_registro=TRUE";
                  $cadenaSql.=" AND tipo_llamada='1'";
                  $cadenaSql.=" AND to_char(t1,'yyyy-MM')=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" ORDER BY annio DESC, id_mes DESC, t1 DESC";

                    break;
                case 'consultarInformacionB':
                  $cadenaSql=" SELECT fecha, sum(total_llam) as total_llamadas, sum(person_llam) as person_llamadas, sum(dentro_llam) as dentro_llamadas, sum(fuera_llam) as fuera_llamadas";
                  $cadenaSql.=" , coalesce((sum(dentro_llam)/nullif(sum(person_llam),0))*100,0) as calculo_indicador";
                  $cadenaSql.=" FROM (SELECT to_char(t1,'yyyy-MM') as fecha, count(id_info_llam) as total_llam, 0 as person_llam, 0 as dentro_llam, 0 as fuera_llam";
                  $cadenaSql.=" FROM logica.info_llam";
                  $cadenaSql.=" WHERE estado_registro=TRUE";
                  $cadenaSql.=" GROUP BY fecha";
                  $cadenaSql.=" UNION";
                  $cadenaSql.=" SELECT to_char(t1,'yyyy-MM') as fecha, 0 as total_llam, count(id_info_llam) as person_llam, 0 as dentro_llam, 0 as fuera_llam";
                  $cadenaSql.=" FROM logica.info_llam";
                  $cadenaSql.=" WHERE estado_registro=TRUE";
                  $cadenaSql.=" AND tipo_llamada='1'";
                  $cadenaSql.=" GROUP BY fecha";
                  $cadenaSql.=" UNION";
                  $cadenaSql.=" SELECT to_char(t1,'yyyy-MM') as fecha,0 as total_llam, 0 as person_llam, count(id_info_llam) as dentro_llam, 0 as fuera_llam";
                  $cadenaSql.=" FROM logica.info_llam";
                  $cadenaSql.=" WHERE estado_registro=TRUE";
                  $cadenaSql.=" AND tipo_llamada='1'";
                  $cadenaSql.=" AND extract(EPOCH from (t3-t2))>20";
                  $cadenaSql.=" GROUP BY fecha) as resultado";
                  $cadenaSql.=" WHERE fecha=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" GROUP BY fecha";
                  $cadenaSql.=" ORDER BY fecha DESC";
                  break;
                case 'consultarInformacionC':
                  $cadenaSql=" SELECT fecha, sum(total_llam) as total_llamadas, sum(person_llam) as person_llamadas, sum(dentro_llam) as dentro_llamadas, sum(fuera_llam) as fuera_llamadas";
                  $cadenaSql.=" , coalesce((sum(dentro_llam)/nullif(sum(person_llam),0))*100,0) as calculo_indicador";
                  $cadenaSql.=" FROM (SELECT to_char(t1,'yyyy-MM') as fecha, count(id_info_llam) as total_llam, 0 as person_llam, 0 as dentro_llam, 0 as fuera_llam";
                  $cadenaSql.=" FROM logica.info_llam";
                  $cadenaSql.=" WHERE estado_registro=TRUE";
                  $cadenaSql.=" GROUP BY fecha";
                  $cadenaSql.=" UNION";
                  $cadenaSql.=" SELECT to_char(t1,'yyyy-MM') as fecha, 0 as total_llam, count(id_info_llam) as person_llam, 0 as dentro_llam, 0 as fuera_llam";
                  $cadenaSql.=" FROM logica.info_llam";
                  $cadenaSql.=" WHERE estado_registro=TRUE";
                  $cadenaSql.=" AND tipo_llamada='1'";
                  $cadenaSql.=" GROUP BY fecha";
                  $cadenaSql.=" UNION";
                  $cadenaSql.=" SELECT to_char(t1,'yyyy-MM') as fecha,0 as total_llam, 0 as person_llam, count(id_info_llam) as dentro_llam, 0 as fuera_llam";
                  $cadenaSql.=" FROM logica.info_llam";
                  $cadenaSql.=" WHERE estado_registro=TRUE";
                  $cadenaSql.=" AND tipo_llamada='1'";
                  $cadenaSql.=" AND extract(EPOCH from (t3-t2))>20";
                  $cadenaSql.=" GROUP BY fecha) as resultado";
                  $cadenaSql.=" WHERE fecha between to_char(('".$_REQUEST ['fecha_final']."'::timestamp - INTERVAL '6 MONTH'),'yyyy-MM') AND to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" GROUP BY fecha";
                  $cadenaSql.=" ORDER BY fecha DESC";

                        break;
            case 'consultarBeneficiariosPotenciales':
                $cadenaSql = " SELECT value , data ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "(SELECT DISTINCT cn.numero_identificacion ||' - ('||cn.nombres||' '||cn.primer_apellido||' '||(CASE WHEN cn.segundo_apellido IS NULL THEN '' ELSE cn.segundo_apellido END)||')' AS value, bp.id_beneficiario AS data ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp ";
                $cadenaSql .= " JOIN interoperacion.contrato cn ON cn.id_beneficiario=bp.id_beneficiario ";
                $cadenaSql .= " WHERE bp.estado_registro=TRUE ";
                $cadenaSql .= " AND cn.estado_registro=TRUE ";
                $cadenaSql .= "     ) datos ";
                $cadenaSql .= "WHERE value ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

        }

        return $cadenaSql;
    }
}
?>
