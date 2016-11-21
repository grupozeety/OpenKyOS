<?php
namespace reportes\parametrizacionInforme\entidad;
include_once 'Redireccionador.php';

class ProcesarParametrizacion {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $informacion = array();
    public $proyecto;
    public $registroCampo;
    public $error;
    public function __construct($sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;

        /**
         * 1. Estruturar Información
         **/

        $this->estruturarInformacion();

        /**
         * 2. Registrar Información
         **/

        $this->registrarInformacion();

        /**
         * 3. Redireccionar
         **/

        if ($this->error != true) {
            Redireccionador::redireccionar('registro');
        } else {
            Redireccionador::redireccionar('noRegistro');
        }

    }

    public function registrarInformacion() {

        $conexion = "estructura";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('eliminarInformacionProyecto', $this->proyecto);
        $registroCampo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

        foreach ($this->informacion as $key => $value) {
            $cadenaSql = $this->miSql->getCadenaSql('registrarParametrizacion', $value);
            $registroCampo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

            if ($registroCampo === false) {
                $this->error = true;

            }

        }

    }
    public function estruturarInformacion() {

        $this->proyecto = $_REQUEST['id_proyecto'];

        unset($_REQUEST['id_proyecto']);

        foreach ($_REQUEST as $key => $value) {
            $busqueda_1 = strpos($key, "id_");
            if (!($busqueda_1 === false) && $_REQUEST[$key] != '') {
                $campo = $key;
                $valor_actividad = $_REQUEST[$key];

                foreach ($_REQUEST as $llave => $valor) {

                    $busqueda_2 = strpos($llave, "id_");

                    $busqueda_3 = strpos($valor, $valor_actividad);
                    if ($busqueda_2 === false && !($busqueda_3 === false)) {
                        $valor_campo = $valor;

                        $id_campo_hijos = str_replace("id_", "", $key);

                        $id_campo_hijos .= "-informacion";

                        if (isset($_REQUEST[$id_campo_hijos])) {

                            $campos_hijos = $_REQUEST[$id_campo_hijos];

                        } else {

                            $campos_hijos = "1";

                        }

                    }

                }

                $arreglo = array(
                    'campo' => $campo,
                    'valor_campo' => $valor_campo,
                    'valor_actividad' => $valor_actividad,
                    'id_proyecto' => $this->proyecto,
                    'tipo_proyecto' => $_REQUEST['tipo_proyecto'],
                    'info_hijos' => $campos_hijos,

                );
                $this->informacion[] = $arreglo;

            }

        }

    }

}

$miProcesador = new ProcesarParametrizacion($this->sql);

?>

