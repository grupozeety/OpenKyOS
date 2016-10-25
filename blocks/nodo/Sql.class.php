<?php

namespace nodo;

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

            case "registrarNodo":
                		 
                $cadenaSql = "INSERT INTO interoperacion.nodo (";
                $cadenaSql .= "codigo_nodo,";
                $cadenaSql .= "codigo_cabecera,";
                $cadenaSql .= "departamento,";
                $cadenaSql .= "municipio,";
                $cadenaSql .= "urbanizacion,";
                $cadenaSql .= "id_urbanizacion,";
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
                $cadenaSql .= "mac_cpe_celda";
                $cadenaSql .= ") VALUES ";
                $cadenaSql .= "(";
                $cadenaSql .= "'" . $variable['codigo_nodo'] . "',";
                $cadenaSql .= "'" . $variable['codigo_cabecera'] . "',";
                $cadenaSql .= "'" . $variable['departamento'] . "',";
                $cadenaSql .= "'" . $variable['municipio'] . "',";
                $cadenaSql .= "'" . $variable['urbanizacion'] . "',";
                $cadenaSql .= "'" . $variable['id_urbanizacion'] . "',";
                $cadenaSql .= "'" . $variable['tipo_tecnologia'] . "',";
                $cadenaSql .= "'" . $variable['mac_master_eoc'] . "',";
                $cadenaSql .= "'" . $variable['ip_master_eoc'] . "',";
                $cadenaSql .= "'" . $variable['mac_onu_eoc'] . "',";
                $cadenaSql .= "'" . $variable['ip_onu_eoc'] . "',";
                $cadenaSql .= "'" . $variable['mac_hub_eoc'] . "',";
                $cadenaSql .= "'" . $variable['ip_hub_eoc'] . "',";
                $cadenaSql .= "'" . $variable['mac_cpe_eoc'] . "',";
                $cadenaSql .= "'" . $variable['mac_celda'] . "',";
                $cadenaSql .= "'" . $variable['ip_celda'] . "',";
                $cadenaSql .= "'" . $variable['nombre_nodo'] . "',";
                $cadenaSql .= "'" . $variable['nombre_sectorial'] . "',";
                $cadenaSql .= "'" . $variable['ip_switch_celda'] . "',";
                $cadenaSql .= "'" . $variable['mac_sm_celda'] . "',";
                $cadenaSql .= "'" . $variable['ip_sm_celda'] . "',";
                $cadenaSql .= "'" . $variable['mac_cpe_celda'] . "'";
                $cadenaSql .= ")";
                break;
                	
            case "consultarNodo":
                
               	$cadenaSql = "SELECT ";
               	$cadenaSql .= "nodo.codigo_nodo,";
               	$cadenaSql .= "nodo.codigo_cabecera,";
               	$cadenaSql .= "tipotec.descripcion AS tipo_tecnologia,";
               	$cadenaSql .= "cab.urbanizacion ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "interoperacion.nodo AS nodo ";
               	$cadenaSql .= "left join interoperacion.cabecera AS cab ";
               	$cadenaSql .= "ON ";
               	$cadenaSql .= "nodo.codigo_cabecera=cab.codigo_cabecera, ";
               	$cadenaSql .= "(SELECT        ";
               	$cadenaSql .= "codigo, ";
               	$cadenaSql .= "param.descripcion ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "parametros.parametros as param ";
               	$cadenaSql .= "INNER JOIN ";
               	$cadenaSql .= "parametros.relacion_parametro as rparam ";
               	$cadenaSql .= "ON ";
               	$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "rparam.descripcion = 'Tipo de Tecnología') AS tipotec ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "1=1 ";
               	$cadenaSql .= "AND ";
               	$cadenaSql .= "tipo_tecnologia=cast ( tipotec.codigo as int8) ";
               	$cadenaSql .= "AND ";
               	$cadenaSql .= "nodo.estado_registro=true ";
               	$cadenaSql .= "AND ";
               	$cadenaSql .= "cab.estado_registro=true ";
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
               	
             case "parametroTipoTecnologia":
               	$cadenaSql = "SELECT        ";
               	$cadenaSql .= "codigo, ";
               	$cadenaSql .= "param.descripcion ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "parametros.parametros as param ";
               	$cadenaSql .= "INNER JOIN ";
               	$cadenaSql .= "parametros.relacion_parametro as rparam ";
               	$cadenaSql .= "ON ";
               	$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "rparam.descripcion = 'Tipo de Tecnología' ";
               	break;
               	
             case "consultarProyectoCabecera":
             	$cadenaSql = "SELECT ";
               	$cadenaSql .= "id_urbanizacion AS proyecto ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "interoperacion.cabecera ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "estado_registro=true ";
               	$cadenaSql .= "AND codigo_cabecera=" . "'" . $variable . "'";
               	break;
                	
        }

        return $cadenaSql;
    }
}

?>
