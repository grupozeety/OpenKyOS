<?php
namespace gestionBeneficiarios\generarContratosMasivos;
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

            case 'insertarBloque':
                $cadenaSql = 'INSERT INTO ';
                $cadenaSql .= $prefijo . 'bloque ';
                $cadenaSql .= '( ';
                $cadenaSql .= 'nombre,';
                $cadenaSql .= 'descripcion,';
                $cadenaSql .= 'grupo';
                $cadenaSql .= ') ';
                $cadenaSql .= 'VALUES ';
                $cadenaSql .= '( ';
                $cadenaSql .= '\'' . $_REQUEST['nombre'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST['descripcion'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST['grupo'] . '\' ';
                $cadenaSql .= '); ';
                break;

            case 'consultarExitenciaContrato':
                $cadenaSql = " SELECT id_beneficiario, numero_contrato,numero_identificacion";
                $cadenaSql .= " FROM interoperacion.contrato";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND numero_identificacion='" . $variable . "';";
                break;

            case 'consultarExitenciaBeneficiario':
                $cadenaSql = " SELECT id_beneficiario";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial";
                $cadenaSql .= " WHERE estado_registro='TRUE'";
                $cadenaSql .= " AND identificacion='" . $variable . "';";
                break;

            case 'consultarInformacionBeneficiario':
                $cadenaSql = " SELECT bp.* ,mn.municipio as nombre_municipio,dp.departamento as nombre_departamento";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial bp";
                $cadenaSql .= " JOIN parametros.municipio mn ON mn.codigo_mun=bp.municipio";
                $cadenaSql .= " JOIN parametros.departamento dp ON dp.codigo_dep=bp.departamento";
                $cadenaSql .= " WHERE bp.estado_registro='TRUE' ";
                $cadenaSql .= " AND bp.identificacion='" . $variable . "';";

                break;

            case 'registrarContrato':

                $cadenaSql = " INSERT INTO interoperacion.contrato(";
                $cadenaSql .= " id_beneficiario,";
                $cadenaSql .= " estado_contrato, ";
                $cadenaSql .= " nombres, ";
                $cadenaSql .= " primer_apellido,";
                $cadenaSql .= " segundo_apellido,";
                $cadenaSql .= " tipo_documento,";
                $cadenaSql .= " numero_identificacion, ";
                $cadenaSql .= " direccion_domicilio,";
                $cadenaSql .= " direccion_instalacion, ";
                $cadenaSql .= " departamento,";
                $cadenaSql .= " municipio, ";
                $cadenaSql .= " urbanizacion,";
                $cadenaSql .= " estrato,";
                $cadenaSql .= " telefono, ";
                $cadenaSql .= " celular,";
                $cadenaSql .= " correo,";
                $cadenaSql .= " velocidad_internet, ";
                $cadenaSql .= " valor_mensual, ";
                $cadenaSql .= " tecnologia,";
                $cadenaSql .= " estado,";
                $cadenaSql .= " usuario,";
                $cadenaSql .= " manzana,";
                $cadenaSql .= " bloque,";
                $cadenaSql .= " torre, ";
                $cadenaSql .= " casa_apartamento,";
                $cadenaSql .= " tipo_tecnologia, ";
                $cadenaSql .= " valor_tarificacion, ";
                $cadenaSql .= " interior,";
                $cadenaSql .= " lote,";
                $cadenaSql .= " piso,";
                $cadenaSql .= " nombre_comisionador,";
                $cadenaSql .= " fecha_contrato)";
                $cadenaSql .= " VALUES (";
                foreach ($variable as $key => $value) {

                    if ($key == 'correo' && $value = 'Sin Correo') {
                        $cadenaSql .= "NULL,";
                    } else if ($key == 'nombre_comisionador' && $value = 'Sin Comisionador') {
                        $cadenaSql .= "NULL,";
                    } else {

                        $cadenaSql .= "'" . $value . "',";

                    }

                }

                $cadenaSql .= ")RETURNING numero_contrato;";
                break;

            case 'registrarProceso':
                $cadenaSql = " INSERT INTO parametros.procesos_masivos(";
                $cadenaSql .= " descripcion,";
                $cadenaSql .= " estado,";
                $cadenaSql .= " nombre_archivo,";
                $cadenaSql .= " parametro_inicio,";
                $cadenaSql .= " parametro_fin)";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= " 'Contratos',";
                $cadenaSql .= " 'No Iniciado',";
                $cadenaSql .= " '" . $variable['nombre_contrato'] . "',";
                $cadenaSql .= " '" . $variable['contrato_inicio'] . "',";
                $cadenaSql .= " '" . $variable['contrato_final'] . "'";
                $cadenaSql .= " )RETURNING id_proceso;";
                break;

            case 'consultarProceso':
                $cadenaSql = " SELECT * ";
                $cadenaSql .= " FROM parametros.procesos_masivos";
                $cadenaSql .= " WHERE descripcion='Contratos'";
                $cadenaSql .= " ORDER BY id_proceso DESC;";
                break;
        }

        return $cadenaSql;
    }
}
?>

