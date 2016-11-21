<?php
namespace reportes\informacionBeneficiarios\entidad;

class GenerarReporteInstalaciones {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;
    public $proyectos_general;

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        $conexion = "interoperacion";

        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        /**
         * 0. Estrucurar Información Reporte
         **/
        $this->estruturarProyectos();

        /**
         * 6. Crear Documento Hoja de Calculo(Reporte)
         **/

        $this->crearHojaCalculo();

    }

    public function estruturarProyectos() {

        $cadenaSql = $this->miSql->getCadenaSql('consultaInformacionBeneficiario');
        $this->beneficiarios = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    }

    public function crearHojaCalculo() {
        include_once "crearDocumentoHojaCalculo.php";

    }

}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>

