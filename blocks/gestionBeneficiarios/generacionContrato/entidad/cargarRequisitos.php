<?php
namespace gestionBeneficiarios\generacionContrato\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

include_once 'Redireccionador.php';
class FormProcessor {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $archivos_datos;

    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        var_dump($_REQUEST);

        /**
         *  1. CargarArchivos en el Directorio
         **/

        $this->cargarArchivos();

        /**
         *  2. Asociar Codigo Documento
         **/

        $this->asosicarCodigoDocumento();

        /**
         *  3. Validar Elementos Proyecto y Elementos Consumo
         **/

        $this->validarElementos();

        /**
         *  3. Generar Documento PDF
         **/

        $this->generarDocumentoPDF();

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
            $ruta_absoluta = $this->miConfigurador->configuracion['raizDocumento'] . "/archivos/" . $this->prefijo . "_" . $nombre_archivo;
            $ruta_relativa = $this->miConfigurador->configuracion['host'] . $this->miConfigurador->configuracion['site'] . "/archivos/" . $this->prefijo . "_" . $nombre_archivo;
            $archivo['rutaDirectorio'] = $ruta_absoluta;
            if (!copy($archivo['tmp_name'], $ruta_absoluta)) {
                exit;
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

    public function generarDocumentoPDF() {
        include_once "generarDocumentoPdf.php";
    }

    public function validarElementos() {

        if ($this->elementos_consumidos) {

            foreach ($this->elementos_projecto as $key => $value) {

                $elemento = $value;

                foreach ($this->elementos_consumidos as $key => $value) {

                    if ($elemento['name'] == $value['nombre']) {

                        $elemento['qty'] = $elemento['qty'] - $value['consumo'];

                    }

                }

                if ($elemento['qty'] > 0) {

                    $elementos_reporte[] = $elemento;

                }

            }

        } else {
            $elementos_reporte = $this->elementos_projecto;
        }

        $this->elementos_reporte = $elementos_reporte;

    }
    public function consultarElementosConsumidos() {

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('elementosConsumidos');
        $this->elementos_consumidos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    }

    public function procesarVariables() {

        $_REQUEST['elementos'] = explode("@", $_REQUEST['elementos']);
        $_REQUEST['ordenes'] = explode("@", $_REQUEST['ordenes']);

        foreach ($_REQUEST['elementos'] as $key => $value) {

            $array = json_decode(base64_decode($value), true);

            foreach ($array as $key => $value) {
                $elementos[] = $value;
            }

        }
        unset($array);
        foreach ($_REQUEST['ordenes'] as $key => $value) {
            $array = json_decode(base64_decode($value), true);

            foreach ($array as $key => $value) {
                $ordenes[] = $value;
            }
        }
        unset($array);

        foreach ($elementos as $key => $value) {

            $elemento = $value;
            unset($elemento['material']);

            foreach ($ordenes as $key => $value) {
                unset($value['project']);
                if ($value['name'] == $elemento['parent']) {

                    $elemento['numero_orden'] = $value['id_orden_trabajo'];
                    $elemento['descripcion_orden'] = $value['descripcion_orden'];
                    $elementos_projecto[] = $elemento;

                }
            }

        }
        $this->elementos_projecto = $elementos_projecto;

    }

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);

?>

