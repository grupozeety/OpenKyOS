<?php
namespace reportes\informacionBeneficiarios\entidad;

include_once 'Redireccionador.php';

class GenerarReporteInstalaciones
{

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;

    public function __construct($sql)
    {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        $conexion = "interoperacion";

        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        $ruta_directorio_raiz = $rutaAbsoluta . "/archivos/archivosDescargaAccesos/";

        $cadenaSql = $this->miSql->getCadenaSql('consultarEstadoProceso');
        $estadoproceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

        if (is_null($estadoproceso)) {
            Redireccionador::redireccionar('ErrorEliminarProceso');
        } else {

            if (isset($estadoproceso['nombre_archivo']) && $estadoproceso['estado'] == 'Finalizado') {

                $archivo = $ruta_directorio_raiz . "/" . $estadoproceso['nombre_archivo'];

                unlink($archivo);
            }

            $cadenaSql = $this->miSql->getCadenaSql('eliminarProceso');
            $proceso = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

            if ($proceso) {
                Redireccionador::redireccionar('ExitoEliminarProceso');
            }

        }

    }

}

$miProcesador = new GenerarReporteInstalaciones($this->sql);
