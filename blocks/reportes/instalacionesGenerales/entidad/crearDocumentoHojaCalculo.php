<?php
namespace reportes\instalacionesGenerales\entidad;

class GenerarReporteExcelInstalaciones {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;

    public function __construct($sql, $proyectos) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        $this->proyectos = $proyectos;
        var_dump($this->proyectos);
        var_dump($this->proyectos[0]['paquetesTrabajo']);

        var_dump($this->proyectos[0]['paquetesTrabajo'][1]['actividades']);
        exit;

    }

}

$miProcesador = new GenerarReporteExcelInstalaciones($this->miSql, $this->proyectos);

?>

