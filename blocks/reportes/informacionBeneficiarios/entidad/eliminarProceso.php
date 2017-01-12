<?php
namespace reportes\informacionBeneficiarios\entidad;

include_once 'Redireccionador.php';

class GenerarReporteInstalaciones {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        $conexion = "interoperacion";

        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('consultarEstadoProceso');
        $estadoproceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

        if (is_null($estadoproceso)) {
            Redireccionador::redireccionar('ErrorEliminarProceso');
        } else {

            $cadenaSql = $this->miSql->getCadenaSql('eliminarProceso');
            $proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "accesp");

            if ($proceso) {
                Redireccionador::redireccionar('ExitoEliminarProceso');
            }

        }

    }

}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>

