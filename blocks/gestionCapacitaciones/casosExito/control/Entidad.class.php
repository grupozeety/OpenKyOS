<?php
namespace gestionCapacitaciones\casosExito;

if (!isset($GLOBALS["autorizado"])) {
    include "../index.php";
    exit();
}

require_once "core/manager/Configurador.class.php";
require_once "core/builder/InspectorHTML.class.php";
require_once "core/builder/Mensaje.class.php";
require_once "core/crypto/Encriptador.class.php";

// Esta clase contiene la logica de negocio del bloque y extiende a la clase funcion general la cual encapsula los
// metodos mas utilizados en la aplicacion

// Para evitar redefiniciones de clases el nombre de la clase del archivo funcion debe corresponder al nombre del bloque
// en camel case precedido por la palabra Funcion
class Entidad
{
    public $sql;
    public $entidad;
    public $lenguaje;
    public $ruta;
    public $miConfigurador;
    public $error;
    public $miRecursoDB;
    public $crypto;
    public function verificarCampos()
    {
        include_once $this->ruta . "/funcion/verificarCampos.php";
        if ($this->error == true) {
            return false;
        } else {
            return true;
        }
    }
    public function redireccionar($opcion, $valor = "")
    {
        include_once $this->ruta . "entidad/Redireccionador.php";
    }

    public function procesarAjax()
    {
        include_once $this->ruta . "entidad/procesarAjax.php";
    }

    public function validarInformacion()
    {
        include_once $this->ruta . "entidad/validarInformacion.php";
    }

    public function cargarInformacion()
    {
        include_once $this->ruta . "entidad/registrarActualizar.php";
    }

    public function eliminar()
    {
        include_once $this->ruta . "entidad/eliminar.php";
    }
    public function action()
    {
        $resultado = true;

        // Aquí se coloca el código que procesará los diferentes formularios que pertenecen al bloque
        // aunque el código fuente puede ir directamente en este script, para facilitar el mantenimiento
        // se recomienda que aqui solo sea el punto de entrada para incluir otros scripts que estarán
        // en la carpeta funcion

        // Importante: Es adecuado que sea una variable llamada opcion o action la que guie el procesamiento:
        if (isset($_REQUEST['procesarAjax'])) {
            $this->procesarAjax();
        }

        switch ($_REQUEST['opcion']) {
            case 'registrarCompetencia':
                $this->cargarInformacion();
                break;

            case 'actualizarCompetencia':
                $this->cargarInformacion();
                break;

        }

        return $resultado;
    }
    public function __construct()
    {
        $this->miConfigurador = \Configurador::singleton();

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");

        $this->miMensaje = \Mensaje::singleton();

        $conexion = "aplicativo";
        $this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        if (!$this->miRecursoDB) {
            $this->miConfigurador->fabricaConexiones->setRecursoDB($conexion, "tabla");
            $this->miRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        }
    }
    public function setRuta($unaRuta)
    {
        $this->ruta = $unaRuta;
    }
    public function setSql($a)
    {
        $this->sql = $a;
    }
    public function setEntidad($entidad)
    {
        $this->entidad = $entidad;
    }
    public function setLenguaje($lenguaje)
    {
        $this->lenguaje = $lenguaje;
    }
    public function setFormulario($formulario)
    {
        $this->formulario = $formulario;
    }
}
