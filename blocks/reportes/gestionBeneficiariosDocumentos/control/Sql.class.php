<?php

namespace reportes\gestionBeneficiariosDocumentos;

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
                $cadenaSql .= " AND departamento IS NOT NULL;";
                break;

            case 'consultarMunicipio':

                $cadenaSql = " SELECT DISTINCT municipio as valor, municipio ";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " AND municipio IS NOT NULL;";

                break;

            case 'consultarUrbanizacion':

                $cadenaSql = " SELECT DISTINCT urbanizacion as valor, urbanizacion";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro=TRUE";
                $cadenaSql .= " AND urbanizacion IS NOT NULL;";

                break;

            case 'consultarInformacion':
                $cadenaSql = " SELECT";
                $cadenaSql .= " cn.municipio,";
                $cadenaSql .= " cn.urbanizacion,";
                $cadenaSql .= " cn.id_beneficiario,";
                $cadenaSql .= " cn.numero_identificacion,";
                $cadenaSql .= " cn.nombres||' '||cn.primer_apellido||' '|| CASE WHEN cn.segundo_apellido IS NULL THEN ' 'ELSE cn.segundo_apellido END as \"Nombre Beneficiario\",";
                $cadenaSql .= " cn.numero_contrato,";
                $cadenaSql .= " cn.direccion_domicilio as direccion,";
                $cadenaSql .= " cn.manzana,";
                $cadenaSql .= " cn.torre,";
                $cadenaSql .= " cn.bloque,";
                $cadenaSql .= " cn.interior,";
                $cadenaSql .= " cn.lote,";
                $cadenaSql .= " cn.piso,";
                $cadenaSql .= " cn.casa_apartamento,";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '130') as \"Cédula Beneficiario (Reverso)\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '142') as \"Pantallazo aprovisionamiento velocidad contratada\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '101') as \"Certificado del proyecto catalogado como VIP\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '105') as \"Fotocopia Acta de entrega VIP\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '134') as \"Fotografias de la vivienda con la dirección\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '100') as \"Certificado de servicio publico\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '141') as \"Pantallazo o fotografia de la prueba de velocidad\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '124') as \"Certificado No Internet ultimos 6 meses\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '137') as \"Foto en sitio portátil se entrega embalado\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '135') as \"Fotografia panorámica de la vivienda\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '138') as \"Fotografias del computador navegando con el acceso instalado y cartel\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '103') as \"Documento que demuestra dirección de la vivienda del beneficiario\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '99') as \"Cedula Beneficiario (Frente)\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '136') as \"Fotografia visita viabilidad comercial\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '140') as \"Foto personalización de la la carcasa\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '131') as \"Formato de recibo entrega de portátil\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '132') as \"Acta de Entrega de Servicios de Banda Ancha al Usuario\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '133') as \"Fotografias de los equipos instalados en la vivienda\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '139') as \"Fotografias del serial del computador\",";
                $cadenaSql .= " count(dc.id) FILTER (WHERE dc.tipologia_documento = '102') as \"Documento que demuestre beneficiario acceso es el propietario\",";
                $cadenaSql .= " CASE WHEN cn.ruta_documento_contrato IS NULL THEN 0 ELSE 1 END as \"Marco Contrato\"  ";
                $cadenaSql .= " FROM interoperacion.contrato cn ";
                $cadenaSql .= " LEFT JOIN interoperacion.documentos_contrato dc ON dc.id_beneficiario=cn.id_beneficiario";
                $cadenaSql .= " WHERE cn.numero_identificacion IS NOT NULL AND cn.estado_registro='TRUE'";

                if (isset($_REQUEST['municipio']) && $_REQUEST['municipio'] != '') {
                    $cadenaSql .= " AND cn.municipio='" . $_REQUEST['municipio'] . "'";
                }
                if (isset($_REQUEST['departamento']) && $_REQUEST['departamento'] != '') {

                    $cadenaSql .= " AND cn.departamento='" . $_REQUEST['departamento'] . "'";
                }

                if (isset($_REQUEST['urbanizacion']) && $_REQUEST['urbanizacion'] != '') {
                    $cadenaSql .= " AND cn.urbanizacion='" . $_REQUEST['urbanizacion'] . "'";
                }

                if (isset($_REQUEST['id_beneficiario']) && $_REQUEST['id_beneficiario'] != '') {
                    $cadenaSql .= " AND cn.id_beneficiario='" . $_REQUEST['id_beneficiario'] . "'";
                }

                $cadenaSql .= " GROUP BY ";
                $cadenaSql .= " cn.id_beneficiario,";
                $cadenaSql .= " cn.numero_identificacion,";
                $cadenaSql .= " cn.numero_contrato,";
                $cadenaSql .= " cn.ruta_documento_contrato,";
                $cadenaSql .= " cn.nombres,";
                $cadenaSql .= " cn.primer_apellido,";
                $cadenaSql .= " cn.segundo_apellido,";
                $cadenaSql .= " cn.municipio,";
                $cadenaSql .= " cn.urbanizacion,";
                $cadenaSql .= " cn.direccion_domicilio,";
                $cadenaSql .= " cn.manzana,";
                $cadenaSql .= " cn.torre,";
                $cadenaSql .= " cn.bloque,";
                $cadenaSql .= " cn.interior,";
                $cadenaSql .= " cn.lote,";
                $cadenaSql .= " cn.piso,";
                $cadenaSql .= " cn.casa_apartamento";
                $cadenaSql .= " ORDER BY cn.numero_contrato;";

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

