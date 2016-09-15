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

    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;

        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        /**
         *  1. CargarArchivos en el Directorio
         **/

        $this->cargarArchivos();

        /**
         *  2. Asociar Codigo Documento
         **/

        $this->asosicarCodigoDocumento();

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

    public function asosicarCodigoDocumento() {

        foreach ($this->archivos_datos AS $key => $value) {

            switch ($value['campo']) {
                case 'cedula':
                    $this->archivos_datos[$key]['tipo_documento'] = 75;
                    break;

                case 'certificado_servicio':
                    $this->archivos_datos[$key]['tipo_documento'] = 77;
                    break;

                case 'acta_vip':
                    $this->archivos_datos[$key]['tipo_documento'] = 81;
                    break;

                case 'documento_acceso_propietario':
                    $this->archivos_datos[$key]['tipo_documento'] = 78;
                    break;

                case 'documento_direccion':
                    $this->archivos_datos[$key]['tipo_documento'] = 79;
                    break;

                case 'certificado_proyecto_vip':
                    $this->archivos_datos[$key]['tipo_documento'] = 77;
                    break;

                case 'cedula_cliente':
                    $this->archivos_datos[$key]['tipo_documento'] = 80;
                    break;

            }

        }

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

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);
?>

