<?php
namespace gestionBeneficiarios\aprobacionContrato\entidad;
include_once 'Redireccionador.php';

class FormProcessor {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $esteRecursoDB;
    public $infoDocumento;
    public $prefijo;
    public $actualizarContrato;
    public $actualizarServicio;
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
         * 1. Actualizar Contrato
         **/

        $this->actualizarContrato();

        /**
         * 2. Actualizar Servicio
         **/

        $this->actualizarServicio();

        /**
         * 3. Redireccionar
         **/

        if ($this->actualizarContrato && $this->actualizarServicio) {
            Redireccionador::redireccionar('actualizoContrato');
        } else {
            Redireccionador::redireccionar('noActualizo');
        }
    }

    public function actualizarServicio() {

        if ($this->actualizarContrato) {
            $cadenaSql = $this->miSql->getCadenaSql('consultarEstadoInstalarAgendar');
            $id_estadoServicio = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            $id_estadoServicio = $id_estadoServicio[0];

            $cadenaSql = $this->miSql->getCadenaSql('actualizarServicio', $id_estadoServicio);
            $this->actualizarServicio = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

        }

    }
    public function actualizarContrato() {

        $cadenaSql = $this->miSql->getCadenaSql('consultarEstadoAprobado');
        $id_estadoContrato = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $id_estadoContrato = $id_estadoContrato[0];

        $this->cargarDocumento();

        $array = array_merge($this->infoDocumento, $id_estadoContrato);

        $cadenaSql = $this->miSql->getCadenaSql('actualizarContrato', $array);

        $this->actualizarContrato = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

    }
    public function cargarDocumento() {

        $archivo = $_FILES['archivo_contrato'];

        $this->prefijo = substr(md5(uniqid(time())), 0, 6);
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

            Redireccionador::redireccionar("ErrorCargarFicheroDirectorio");

        }

        $this->infoDocumento = array(
            'ruta_archivo' => $ruta_relativa,
            'nombre_archivo' => $archivo['name'],
        );

    }

    public function procesarFormulario() {
        echo "qweqweq";
        var_dump($_REQUEST);exit;
        //Aquí va la lógica de procesamiento

        //Al final se ejecuta la redirección la cual pasará el control a otra página
        $variable = 'cualquierDato';
        Redireccionador::redireccionar('opcion1', $variable);

    }

    public function resetForm() {
        foreach ($_REQUEST as $clave => $valor) {

            if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
                unset($_REQUEST[$clave]);
            }
        }
    }

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);

?>

