<?php
namespace gestionNotificacionesCorreo\entidad;

class procesarAjax
{
    public $miConfigurador;
    public $sql;
    public function __construct($sql)
    {
        $this->miConfigurador = \Configurador::singleton();

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");

        $this->sql = $sql;

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        switch ($_REQUEST['funcion']) {

            case 'consultarUsuarios':
                include_once "consultarUsuarios.php";
                break;

            case 'ejecutarNotificaciones':
                include_once "ejecutarNotificaciones.php";
                break;
        }
    }
}

$miProcesarAjax = new procesarAjax($this->sql);
