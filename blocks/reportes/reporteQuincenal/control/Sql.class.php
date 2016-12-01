<?php
namespace reportes\reporteQuincenal;
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
            case 'consultarProyectosParametrizados':
                $cadenaSql = " SELECT DISTINCT tipo_proyecto, id_proyecto";
                $cadenaSql .= " FROM parametros.parametrizacion_reporte";
                $cadenaSql .= " WHERE estado_registro= TRUE AND tipo_proyecto <>'core' ";
                $cadenaSql .= " ORDER BY id_proyecto;";
                break;

            case 'consultarCamposParametrizados':
                $cadenaSql = " SELECT DISTINCT pr.campo, pr.valor_campo, ";
                $cadenaSql .= " pr.valor_actividad, pr.info_hijos, cr.tipo,cr.sub_tipo,cr.nombre_formulario ";
                $cadenaSql .= " FROM parametros.parametrizacion_reporte AS pr ";
                $cadenaSql .= " JOIN parametros.campos_reporte as cr ON cr.identificador_campo=pr.campo";
                $cadenaSql .= " WHERE pr.estado_registro= TRUE";
                $cadenaSql .= " AND pr.id_proyecto='" . $variable . "'";
                break;

            case 'consultarInformacionReporte':
                $cadenaSql = " SELECT *";
                $cadenaSql .= " FROM public.reporte_semanal";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " AND ( fecha_registro BETWEEN '" . $_REQUEST['fecha_inicio'] . " 00:00:00'::timestamp AND '" . $_REQUEST['fecha_final'] . " 23:59:59'::timestamp);";
                break;

            case 'consultarInformacionCore':
                $cadenaSql = " SELECT DISTINCT tipo_proyecto, id_proyecto";
                $cadenaSql .= " FROM parametros.parametrizacion_reporte";
                $cadenaSql .= " WHERE estado_registro= TRUE";
                $cadenaSql .= " AND tipo_proyecto= 'core' ";
                $cadenaSql .= " ORDER BY id_proyecto;";
                break;
        }

        return $cadenaSql;
    }
}
?>

