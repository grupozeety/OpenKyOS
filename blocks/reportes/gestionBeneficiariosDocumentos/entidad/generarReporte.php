<?php

namespace reportes\gestionBeneficiariosDocumentos\entidad;

include_once 'Redireccionador.php';
class GenerarReporteInstalaciones
{
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $informacion;
    public $encriptador;
    public $esteRecursoERP;
    public function __construct($sql)
    {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $conexion = "interoperacion";
//        $conexion = "produccion";
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
    public function consultarInformacion()
    {

        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacion');

        $this->informacion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        if ($this->informacion == false) {

            Redireccionador::redireccionar('SinResultado');
        }

    }
    public function crearHojaCalculo()
    {
        include_once "crearDocumentoHojaCalculo.php";
    }
}

$miProcesador = new GenerarReporteInstalaciones($this->sql);
