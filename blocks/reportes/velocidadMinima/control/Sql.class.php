<?php

namespace reportes\velocidadMinima;

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
                  $cadenaSql=" Select *,CASE ";
                  $cadenaSql.=" WHEN qqqqq.velocidad_subida>=qqqqq.vel_contratada_sub AND qqqqq.velocidad_bajada>=qqqqq.vel_contratada_baj THEN 'E'";
                  $cadenaSql.=" ELSE 'F'";
                  $cadenaSql.=" END as condicion_obtenida";
                  $cadenaSql.=" from";
                  $cadenaSql.=" (";
                  $cadenaSql.=" ";
                  $cadenaSql.=" Select qqqq.fecha as fecha,qqqq.dane_municipio as dane_municipio,qqqq.tecnologia_instalada as tecnologia,j.accesos_activos,qqqq.total_pruebas as total_pruebas, qqqq.total_pruebaexitosa as total_pruebaexitosa,qqqq.total_accesosExito as total_accesosExito,qqqq.total_accesosfalla as total_accesosfalla, 4000 as vel_contratada_sub, 1000 as vel_contratada_baj, vel.velocidad_subida,vel.velocidad_bajada";
                  $cadenaSql.=" from";
                  $cadenaSql.=" (";
                  $cadenaSql.=" ";
                  $cadenaSql.=" Select q.fecha,q.dane_municipio,q.tecnologia_instalada,sum(q.total_registro) as total_pruebas, sum(q.total_exito) as total_pruebaexitosa,sum(q.total_accesosExito) as total_accesosExito ,sum(q.total_accesosfalla) as total_accesosfalla";
                  $cadenaSql.=" from";
                  $cadenaSql.=" (";
                  $cadenaSql.=" select h.fecha, h.dane_municipio,h.tecnologia_instalada, 0 as total_registro, 0 as total_exito, 0 as total_accesosExito, count(*) as total_accesosfalla";
                  $cadenaSql.=" from(Select distinct to_char(fecha_medicion,'yyyy-MM') as fecha, dane_municipio,tecnologia_instalada,id_beneficiario";
                  $cadenaSql.=" from logica.info_indca_veloc";
                  $cadenaSql.=" WHERE condicion_exito=false";
                  $cadenaSql.=" )h";
                  $cadenaSql.=" group by fecha, dane_municipio,tecnologia_instalada";
                  $cadenaSql.=" UNION";
                  $cadenaSql.=" ";
                  $cadenaSql.=" select i.fecha, i.dane_municipio,i.tecnologia_instalada, 0 as total_registro, 0 as total_exito, count(*) as total_accesosExito, 0 as total_accesosfalla";
                  $cadenaSql.=" from(Select distinct to_char(fecha_medicion,'yyyy-MM') as fecha, dane_municipio,tecnologia_instalada,id_beneficiario";
                  $cadenaSql.=" from logica.info_indca_veloc";
                  $cadenaSql.=" WHERE condicion_exito=true";
                  $cadenaSql.=" )i";
                  $cadenaSql.=" group by fecha, dane_municipio,tecnologia_instalada";
                  $cadenaSql.=" UNION";
                  $cadenaSql.=" Select to_char(fecha_medicion,'yyyy-MM') as fecha, dane_municipio,tecnologia_instalada, 0 as total_registro, count(*) as total_exito, 0 as total_accesosExito, 0 as total_accesosfalla";
                  $cadenaSql.=" from logica.info_indca_veloc";
                  $cadenaSql.=" WHERE condicion_exito=true";
                  $cadenaSql.=" GROUP BY to_char(fecha_medicion,'yyyy-MM'), dane_municipio,tecnologia_instalada";
                  $cadenaSql.=" UNION";
                  $cadenaSql.=" Select to_char(fecha_medicion,'yyyy-MM') as fecha, dane_municipio,tecnologia_instalada,count(*) as total_registro, 0, 0 as total_accesosExito,0 as total_accesosfalla";
                  $cadenaSql.=" from logica.info_indca_veloc";
                  $cadenaSql.=" GROUP BY to_char(fecha_medicion,'yyyy-MM'), dane_municipio,tecnologia_instalada";
                  $cadenaSql.=" ) as q";
                  $cadenaSql.=" GROUP BY fecha,dane_municipio,tecnologia_instalada";
                  $cadenaSql.=" /*ORDER BY dane_municipio,fecha DESC*/) as qqqq";
                  $cadenaSql.=" INNER JOIN";
                  $cadenaSql.=" (";
                  $cadenaSql.=" Select qq.row_number, qq.fecha, qq.dane_municipio,qq.tecnologia_instalada,sum(qq.velocidad_subida) as velocidad_subida,sum(qq.velocidad_bajada) as velocidad_bajada";
                  $cadenaSql.=" from";
                  $cadenaSql.=" (Select a.row_number, a.fecha, a.dane_municipio,a.tecnologia_instalada, 0 as velocidad_subida,a.velocidad_bajada";
                  $cadenaSql.=" from";
                  $cadenaSql.=" (Select ROW_NUMBER() OVER(PARTITION BY to_char(fecha_medicion,'yyyy-MM'),dane_municipio ORDER BY to_char(fecha_medicion,'yyyy-MM'), dane_municipio, velocidad_bajada ASC) AS row_number,to_char(fecha_medicion,'yyyy-MM') as fecha, dane_municipio,tecnologia_instalada, velocidad_bajada";
                  $cadenaSql.=" from logica.info_indca_veloc";
                  $cadenaSql.=" WHERE condicion_exito=true ";
                  $cadenaSql.=" ) as a";
                  $cadenaSql.=" inner join";
                  $cadenaSql.=" (Select to_char(fecha_medicion,'yyyy-MM') as fecha, dane_municipio, tecnologia_instalada,count(*) as total_registro,CASE WHEN round(count(*)*0.05)=0 THEN 1 ELSE round(count(*)*0.05) END as cinco_porc";
                  $cadenaSql.=" from logica.info_indca_veloc";
                  $cadenaSql.=" WHERE condicion_exito=true ";
                  $cadenaSql.=" GROUP BY to_char(fecha_medicion,'yyyy-MM'), dane_municipio, tecnologia_instalada";
                  $cadenaSql.=" ORDER BY fecha,dane_municipio) as b";
                  $cadenaSql.=" ON (a.fecha=b.fecha and a.dane_municipio=b.dane_municipio and a.row_number=b.cinco_porc)";
                  $cadenaSql.=" UNION";
                  $cadenaSql.=" Select c.row_number, c.fecha, c.dane_municipio,c.tecnologia_instalada, c.velocidad_subida,0 as velocidad_bajada";
                  $cadenaSql.=" from";
                  $cadenaSql.=" (Select ROW_NUMBER() OVER(PARTITION BY to_char(fecha_medicion,'yyyy-MM'),dane_municipio ORDER BY to_char(fecha_medicion,'yyyy-MM'), dane_municipio,velocidad_subida ASC) AS row_number,to_char(fecha_medicion,'yyyy-MM') as fecha, dane_municipio,tecnologia_instalada, velocidad_subida";
                  $cadenaSql.=" from logica.info_indca_veloc";
                  $cadenaSql.=" WHERE condicion_exito=true ";
                  $cadenaSql.=" ) as c";
                  $cadenaSql.=" inner join";
                  $cadenaSql.=" (Select to_char(fecha_medicion,'yyyy-MM') as fecha, dane_municipio,tecnologia_instalada,count(*) as total_registro,CASE WHEN round(count(*)*0.05)=0 THEN 1 ELSE round(count(*)*0.05) END as cinco_porc";
                  $cadenaSql.=" from logica.info_indca_veloc";
                  $cadenaSql.=" WHERE condicion_exito=true ";
                  $cadenaSql.=" GROUP BY to_char(fecha_medicion,'yyyy-MM'), dane_municipio, tecnologia_instalada";
                  $cadenaSql.=" ORDER BY fecha,dane_municipio) as d";
                  $cadenaSql.=" ON (c.fecha=d.fecha and c.dane_municipio=d.dane_municipio and c.row_number=d.cinco_porc)";
                  $cadenaSql.=" ) as qq";
                  $cadenaSql.=" GROUP BY qq.row_number, qq.fecha, qq.dane_municipio, qq.tecnologia_instalada";
                  $cadenaSql.=" ) as vel";
                  $cadenaSql.=" ON";
                  $cadenaSql.=" (qqqq.fecha=vel.fecha and qqqq.dane_municipio=vel.dane_municipio)";
                  $cadenaSql.=" left outer join";
                  $cadenaSql.=" (Select dane_municipio, count(*) accesos_activos";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_registro=true";
                  $cadenaSql.=" and estado_servicio='ET0'";
                  $cadenaSql.=" and to_char(fecha_novedad,'yyyy-MM')<=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" group by dane_municipio) as j";
                  $cadenaSql.=" ON(qqqq.dane_municipio=j.dane_municipio)";
                  $cadenaSql.=" where qqqq.fecha=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" ORDER BY qqqq.fecha,qqqq.dane_municipio";
                  $cadenaSql.=" )qqqqq";
                  if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                     $cadenaSql .= " where qqqqq.dane_municipio='" . $_REQUEST['municipio'] . "'";

                  };


                    break;
                case 'consultarInformacionB':
                  $cadenaSql=" Select * from(";
                  $cadenaSql.=" Select to_char(v.fecha_medicion,'yyyy-MM') as anho,to_char(v.fecha_medicion,'dd-MM-yyyy hh:mi:ss') as fecha, v.dane_municipio, id_urbanizacion as urbanizacion,v.id_beneficiario,v.velocidad_bajada, 'Bajada' as Sentido, v.tecnologia_instalada, meta_proyecto as meta";
                  $cadenaSql.=" from logica.info_indca_veloc v ";
                  $cadenaSql.=" UNION";
                  $cadenaSql.=" Select to_char(fecha_medicion,'yyyy-MM') as anho,to_char(fecha_medicion,'dd-MM-yyyy hh:mi:ss') as fecha, dane_municipio, id_urbanizacion as urbanizacion,id_beneficiario,velocidad_subida, 'Subida' as Sentido, tecnologia_instalada, meta_proyecto as meta";
                  $cadenaSql.=" from logica.info_indca_veloc) as a";
                  $cadenaSql.=" where anho=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                     $cadenaSql .= " AND a.dane_municipio='" . $_REQUEST['municipio'] . "'";

                  }
                  $cadenaSql.=" ORDER BY a.anho DESC, a.dane_municipio, a.sentido";


                  //      var_dump(" where anho=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')"  );
                        break;
                case 'consultarInformacionC':
                  $cadenaSql=" SELECT ";
                  $cadenaSql.=" to_char(info_pqr.fecha_registro,'yyyy-mm') as fecha,";
                  $cadenaSql.=" to_char(info_pqr.fecha_registro, 'dd-mm-yyyy hh:mi:ss') as fecha_medicion,";
                  $cadenaSql.=" info_pqr.dane_municipio, ";
                  $cadenaSql.=" info_pqr.identificacion_proyecto, ";
                  $cadenaSql.=" info_pqr.id_beneficiario, ";
                  $cadenaSql.=" info_pqr.numero_ticket, ";
                  $cadenaSql.=" to_char(info_pqr.fecha_apertura, 'dd-mm-yyyy hh:mi:ss') as fecha_apertura,";
                  $cadenaSql.=" CASE info_pqr.identificador_ticket ";
                  $cadenaSql.=" WHEN '0' THEN 'Cerrado'";
                  $cadenaSql.=" WHEN '1' THEN 'Abierto'";
                  $cadenaSql.=" WHEN '2' THEN 'Parada de Reloj'";
                  $cadenaSql.=" WHEN '3' THEN 'Reabierto'";
                  $cadenaSql.=" END as identificador_ticket,";
                  $cadenaSql.=" info_pqr.descripcion_diagnostico_contratista, ";
                  $cadenaSql.=" info_pqr.solucion, ";
                  $cadenaSql.=" info_pqr.meta_proyecto";
                  $cadenaSql.=" FROM ";
                  $cadenaSql.=" logica.info_pqr";
                  $cadenaSql.=" WHERE ";
                  $cadenaSql.=" info_pqr.tipo_ticket = '1112' ";
                  $cadenaSql.=" AND to_char(info_pqr.fecha_registro,'yyyy-mm')=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                     $cadenaSql .= " AND info.pqr.dane_municipio='" . $_REQUEST['municipio'] . "'";

                  }
                  $cadenaSql.=" ORDER BY";
                  $cadenaSql.=" fecha ASC, ";
                  $cadenaSql.=" info_pqr.dane_municipio ASC;";
                  $cadenaSql.=" ";
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
