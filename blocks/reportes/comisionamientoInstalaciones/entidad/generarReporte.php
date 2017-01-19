<?php
namespace reportes\comisionamientoInstalaciones\entidad;

include_once 'Redireccionador.php';

class GenerarReporteInstalaciones {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;
    public $proyectos_general;
    public $directorio_archivos;
    public $ruta_directorio = '';

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        $conexion = "interoperacion";

        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $this->rutaURL = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site");
        $this->rutaAbsoluta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");

        $this->estruturarProyectos();
        $this->crearHojaCalculo();

    }

    public function estruturarProyectos() {

        ini_set('xdebug.var_display_max_depth', 5);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 1024);
        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionBeneficiario');

        $this->beneficiarios = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        if ($this->beneficiarios == false) {

            Redireccionador::redireccionar('SinResultado');
        }

    }

    public function crearHojaCalculo() {
        include_once "crearDocumentoHojaCalculo.php";

    }

}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>

