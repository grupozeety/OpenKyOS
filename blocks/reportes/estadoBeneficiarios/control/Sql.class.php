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
            case 'consultarBloques':

                $cadenaSql = " SELECT id_bloque, nombre, descripcion, grupo ";
                $cadenaSql .= " FROM " . $prefijo . "bloque;";

                break;

            case 'consultaGeneralBeneficiarios':
                $cadenaSql = " SELECT";
                $cadenaSql .= " proyecto,id_proyecto,";
                $cadenaSql .= " beneficiarios,";
                $cadenaSql .= " (beneficiarios*100)/beneficiarios as preventas,";
                $cadenaSql .= " (contratos*100)/ beneficiarios as ventas,";
                $cadenaSql .= " (portatiles_asignados*100)/ beneficiarios as asignacion_portatiles,";
                $cadenaSql .= " (servicios_asignados*100)/ beneficiarios as asignacion_servicios,";
                $cadenaSql .= " (nactivacion*100)/ beneficiarios as activacion,";
                $cadenaSql .= " (nrevision*100)/ beneficiarios as revision,";
                $cadenaSql .= " (naprobacion*100)/ beneficiarios as aprobacion";
                $cadenaSql .= " FROM (SELECT bp.proyecto,bp.id_proyecto, ";
                $cadenaSql .= " count(bp.id_beneficiario) as beneficiarios, ";
                $cadenaSql .= " count(c.id_beneficiario) as contratos, ";
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
                $cadenaSql .= " LEFT JOIN ";
                $cadenaSql .= " (SELECT dc.id_beneficiario ";
                $cadenaSql .= " FROM interoperacion.contrato io ";
                $cadenaSql .= " JOIN interoperacion.documentos_contrato dc ON dc.id_beneficiario=io.id_beneficiario ";
                $cadenaSql .= " WHERE ruta_documento_contrato IS NOT NULL";
                $cadenaSql .= " AND dc.tipologia_documento=132";
                $cadenaSql .= " ) as revision on revision.id_beneficiario=aes.id_beneficiario";
                $cadenaSql .= " GROUP BY bp.proyecto,bp.id_proyecto ";
                $cadenaSql .= " order by bp.proyecto ASC) as precalculos";
                break;

            case 'consultaParticularBeneficiarios':
                $cadenaSql = " SELECT bp.identificacion||' - '||bp.nombre||' '|| bp.primer_apellido as beneficiario,";
                $cadenaSql .= " count(c.id_beneficiario)*100 as contratos,";
                $cadenaSql .= " count(ap.id_beneficiario)*100 as portatiles_asignados,";
                $cadenaSql .= " count(aes.id_beneficiario)*100 as servicios_asignados,";
                $cadenaSql .= " count(revision.id_beneficiario)*100 as nrevision,";
                $cadenaSql .= " count(apnull.id_beneficiario)*100 as nactivacion,";
                $cadenaSql .= " count(bp.estado_beneficiario) filter (where bp.estado_beneficiario='APROBACION') *100 as naprobacion";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " LEFT JOIN interoperacion.contrato c ON c.id_beneficiario=bp.id_beneficiario AND bp.proyecto=c.urbanizacion";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_portatil ap on ap.id_beneficiario=bp.id_beneficiario AND ap.serial IS NOT NULL";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_portatil apnull on apnull.id_beneficiario=bp.id_beneficiario";
                $cadenaSql .= " LEFT JOIN interoperacion.acta_entrega_servicios aes on aes.id_beneficiario=bp.id_beneficiario AND aes.serial_esc IS NOT NULL";
                $cadenaSql .= " LEFT JOIN";
                $cadenaSql .= " (SELECT dc.id_beneficiario";
                $cadenaSql .= " FROM interoperacion.contrato io";
                $cadenaSql .= " JOIN interoperacion.documentos_contrato dc ON dc.id_beneficiario=io.id_beneficiario";
                $cadenaSql .= " WHERE ruta_documento_contrato IS NOT NULL";
                $cadenaSql .= " AND dc.tipologia_documento=132";
                $cadenaSql .= " ) as revision on revision.id_beneficiario=aes.id_beneficiario";
                $cadenaSql .= " WHERE bp.id_proyecto='" . $variable . "'";
                $cadenaSql .= " group by beneficiario";
                break;
        }

        return $cadenaSql;
    }
}
?>



