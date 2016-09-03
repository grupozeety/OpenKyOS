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

            case "registrarBeneficiarioPotencial":
            	
                $cadenaSql = "INSERT INTO interoperacion.beneficiario_potencial (";
                $cadenaSql .= "id,";
                $cadenaSql .= "tipo,";
                $cadenaSql .= "identificacion,";
                $cadenaSql .= "nombre,";
                $cadenaSql .= "genero,";
                $cadenaSql .= "edad,";
                $cadenaSql .= "nivel_estudio,";
                $cadenaSql .= "correo,";
                $cadenaSql .= "foto,";
                $cadenaSql .= "direccion,";
                $cadenaSql .= "tipo_vivienda,";
                $cadenaSql .= "telefono,";
                $cadenaSql .= "celular,";
                $cadenaSql .= "whatsapp,";
                $cadenaSql .= "departamento,";
                $cadenaSql .= "municipio,";
                $cadenaSql .= "urbanizacion,";
                $cadenaSql .= "territorio,";
                $cadenaSql .= "estrato,";
                $cadenaSql .= "geolocalizacion,";
                $cadenaSql .= "jefe_hogar,";
                $cadenaSql .= "pertenencia_etnica,";
                $cadenaSql .= "ocupacion";
                $cadenaSql .= ") VALUES ";
                $cadenaSql .= "(";
                $cadenaSql .= "'" . $variable['id_beneficiario'] . "',";
                $cadenaSql .= "'" . $variable['tipo_beneficiario'] . "',";
                $cadenaSql .= "'" . $variable['identificacion_beneficiario'] . "',";
                $cadenaSql .= "'" . $variable['nombre_beneficiario'] . "',";
                $cadenaSql .= "'" . $variable['genero_beneficiario'] . "',";
                $cadenaSql .= "'" . $variable['edad_beneficiario'] . "',";
                $cadenaSql .= "'" . $variable['nivel_estudio'] . "',";
                $cadenaSql .= "'" . $variable['correo'] . "',";
                $cadenaSql .= "'" . $variable['foto'] . "',";
                $cadenaSql .= "'" . $variable['direccion'] . "',";
                $cadenaSql .= "'" . $variable['tipo_vivienda'] . "',";
                $cadenaSql .= "'" . $variable['telefono'] . "',";
                $cadenaSql .= "'" . $variable['celular'] . "',";
                $cadenaSql .= "'" . $variable['whatsapp'] . "',";
                $cadenaSql .= "'" . $variable['departamento'] . "',";
                $cadenaSql .= "'" . $variable['municipio'] . "',";
                $cadenaSql .= "'" . $variable['urbanizacion'] . "',";
                $cadenaSql .= "'" . $variable['territorio'] . "',";
                $cadenaSql .= "'" . $variable['estrato'] . "',";
                $cadenaSql .= "'" . $variable['geolocalizacion'] . "',";
                $cadenaSql .= "'" . $variable['jefe_hogar'] . "',";
                $cadenaSql .= "'" . $variable['pertenencia_etnica'] . "',";
                $cadenaSql .= "'" . $variable['ocupacion'] . "'";
                $cadenaSql .= ")";
                
               	break;
                
            case "registrarConsumo":

                $cadenaSql = "INSERT INTO interoperacion.consumo_material(";
                $cadenaSql .= "nombre,";
                $cadenaSql .= "orden_trabajo,";
                $cadenaSql .= "descripcion,";
                $cadenaSql .= "proyecto,";
                $cadenaSql .= "salida,";
                $cadenaSql .= "nombre_material,";
                $cadenaSql .= "cantidad_asignada,";
                $cadenaSql .= "consumo,";
                $cadenaSql .= "porcentaje_consumo,";
                $cadenaSql .= "geolocalizacion,";
                $cadenaSql .= "usuario";
                $cadenaSql .= ") VALUES ";

                foreach ($variable as $clave => $valor) {

                    $cadenaSql .= "(";
                    $cadenaSql .= "'" . $valor['name'] . "',";
                    $cadenaSql .= "'" . $valor['ordenTrabajo'] . "',";
                    $cadenaSql .= "'" . $valor['descripcion'] . "',";
                    $cadenaSql .= "'" . $valor['proyecto'] . "',";
                    $cadenaSql .= "'" . $valor['salida'] . "',";
                    $cadenaSql .= "'" . $valor['material'] . "',";
                    $cadenaSql .= "'" . $valor['asignada'] . "',";
                    $cadenaSql .= "'" . $valor['consume'] . "',";
                    $cadenaSql .= "'" . $valor['porcentajecons'] . "',";
                    $cadenaSql .= "'" . $valor['geolocalizacion'] . "',";
                    $cadenaSql .= "'" . $info_usuario['uid'][0] . "'";
                    $cadenaSql .= "),";
                }

                $cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));

                break;

            case "actualizarConsumo":

                $cadenaSql = "UPDATE interoperacion.consumo_material ";
                $cadenaSql .= "SET ";
                $cadenaSql .= "estado_registro=FALSE ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "nombre ";
                $cadenaSql .= "IN ";
                $cadenaSql .= "(";

                foreach ($variable as $clave => $valor) {

                    $cadenaSql .= "'" . $valor['name'] . "',";

                }

                $cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));
                $cadenaSql .= ")";

                break;

            case "obtenerConsumo":

                $cadenaSql = "SELECT consumo,geolocalizacion, porcentaje_consumo from interoperacion.consumo_material where nombre='" . $variable . "' AND estado_registro=TRUE;";

                break;

                
            case "parametroTipoBeneficiario":
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
               	$cadenaSql .= "rparam.descripcion = 'Tipo de Beneficario o Cliente' ";
               	break;
                	
            case "parametroGenero":
            	$cadenaSql = "SELECT        ";
                $cadenaSql .= " codigo, ";
                $cadenaSql .= "param.descripcion ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "parametros.parametros as param ";
                $cadenaSql .= "INNER JOIN ";
                $cadenaSql .= "parametros.relacion_parametro as rparam ";
                $cadenaSql .= "ON ";
                $cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "rparam.descripcion = 'Genero' ";
                break;
                
            case "parametroNivelEstudio":
               	$cadenaSql = "SELECT        ";
               	$cadenaSql .= " codigo, ";
               	$cadenaSql .= "param.descripcion ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "parametros.parametros as param ";
               	$cadenaSql .= "INNER JOIN ";
               	$cadenaSql .= "parametros.relacion_parametro as rparam ";
               	$cadenaSql .= "ON ";
               	$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "rparam.descripcion = 'Nivel de Estudio' ";
               	break;
               	
            case "parametroTipoVivienda":
            	$cadenaSql = "SELECT        ";
            	$cadenaSql .= " codigo, ";
             	$cadenaSql .= "param.descripcion ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "parametros.parametros as param ";
               	$cadenaSql .= "INNER JOIN ";
               	$cadenaSql .= "parametros.relacion_parametro as rparam ";
               	$cadenaSql .= "ON ";
               	$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "rparam.descripcion = 'Tipo de Vivienda' ";
               	break;
               	
            case "parametroTerritorio":
              	$cadenaSql = "SELECT        ";
               	$cadenaSql .= " codigo, ";
               	$cadenaSql .= "param.descripcion ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "parametros.parametros as param ";
               	$cadenaSql .= "INNER JOIN ";
               	$cadenaSql .= "parametros.relacion_parametro as rparam ";
               	$cadenaSql .= "ON ";
               	$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "rparam.descripcion = 'Territorio' ";
               	break;
               	
            case "parametroEstrato":
             	$cadenaSql = "SELECT        ";
               	$cadenaSql .= " codigo, ";
               	$cadenaSql .= "param.descripcion ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "parametros.parametros as param ";
               	$cadenaSql .= "INNER JOIN ";
               	$cadenaSql .= "parametros.relacion_parametro as rparam ";
               	$cadenaSql .= "ON ";
               	$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "rparam.descripcion = 'Estrato' ";
               	break;
               	
            case "parametroJefeHogar":
            	$cadenaSql = "SELECT        ";
               	$cadenaSql .= " id_parametro, ";
               	$cadenaSql .= "param.descripcion ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "parametros.parametros as param ";
               	$cadenaSql .= "INNER JOIN ";
               	$cadenaSql .= "parametros.relacion_parametro as rparam ";
               	$cadenaSql .= "ON ";
               	$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "rparam.descripcion = 'Jefe de Hogar' ";
               	break;
               	
            case "parametroPertenenciaEtnica":
              	$cadenaSql = "SELECT        ";
               	$cadenaSql .= " codigo, ";
               	$cadenaSql .= "param.descripcion ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "parametros.parametros as param ";
               	$cadenaSql .= "INNER JOIN ";
               	$cadenaSql .= "parametros.relacion_parametro as rparam ";
               	$cadenaSql .= "ON ";
               	$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "rparam.descripcion = 'Pertinencia Étnica' ";
               	break;
               	
             case "parametroOcupacion":
               	$cadenaSql = "SELECT        ";
               	$cadenaSql .= " id_parametro, ";
               	$cadenaSql .= "param.descripcion ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "parametros.parametros as param ";
               	$cadenaSql .= "INNER JOIN ";
               	$cadenaSql .= "parametros.relacion_parametro as rparam ";
               	$cadenaSql .= "ON ";
               	$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "rparam.descripcion = 'Ocupación' ";
               	break;
               	
             case "parametroParentesco":
             	$cadenaSql = "SELECT        ";
               	$cadenaSql .= " codigo, ";
               	$cadenaSql .= "param.descripcion ";
               	$cadenaSql .= "FROM ";
               	$cadenaSql .= "parametros.parametros as param ";
               	$cadenaSql .= "INNER JOIN ";
               	$cadenaSql .= "parametros.relacion_parametro as rparam ";
               	$cadenaSql .= "ON ";
               	$cadenaSql .= "(param.rel_parametro = rparam.id_rel_parametro) ";
               	$cadenaSql .= "WHERE ";
               	$cadenaSql .= "rparam.descripcion = 'Parentesco con jefe de hogar' ";
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
        }

        return $cadenaSql;
    }
}

?>
