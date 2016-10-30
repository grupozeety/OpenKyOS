<?php
namespace gestionBeneficiarios\generacionContrato\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

use gestionBeneficiarios\generacionContrato\entidad\Redireccionador;

include_once 'Redireccionador.php';
class FormProcessor {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $archivos_datos;
    public $esteRecursoDB;
    public $datos_contrato;
    public $rutaURL;
    public $rutaAbsoluta;
    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");

        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        if (!isset($_REQUEST["bloqueGrupo"]) || $_REQUEST["bloqueGrupo"] == "") {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloque"] . "/";
        } else {
            $this->rutaURL .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
            $this->rutaAbsoluta .= "/blocks/" . $_REQUEST["bloqueGrupo"] . "/" . $_REQUEST["bloque"] . "/";
        }
        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        echo "procesando";

        var_dump($_FILES);
        var_dump($_REQUEST);

        /**
         *  1. CargarArchivos en el Directorio
         **/

        $this->cargarArchivos();

        /**
         *  2. Procesar Informacion Contrato
         **/

        $this->procesarInformacion();
        exit;
        /**
         *  3. Registrar Documentos
         **/

        $this->registrarDocumentos();

        /**
         *  4. Registrar Contrato Borrador y Servicio
         **/

        $this->registrarContratoBorrador();

        if ($this->datos_contrato) {
            Redireccionador::redireccionar("Inserto");
        } else {
            Redireccionador::redireccionar("NoInserto");
        }

    }

    public function registrarContratoBorrador() {

        $cadenaSql = $this->miSql->getCadenaSql('registrarContrato');
        $registro_contrato = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $this->datos_contrato = $registro_contrato;

        $cadenaSql = $this->miSql->getCadenaSql('registrarServicio', $registro_contrato[0][0]);
        $registro_servicio = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

    }
    public function registrarDocumentos() {

        foreach ($this->archivos_datos as $key => $value) {
            $cadenaSql = $this->miSql->getCadenaSql('registrarDocumentos', $value);
            $registro_docmuentos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

        }

    }

    public function procesarInformacion() {

        $arreglo = array('' => "");

    }

    public function cargarArchivos() {

        foreach ($_FILES as $key => $archivo) {

            $this->prefijo = substr(md5(uniqid(time())), 0, 6);
            /*
             * obtenemos los datos del Fichero
             */
            $tamano = $archivo['size'];
            $tipo = $archivo['type'];
            $nombre_archivo = str_replace(" ", "", $archivo['name']);
            /*
             * guardamos el fichero en el Directorio
             */
            $ruta_absoluta = $this->rutaAbsoluta . "/entidad/firmas/" . $this->prefijo . "_" . $nombre_archivo;

            $ruta_relativa = $this->rutaURL . "/entidad/firmas/" . $this->prefijo . "_" . $nombre_archivo;

            $archivo['rutaDirectorio'] = $ruta_absoluta;

            if (!copy($archivo['tmp_name'], $ruta_absoluta)) {
                echo "error";exit;
                Redireccionador::redireccionar("ErrorCargarFicheroDirectorio");
            }

            $archivo_datos[] = array(
                'ruta_archivo' => $ruta_relativa,
                'nombre_archivo' => $archivo['name'],
                'campo' => $key,
            );
        }

        $this->archivos_datos = $archivo_datos;

    }

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
?>

