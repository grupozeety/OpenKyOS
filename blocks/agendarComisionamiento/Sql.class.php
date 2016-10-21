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
            case "consultarBeneficiarios":
                
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
				//select urbanizacion as urbanizacion, urbanizacion as id_urbanizacion, manzana, torre, bloque, apartamento, identificacion as identificacion_beneficiario, nombre || ' ' || primer_apellido || ' ' || segundo_apellido  as nombre from interoperacion.beneficiario_potencial natural join  WHERE estado_registro=TRUE

            case "comisionador":
            	$cadenaSql=" SELECT usr.id as identificador, usr.firstname||' '||lastname as nombre_usuario";
            	$cadenaSql.=" FROM public.group_users as gu";
            	$cadenaSql.=" JOIN public.users as usr ON usr.id=gu.user_id AND usr.status=1";
            	$cadenaSql.=" WHERE group_id=(SELECT DISTINCT id";
            	$cadenaSql.=" FROM public.users";
            	$cadenaSql.=" WHERE lastname= 'Comisionadores'";
            	$cadenaSql.=" AND TYPE= 'Group'";
            	$cadenaSql.=" LIMIT 1);";
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
               	$cadenaSql .= "id_comisionador,";
               	$cadenaSql .= "nombre_comisionador,";
               	$cadenaSql .= "fecha_agendamiento,";
               	$cadenaSql .= "codigo_nodo,";
               	$cadenaSql .= "manzana,";
               	$cadenaSql .= "torre,";
               	$cadenaSql .= "bloque,";
               	$cadenaSql .= "apartamento";
               	$cadenaSql .= ") VALUES ";
               	
               	foreach ($variable as $clave => $valor) {
               	
               		$cadenaSql .= "(";
               		$cadenaSql .= "" . "(SELECT 'AG-' || MAX(consecutivo) + 1 from  interoperacion.consecutivo_agendamiento)" . ",";
               		$cadenaSql .= "'" . $valor['id_orden_trabajo'] . "',";
               		$cadenaSql .= "'" . $valor['descripcion_orden_trabajo'] . "',";
               		$cadenaSql .= "'" . $valor['id_urbanizacion'] . "',";
               		$cadenaSql .= "'" . $valor['descripcion_urbanizacion'] . "',";
               		$cadenaSql .= "'" . $valor['identificacion_beneficiario'] . "',";
               		$cadenaSql .= "'" . $valor['nombre_beneficiario'] . "',";
               		$cadenaSql .= "'" . $valor['tipo_agendamiento'] . "',";
               		$cadenaSql .= "'" . $valor['tipo_tecnologia'] . "',";
               		$cadenaSql .= "'" . $valor['id_comisionador'] . "',";
               		$cadenaSql .= "'" . $valor['nombre_comisionador'] . "',";
               		$cadenaSql .= "'" . $valor['fecha_agendamiento'] . "',";
               		$cadenaSql .= "'" . $valor['codigo_nodo'] . "',";
               		$cadenaSql .= "'" . $valor['manzana'] . "',";
               		$cadenaSql .= "'" . $valor['torre'] . "',";
               		$cadenaSql .= "'" . $valor['bloque'] . "',";
               		$cadenaSql .= "'" . $valor['apartamento'] . "'";
               		$cadenaSql .= "),";
               	}
               	
               	$cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));
               			
               	break;
                	
			case "registrarConsecutivoAgendamiento":
               		
               	$cadenaSql = "INSERT INTO interoperacion.consecutivo_agendamiento(";
              	$cadenaSql .= "nombre_consecutivo";
               	$cadenaSql .= ") VALUES ";
               	$cadenaSql .= "(";
               	$cadenaSql .= "(SELECT 'AG-' || MAX(consecutivo) + 1 from  interoperacion.consecutivo_agendamiento)";
               	$cadenaSql .= ")";
           		break;
               			 
        }

        return $cadenaSql;
    }
}

?>
