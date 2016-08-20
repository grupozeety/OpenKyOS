<?php
namespace gestionEquipos\asignacionEquipos\entidad;

include_once "core/builder/FormularioHtml.class.php";

class ConsultarEquipos {

    public $miConfigurador;
    public $miSql;
    public $conexion;
    public $infEquipos;
    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        /**
         * Infomación de Beneficiarios
         */
        $this->estructurarInformacionEquipos();

        /**
         * Restornar Proyectos
         */

        $this->retornarEquipos();

    }

    public function estructurarInformacionEquipos() {
        $this->infEquipos[] = array(
            "identificador" => '1026276984',
            "marca" => 'Lenovo',
            "serial" => 'ajw123tya',
            "descripcion" => 'descripcion',

        );

        $this->infEquipos[] = array(
            "identificador" => '1256346154',
            "marca" => 'Hewlett-Packard',
            "serial" => 'aas123tya',
            "descripcion" => 'descripcion',

        );

        $this->infEquipos[] = array(
            "identificador" => '6622626622',
            "marca" => 'Asus',
            "serial" => 'ajd123tya',
            "descripcion" => 'descripcion',

        );

        $this->infEquipos[] = array(
            "identificador" => '9898989898',
            "marca" => 'Toshiva',
            "serial" => 'sda123tya',
            "descripcion" => 'descripcion',

        );

        $this->infEquipos[] = array(
            "identificador" => '565656564732',
            "marca" => 'iMac',
            "serial" => 'uys123tya',
            "descripcion" => 'descripcion',

        );
    }
    public function retornarEquipos() {

        var_dump($this->infEquipos);exit;
        foreach ($this->infEquipos as $key => $values) {
            $keys = array(
                'value',
                'data',
            );
            $resultado[$key] = array_intersect_key($this->infEquipos[$key], array_flip($keys));
        }

        echo '{"suggestions":' . json_encode($resultado) . '}';
    }

}
$Consultar = new ConsultarEquipos($this->sql);

?>