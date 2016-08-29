<?php
namespace reportes\materialNoConsumido\entidad;

class FormProcessor {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $elementos_projecto;
    public $elementos_consumidos;
    public $elementos_reporte;

    public function __construct($lenguaje, $sql) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;

        $_REQUEST['tiempo'] = time();

        /**
         *  1. Crear arreglo de elementos del Proyectos
         **/

        $this->procesarVariables();

        /**
         *  2. Consultar Elementos Consumidos
         **/

        $this->consultarElementosConsumidos();

        /**
         *  3. Validar Elementos Proyecto y Elementos Consumo
         **/

        $this->validarElementos();

        /**
         *  3. Generar Documento PDF
         **/

        $this->generarDocumentoPDF();

    }

    public function generarDocumentoPDF() {
        include_once "generarDocumentoPdf.php";
    }

    public function validarElementos() {

        if ($this->elementos_consumidos) {

            foreach ($this->elementos_projecto as $key => $value) {

                $elemento = $value;

                foreach ($this->elementos_consumidos as $key => $value) {

                    if ($elemento['name'] == $value['nombre']) {

                        $elemento['qty'] = $elemento['qty'] - $value['consumo'];

                    }

                }

                if ($elemento['qty'] > 0) {

                    $elementos_reporte[] = $elemento;

                }

            }

        } else {
            $elementos_reporte = $this->elementos_projecto;
        }

        $this->elementos_reporte = $elementos_reporte;

    }
    public function consultarElementosConsumidos() {

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('elementosConsumidos');
        $this->elementos_consumidos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    }

    public function procesarVariables() {

        $_REQUEST['elementos'] = explode("@", $_REQUEST['elementos']);
        $_REQUEST['ordenes'] = explode("@", $_REQUEST['ordenes']);

        foreach ($_REQUEST['elementos'] as $key => $value) {

            $array = json_decode(base64_decode($value), true);

            foreach ($array as $key => $value) {
                $elementos[] = $value;
            }

        }
        unset($array);
        foreach ($_REQUEST['ordenes'] as $key => $value) {
            $array = json_decode(base64_decode($value), true);

            foreach ($array as $key => $value) {
                $ordenes[] = $value;
            }
        }
        unset($array);

        foreach ($elementos as $key => $value) {

            $elemento = $value;
            unset($elemento['material']);

            foreach ($ordenes as $key => $value) {
                unset($value['project']);
                if ($value['name'] == $elemento['parent']) {

                    $elemento['numero_orden'] = $value['orden_trabajo'];
                    $elemento['descripcion_orden'] = $value['descripcion_orden'];
                    $elementos_projecto[] = $elemento;

                }
            }

        }
        $this->elementos_projecto = $elementos_projecto;

    }

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);

?>

