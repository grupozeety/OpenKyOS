<?php

namespace reportes\velocidadMinima\entidad;

include_once 'Redireccionador.php';
class GenerarReporteInstalaciones {
    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $informacion;
    public $informacion1;
    public $informacion2;
    public $encriptador;
    public $esteRecursoERP;
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $conexion = "webservices";
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

        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacion');

        $this->informacion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        //var_dump(  $this->informacion);
        //     var_dump($cadenaSql);
        //    var_dump($this->objCal3);
      //    exit;

        //Consulta repote B
         $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionB');

         $this->informacion1 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");




         //Consulta reporte C
         $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionC');

         $this->informacion2 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

         //var_dump(  $this->informacion2);
         //exit;
        if ($this->informacion == false&&$this->informacion1 == false&&$this->informacion2 == false) {

            Redireccionador::redireccionar('SinResultado');
        }



    }
    public function crearHojaCalculo() {
        include_once "crearDocumentoHojaCalculo.php";
    }
}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>
