<?php
namespace gestionEquipos\asignacionEquipos\entidad;

class ConsultarEquipos {

    public $miConfigurador;
    public $miSql;
    public $conexion;

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        /**
         * Infomación de Beneficiarios
         */
        $this->estructurarInformacionBeneficiarios();

        /**
         * Restornar Proyectos
         */

        $this->retornarBeneficiarios();

    }

    public function estructurarInformacionBeneficiarios() {
        $this->infoProyectos[] = array(
            "identificador" => '1026276984',
            "marca" => '1026276984 - Michael Stiv Verdugo Marquez',
            "serial" => '',
            "descripcion" => '',
        );

        $this->infoProyectos[] = array(
            "data" => '2131325551',
            "value" => '2131325551 - Emmanuel Taborda',
        );

        $this->infoProyectos[] = array(
            "data" => '32222222111',
            "value" => '32222222111 - Violeta Ana Luz Sosa León',
        );

        $this->infoProyectos[] = array(
            "data" => '441414141414',
            "value" => '441414141414 - Paulo Cesar Coronado',
        );

        $this->infoProyectos[] = array(
            "data" => '2222323232323',
            "value" => '2222323232323 - Diana Tinjaca',
        );
    }
    public function retornarBeneficiarios() {
        foreach ($this->infoProyectos as $key => $values) {
            $keys = array(
                'value',
                'data',
            );
            $resultado[$key] = array_intersect_key($this->infoProyectos[$key], array_flip($keys));
        }

        echo '{"suggestions":' . json_encode($resultado) . '}';
    }

}
$Consultar = new ConsultarEquipos($this->sql);

?>