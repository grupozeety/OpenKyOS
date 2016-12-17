<?php

namespace gestionBeneficiarios\aprobacionContrato\entidad;

include_once 'Redireccionador.php';
require_once 'sincronizar.php';
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

        date_default_timezone_set('America/Bogota');
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->sincronizacion = new Sincronizar($lenguaje, $sql);
        // Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        /**
         * 1.
         * Actualizar Contrato
         */

        $this->actualizarContrato();

        /**
         * 2.
         * Actualizar Servicio
         */

        $this->actualizarServicio();

        /**
         * 3.
         * Actualizar Servicio
         */
        $this->estruturarComisionamiento();

        /**
         * 4.
         * Redireccionar
         */

        if ($this->actualizarContrato && $this->actualizarServicio) {
            Redireccionador::redireccionar('actualizoContrato');
        } else {
            Redireccionador::redireccionar('noActualizo');
        }
    }
    public function estruturarComisionamiento() {
        include_once "estructuracionComisionamiento.php";
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

        $this->asosicarCodigoDocumento();
        $this->cargarDocumento();

        $this->sincronizacion->sincronizarAlfresco($_REQUEST['id_beneficiario'], $this->archivos_datos[0]);

        $array = array_merge($this->infoDocumento, $id_estadoContrato);

        $cadenaSql = $this->miSql->getCadenaSql('actualizarContrato', $array);

        $this->actualizarContrato = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
    }

    public function asosicarCodigoDocumento() {
        foreach ($_FILES as $key => $value) {
            $cadenaSql = $this->miSql->getCadenaSql('consultarParametro', $key);
            $id_parametro = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
            $_FILES[$key]['tipo_documento'] = $id_parametro[0]['codigo'];
            $_FILES[$key]['descripcion_documento'] = $id_parametro[0]['codigo'] . '_' . $id_parametro[0]['descripcion'];
        }

    }

    public function cargarDocumento() {
        foreach ($_FILES as $key => $archivo) {
            if ($_FILES[$key]['size'] != 0) {
                $this->prefijo = substr(md5(uniqid(time())), 0, 6);
                $exten = pathinfo($archivo['name']);
                
                $allowed =  array('image/jpeg','image/png','image/psd','image/bmp','application/pdf');
                	
                if(!in_array($_FILES[$key]['type'],$allowed) ) {
                	Redireccionador::redireccionar ( "ErrorCargarFicheroDirectorio" );
                	exit ();
                }
                
                if( isset($exten ['extension'])==false){
                	$exten ['extension']='txt';
                }
                $tamano = $archivo['size'];
                $tipo = $archivo['type'];
                $nombre_archivo = str_replace(" ", "_", $archivo['descripcion_documento']);
                $doc =$nombre_archivo . "_" . $this->prefijo . '.' . $exten['extension'];
                /*
                 * guardamos el fichero en el Directorio
                 */
                $ruta_absoluta = $this->miConfigurador->configuracion['raizDocumento'] . "/archivos/contratos/" . $doc;
                $ruta_relativa = $this->miConfigurador->configuracion['host'] . $this->miConfigurador->configuracion['site'] . "/archivos/contratos/" . $doc;
                $archivo['rutaDirectorio'] = $ruta_absoluta;
                if (!copy($archivo['tmp_name'], $ruta_absoluta)) {
                    Redireccionador::redireccionar("ErrorCargarFicheroDirectorio");
                    exit();
                }
                $archivo_datos[] = array(
                    'ruta_archivo' => $ruta_relativa,
                    'rutaabsoluta' => $ruta_absoluta,
                    'nombre_archivo' => $doc,
                    'campo' => $key,
                    'tipo_documento' => $archivo['tipo_documento'],
                );

                $this->infoDocumento = array(
                    'ruta_archivo' => $ruta_relativa,
                    'nombre_archivo' => $doc,
                );

            }
        }

        $this->archivos_datos = $archivo_datos;
    }

    public function procesarFormulario() {
        echo "qweqweq";
        var_dump($_REQUEST);
        exit();
        // Aquí va la lógica de procesamiento

        // Al final se ejecuta la redirección la cual pasará el control a otra página
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

