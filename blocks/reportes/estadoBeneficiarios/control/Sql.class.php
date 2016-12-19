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
                $cadenaSql = " SELECT ";
                $cadenaSql .= " precalculos.proyecto,";
                $cadenaSql .= " precalculos.id_proyecto,";
                $cadenaSql .= " meta,";
                $cadenaSql .= " num_beneficiarios as beneficiarios_meta,";
                $cadenaSql .= " cant_beneficiarios as beneficiarios_sistema,";
                $cadenaSql .= " (cant_beneficiarios*100)/num_beneficiarios as preventas,";
                $cadenaSql .= " (contratos*100)/num_beneficiarios as ventas,";
                $cadenaSql .= " (portatiles_asignados*100)/num_beneficiarios as asignacion_portatiles,";
                $cadenaSql .= " (servicios_asignados*100)/num_beneficiarios as asignacion_servicios,";
                $cadenaSql .= " (nactivacion*100)/num_beneficiarios as activacion,";
                $cadenaSql .= " (nrevision*100)/num_beneficiarios as revision,";
                $cadenaSql .= " (naprobacion*100)/num_beneficiarios as aprobacion";
                $cadenaSql .= " FROM (SELECT bp.proyecto,bp.id_proyecto,";
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
                $cadenaSql .= " GROUP BY bp.id_proyecto,bp.proyecto";
                $cadenaSql .= " order by bp.id_proyecto ASC) as precalculos";
                $cadenaSql .= " JOIN parametros.proyectos_metas on precalculos.id_proyecto=proyectos_metas.id_proyecto ";
                if ($variable != '0') {
                    $cadenaSql .= " AND meta='" . $variable . "'";
                }
                break;

            case 'consultaGeneralBeneficiariosNumerico':
                $cadenaSql = " SELECT bp.proyecto,bp.id_proyecto,";
                $cadenaSql .= " pm.num_beneficiarios as beneficiarios_meta,";
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
                $cadenaSql .= " LEFT JOIN";
                $cadenaSql .= " (SELECT dc.id_beneficiario";
                $cadenaSql .= " FROM interoperacion.contrato io";
                $cadenaSql .= " JOIN interoperacion.documentos_contrato dc ON dc.id_beneficiario=io.id_beneficiario";
                $cadenaSql .= " WHERE ruta_documento_contrato IS NOT NULL";
                $cadenaSql .= " AND dc.tipologia_documento=132 and dc.estado_registro=TRUE";
                $cadenaSql .= " ) as revision on revision.id_beneficiario=bp.id_beneficiario";
                $cadenaSql .= " JOIN parametros.proyectos_metas pm on pm.id_proyecto=bp.id_proyecto";
                $cadenaSql .= " WHERE bp.estado_registro=TRUE";
                if ($variable != '0') {
                    $cadenaSql .= " AND meta='" . $variable . "'";
                }
                $cadenaSql .= " GROUP BY bp.id_proyecto,bp.proyecto,pm.num_beneficiarios";
                $cadenaSql .= " order by bp.id_proyecto ASC";

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
        }

        return $cadenaSql;
    }
}
?>



