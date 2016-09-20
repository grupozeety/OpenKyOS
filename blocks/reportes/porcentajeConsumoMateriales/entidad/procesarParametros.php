<?php
namespace reportes\porcentajeConsumoMateriales\entidad;

class FormProcessor {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $elementos_reporte;

    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        /**
         *  1. Consultar Los porcentajes por Proyecto Consumidos
         **/

        $this->consultarPorcentajeOrden();

        /**
         *  2. Generar Documento PDF
         **/

        $this->generarDocumentoPDF();

    }

    public function generarDocumentoPDF() {
        include_once "generarDocumentoPdf.php";
    }

    public function consultarPorcentajeOrden() {

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        if($_REQUEST['proyecto'] == "Todos los Proyectos"){
        	$cadenaSql = $this->miSql->getCadenaSql('porcentajeConsumoTodos', json_decode($_REQUEST['elementos'])); 
        	$this->elementos_reporte = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        }else{
        	$cadenaSql = $this->miSql->getCadenaSql('porcentajeConsumo', $_REQUEST['proyecto']);
        	$this->elementos_reporte = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        }
       
        
    }
}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);

?>

