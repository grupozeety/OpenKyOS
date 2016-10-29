<?php

namespace registroBeneficiario;

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

            case "consultarBeneficiario":
                
               	$cadenaSql = "SELECT ";
               	$cadenaSql .= "proyecto  AS urbanizacion,";
               	$cadenaSql .= "(nombre ||' '|| primer_apellido ||' '|| segundo_apellido) as nombre,";
               	$cadenaSql .= "identificacion,";
               	$cadenaSql .= "tipoben.descripcion as tipo_beneficiario, ";
               	$cadenaSql .= "id_beneficiario ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "interoperacion.beneficiario_potencial, ";
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
               		$cadenaSql .= "rparam.descripcion = 'Tipo de Beneficario o Cliente') AS tipoben ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "tipo_beneficiario=cast ( tipoben.codigo as int8) ";
               	$cadenaSql .= "AND ";
               	$cadenaSql .= "estado_registro=true ";
                
               	break;
               	
               	case "parametroTipoBeneficiario":
               		
               		break;
               	
            case "inhabilitarBeneficiario":
               	
               	$cadenaSql = "UPDATE interoperacion.beneficiario_potencial ";
               	$cadenaSql .= "SET ";
               	$cadenaSql .= "estado_registro=FALSE ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "id_beneficiario=" . "'" . $variable . "'";
               	break;
                	
        }

        return $cadenaSql;
    }
}

?>
