<?php

namespace reportes\disponibilidad;

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
                  $cadenaSql=" SELECT c.fecha, c.dane_municipio, ";
                  $cadenaSql.=" c.tickes_abiertos, ";
                  $cadenaSql.=" c.tickes_cerrados, ";
                  $cadenaSql.=" e.tickets_anteriores,";
                  $cadenaSql.=" d.tickes_indisponibilidad,";
                  $cadenaSql.=" d.tiempo_indisponibilidad,";
                  $cadenaSql.=" c.tickes_parada,";
                  $cadenaSql.=" c.tiempo_parada_reloj";
                  $cadenaSql.=" FROM";
                  $cadenaSql.=" (SELECT a.fecha, a.dane_municipio, ";
                  $cadenaSql.=" SUM(a.tickes_abiertos) as tickes_abiertos, ";
                  $cadenaSql.=" SUM(a.tickes_cerrados) as tickes_cerrados, ";
                  $cadenaSql.=" SUM(a.tickes_parada) as tickes_parada,";
                  $cadenaSql.=" b.tiempo_parada_reloj";
                  $cadenaSql.=" from";
                  $cadenaSql.=" (";
                  $cadenaSql.=" SELECT to_char(info_pqr.fecha_apertura,'yyyy-mm') as fecha,";
                  $cadenaSql.=" info_pqr.dane_municipio, ";
                  $cadenaSql.=" count(info_pqr.identificador_ticket) as tickes_abiertos,";
                  $cadenaSql.=" 0 as tickes_cerrados,";
                  $cadenaSql.=" 0 as tickes_parada";
                  $cadenaSql.=" from logica.info_pqr";
                  $cadenaSql.=" WHERE info_pqr.identificador_ticket='1'";
                  $cadenaSql.=" GROUP BY to_char(info_pqr.fecha_apertura,'yyyy-mm') , info_pqr.dane_municipio";
                  $cadenaSql.=" UNION";
                  $cadenaSql.=" SELECT to_char(info_pqr.fecha_apertura,'yyyy-mm') as fecha,";
                  $cadenaSql.=" info_pqr.dane_municipio, ";
                  $cadenaSql.=" 0 as tickes_abiertos,";
                  $cadenaSql.=" count(info_pqr.identificador_ticket) as tickes_cerrados,";
                  $cadenaSql.=" 0 as tickes_parada";
                  $cadenaSql.=" from logica.info_pqr";
                  $cadenaSql.=" WHERE info_pqr.identificador_ticket='0'";
                  $cadenaSql.=" GROUP BY to_char(info_pqr.fecha_apertura,'yyyy-mm') , info_pqr.dane_municipio";
                  $cadenaSql.=" UNION";
                  $cadenaSql.=" SELECT to_char(info_pqr.fecha_apertura,'yyyy-mm') as fecha,";
                  $cadenaSql.=" info_pqr.dane_municipio, ";
                  $cadenaSql.=" 0 as tickes_abiertos,";
                  $cadenaSql.=" 0 as tickes_cerrados,";
                  $cadenaSql.=" count(info_pqr.identificador_ticket) as tickes_parada";
                  $cadenaSql.=" from logica.info_pqr";
                  $cadenaSql.=" WHERE info_pqr.fecha_inicio_parada_reloj is not null";
                  $cadenaSql.=" GROUP BY to_char(info_pqr.fecha_apertura,'yyyy-mm') , info_pqr.dane_municipio";
                  $cadenaSql.=" ) as a";
                  $cadenaSql.=" left outer join";
                  $cadenaSql.=" (";
                  $cadenaSql.=" SELECT to_char(info_pqr.fecha_apertura,'yyyy-mm') as fecha,";
                  $cadenaSql.=" info_pqr.dane_municipio, ";
                  $cadenaSql.=" count(info_pqr.identificador_ticket) as tickes_parada,";
                  $cadenaSql.=" sum(info_pqr.fecha_fin_parada_reloj - info_pqr.fecha_inicio_parada_reloj) as tiempo_parada_reloj";
                  $cadenaSql.=" from logica.info_pqr";
                  $cadenaSql.=" WHERE info_pqr.fecha_inicio_parada_reloj is not null";
                  $cadenaSql.=" GROUP BY to_char(info_pqr.fecha_apertura,'yyyy-mm') , info_pqr.dane_municipio";
                  $cadenaSql.=" ) as b";
                  $cadenaSql.=" on(a.fecha=b.fecha and a.dane_municipio=b.dane_municipio)";
                  $cadenaSql.=" GROUP BY a.fecha , a.dane_municipio,b.tiempo_parada_reloj) as c";
                  $cadenaSql.=" LEFT OUTER JOIN";
                  $cadenaSql.=" (";
                  $cadenaSql.=" SELECT to_char(info_pqr.fecha_apertura,'yyyy-mm') as fecha,";
                  $cadenaSql.=" info_pqr.dane_municipio, ";
                  $cadenaSql.=" count(info_pqr.identificador_ticket) as tickes_indisponibilidad,";
                  $cadenaSql.=" sum(info_pqr.fecha_cierre - info_pqr.fecha_apertura) as tiempo_indisponibilidad";
                  $cadenaSql.=" from logica.info_pqr";
                  $cadenaSql.=" WHERE info_pqr.tipo_ticket in ('1101',";
                  $cadenaSql.=" '1102',";
                  $cadenaSql.=" '1103',";
                  $cadenaSql.=" '1104',";
                  $cadenaSql.=" '1105',";
                  $cadenaSql.=" '1106',";
                  $cadenaSql.=" '1107',";
                  $cadenaSql.=" '1108',";
                  $cadenaSql.=" '1109',";
                  $cadenaSql.=" '1111',";
                  $cadenaSql.=" '1113',";
                  $cadenaSql.=" '1114',";
                  $cadenaSql.=" '1115',";
                  $cadenaSql.=" '3101',";
                  $cadenaSql.=" '1117',";
                  $cadenaSql.=" '3102',";
                  $cadenaSql.=" '1118',";
                  $cadenaSql.=" '3103',";
                  $cadenaSql.=" '2226')";
                  $cadenaSql.=" GROUP BY to_char(info_pqr.fecha_apertura,'yyyy-mm') , info_pqr.dane_municipio";
                  $cadenaSql.=" ) as d";
                  $cadenaSql.=" ON(c.fecha=d.fecha and c.dane_municipio=d.dane_municipio)";
                  $cadenaSql.=" left outer join";
                  $cadenaSql.=" (";
                  $cadenaSql.=" Select to_char(fecha_apertura,'yyyy-mm') as fecha_apertura,dane_municipio, count(*) as tickets_anteriores";
                  $cadenaSql.=" from logica.info_pqr";
                  $cadenaSql.=" where to_char(info_pqr.fecha_apertura,'yyyy-mm')=to_char(('".$_REQUEST ['fecha_final']."'::timestamp - INTERVAL '1 MONTH'),'yyyy-MM')";
                  $cadenaSql.=" AND identificador_ticket='1'";
                  $cadenaSql.=" GROUP BY to_char(fecha_apertura,'yyyy-mm') ,dane_municipio";
                  $cadenaSql.=" ) as e ";
                  $cadenaSql.=" ON (c.dane_municipio=e.dane_municipio)";
                  $cadenaSql.=" WHERE c.fecha=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                     $cadenaSql .= " AND c.dane_municipio='" . $_REQUEST['municipio'] . "'";

                  }
                  $cadenaSql.=" ORDER BY c.fecha DESC, c.dane_municipio";

                    break;
                case 'consultarInformacionAB':
                  $cadenaSql=" Select c.fecha, c.mes,c.dane_municipio, case when c.indicador>=0 then c.indicador else '100'end as indicador, c.umbral, case when c.indicador <=95 then 'F' else 'E'end as cumplimiento";
                  $cadenaSql.=" From";
                  $cadenaSql.=" (";
                  $cadenaSql.=" Select a.fecha,a.mes, a.dane_municipio, (1-(a.tiempo_falla/b.abajo))*100 as indicador, a.umbral";
                  $cadenaSql.=" From";
                  $cadenaSql.=" (SELECT ";
                  $cadenaSql.=" to_char(info_pqr.fecha_apertura,'yyyy-mm') as fecha,";
                  $cadenaSql.=" to_char(info_pqr.fecha_apertura,'MM') as mes,";
                  $cadenaSql.=" info_pqr.dane_municipio, ";
                  $cadenaSql.=" sum((extract(EPOCH from (info_pqr.fecha_cierre - info_pqr.fecha_apertura)))/3600) as tiempo_falla,";
                  $cadenaSql.=" 95 as umbral";
                  $cadenaSql.=" FROM ";
                  $cadenaSql.=" logica.info_pqr";
                  $cadenaSql.=" WHERE to_char(info_pqr.fecha_apertura,'yyyy-MM')=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" GROUP BY to_char(info_pqr.fecha_apertura,'yyyy-mm') ,mes, info_pqr.dane_municipio";
                  $cadenaSql.=" ORDER BY fecha DESC, dane_municipio) as a";
                  $cadenaSql.=" left outer join";
                  $cadenaSql.=" (";
                  $cadenaSql.=" Select dane_municipio, fecha, (accesos_activos*horas) as abajo";
                  $cadenaSql.=" from";
                  $cadenaSql.=" (";
                  $cadenaSql.=" Select dane_municipio,to_char(fecha_novedad,'yyyy-MM') as fecha, count(*) accesos_activos, 24*(DATE_PART('days', DATE_TRUNC('month', info_avan_oper.fecha_novedad) + '1 MONTH'::INTERVAL - DATE_TRUNC('month', info_avan_oper.fecha_novedad))) as horas";
                  $cadenaSql.=" from logica.info_avan_oper";
                  $cadenaSql.=" where estado_registro=true";
                  $cadenaSql.=" and estado_servicio='ET0'";
                  $cadenaSql.=" and to_char(fecha_novedad,'yyyy-MM')<=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" group by dane_municipio, horas, fecha";
                  $cadenaSql.=" ) as abajo)b";
                  $cadenaSql.=" ON(b.dane_municipio=a.dane_municipio and a.fecha=b.fecha)";
                  $cadenaSql.=" ) as c";
                  if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                     $cadenaSql .= " WHERE c.dane_municipio='" . $_REQUEST['municipio'] . "'";

                  }
                  $cadenaSql.=" order by fecha ASC,dane_municipio";



                  break;
                case 'consultarInformacionB':
                  $cadenaSql=" Select to_char(info_pqr.fecha_apertura,'yyyy') as anho,";
                  $cadenaSql.=" to_char(info_pqr.fecha_apertura,'mm') as mes, ";
                  $cadenaSql.=" info_pqr.dane_municipio,";
                  $cadenaSql.=" info_pqr.identificacion_proyecto,";
                  $cadenaSql.=" info_pqr.id_beneficiario,";
                  $cadenaSql.=" info_pqr.fuente_generacion,";
                  $cadenaSql.=" info_pqr.numero_ticket,";
                  $cadenaSql.=" to_char(info_pqr.fecha_apertura,'dd/mm/yyyy hh:mi:ss') as fecha_apertura,";
                  $cadenaSql.=" to_char(info_pqr.fecha_cierre,'dd/mm/yyyy hh:mi:ss') as fecha_cierre,";
                  $cadenaSql.=" to_char(info_pqr.fecha_registro,'dd/mm/yyyy hh:mi:ss') as fecha_registro,";
                  $cadenaSql.=" CASE info_pqr.afectacion_servicio";
                  $cadenaSql.=" WHEN true THEN 'S'";
                  $cadenaSql.=" WHEN false THEN 'N'";
                  $cadenaSql.=" END as afectacion_servicio,";
                  $cadenaSql.=" info_pqr.bandera_parad_reloj,";
                  $cadenaSql.=" info_pqr.fecha_inicio_parada_reloj,";
                  $cadenaSql.=" info_pqr.fecha_fin_parada_reloj,";
                  $cadenaSql.=" (info_pqr.fecha_cierre - info_pqr.fecha_apertura) as tiempo_falla,";
                  $cadenaSql.=" (info_pqr.fecha_fin_parada_reloj - info_pqr.fecha_inicio_parada_reloj) as tiempo_parada_reloj,";
                  $cadenaSql.=" info_pqr.responsable,";
                  $cadenaSql.=" info_pqr.tipo_ticket,";
                  $cadenaSql.=" CASE info_pqr.identificador_ticket";
                  $cadenaSql.=" WHEN '0' THEN 'Cerrado'";
                  $cadenaSql.=" WHEN '1' THEN 'Abierto'";
                  $cadenaSql.=" WHEN '2' THEN 'Parada de Reloj'";
                  $cadenaSql.=" WHEN '3' THEN 'Reabierto'";
                  $cadenaSql.=" END as estado_ticket,";
                  $cadenaSql.=" info_pqr.tiempo_resolucion,";
                  $cadenaSql.=" info_pqr.justificacion_parada_reloj,";
                  $cadenaSql.=" info_pqr.descripcion_ticket,";
                  $cadenaSql.=" info_pqr.descripcion_diagnostico_contratista,";
                  $cadenaSql.=" info_pqr.solucion,";
                  $cadenaSql.=" info_pqr.meta_proyecto";
                  $cadenaSql.=" FROM ";
                  $cadenaSql.=" logica.info_pqr";
                  $cadenaSql.=" WHERE to_char(info_pqr.fecha_registro,'yyyy-MM')=to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                     $cadenaSql .= " AND info_pqr.dane_municipio='" . $_REQUEST['municipio'] . "'";

                  }
                  $cadenaSql.=" ORDER BY anho DESC,mes DESC,dane_municipio";
                  break;
                case 'consultarInformacionC':
                  $cadenaSql=" SELECT ";
                  $cadenaSql.=" to_char(fecha_registro,'yyyy-mm') as fecha,";
                  $cadenaSql.=" dane_municipio,";
                  $cadenaSql.=" identificacion_proyecto,";
                  $cadenaSql.=" id_beneficiario,";
                  $cadenaSql.=" CASE estado_servicio";
                  $cadenaSql.=" WHEN 'ET0' THEN 'Activo'";
                  $cadenaSql.=" WHEN 'ET1' THEN 'Inactivo'";
                  $cadenaSql.=" WHEN 'ET11' THEN 'Suspendido por voluntad del usuario'";
                  $cadenaSql.=" WHEN 'EP01' THEN 'En Operacion'";
                  $cadenaSql.=" WHEN 'EP02' THEN 'Suspendido'";
                  $cadenaSql.=" WHEN 'EP03' THEN 'En Traslado'";
                  $cadenaSql.=" WHEN 'EP04' THEN 'Retirado'";
                  $cadenaSql.=" WHEN 'EP05' THEN 'En Instalacion'";
                  $cadenaSql.=" WHEN 'EP06' THEN 'En Mantenimiento'";
                  $cadenaSql.=" WHEN 'EP07' THEN 'En Reubicacion'";
                  $cadenaSql.=" END as estado_servicio,";
                  $cadenaSql.=" novedad_estado as causa";
                  $cadenaSql.=" FROM ";
                  $cadenaSql.=" logica.info_avan_oper";
                  $cadenaSql.=" WHERE to_char(fecha_registro,'yyyy-mm')  = to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                     $cadenaSql .= " AND dane_municipio='" . $_REQUEST['municipio'] . "'";

                  }
                  $cadenaSql.=" order by fecha ASC";
                        break;
            case 'consultarInformacionD':
                  $cadenaSql=" SELECT ";
                  $cadenaSql.=" to_char(fecha_registro,'yyyy-mm') as fecha,";
                  $cadenaSql.=" to_char(fecha_registro,'yyyy') as anho,";
                  $cadenaSql.=" to_char(fecha_registro,'mm') as mes,";
                  $cadenaSql.=" numero_ticket,";
                  $cadenaSql.=" accion_seguimiento";
                  $cadenaSql.=" FROM";
                  $cadenaSql.=" logica.info_pqr";
                  $cadenaSql.=" WHERE to_char(fecha_registro,'yyyy-mm')  = to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" order by fecha ASC";
                break;
            case 'consultarInformacionE':
                  $cadenaSql=" SELECT ";
                  $cadenaSql.=" numero_ticket,";
                  $cadenaSql.=" to_char(fecha_registro,'yyyy-mm') as fecha,";
                  $cadenaSql.=" descripcion_diagnostico_contratista,";
                  $cadenaSql.=" pruebas_mantenimiento,";
                  $cadenaSql.=" solucion";
                  $cadenaSql.=" FROM logica.info_pqr";
                  $cadenaSql.=" WHERE info_pqr.tipo_ticket='5368'";
                  $cadenaSql.=" AND to_char(fecha_registro,'yyyy-mm')  = to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  $cadenaSql.=" order by fecha DESC";
                break;
              case 'consultarInformacionF':
                  $cadenaSql=" SELECT dane_municipio,";
                  $cadenaSql.=" id_beneficiario,";
                  $cadenaSql.=" to_char(fecha_medicion, 'mm') as mes,";
                  $cadenaSql.=" to_char(fecha_medicion, 'yyyy-mm-dd hh:mi:ss') as fecha,";
                  $cadenaSql.=" velocidad_subida,";
                  $cadenaSql.=" velocidad_bajada,";
                  $cadenaSql.=" observaciones_no_medicion";
                  $cadenaSql.=" FROM logica.info_indca_veloc";
                  $cadenaSql.=" WHERE to_char(fecha_medicion, 'yyyy-MM') = to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                     $cadenaSql .= " AND dane_municipio='" . $_REQUEST['municipio'] . "'";

                  }
                  $cadenaSql.=" order by fecha DESC,dane_municipio,id_beneficiario";
                  break;
              case 'consultarInformacionG':
                  $cadenaSql=" SELECT dane_municipio,";
                  $cadenaSql.=" id_beneficiario,";
                  $cadenaSql.=" to_char(fecha_medicion, 'mm') as mes,";
                  $cadenaSql.=" to_char(fecha_medicion, 'yyyy-mm-dd hh:mi:ss') as fecha,";
                  $cadenaSql.=" '' as paginas";
                  $cadenaSql.=" FROM logica.info_indca_veloc";
                  $cadenaSql.=" WHERE to_char(fecha_medicion, 'yyyy-MM') = to_char('".$_REQUEST ['fecha_final']."'::timestamp,'yyyy-MM')";
                  if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                     $cadenaSql .= " AND dane_municipio='" . $_REQUEST['municipio'] . "'";

                  }
                  $cadenaSql.=" order by fecha DESC,dane_municipio,id_beneficiario";

        }

        return $cadenaSql;
    }
}
?>
