<?php
namespace reportes\reporteQuincenal\entidad;

class GenerarReporteInstalaciones {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $informacion;

    //_______________________________
    public $esteRecursoAD;
    //________________________________
    
    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $conexion = "almacendatos";
        $this->esteRecursoAD = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $_REQUEST['tiempo'] = time();

        /**
         * XX. Consultar Informacion(Reporte)
         **/

        $this->consultarInformacion();

        /**
         * 6. Crear Documento Hoja de Calculo(Reporte)
         **/

        $this->crearHojaCalculo();

    }

    public function consultarInformacion() {

       	$this->informacion = array();
        
    }
    
    public function crearHojaCalculo() {
    	include_once "crearDocumentoHojaCalculo.php";
    }

}

$miProcesador = new GenerarReporteInstalaciones($this->sql);

?>

