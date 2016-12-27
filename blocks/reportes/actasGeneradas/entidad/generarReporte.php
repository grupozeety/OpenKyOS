<?php

namespace reportes\actasGeneradas\entidad;

include_once 'Redireccionador.php';
class GenerarReporteInstalaciones {
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $informacion;
    public $encriptador;
    public $esteRecursoERP;
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        /**
         * XX.
         * Consultar Informacion(Reporte)
         */

        $this->consultarInformacion();

        /**
         * 6.
         * Crear Documento Hoja de Calculo(Reporte)
         */

        $this->crearHojaCalculo();
    }
    public function consultarInformacion() {

        switch ($_REQUEST['tipo_acta']) {
            case '1':
                $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionPortatil');
                break;

            case '2':
                $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionServicios');
                break;
        }

        $this->informacion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        if ($this->informacion == false) {

            Redireccionador::redireccionar('SinResultado');
        }

    }
    public function crearHojaCalculo() {
        include_once "crearDocumentoHojaCalculo.php";
    }
}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>

