<?php
namespace reportes\estadoBeneficiarios;
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
            case 'consultarMetas':
                $cadenaSql = " SELECT DISTINCT meta, 'META '||meta descripcion";
                $cadenaSql .= " FROM parametros.proyectos_metas";
                break;

            case 'consultaGeneralBeneficiariosPorcentaje':

                $cadenaSql = " SELECT municipio,pm.proyecto,meta,total.id_proyecto, ";
                $cadenaSql .= " num_beneficiarios as beneficiarios_meta, ";
                $cadenaSql .= " beneficiarios_sistema, ";
                $cadenaSql .= " (contratos*100)/num_beneficiarios as ventas, ";
                $cadenaSql .= " (beneficiarios_sistema*100)/num_beneficiarios as preventas, ";
                $cadenaSql .= " (asignacion_portatiles*100)/num_beneficiarios as asignacion_portatiles, ";
                $cadenaSql .= " (asignacion_servicios*100)/num_beneficiarios as asignacion_servicios, ";
                $cadenaSql .= " (revision*100)/num_beneficiarios as revision, ";
                $cadenaSql .= " (activacion*100)/num_beneficiarios as activacion, ";
                $cadenaSql .= " (aprobacion*100)/num_beneficiarios as aprobacion";
                $cadenaSql .= " FROM(SELECT proyecto, id_proyecto,";
                $cadenaSql .= " count(bp.id_beneficiario) as beneficiarios_sistema,";
                $cadenaSql .= " count(c.id_beneficiario) as contratos,";
                $cadenaSql .= " count(ap.id_beneficiario) as asignacion_portatiles,";
                $cadenaSql .= " count(aes.id_beneficiario) as asignacion_servicios,";
                $cadenaSql .= " count(revision.id_beneficiario) as revision,";
                $cadenaSql .= " count(apnull.id_beneficiario) as activacion,";
                $cadenaSql .= " count(bp.estado_beneficiario) filter (where bp.estado_beneficiario='APROBACION') as aprobacion";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.contrato c ON c.id_beneficiario=bp.id_beneficiario AND bp.proyecto=c.urbanizacion";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_portatil ap on ap.id_beneficiario=bp.id_beneficiario AND ap.serial IS NOT NULL AND bp.proyecto=ap.urbanizacion";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_portatil apnull on apnull.id_beneficiario=bp.id_beneficiario";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_servicios aes on aes.id_beneficiario=bp.id_beneficiario AND aes.serial_esc IS NOT NULL";
                $cadenaSql .= " LEFT JOIN (SELECT distinct id_beneficiario, estado_registro FROM interoperacion.documentos_contrato) as revision on revision.id_beneficiario=bp.id_beneficiario and revision.estado_registro=TRUE";
                $cadenaSql .= " WHERE bp.estado_registro=TRUE";
                $cadenaSql .= " GROUP BY proyecto, id_proyecto) as total";
                $cadenaSql .= " right JOIN parametros.proyectos_metas pm on pm.id_proyecto=total.id_proyecto";
                $cadenaSql .= " JOIN parametros.urbanizacion u ON u.id_urbanizacion=pm.id_proyecto";
                $cadenaSql .= " JOIN parametros.municipio m ON u.codigo_mun=m.codigo_mun";
                if ($variable != '0') {
                    $cadenaSql .= " WHERE meta='" . $variable . "'";
                }

                break;

            case 'consultaGeneralBeneficiariosNumerico':

                $cadenaSql = " SELECT municipio,pm.proyecto,meta, ";
                $cadenaSql .= " num_beneficiarios as beneficiarios_meta ,id_urbanizacion as id_proyecto, ";
                $cadenaSql .= " beneficiarios_sistema, ";
                $cadenaSql .= " contratos, ";
                $cadenaSql .= " asignacion_portatiles, ";
                $cadenaSql .= " asignacion_servicios, ";
                $cadenaSql .= " revision, ";
                $cadenaSql .= " activacion, ";
                $cadenaSql .= " aprobacion";
                $cadenaSql .= " FROM(SELECT proyecto, id_proyecto,";
                $cadenaSql .= " count(bp.id_beneficiario) as beneficiarios_sistema,";
                $cadenaSql .= " count(c.id_beneficiario) as contratos,";
                $cadenaSql .= " count(ap.id_beneficiario) as asignacion_portatiles,";
                $cadenaSql .= " count(aes.id_beneficiario) as asignacion_servicios,";
                $cadenaSql .= " count(revision.id_beneficiario) as revision,";
                $cadenaSql .= " count(apnull.id_beneficiario) as activacion,";
                $cadenaSql .= " count(bp.estado_beneficiario) filter (where bp.estado_beneficiario='APROBACION') as aprobacion";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.contrato c ON c.id_beneficiario=bp.id_beneficiario AND bp.proyecto=c.urbanizacion";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_portatil ap on ap.id_beneficiario=bp.id_beneficiario AND ap.serial IS NOT NULL AND bp.proyecto=ap.urbanizacion";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_portatil apnull on apnull.id_beneficiario=bp.id_beneficiario";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_servicios aes on aes.id_beneficiario=bp.id_beneficiario AND aes.serial_esc IS NOT NULL";
                $cadenaSql .= " LEFT JOIN (SELECT distinct id_beneficiario, estado_registro FROM interoperacion.documentos_contrato) as revision on revision.id_beneficiario=bp.id_beneficiario and revision.estado_registro=TRUE";
                $cadenaSql .= " WHERE bp.estado_registro=TRUE";
                $cadenaSql .= " GROUP BY proyecto, id_proyecto) as total";
                $cadenaSql .= " RIGHT JOIN parametros.proyectos_metas pm on pm.id_proyecto=total.id_proyecto";
                $cadenaSql .= " JOIN parametros.urbanizacion u ON u.id_urbanizacion=pm.id_proyecto";
                $cadenaSql .= " JOIN parametros.municipio m ON u.codigo_mun=m.codigo_mun";
                if ($variable != '0') {
                    $cadenaSql .= " WHERE meta='" . $variable . "'";
                }
                break;

            case 'consultaParticularBeneficiarios':
                $cadenaSql = " SELECT";
                $cadenaSql .= " precalculos.id_beneficiario,ben.identificacion||' - '||ben.nombre||' '||ben.primer_apellido||' '||ben.segundo_apellido as beneficiario,";
                $cadenaSql .= " precalculos.proyecto,precalculos.id_proyecto,";
                $cadenaSql .= " meta, num_beneficiarios as beneficiarios_meta,cant_beneficiarios as beneficiarios_sistema,";
                $cadenaSql .= " (cant_beneficiarios*100) as preventas,";
                $cadenaSql .= " (contratos*100)as contrato,";
                $cadenaSql .= " (portatiles_asignados*100) as asignacion_portatiles,";
                $cadenaSql .= " (servicios_asignados*100)as asignacion_servicios,";
                $cadenaSql .= " (nactivacion*100) as activacion,";
                $cadenaSql .= " (nrevision*100) as revision,";
                $cadenaSql .= " (naprobacion*100) as aprobacion";
                $cadenaSql .= " FROM (SELECT bp.proyecto,bp.id_proyecto,bp.id_beneficiario,";
                $cadenaSql .= " count(bp.id_beneficiario) as cant_beneficiarios,";
                $cadenaSql .= " count(c.id_beneficiario) as contratos,";
                $cadenaSql .= " count(ap.id_beneficiario) as portatiles_asignados,";
                $cadenaSql .= " count(aes.id_beneficiario) as servicios_asignados,";
                $cadenaSql .= " count(revision.id_beneficiario) as nrevision,";
                $cadenaSql .= " count(apnull.id_beneficiario) as nactivacion,";
                $cadenaSql .= " count(bp.estado_beneficiario) filter (where bp.estado_beneficiario='APROBACION') as naprobacion";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.contrato c ON c.id_beneficiario=bp.id_beneficiario AND bp.proyecto=c.urbanizacion";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_portatil ap on ap.id_beneficiario=bp.id_beneficiario AND ap.serial IS NOT NULL";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_portatil apnull on apnull.id_beneficiario=bp.id_beneficiario";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_servicios aes on aes.id_beneficiario=bp.id_beneficiario AND aes.serial_esc IS NOT NULL";
                $cadenaSql .= " LEFT JOIN";
                $cadenaSql .= " (SELECT DISTINCT dc.id_beneficiario";
                $cadenaSql .= " FROM interoperacion.contrato io";
                $cadenaSql .= " JOIN interoperacion.documentos_contrato dc ON dc.id_beneficiario=io.id_beneficiario";
                $cadenaSql .= " WHERE ruta_documento_contrato IS NOT NULL";
                $cadenaSql .= " AND dc.tipologia_documento=132 and dc.estado_registro=TRUE";
                $cadenaSql .= " ) as revision on revision.id_beneficiario=bp.id_beneficiario";
                $cadenaSql .= " WHERE bp.estado_registro=TRUE";
                $cadenaSql .= " AND bp.id_proyecto='" . $variable . "'";
                $cadenaSql .= " GROUP BY bp.id_beneficiario,bp.proyecto,bp.id_proyecto";
                $cadenaSql .= " order by bp.id_proyecto ASC) as precalculos";
                $cadenaSql .= " JOIN parametros.proyectos_metas on precalculos.id_proyecto=proyectos_metas.id_proyecto";
                $cadenaSql .= " JOIN interoperacion.beneficiario_potencial ben on precalculos.id_beneficiario=ben.id_beneficiario";
                $cadenaSql .= " ORDER BY asignacion_portatiles DESC";
                break;

            case 'consultarDocumentos':
                $cadenaSql = " SELECT DISTINCT id_beneficiario ";
                $cadenaSql .= " FROM interoperacion.documentos_contrato";
                $cadenaSql .= " WHERE id_beneficiario='" . $variable . "'";
                $cadenaSql .= " AND estado_registro='TRUE'";

                break;
        }

        return $cadenaSql;
    }
}
?>



