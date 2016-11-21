<?php
namespace reportes\parametrizacionInforme;
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

            case 'consultarActividades':

                $cadenaSql = " SELECT DISTINCT ws.id as data , ws.id||' - '||ws.subject as value, ws.project_id ";
                $cadenaSql .= " FROM public.work_packages AS ws";
                $cadenaSql .= " JOIN public.work_packages AS sw ON sw.project_id=ws.project_id AND sw.project_id='" . $_REQUEST['proyecto'] . "'";
                $cadenaSql .= " WHERE cast(ws.id as text) ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= " OR ws.subject ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= " ORDER BY ws.id ASC LIMIT 10;";

                break;

            case 'consultarProyectosWman':
                $cadenaSql = " SELECT DISTINCT id as data , name as value ";
                $cadenaSql .= " FROM projects";
                $cadenaSql .= " WHERE description ILIKE '%(Proyecto/Urbanizacion)%' ";
                $cadenaSql .= "AND identifier ILIKE '%wman%' ";
                $cadenaSql .= "AND name ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

            case 'consultarProyectosHfc':
                $cadenaSql = " SELECT DISTINCT id as data , name as value ";
                $cadenaSql .= " FROM projects";
                $cadenaSql .= " WHERE description ILIKE '%(Proyecto/Urbanizacion)%' ";
                $cadenaSql .= "AND identifier NOT ILIKE '%wman%' ";
                $cadenaSql .= "AND name ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

            case 'consultarProyectosCabecera':
                $cadenaSql = " SELECT DISTINCT id as data , name as value ";
                $cadenaSql .= " FROM projects";
                $cadenaSql .= " WHERE identifier ILIKE '%cabecera%' ";
                $cadenaSql .= "AND name ILIKE '%" . $_GET['query'] . "%' ";
                $cadenaSql .= "LIMIT 10; ";
                break;

            case 'consultarProyectosCore':
                $cadenaSql = " SELECT id as data , name as value ";
                $cadenaSql .= " FROM projects";
                $cadenaSql .= " WHERE identifier='ins';";
                break;

            case 'consultarCampos':
                $cadenaSql = " SELECT tipo_proyecto, abr_tipo_proyecto, tipo, abr_tipo, ";
                $cadenaSql .= " sub_tipo, abr_sub_tipo, nombre_formulario, nombre_campo, campo, ";
                $cadenaSql .= " identificador_campo";
                $cadenaSql .= " FROM parametros.campos_reporte";
                $cadenaSql .= " WHERE tipo_proyecto='" . $variable . "'";
                $cadenaSql .= " AND estado_registro=TRUE";
                $cadenaSql .= " ORDER BY id_campos ASC;";
                break;

            case 'consultarCamposGeneral':
                $cadenaSql = " SELECT  tipo_proyecto, abr_tipo_proyecto, tipo, abr_tipo, ";
                $cadenaSql .= " sub_tipo, abr_sub_tipo, nombre_formulario, nombre_campo, campo, ";
                $cadenaSql .= " identificador_campo";
                $cadenaSql .= " FROM parametros.campos_reporte";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " ORDER BY id_campos ASC;";
                break;

            case 'consultarTema':
                $cadenaSql = " SELECT DISTINCT tipo,";
                $cadenaSql .= " sub_tipo ";
                $cadenaSql .= " FROM parametros.campos_reporte";
                $cadenaSql .= " WHERE tipo_proyecto='" . $variable . "'";
                $cadenaSql .= " AND estado_registro=TRUE";
                $cadenaSql .= " GROUP BY tipo, sub_tipo ORDER BY tipo ";
                break;

            case 'registrarParametrizacion':
                $cadenaSql = " INSERT INTO parametros.parametrizacion_reporte(tipo_proyecto, id_proyecto, campo, valor_campo, ";
                $cadenaSql .= " valor_actividad, info_hijos)";
                $cadenaSql .= " VALUES ('" . $variable['tipo_proyecto'] . "',";
                $cadenaSql .= " '" . $variable['id_proyecto'] . "',";
                $cadenaSql .= " '" . $variable['campo'] . "',";
                $cadenaSql .= " '" . $variable['valor_campo'] . "',";
                $cadenaSql .= " '" . $variable['valor_actividad'] . "', ";
                $cadenaSql .= " '" . $variable['info_hijos'] . "');";
                break;

            case 'eliminarInformacionProyecto':
                $cadenaSql = " UPDATE parametros.parametrizacion_reporte";
                $cadenaSql .= " SET estado_registro=FALSE ";
                $cadenaSql .= " WHERE id_proyecto='" . $variable . "';";
                break;

            case 'consultarParametrizacion':
                $cadenaSql = " SELECT DISTINCT tipo_proyecto, id_proyecto";
                $cadenaSql .= " FROM parametros.parametrizacion_reporte";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " ORDER BY id_proyecto ASC;";
                break;

            case 'consultarNombreProyecto':
                $cadenaSql = " SELECT name";
                $cadenaSql .= " FROM public.projects";
                $cadenaSql .= " WHERE id='" . $variable . "';";
                break;

        }

        return $cadenaSql;
    }
}
?>

