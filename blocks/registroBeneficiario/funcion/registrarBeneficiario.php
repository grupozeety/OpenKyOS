<?php

namespace registroBeneficiario\funcion;

use agendarComisionamiento\funcion\sincronizar;
use registroBeneficiario\funcion\redireccionar;

require_once 'blocks/agendarComisionamiento/funcion/sincronizar.php';

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}
class Registrar {
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miFuncion;
    public $miSql;
    public $conexion;
    public function __construct($lenguaje, $sql, $funcion) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->miFuncion = $funcion;
        $this->sincronizacion = new sincronizar($lenguaje, $sql, $funcion);
    }
    public function procesarFormulario() {

    	$beneficiarioPotencial = array();

        $beneficiarioPotencial['id_beneficiario'] = $_REQUEST['id_beneficiario'];
        $beneficiarioPotencial['tipo_beneficiario'] = $_REQUEST['tipo_beneficiario'];
        $beneficiarioPotencial['identificacion_beneficiario'] = $_REQUEST['identificacion_beneficiario'];
        $beneficiarioPotencial['tipo_documento'] = $_REQUEST['tipo_documento'];
        $beneficiarioPotencial['nombre_beneficiario'] = $_REQUEST['nombre_beneficiario'];
        $beneficiarioPotencial['primer_apellido'] = $_REQUEST['primer_apellido'];
        $beneficiarioPotencial['segundo_apellido'] = $_REQUEST['segundo_apellido'];
        $beneficiarioPotencial['genero_beneficiario'] = $_REQUEST['genero_beneficiario'];
        $beneficiarioPotencial['edad_beneficiario'] = $_REQUEST['edad_beneficiario'];
        $beneficiarioPotencial['nivel_estudio'] = $_REQUEST['nivel_estudio'];
        $beneficiarioPotencial['correo'] = $_REQUEST['correo'];
        $beneficiarioPotencial['foto'] = $_REQUEST['nombre_foto'];
        $beneficiarioPotencial['url_foto'] = $_REQUEST['urlFoto'];
        $beneficiarioPotencial['ruta_foto'] = $_REQUEST['rutaFoto'];
        $beneficiarioPotencial['direccion'] = $_REQUEST['direccion'];
        $beneficiarioPotencial['tipo_vivienda'] = $_REQUEST['tipo_vivienda'];
        $beneficiarioPotencial['manzana'] = $_REQUEST['manzana'];
        $beneficiarioPotencial['torre'] = $_REQUEST['torre'];
        $beneficiarioPotencial['bloque'] = $_REQUEST['bloque'];
        $beneficiarioPotencial['apartamento'] = $_REQUEST['apartamento'];
        $beneficiarioPotencial['telefono'] = $_REQUEST['telefono'];
        $beneficiarioPotencial['celular'] = $_REQUEST['celular'];
        $beneficiarioPotencial['whatsapp'] = $_REQUEST['whatsapp'];
        $beneficiarioPotencial['facebook'] = $_REQUEST['facebook'];
        $departamento = explode(" ", $_REQUEST['departamento']);
        $beneficiarioPotencial['departamento'] = $departamento[0];
        $municipio = explode(" ", $_REQUEST['municipio']);
        $beneficiarioPotencial['municipio'] = $municipio[0];
        $beneficiarioPotencial['id_proyecto'] = $_REQUEST['urbanizacion'];
        $beneficiarioPotencial['proyecto'] = $_REQUEST['id_urbanizacion'];
        $beneficiarioPotencial['territorio'] = $_REQUEST['territorio'];
        $beneficiarioPotencial['estrato'] = $_REQUEST['estrato'];
        $beneficiarioPotencial['geolocalizacion'] = $_REQUEST['geolocalizacion'];
        $beneficiarioPotencial['jefe_hogar'] = $_REQUEST['jefe_hogar'];
        $beneficiarioPotencial['pertenencia_etnica'] = $_REQUEST['pertenencia_etnica'];
        $beneficiarioPotencial['ocupacion'] = $_REQUEST['ocupacion'];
        $beneficiarioPotencial['id_hogar'] = $_REQUEST['id_hogar'];
        $beneficiarioPotencial['nomenclatura'] = $_REQUEST['nomenclatura'];
        $beneficiarioPotencial['resolucion_adjudicacion'] = $_REQUEST['resolucion_adjudicacion'];

        $familiar = array();

        for ($i = 0; $i < $_REQUEST['familiares']; $i++) {

        	
            $familiar[$i]['id_beneficiario'] = $_REQUEST['id_beneficiario'];
            $familiar[$i]['tipo_documento'] = $_REQUEST['tipo_documento_familiar_' . $i];
            $familiar[$i]['identificacion'] = $_REQUEST['identificacion_familiar_' . $i];
            $familiar[$i]['nombre'] = $_REQUEST['nombre_familiar_' . $i];
            $familiar[$i]['primer_apellido'] = $_REQUEST['primer_apellido_familiar_' . $i];
            $familiar[$i]['segundo_apellido'] = $_REQUEST['segundo_apellido_familiar_' . $i];
            $familiar[$i]['parentesco'] = $_REQUEST['parentesco_' . $i];
            $familiar[$i]['genero'] = $_REQUEST['genero_familiar_' . $i];
            $familiar[$i]['edad'] = $_REQUEST['edad_familiar_' . $i];
            $familiar[$i]['celular'] = $_REQUEST['celular_familiar_' . $i];
            $familiar[$i]['nivel_estudio'] = $_REQUEST['nivel_estudio_familiar_' . $i];
            $familiar[$i]['correo'] = $_REQUEST['correo_familiar_' . $i];
            $familiar[$i]['grado'] = $_REQUEST['grado_familiar_' . $i];
            $familiar[$i]['institucion_educativa'] = $_REQUEST['institucion_educativa_familiar_' . $i];
            $familiar[$i]['pertenencia_etnica'] = $_REQUEST['pertenencia_etnica_familiar_' . $i];
            $familiar[$i]['ocupacion'] = $_REQUEST['ocupacion_familiar_' . $i];
        }

        $beneficiarioPotencial['familiar'] = $familiar;

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/blocks/";
        $rutaBloque .= $esteBloque['nombre'];
        $host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/" . $esteBloque['nombre'];

        $cadenaSql = $this->miSql->getCadenaSql('actualizarBeneficiario', $beneficiarioPotencial['id_beneficiario']);
        $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "actualizar");

        if ($resultado) {
            $cadenaSql = $this->miSql->getCadenaSql('registrarBeneficiarioPotencial', $beneficiarioPotencial);
            $cadenaSql = str_replace("''", 'null', $cadenaSql);
            $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");
        }

        if ($resultado) {
            $cadenaSql = $this->miSql->getCadenaSql('actualizarFamiliarBeneficiario', $beneficiarioPotencial['id_beneficiario']);
            $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "actualizar");
        }

        if ($resultado && $_REQUEST['familiares'] > 0) {
            $cadenaSql = $this->miSql->getCadenaSql('registrarFamiliares', $beneficiarioPotencial['familiar']);
            $cadenaSql = str_replace("''", 'null', $cadenaSql);
            $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");
        }

        if ($resultado) {
            // Crear carpeta Alfresco
            $cadenaSql = $this->miSql->getCadenaSql('estadoAlfresco', $_REQUEST['id_beneficiario']);
            $estado_carpeta = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            if ($estado_carpeta[0][0] == 'f') {
                $alfresco = $this->sincronizacion->alfresco($_REQUEST['id_beneficiario']);
                if ($alfresco['estado'][0] == 0) {
                    $cadenaSql = $this->miSql->getCadenaSql('estadoAlfrescoUpdate', $_REQUEST['id_beneficiario']);
                    $estado_carpeta = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                    redireccion::redireccionar('inserto');
                    exit();
                } else {
                    redireccion::redireccionar('insertoAlfresco');
                    exit();
                }
            }

            redireccion::redireccionar('inserto');
            exit();
        } else {
            redireccion::redireccionar('noInserto');
            exit();
        }
    }
    public function resetForm() {
        foreach ($_REQUEST as $clave => $valor) {

            if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
                unset($_REQUEST[$clave]);
            }
        }
    }
}

$miRegistrador = new Registrar($this->lenguaje, $this->sql, $this->funcion);

$resultado = $miRegistrador->procesarFormulario();

?>
