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

        // $this->miSesionSso = \SesionSso::singleton();
    }
    public function getCadenaSql($tipo, $variable = "") {

        // $info_usuario = $this->miSesionSso->getParametrosSesionAbierta();

        // foreach ($info_usuario['description'] as $key => $rol) {

        // $info_usuario['rol'][] = $rol;

        // }

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
                $cadenaSql = "START TRANSACTION;";
                break;

            case "finalizarTransaccion":
                $cadenaSql = "COMMIT;";
                break;

            case "cancelarTransaccion":
                $cadenaSql = "ROLLBACK;";
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

            case "cargarBeneficiarioPotencial":

                $cadenaSql = "SELECT ";
                $cadenaSql .= "consecutivo AS consecutivo,";
                $cadenaSql .= "id_beneficiario AS id_beneficiario,";
                $cadenaSql .= "tipo_beneficiario AS tipo_beneficiario,";
                $cadenaSql .= "tipo_documento AS tipo_documento,";
                $cadenaSql .= "identificacion AS identificacion_beneficiario,";
                $cadenaSql .= "nombre AS nombre_beneficiario,";
                $cadenaSql .= "primer_apellido AS primer_apellido,";
                $cadenaSql .= "segundo_apellido AS segundo_apellido,";
                $cadenaSql .= "genero AS genero_beneficiario,";
                $cadenaSql .= "edad AS edad_beneficiario,";
                $cadenaSql .= "nivel_estudio AS nivel_estudio,";
                $cadenaSql .= "correo AS correo,";
                $cadenaSql .= "foto AS nombre_foto,";
                $cadenaSql .= 'ruta_foto AS "rutaFoto",';
                $cadenaSql .= 'url_foto AS "urlFoto",';
                $cadenaSql .= "direccion AS direccion,";
                $cadenaSql .= "tipo_vivienda AS tipo_vivienda,";
                $cadenaSql .= "manzana AS manzana,";
                $cadenaSql .= "bloque AS bloque,";
                $cadenaSql .= "torre AS torre,";
                $cadenaSql .= "apartamento AS apartamento,";
                $cadenaSql .= "telefono AS telefono,";
                $cadenaSql .= "celular AS celular,";
                $cadenaSql .= "whatsapp AS whatsapp,";
                $cadenaSql .= "facebook AS facebook,";
                $cadenaSql .= "departamento AS departamento,";
                $cadenaSql .= "municipio AS municipio,";
                $cadenaSql .= "id_proyecto AS id_urbanizacion,";
                $cadenaSql .= "territorio AS territorio,";
                $cadenaSql .= "estrato AS estrato,";
                $cadenaSql .= "geolocalizacion AS geolocalizacion,";
                $cadenaSql .= "jefe_hogar AS jefe_hogar,";
                $cadenaSql .= "pertenencia_etnica AS pertenencia_etnica,";
                $cadenaSql .= "id_hogar AS id_hogar, ";
                $cadenaSql .= "nomenclatura AS nomenclatura ,";
                $cadenaSql .= "resolucion_adjudicacion AS resolucion_adjudicacion, ";
                $cadenaSql .= "ocupacion AS ocupacion, ";
                $cadenaSql .= "minvi AS minvi ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "interoperacion.beneficiario_potencial ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "estado_registro=true ";

                $cont = 0;

                if (isset($_REQUEST['id']) && $_REQUEST['id'] != '') {
                    $cadenaSql .= " AND id_beneficiario='" . $_REQUEST['id'] . "'";
                    $cont++;
                }

                if (isset($_REQUEST['direccion']) && $_REQUEST['direccion'] != '') {
                    $cadenaSql .= " AND direccion='" . $_REQUEST['direccion'] . "'";
                    $cont++;
                }

                if ((isset($_REQUEST['urbanizacion']) && $_REQUEST['urbanizacion'] != "") || (isset($_REQUEST['id_urbanizacion']) && $_REQUEST['id_urbanizacion'] != "")) {
                    if (isset($_REQUEST['id_urbanizacion']) && $_REQUEST['id_urbanizacion'] != "") {
                        $cadenaSql .= " AND id_proyecto='" . $_REQUEST['id_urbanizacion'] . "'";
                    } else {
                        $cadenaSql .= " AND id_proyecto='" . $_REQUEST['urbanizacion'] . "'";
                    }
                    $cont++;
                }

                if ((isset($_REQUEST['bloque_manzana']) && $_REQUEST['bloque_manzana'] != "") || (isset($_REQUEST['id_bloque_manzana']) && $_REQUEST['id_bloque_manzana'] != "")) {
                    if (isset($_REQUEST['id_bloque_manzana']) && $_REQUEST['id_bloque_manzana'] != "") {
                        $cadenaSql .= " AND manzana='" . $_REQUEST['id_bloque_manzana'] . "'";
                    } else {
                        $cadenaSql .= " AND manzana='" . $_REQUEST['bloque_manzana'] . "'";
                    }
                    $cont++;
                }

                if ((isset($_REQUEST['casa_aparta']) && $_REQUEST['casa_aparta'] != "") || (isset($_REQUEST['id_casa_aparta']) && $_REQUEST['id_casa_aparta'] != "")) {
                    if (isset($_REQUEST['id_casa_aparta']) && $_REQUEST['id_casa_aparta'] != "") {
                        $cadenaSql .= " AND apartamento='" . $_REQUEST['id_casa_aparta'] . "'";
                    } else {
                        $cadenaSql .= " AND apartamento='" . $_REQUEST['casa_aparta'] . "'";
                    }
                    $cont++;
                }
                if ($cont == 0) {
                    $cadenaSql = '';
                }

                break;

            case "cargarFamiliares":

                $cadenaSql = "SELECT ";
                $cadenaSql .= "id_beneficiario,";
                $cadenaSql .= "tipo_documento AS tipo_documento_familiar,";
                $cadenaSql .= "identificacion_familiar,";
                $cadenaSql .= "nombre_familiar,";
                $cadenaSql .= "primer_apellido_familiar,";
                $cadenaSql .= "segundo_apellido_familiar,";
                $cadenaSql .= "parentesco,";
                $cadenaSql .= "genero_familiar,";
                $cadenaSql .= "edad_familiar,";
                $cadenaSql .= "celular_familiar,";
                $cadenaSql .= "nivel_estudio_familiar,";
                $cadenaSql .= "correo_familiar,";
                $cadenaSql .= "grado_estudio_familiar,";
                $cadenaSql .= "pertenencia_etnica_familiar,";
                $cadenaSql .= "institucion_educativa_familiar,";
                $cadenaSql .= "ocupacion_familiar ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "interoperacion.familiar_beneficiario_potencial ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "estado_registro=true ";
                $cadenaSql .= "AND ";
                $cadenaSql .= "id_beneficiario=" . "'" . $variable . "';";
                break;

            case "actualizarBeneficiario":

                $cadenaSql = "UPDATE interoperacion.beneficiario_potencial ";
                $cadenaSql .= "SET ";
                $cadenaSql .= "estado_registro=FALSE ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "id_beneficiario=";
                $cadenaSql .= "'" . $variable . "';";
                break;

            case "actualizarFamiliarBeneficiario":

                $cadenaSql = "UPDATE interoperacion.familiar_beneficiario_potencial ";
                $cadenaSql .= "SET ";
                $cadenaSql .= "estado_registro=FALSE ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "id_beneficiario=";
                $cadenaSql .= "'" . $variable . "';";
                break;

            case "registrarBeneficiarioPotencial":

                $cadenaSql = "INSERT INTO interoperacion.beneficiario_potencial (";
                $cadenaSql .= "id_beneficiario,";
                $cadenaSql .= "tipo_beneficiario,";
                $cadenaSql .= "tipo_documento,";
                $cadenaSql .= "identificacion,";
                $cadenaSql .= "nombre,";
                $cadenaSql .= "primer_apellido,";
                $cadenaSql .= "segundo_apellido,";
                $cadenaSql .= "genero,";
                $cadenaSql .= "edad,";
                $cadenaSql .= "nivel_estudio,";
                $cadenaSql .= "correo,";
                $cadenaSql .= "foto,";
                $cadenaSql .= "ruta_foto,";
                $cadenaSql .= "url_foto,";
                $cadenaSql .= "direccion,";
                $cadenaSql .= "tipo_vivienda,";
                $cadenaSql .= "manzana,";
                $cadenaSql .= "bloque,";
                $cadenaSql .= "torre,";
                $cadenaSql .= "apartamento,";
                $cadenaSql .= "telefono,";
                $cadenaSql .= "celular,";
                $cadenaSql .= "whatsapp,";
                $cadenaSql .= "facebook,";
                $cadenaSql .= "departamento,";
                $cadenaSql .= "municipio,";
                $cadenaSql .= "proyecto,";
                $cadenaSql .= "id_proyecto,";
                $cadenaSql .= "territorio,";
                $cadenaSql .= "estrato,";
                $cadenaSql .= "geolocalizacion,";
                $cadenaSql .= "jefe_hogar,";
                $cadenaSql .= "pertenencia_etnica,";
                $cadenaSql .= "ocupacion,";
                $cadenaSql .= "nomenclatura,";
                $cadenaSql .= "minvi";
                $cadenaSql .= ") VALUES ";
                $cadenaSql .= "(";
                $cadenaSql .= "'" . $variable['id_beneficiario'] . "',";
                $cadenaSql .= "'" . $variable['tipo_beneficiario'] . "',";
                $cadenaSql .= "'" . $variable['tipo_documento'] . "',";
                $cadenaSql .= "'" . $variable['identificacion_beneficiario'] . "',";
                $cadenaSql .= "'" . $variable['nombre_beneficiario'] . "',";
                $cadenaSql .= "'" . $variable['primer_apellido'] . "',";
                $cadenaSql .= "'" . $variable['segundo_apellido'] . "',";
                $cadenaSql .= "'" . $variable['genero_beneficiario'] . "',";
                $cadenaSql .= "'" . $variable['edad_beneficiario'] . "',";
                $cadenaSql .= "'" . $variable['nivel_estudio'] . "',";
                $cadenaSql .= "'" . $variable['correo'] . "',";
                $cadenaSql .= "'" . $variable['foto'] . "',";
                $cadenaSql .= "'" . $variable['ruta_foto'] . "',";
                $cadenaSql .= "'" . $variable['url_foto'] . "',";
                $cadenaSql .= "'" . $variable['direccion'] . "',";
                $cadenaSql .= "'" . $variable['tipo_vivienda'] . "',";
                $cadenaSql .= "'" . $variable['manzana'] . "',";
                $cadenaSql .= "'" . $variable['bloque'] . "',";
                $cadenaSql .= "'" . $variable['torre'] . "',";
                $cadenaSql .= "'" . $variable['apartamento'] . "',";
                $cadenaSql .= "'" . $variable['telefono'] . "',";
                $cadenaSql .= "'" . $variable['celular'] . "',";
                $cadenaSql .= "'" . $variable['whatsapp'] . "',";
                $cadenaSql .= "'" . $variable['facebook'] . "',";
                $cadenaSql .= "'" . $variable['departamento'] . "',";
                $cadenaSql .= "'" . $variable['municipio'] . "',";
                $cadenaSql .= "'" . $variable['proyecto'] . "',";
                $cadenaSql .= "'" . $variable['id_proyecto'] . "',";
                $cadenaSql .= "'" . $variable['territorio'] . "',";
                $cadenaSql .= "'" . $variable['estrato'] . "',";
                $cadenaSql .= "'" . $variable['geolocalizacion'] . "',";
                $cadenaSql .= "'" . $variable['jefe_hogar'] . "',";
                $cadenaSql .= "'" . $variable['pertenencia_etnica'] . "',";
                $cadenaSql .= "'" . $variable['ocupacion'] . "',";
                $cadenaSql .= "'" . $variable['nomenclatura'] . "',";
                $cadenaSql .= "'" . $variable['minvi'] . "'";
                $cadenaSql .= ");";
                break;

            case "actualizarBeneficiarioPotencial":

                $cadenaSql = "UPDATE interoperacion.beneficiario_potencial SET ";
                $cadenaSql .= "tipo_beneficiario=" . "'" . $variable['tipo_beneficiario'] . "',";
                $cadenaSql .= "tipo_documento=" . "'" . $variable['tipo_documento'] . "',";
                $cadenaSql .= "identificacion=" . "'" . $variable['identificacion_beneficiario'] . "',";
                $cadenaSql .= "nombre=" . "'" . $variable['nombre_beneficiario'] . "',";
                $cadenaSql .= "primer_apellido=" . "'" . $variable['primer_apellido'] . "',";
                $cadenaSql .= "segundo_apellido=" . "'" . $variable['segundo_apellido'] . "',";
                $cadenaSql .= "genero=" . "'" . $variable['genero_beneficiario'] . "',";
                $cadenaSql .= "edad=" . "'" . $variable['edad_beneficiario'] . "',";
                $cadenaSql .= "nivel_estudio=" . "'" . $variable['nivel_estudio'] . "',";
                $cadenaSql .= "correo=" . "'" . $variable['correo'] . "',";
                $cadenaSql .= "foto=" . "'" . $variable['foto'] . "',";
                $cadenaSql .= "ruta_foto=" . "'" . $variable['ruta_foto'] . "',";
                $cadenaSql .= "url_foto=" . "'" . $variable['url_foto'] . "',";
                $cadenaSql .= "direccion=" . "'" . $variable['direccion'] . "',";
                $cadenaSql .= "tipo_vivienda=" . "'" . $variable['tipo_vivienda'] . "',";
                $cadenaSql .= "manzana=" . "'" . $variable['manzana'] . "',";
                $cadenaSql .= "bloque=" . "'" . $variable['bloque'] . "',";
                $cadenaSql .= "torre=" . "'" . $variable['torre'] . "',";
                $cadenaSql .= "apartamento=" . "'" . $variable['apartamento'] . "',";
                $cadenaSql .= "telefono=" . "'" . $variable['telefono'] . "',";
                $cadenaSql .= "celular=" . "'" . $variable['celular'] . "',";
                $cadenaSql .= "whatsapp=" . "'" . $variable['whatsapp'] . "',";
                $cadenaSql .= "facebook=" . "'" . $variable['facebook'] . "',";
                $cadenaSql .= "departamento=" . "'" . $variable['departamento'] . "',";
                $cadenaSql .= "municipio=" . "'" . $variable['municipio'] . "',";
                $cadenaSql .= "proyecto=" . "'" . $variable['proyecto'] . "',";
                $cadenaSql .= "id_proyecto=" . "'" . $variable['id_proyecto'] . "',";
                $cadenaSql .= "territorio=" . "'" . $variable['territorio'] . "',";
                $cadenaSql .= "estrato=" . "'" . $variable['estrato'] . "',";
                $cadenaSql .= "geolocalizacion=" . "'" . $variable['geolocalizacion'] . "',";
                $cadenaSql .= "jefe_hogar=" . "'" . $variable['jefe_hogar'] . "',";
                $cadenaSql .= "pertenencia_etnica=" . "'" . $variable['pertenencia_etnica'] . "',";
                $cadenaSql .= "ocupacion=" . "'" . $variable['ocupacion'] . "' ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "id_beneficiario=" . "'" . $variable['id_beneficiario'] . "';";
                break;

            case "registrarFamiliares":

                $cadenaSql = "INSERT INTO interoperacion.familiar_beneficiario_potencial(";
                $cadenaSql .= "id_beneficiario,";
                $cadenaSql .= "tipo_documento,";
                $cadenaSql .= "identificacion_familiar,";
                $cadenaSql .= "nombre_familiar,";
                $cadenaSql .= "primer_apellido_familiar,";
                $cadenaSql .= "segundo_apellido_familiar,";
                $cadenaSql .= "parentesco,";
                $cadenaSql .= "genero_familiar,";
                $cadenaSql .= "edad_familiar,";
                $cadenaSql .= "celular_familiar,";
                $cadenaSql .= "nivel_estudio_familiar,";
                $cadenaSql .= "correo_familiar,";
                $cadenaSql .= "grado_estudio_familiar,";
                $cadenaSql .= "pertenencia_etnica_familiar,";
                $cadenaSql .= "institucion_educativa_familiar,";
                $cadenaSql .= "ocupacion_familiar";
                $cadenaSql .= ") VALUES ";

                foreach ($variable as $clave => $valor) {

                    $cadenaSql .= "(";
                    $cadenaSql .= "'" . $valor['id_beneficiario'] . "',";
                    $cadenaSql .= "'" . $valor['tipo_documento'] . "',";
                    $cadenaSql .= "'" . $valor['identificacion'] . "',";
                    $cadenaSql .= "'" . $valor['nombre'] . "',";
                    $cadenaSql .= "'" . $valor['primer_apellido'] . "',";
                    $cadenaSql .= "'" . $valor['segundo_apellido'] . "',";
                    $cadenaSql .= "'" . $valor['parentesco'] . "',";
                    $cadenaSql .= "'" . $valor['genero'] . "',";
                    $cadenaSql .= "'" . $valor['edad'] . "',";
                    $cadenaSql .= "'" . $valor['celular'] . "',";
                    $cadenaSql .= "'" . $valor['nivel_estudio'] . "',";
                    $cadenaSql .= "'" . $valor['correo'] . "',";
                    $cadenaSql .= "'" . $valor['grado'] . "',";
                    $cadenaSql .= "'" . $valor['pertenencia_etnica'] . "',";
                    $cadenaSql .= "'" . $valor['institucion_educativa'] . "',";
                    $cadenaSql .= "'" . $valor['ocupacion'] . "'";
                    $cadenaSql .= "),";
                }

                $cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));
                $cadenaSql .= ";";
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

            case "parametroTipoDocumento":
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
                $cadenaSql .= "rparam.descripcion = 'Tipo de Documento' ";
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
                $cadenaSql .= " codigo, ";
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
                $cadenaSql .= " codigo, ";
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

            case "actualizarCampo":
                $campo = $this->miConfigurador->fabricaConexiones->crypto->decodificar($variable['campo']);

                $cadenaSql = "UPDATE interoperacion.beneficiario_potencial SET ";
                if (is_numeric(strpos($campo, 'identificacion_beneficiario'))) {
                    $cadenaSql .= "identificacion ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'tipo_beneficiario'))) {
                    $cadenaSql .= "tipo_beneficiario ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'tipo_documento'))) {
                    $cadenaSql .= "tipo_documento ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'nombre_beneficiario'))) {
                    $cadenaSql .= "nombre ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'primer_apellido'))) {
                    $cadenaSql .= "primer_apellido ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'segundo_apellido'))) {
                    $cadenaSql .= "segundo_apellido ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'genero_beneficiario'))) {
                    $cadenaSql .= "genero ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'edad_beneficiario'))) {
                    $cadenaSql .= "edad ='" . $variable['valor'] . "' ";
                }

                if (is_numeric(strpos($campo, 'nivel_estudio'))) {
                    $cadenaSql .= "nivel_estudio ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'correo'))) {
                    $cadenaSql .= "correo='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'direccion'))) {
                    $cadenaSql .= "direccion='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'tipo_vivienda'))) {
                    $cadenaSql .= "tipo_vivienda ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'manzana'))) {
                    $cadenaSql .= "manzana ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'torre'))) {
                    $cadenaSql .= "torre='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'bloque'))) {
                    $cadenaSql .= "bloque ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'apartamento'))) {
                    $cadenaSql .= "apartamento='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'telefono'))) {
                    $cadenaSql .= "telefono='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'celular'))) {
                    $cadenaSql .= "celular='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'whatsapp'))) {
                    $cadenaSql .= "whatsapp ='" . $variable['valor'] . "' ";
                }
                // if(strpos ($campo,'urbanizacion'),
                // if(strpos ($campo,'departamento'),
                // if(strpos ($campo,'municipio'),
                if (is_numeric(strpos($campo, 'territorio'))) {
                    $cadenaSql .= "territorio ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'facebook'))) {
                    $cadenaSql .= "facebook ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'estrato'))) {
                    $cadenaSql .= "estrato ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'geolocalizacion'))) {
                    $cadenaSql .= "geolocalizacion ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'jefe_hogar'))) {
                    $cadenaSql .= "jefe_hogar ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'pertenencia_etnica'))) {
                    $cadenaSql .= "pertenencia_etnica ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'ocupacion'))) {
                    $cadenaSql .= "ocupacion ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'id_hogar'))) {
                    $cadenaSql .= "id_hogar ='" . $variable['valor'] . "' ";
                }
                if (is_numeric(strpos($campo, 'nomenclatura'))) {
                    $cadenaSql .= "nomenclatura ='" . $variable['valor'] . "' ";
                }

                if (is_numeric(strpos($campo, 'resolucion_adjudicacion'))) {
                    $cadenaSql .= "resolucion_adjudicacion ='" . $variable['valor'] . "' ";
                }
                $cadenaSql .= "WHERE id_beneficiario='" . $variable['id'] . "' ";
                break;

            case "actualizarCampoUrb":
                $campo = $this->miConfigurador->fabricaConexiones->crypto->decodificar($variable['campo']);

                $cadenaSql = "UPDATE interoperacion.beneficiario_potencial SET ";
                $cadenaSql .= "id_proyecto ='" . $variable['urba'] . "', ";
                $cadenaSql .= "proyecto ='" . $variable['proy'] . "', ";
                $cadenaSql .= "departamento ='" . explode(" ", $variable['depa'])[0] . "', ";
                $cadenaSql .= "municipio ='" . explode(" ", $variable['muni'])[0] . "' ";
                $cadenaSql .= "WHERE id_beneficiario='" . $variable['id'] . "' ";
                exit;
                break;

            case "estadoAlfresco":
                $cadenaSql = "SELECT carpeta_creada FROM interoperacion.beneficiario_alfresco ";
                $cadenaSql .= "WHERE id_beneficiario='" . $variable . "' ";
                break;

            case "estadoAlfrescoUpdate":
                $cadenaSql = "INSERT INTO interoperacion.beneficiario_alfresco (carpeta_creada,id_beneficiario) VALUES ";
                $cadenaSql .= "(TRUE ,";
                $cadenaSql .= "'" . $variable . "'); ";
                break;

            case "alfrescoUser":
                $cadenaSql = " SELECT DISTINCT id_beneficiario, nombre_carpeta_dep as padre, nombre_carpeta_mun as hijo, site_alfresco as site ";
                $cadenaSql .= " FROM interoperacion.beneficiario_potencial ";
                $cadenaSql .= " INNER JOIN interoperacion.carpeta_alfresco on beneficiario_potencial.departamento=cast(carpeta_alfresco.cod_departamento as integer) ";
                $cadenaSql .= " WHERE cast(cod_municipio as integer)=municipio ";
                $cadenaSql .= " AND id_beneficiario='" . $variable . "' ";
                break;

            case "alfrescoCarpetas":
                $cadenaSql = "SELECT parametros.descripcion ";
                $cadenaSql .= " FROM parametros.parametros ";
                $cadenaSql .= " JOIN parametros.relacion_parametro ON relacion_parametro.id_rel_parametro=parametros.rel_parametro ";
                $cadenaSql .= " WHERE parametros.estado_registro=TRUE AND relacion_parametro.descripcion='Alfresco Folders' ";
                break;

            case "alfrescoDirectorio":
                $cadenaSql = "SELECT parametros.descripcion ";
                $cadenaSql .= " FROM parametros.parametros ";
                $cadenaSql .= " JOIN parametros.relacion_parametro ON relacion_parametro.id_rel_parametro=parametros.rel_parametro ";
                $cadenaSql .= " WHERE parametros.estado_registro=TRUE AND relacion_parametro.descripcion='Directorio Alfresco Site' ";
                break;

            case "alfrescoLog":
                $cadenaSql = "SELECT host, usuario, password ";
                $cadenaSql .= " FROM parametros.api_data ";
                $cadenaSql .= " WHERE componente='alfresco' ";
                break;

            case "codificacion":
                $cadenaSql = "SELECT mc.abreviatura AS abr_mun, ur.abreviatura AS  abr_urb, ur.abreviatura_benef AS abr_benf ";
                $cadenaSql .= "FROM parametros.urbanizacion ur join parametros.municipio mc ON  ur.codigo_mun=mc.codigo_mun ";
                $cadenaSql .= "WHERE id_urbanizacion='" . $variable . "'";
                break;

            case 'consultarConsecutivo':
                $cadenaSql = " select id_beneficiario ";
                $cadenaSql .= "FROM interoperacion.beneficiario_potencial ";
                $cadenaSql .= "WHERE id_beneficiario ";
                $cadenaSql .= "ILIKE '" . $variable['string'] . "%' ";
                $cadenaSql .= "AND substr(id_beneficiario, length(id_beneficiario)-" . $variable['longitud'];
                $cadenaSql .= ", 1) ~ '^[0-9]'";
                $cadenaSql .= "ORDER BY id_beneficiario DESC LIMIT 1";
                break;
        }

        return $cadenaSql;
    }
}

?>