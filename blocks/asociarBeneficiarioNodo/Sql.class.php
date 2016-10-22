<?php

namespace cabecera;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

include_once "core/manager/Configurador.class.php";
include_once "core/connection/Sql.class.php";
include_once "core/auth/SesionSso.class.php";
// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
    public $miConfigurador;
    public $miSesionSso;
    public function __construct() {
        $this->miConfigurador = \Configurador::singleton();

//         $this->miSesionSso = \SesionSso::singleton();
    }
    public function getCadenaSql($tipo, $variable = "") {

//         $info_usuario = $this->miSesionSso->getParametrosSesionAbierta();

//         foreach ($info_usuario['description'] as $key => $rol) {

//             $info_usuario['rol'][] = $rol;

//         }

        /**
         * 1.
         * Revisar las variables para evitar SQL Injection
         */
        $prefijo = $this->miConfigurador->getVariableConfiguracion("prefijo");
        $idSesion = $this->miConfigurador->getVariableConfiguracion("id_sesion");

        switch ($tipo) {

            /**
             * Clausulas genéricas.
             * se espera que estén en todos los formularios
             * que utilicen esta plantilla
             */
            case "iniciarTransaccion":
                $cadenaSql = "START TRANSACTION";
                break;

            case "finalizarTransaccion":
                $cadenaSql = "COMMIT";
                break;

            case "cancelarTransaccion":
                $cadenaSql = "ROLLBACK";
                break;

            case "eliminarTemp":

                $cadenaSql = "DELETE ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= $prefijo . "tempFormulario ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "id_sesion = '" . $variable . "' ";
                break;

            case "insertarTemp":
                $cadenaSql = "INSERT INTO ";
                $cadenaSql .= $prefijo . "tempFormulario ";
                $cadenaSql .= "( ";
                $cadenaSql .= "id_sesion, ";
                $cadenaSql .= "formulario, ";
                $cadenaSql .= "campo, ";
                $cadenaSql .= "valor, ";
                $cadenaSql .= "fecha ";
                $cadenaSql .= ") ";
                $cadenaSql .= "VALUES ";

                foreach ($_REQUEST as $clave => $valor) {
                    $cadenaSql .= "( ";
                    $cadenaSql .= "'" . $idSesion . "', ";
                    $cadenaSql .= "'" . $variable['formulario'] . "', ";
                    $cadenaSql .= "'" . $clave . "', ";
                    $cadenaSql .= "'" . $valor . "', ";
                    $cadenaSql .= "'" . $variable['fecha'] . "' ";
                    $cadenaSql .= "),";
                }

                $cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));
                break;

            case "rescatarTemp":
                $cadenaSql = "SELECT ";
                $cadenaSql .= "id_sesion, ";
                $cadenaSql .= "formulario, ";
                $cadenaSql .= "campo, ";
                $cadenaSql .= "valor, ";
                $cadenaSql .= "fecha ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= $prefijo . "tempFormulario ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "id_sesion='" . $idSesion . "'";
                break;

            /* Consultas del desarrollo */
                case "cargarNodo":
                
                	$cadenaSql = "SELECT ";
                	$cadenaSql .= "codigo_nodo,";
                	$cadenaSql .= "codigo_cabecera,";
                	$cadenaSql .= "tipo_tecnologia,";
                	$cadenaSql .= "mac_master_eoc,";
                	$cadenaSql .= "ip_master_eoc,";
                	$cadenaSql .= "mac_onu_eoc,";
                	$cadenaSql .= "ip_onu_eoc,";
                	$cadenaSql .= "mac_hub_eoc,";
                	$cadenaSql .= "ip_hub_eoc,";
                	$cadenaSql .= "mac_cpe_eoc,";
                	$cadenaSql .= "mac_celda,";
                	$cadenaSql .= "ip_celda,";
                	$cadenaSql .= "nombre_nodo,";
                	$cadenaSql .= "nombre_sectorial,";
                	$cadenaSql .= "ip_switch_celda,";
                	$cadenaSql .= "mac_sm_celda,";
                	$cadenaSql .= "ip_sm_celda,";
                	$cadenaSql .= "mac_cpe_celda ";
                	$cadenaSql .= "FROM ";
                	$cadenaSql .= "interoperacion.nodo ";
                	$cadenaSql .= "WHERE ";
                	$cadenaSql .= "estado_registro=true ";
                	$cadenaSql .= "AND ";
                	$cadenaSql .= "codigo_nodo=" . "'" . $variable . "'";
                	break;
                
            case "actualizarNodo":
                
                $cadenaSql = "UPDATE interoperacion.nodo ";
                $cadenaSql .= "SET ";
                $cadenaSql .= "estado_registro=FALSE ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "codigo_nodo=";
                $cadenaSql .= "'" . $variable . "'";
                break;

            case "registrarBeneficiarioNodo":
                		 
               $cadenaSql = "INSERT INTO interoperacion.Asociacion_benf_nodo (";
               $cadenaSql .= "codigo_nodo,";
               $cadenaSql .= "id_beneficiario";
               $cadenaSql .= ") VALUES ";
              
               foreach ($variable['id_beneficiario'] as $clave => $valor) {

                    $cadenaSql .= "(";
                    $cadenaSql .= "'" . $variable['codigo_nodo'] . "',";
                    $cadenaSql .= "'" . $valor . "'";
                    $cadenaSql .= "),";
                }

                $cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));
                break;
                	
            case "consultarBeneficiarioNodo":
                
            	$cadenaSql = " SELECT ";
            	$cadenaSql .= "nd.codigo_nodo,";
            	$cadenaSql .= "nd.urbanizacion,";
            	$cadenaSql .= "bp.id_beneficiario,";
            	$cadenaSql .= "bp.identificacion,";
            	$cadenaSql .= "bp.nombre || ' ' || bp.primer_apellido || ' ' || segundo_apellido AS nombre ";
            	$cadenaSql .= "FROM ";
            	$cadenaSql .= "interoperacion.asociacion_benf_nodo AS abn ";
            	$cadenaSql .= "join interoperacion.nodo AS nd ON abn.codigo_nodo= nd.codigo_nodo AND nd.estado_registro=true ";
            	$cadenaSql .= "join interoperacion.beneficiario_potencial AS bp ON abn.id_beneficiario=bp.id_beneficiario AND bp.estado_registro=true";
            	$cadenaSql .= "WHERE ";
            	$cadenaSql .= "estado_registro=TRUE";
               	break;
               	
            case "inhabilitarNodo":
               	
               	$cadenaSql = "UPDATE interoperacion.nodo ";
               	$cadenaSql .= "SET ";
               	$cadenaSql .= "estado_registro=FALSE ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "codigo_nodo=" . "'" . $variable . "'";
               	break;
               	
            case "codigoCabecera":
               	$cadenaSql = "SELECT ";
               	$cadenaSql .= "codigo_cabecera, codigo_cabecera ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "interoperacion.cabecera ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "estado_registro=true ";
               	break;
               	
            case "codigoNodo":
               	$cadenaSql = "SELECT ";
               	$cadenaSql .= "codigo_nodo, codigo_nodo ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "interoperacion.nodo ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "estado_registro=true ";
               	break;
               	
             case "obtenerBeneficiarios":
               	$cadenaSql = "SELECT DISTINCT ";
               	$cadenaSql .= "bp.id_beneficiario as value, ";
               	$cadenaSql .= "(bp.id_beneficiario || ' - ' || bp.identificacion || ' - ' || bp.nombre || ' ' || bp.primer_apellido || ' ' || bp.segundo_apellido) as text ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "interoperacion.beneficiario_potencial AS bp left join interoperacion.asociacion_benf_nodo AS abn on bp.id_beneficiario=abn.id_beneficiario ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "not exists (select 1 from interoperacion.asociacion_benf_nodo AS abn where abn.id_beneficiario = bp.id_beneficiario AND abn.estado_registro=TRUE) ";
               	$cadenaSql .= "AND ";
               	$cadenaSql .= "bp.estado_registro='TRUE' ";
               	$cadenaSql .= "AND ";
               	$cadenaSql .= "(LOWER(bp.nombre) like LOWER('%" . $variable . "%') ";
               	$cadenaSql .= "OR LOWER(bp.primer_apellido) like LOWER('%" . $variable . "%') ";
               	$cadenaSql .= "OR LOWER(bp.segundo_apellido) like LOWER('%" . $variable . "%') ";
               	$cadenaSql .= "OR LOWER(bp.id_beneficiario) like LOWER('%" . $variable . "%') ";
               	$cadenaSql .= "OR LOWER(bp.identificacion) like LOWER('%" . $variable . "%')) ";
               	break;
               	
             case "obtenerBeneficiarioEspecifico":
               	$cadenaSql = "SELECT DISTINCT ";
               	$cadenaSql .= "bp.id_beneficiario as value, bp.id_beneficiario as id, ";
               	$cadenaSql .= "(bp.id_beneficiario || ' - ' || bp.identificacion || ' - ' || bp.nombre || ' ' || bp.primer_apellido || ' ' || bp.segundo_apellido) as text ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "interoperacion.beneficiario_potencial AS bp ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "bp.estado_registro='TRUE' ";
               	$cadenaSql .= "AND ";
               	$cadenaSql .= "bp.identificacion='" . $variable . "'" ;
               	break;
        }

        return $cadenaSql;
    }
}

?>
