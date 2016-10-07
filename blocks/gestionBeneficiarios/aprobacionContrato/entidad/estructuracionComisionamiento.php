<?php
namespace gestionBeneficiarios\aprobacionContrato\entidad;

class comisionamientoOP {

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

        echo "estruturar Comisionamiento";
        var_dump($_REQUEST);exit;

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

}

$miProcesador = new comisionamientoOP($this->lenguaje, $this->miSql);

?>

