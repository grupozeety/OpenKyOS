<?php

namespace reportes\disponibilidad\entidad;

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
    public $informacion3;
    public $informacion4;
    public $informacion5;
    public $informacion6;
    public $informacion7;
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

        //Consulta repote A
        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacion');

        $this->informacion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        //Consulta repote AB
        $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionAB');

         $this->informacion1 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");


         //Consulta reporte B
         $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionB');

         $this->informacion2 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

         //Consulta reporte C
         $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionC');

         $this->informacion3 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

         //Consulta reporte D
         $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionD');

         $this->informacion4 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

         //Consulta reporte E
         $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionE');

         $this->informacion5 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

         //Consulta reporte F
         $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionF');

         $this->informacion6 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

         //Consulta reporte G
         $cadenaSql = $this->miSql->getCadenaSql('consultarInformacionG');

         $this->informacion7 = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");


      //      var_dump(  $this->informacion2);
    //        var_dump($cadenaSql);

        if ($this->informacion == false&&$this->informacion1 == false&&$this->informacion2 == false&&$this->informacion3 == false&&$this->informacion4 == false&&$this->informacion5 == false&&$this->informacion6 == false&&$this->informacion7 == false) {

            Redireccionador::redireccionar('SinResultado');
        }



    }
    public function crearHojaCalculo() {
        include_once "crearDocumentoHojaCalculo.php";
    }
}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>
