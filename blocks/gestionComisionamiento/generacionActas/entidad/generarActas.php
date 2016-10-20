<?php
namespace gestionComisionamiento\generacionActas\entidad;

class GenerarActas {

    public $miConfigurador;
    public $miSql;
    public $conexion;
    public $agendamientos;

    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        /**
         * 1. Consultar Informacion Agenadamientos
         **/
        $this->consultarAgendamientos();

        /**
         * 2. Estruturar Documento Actas
         **/
        $this->estructurarDocumento();

        exit;
    }

    public function estructurarDocumento() {

        include_once "estructurarDocumento.php";

    }
    public function consultarAgendamientos() {

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        for ($i = 1; $i < 1000; $i++) {

            if (isset($_REQUEST['item' . $i])) {

                $arreglo[] = $_REQUEST['item' . $i];

            }

        }
        if (isset($arreglo)) {

            $agendamiento = array();
            foreach ($arreglo as $key => $value) {

                $valor = json_decode(base64_decode($value), true);

                $cadenaSql = $this->miSql->getCadenaSql('consultarAgendamientosParticulares', $valor);
                $variable = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                $agendamiento = array_merge($agendamiento, $variable);

            }
            $this->agendamientos = $agendamiento;

        } else {

            $cadenaSql = $this->miSql->getCadenaSql('consultarAgendamientosGeneral');
            $this->agendamientos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        }

    }

}

$miProcesador = new GenerarActas($this->sql);

?>

