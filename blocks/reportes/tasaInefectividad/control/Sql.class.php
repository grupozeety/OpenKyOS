<?php

namespace reportes\tasaInefectividad;

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
                  $cadenaSql=" Select a.dane_departamento,to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM') as fecha, aa.accesos_retirados,b.accesos_ingresados, ";
                  $cadenaSql.=" c.accesos_enreubicacion,d.accesos_reemplazados_mesanterior,e.accesos_porreemplazar_mesanterior,";
                  $cadenaSql.=" f.total_accesos, indi.indicador";
                  $cadenaSql.=" From";
                  $cadenaSql.=" (";
                  $cadenaSql.=" Select distinct dane_departamento";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" ) a";
                  $cadenaSql.=" LEFT OUTER JOIN";
                  $cadenaSql.=" (Select dane_departamento,count(*) as accesos_retirados";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_registro=true";
                  $cadenaSql.=" and estado_servicio='EP04'";
                  $cadenaSql.=" and to_char(fecha_novedad,'yyyy-MM')=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" group by dane_departamento) aa";
                  $cadenaSql.=" ON(a.dane_departamento=aa.dane_departamento)";
                  $cadenaSql.=" LEFT OUTER JOIN";
                  $cadenaSql.=" (Select dane_departamento,count(*) as accesos_ingresados";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_servicio='EP01'";
                  $cadenaSql.=" and to_char(fecha_novedad,'yyyy-MM')=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" group by dane_departamento) b";
                  $cadenaSql.=" ON(a.dane_departamento=b.dane_departamento)";
                  $cadenaSql.=" LEFT OUTER JOIN";
                  $cadenaSql.=" (Select dane_departamento,count(*) as accesos_enreubicacion";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_registro=true";
                  $cadenaSql.=" AND estado_servicio='EP07'";
                  $cadenaSql.=" and to_char(fecha_novedad,'yyyy-MM')=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" group by dane_departamento) c";
                  $cadenaSql.=" ON(a.dane_departamento=c.dane_departamento)";
                  $cadenaSql.=" LEFT OUTER JOIN";
                  $cadenaSql.=" (Select dane_departamento,count(*) as accesos_reemplazados_mesanterior";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_registro=false";
                  $cadenaSql.=" AND estado_servicio='EP07'";
                  $cadenaSql.=" and to_char(fecha_novedad,'yyyy-MM')=to_char(('".$_REQUEST ['fecha_final']."'::timestamp - INTERVAL '1 MONTH'),'yyyy-MM')";
                  $cadenaSql.=" group by dane_departamento) d";
                  $cadenaSql.=" ON(a.dane_departamento=d.dane_departamento)";
                  $cadenaSql.=" LEFT OUTER JOIN";
                  $cadenaSql.=" (Select dane_departamento,count(*) as accesos_porreemplazar_mesanterior";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_registro=true";
                  $cadenaSql.=" AND estado_servicio='EP07'";
                  $cadenaSql.=" and to_char(fecha_novedad,'yyyy-MM')=to_char(('".$_REQUEST ['fecha_final']."'::timestamp - INTERVAL '1 MONTH'),'yyyy-MM')";
                  $cadenaSql.=" group by dane_departamento) e";
                  $cadenaSql.=" ON(a.dane_departamento=e.dane_departamento)";
                  $cadenaSql.=" LEFT OUTER JOIN";
                  $cadenaSql.=" (Select dane_departamento,count(*) as total_accesos";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_registro=true";
                  $cadenaSql.=" AND estado_servicio='ET0'";
                  $cadenaSql.=" AND to_char(fecha_novedad,'yyyy-MM')<=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" group by dane_departamento) f";
                  $cadenaSql.=" ON(a.dane_departamento=f.dane_departamento)";
                  $cadenaSql.=" LEFT OUTER JOIN";
                  $cadenaSql.=" (";
                  $cadenaSql.=" Select total.dane_departamento,(sinservicio.sum/total.total_accesos)*100 as indicador";
                  $cadenaSql.=" from";
                  $cadenaSql.=" (Select dane_departamento,count(*) as total_accesos";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_registro=true";
                  $cadenaSql.=" AND estado_servicio='ET0'";
                  $cadenaSql.=" AND to_char(fecha_novedad,'yyyy-MM')<=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" group by dane_departamento) total";
                  $cadenaSql.=" ";
                  $cadenaSql.=" LEFT OUTER JOIN";
                  $cadenaSql.=" (Select retirados.dane_departamento,retirados.accesos_retirados + translado.accesos_translado + suspendidousuario.accesos_suspendidousuario + reubicacion";
                  $cadenaSql.=" .accesos_reubicacion as sum";
                  $cadenaSql.=" from";
                  $cadenaSql.=" (Select dane_departamento,count(*) as accesos_retirados";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_registro=true";
                  $cadenaSql.=" AND estado_servicio='EP04'";
                  $cadenaSql.=" and to_char(fecha_novedad,'yyyy-MM')=to_char(('".$_REQUEST ['fecha_final']."'::timestamp - INTERVAL '1 MONTH'),'yyyy-MM')";
                  $cadenaSql.=" group by dane_departamento) retirados";
                  $cadenaSql.=" LEFT OUTER JOIN";
                  $cadenaSql.=" (Select dane_departamento,count(*) as accesos_translado";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_registro=true";
                  $cadenaSql.=" AND estado_servicio='EP03'";
                  $cadenaSql.=" and to_char(fecha_novedad,'yyyy-MM')=to_char(('".$_REQUEST ['fecha_final']."'::timestamp - INTERVAL '1 MONTH'),'yyyy-MM')";
                  $cadenaSql.=" group by dane_departamento) translado";
                  $cadenaSql.=" ON(retirados.dane_departamento=translado.dane_departamento)";
                  $cadenaSql.=" LEFT OUTER JOIN";
                  $cadenaSql.=" (Select dane_departamento, count(*) as accesos_suspendidousuario";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_registro=true";
                  $cadenaSql.=" AND estado_servicio='EP011'";
                  $cadenaSql.=" and to_char(fecha_novedad,'yyyy-MM')=to_char(('".$_REQUEST ['fecha_final']."'::timestamp - INTERVAL '1 MONTH'),'yyyy-MM')";
                  $cadenaSql.=" group by dane_departamento) suspendidousuario";
                  $cadenaSql.=" ON(retirados.dane_departamento=suspendidousuario.dane_departamento)";
                  $cadenaSql.=" LEFT OUTER JOIN";
                  $cadenaSql.=" (Select dane_departamento,count(*) as accesos_reubicacion";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_registro=true";
                  $cadenaSql.=" AND estado_servicio='EP07'";
                  $cadenaSql.=" and to_char(fecha_novedad,'yyyy-MM')=to_char(('".$_REQUEST ['fecha_final']."'::timestamp - INTERVAL '1 MONTH'),'yyyy-MM')";
                  $cadenaSql.=" group by dane_departamento) reubicacion";
                  $cadenaSql.=" ON(retirados.dane_departamento=reubicacion.dane_departamento)";
                  $cadenaSql.=" ) sinservicio";
                  $cadenaSql.=" ON(total.dane_departamento=sinservicio.dane_departamento)";
                  $cadenaSql.=" ) indi";
                  $cadenaSql.=" ON(a.dane_departamento=indi.dane_departamento)";
                  $cadenaSql.=" ";


                  break;
                case 'consultarInformacionB':
                  $cadenaSql=" Select to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy') as anho, to_char('".$_REQUEST ['fecha_final']."'::timestamp,'MM') as mes, dane_municipio,id_beneficiario,identificacion_proyecto,";
                  $cadenaSql.=" CASE WHEN estado_servicio='ET0' AND estado_registro=true THEN 'Activo'";
                  $cadenaSql.=" WHEN estado_servicio='ET11' OR estado_servicio='ET1' OR estado_servicio='EP03' OR estado_servicio='EP04' OR estado_servicio='EP07' AND estado_registro=true THEN 'Sin servicio no conectado'";
                  $cadenaSql.=" WHEN estado_servicio='EP02' OR estado_servicio='EP06'OR estado_servicio='EP05' AND estado_registro=true THEN 'Sin servicio conectado'";
                  $cadenaSql.=" END as categoria,";
                  $cadenaSql.=" CASE WHEN estado_servicio='ET11' THEN 'Suspendido por voluntad del usuario'";
                  $cadenaSql.=" WHEN estado_servicio='ET1' THEN 'Inactivo'";
                  $cadenaSql.=" WHEN estado_servicio='EP03' THEN 'En translado'";
                  $cadenaSql.=" WHEN estado_servicio='EP04' THEN 'Retirado'";
                  $cadenaSql.=" WHEN estado_servicio='EP07' THEN 'En reubicacion'";
                  $cadenaSql.=" END as subcategoria_noCon,";
                  $cadenaSql.=" CASE WHEN estado_servicio='EP02' THEN 'Suspendido'";
                  $cadenaSql.=" WHEN estado_servicio='EP06' THEN 'En mantenimmmiento'";
                  $cadenaSql.=" WHEN estado_servicio='EP05' THEN 'En instalacion'";
                  $cadenaSql.=" END as subcategoria_Con,";
                  $cadenaSql.=" CASE WHEN estado_servicio='EP02'OR estado_servicio='EP06' OR estado_servicio='EP05' THEN fecha_novedad";
                  $cadenaSql.=" END as fecha_Con,";
                  $cadenaSql.=" CASE WHEN estado_servicio='EP04' THEN fecha_novedad";
                  $cadenaSql.=" END as fecha_retiro,";
                  $cadenaSql.=" CASE WHEN estado_servicio='ET0' AND estado_registro=true AND to_char(fecha_novedad,'yyyy-MM')=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM') THEN fecha_novedad";
                  $cadenaSql.=" END as fecha_ingreso,";
                  $cadenaSql.=" meta_proyecto as meta";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" WHERE to_char(fecha_novedad,'yyyy-MM')<=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" AND estado_registro=true";
                  $cadenaSql.=" ORDER BY categoria";
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
