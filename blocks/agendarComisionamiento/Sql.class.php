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
                case "cargarCabecera":
                
                	$cadenaSql = "SELECT ";
                	$cadenaSql .= "codigo_cabecera AS codigo_cabecera,";
                	$cadenaSql .= "descripcion AS descripcion,";
                	$cadenaSql .= "departamento AS departamento,";
                	$cadenaSql .= "municipio AS municipio,";
                	$cadenaSql .= "urbanizacion AS id_urbanizacion,";
                	$cadenaSql .= "id_urbanizacion AS urbanizacion,";
                	$cadenaSql .= "ip_olt AS ip_olt,";
                	$cadenaSql .= "mac_olt AS mac_olt,";
                	$cadenaSql .= "port_olt AS port_olt,";
                	$cadenaSql .= "nombre_olt AS nombre_olt,";
                	$cadenaSql .= "puerto_olt AS puerto_olt ";
                	$cadenaSql .= "FROM ";
                	$cadenaSql .= "interoperacion.cabecera ";
                	$cadenaSql .= "WHERE ";
                	$cadenaSql .= "estado_registro=true ";
                	$cadenaSql .= "AND ";
                	$cadenaSql .= "codigo_cabecera=" . "'" . $variable . "'";
                	break;
                
            case "actualizarCabecera":
                
                $cadenaSql = "UPDATE interoperacion.cabecera ";
                $cadenaSql .= "SET ";
                $cadenaSql .= "estado_registro=FALSE ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "codigo_cabecera=";
                $cadenaSql .= "'" . $variable . "'";
                break;

            case "registrarCabecera":
                		 
                $cadenaSql = "INSERT INTO interoperacion.cabecera (";
                $cadenaSql .= "codigo_cabecera,";
                $cadenaSql .= "descripcion,";
                $cadenaSql .= "departamento,";
                $cadenaSql .= "municipio,";
                $cadenaSql .= "urbanizacion,";
                $cadenaSql .= "id_urbanizacion,";
                $cadenaSql .= "ip_olt,";
                $cadenaSql .= "mac_olt,";
                $cadenaSql .= "port_olt,";
                $cadenaSql .= "nombre_olt,";
                $cadenaSql .= "puerto_olt";
                $cadenaSql .= ") VALUES ";
                $cadenaSql .= "(";
                $cadenaSql .= "'" . $variable['codigo_cabecera'] . "',";
                $cadenaSql .= "'" . $variable['descripcion'] . "',";
                $cadenaSql .= "'" . $variable['departamento'] . "',";
                $cadenaSql .= "'" . $variable['municipio'] . "',";
                $cadenaSql .= "'" . $variable['urbanizacion'] . "',";
                $cadenaSql .= "'" . $variable['id_urbanizacion'] . "',";
                $cadenaSql .= "'" . $variable['ip_olt'] . "',";
                $cadenaSql .= "'" . $variable['mac_olt'] . "',";
                $cadenaSql .= "'" . $variable['port_olt'] . "',";
                $cadenaSql .= "'" . $variable['nombre_olt'] . "',";
                $cadenaSql .= "'" . $variable['puerto_olt'] . "'";
                $cadenaSql .= ")";
                break;
                	
            case "consultarCabecera":
                
               	$cadenaSql = "SELECT ";
               	$cadenaSql .= "codigo_cabecera,";
               	$cadenaSql .= "descripcion,";
               	$cadenaSql .= "dep.departamento,";
               	$cadenaSql .= "mun.municipio,";
               	$cadenaSql .= "urbanizacion ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "interoperacion.cabecera AS cab, ";
	               	$cadenaSql .= "(SELECT ";
	               	$cadenaSql .= "codigo_dep, ";
	               	$cadenaSql .= "departamento ";
	               	$cadenaSql .= "FROM ";
	               	$cadenaSql .= "parametros.departamento";
	               	$cadenaSql .= ") AS dep, ";
	               	$cadenaSql .= "(SELECT ";
	               	$cadenaSql .= "codigo_mun, ";
	               	$cadenaSql .= "municipio ";
	               	$cadenaSql .= "FROM ";
	               	$cadenaSql .= "parametros.municipio";
	               	$cadenaSql .= ") AS mun ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "dep.codigo_dep=cast ( cab.departamento as int8) ";
               	$cadenaSql .= "AND ";
               	$cadenaSql .= "mun.codigo_mun=cast ( cab.municipio as int8) ";
               	$cadenaSql .= "AND ";
               	$cadenaSql .= "estado_registro=true ";
               	break;
               	
            case "inhabilitarCabecera":
               	
               	$cadenaSql = "UPDATE interoperacion.cabecera ";
               	$cadenaSql .= "SET ";
               	$cadenaSql .= "estado_registro=FALSE ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "codigo_cabecera=" . "'" . $variable . "'";
               	break;
               	
            case "parametroDepartamento":
            	$cadenaSql = "SELECT ";
               	$cadenaSql .= "codigo_dep, ";
               	$cadenaSql .= "departamento ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "parametros.departamento ";
               	break;
               	
            case "parametroMunicipio":
               	$cadenaSql = "SELECT ";
               	$cadenaSql .= "codigo_mun, ";
               	$cadenaSql .= "municipio ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "parametros.municipio ";
               	break;
               	
            case "parametroTipoAgendamiento":
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
               	$cadenaSql .= "rparam.descripcion = 'Tipo de Agendamiento' ";
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
               	
               	
               	case "registrarAgendamiento":
               	
               		$cadenaSql = "INSERT INTO interoperacion.agendamiento_comisionamiento(";
               		$cadenaSql .= "id_agendamiento,";
               		$cadenaSql .= "id_orden_trabajo,";
               		$cadenaSql .= "descripcion_orden_trabajo,";
               		$cadenaSql .= "id_urbanizacion,";
               		$cadenaSql .= "descripcion_urbanizacion,";
               		$cadenaSql .= "identificacion_beneficiario,";
               		$cadenaSql .= "nombre_beneficiario,";
               		$cadenaSql .= "tipo_agendamiento,";
               		$cadenaSql .= "tipo_tecnologia,";
               		$cadenaSql .= "codigo_nodo,";
               		$cadenaSql .= "manzana,";
               		$cadenaSql .= "torre,";
               		$cadenaSql .= "bloque,";
               		$cadenaSql .= "apartamento";
               		$cadenaSql .= ") VALUES ";
               	
               		foreach ($variable as $clave => $valor) {
               	
               			$cadenaSql .= "(";
               			$cadenaSql .= "'" . $valor['id_agendamiento'] . "',";
               			$cadenaSql .= "'" . $valor['id_orden_trabajo'] . "',";
               			$cadenaSql .= "'" . $valor['descripcion_orden_trabajo'] . "',";
               			$cadenaSql .= "'" . $valor['id_urbanizacion'] . "',";
               			$cadenaSql .= "'" . $valor['descripcion_urbanizacion'] . "',";
               			$cadenaSql .= "'" . $valor['identificacion_beneficiario'] . "',";
               			$cadenaSql .= "'" . $valor['nombre_beneficiario'] . "',";
               			$cadenaSql .= "'" . $valor['tipo_agendamiento'] . "',";
               			$cadenaSql .= "'" . $valor['tipo_tecnologia'] . "',";
               			$cadenaSql .= "'" . $valor['codigo_nodo'] . "',";
               			$cadenaSql .= "'" . $valor['manzana'] . "',";
               			$cadenaSql .= "'" . $valor['torre'] . "',";
               			$cadenaSql .= "'" . $valor['bloque'] . "',";
               			$cadenaSql .= "'" . $valor['apartamento'] . "'";
               			$cadenaSql .= "),";
               		}
               	
               		$cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));
               			
               		break;
                	
        }

        return $cadenaSql;
    }
}

?>
