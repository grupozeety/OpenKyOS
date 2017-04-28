<?php
namespace reportes\adicionalActaPortatil\entidad;

class procesarAjax
{
    public $miConfigurador;
    public $sql;
    public function __construct($sql)
    {
        $this->miConfigurador = \Configurador::singleton();

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");

        $this->sql = $sql;

        $conexion = "interoperacion";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        switch ($_REQUEST['funcion']) {

            case 'consultaBeneficiarios':
                $cadenaSql = $this->sql->getCadenaSql('consultarBeneficiariosPotenciales');

                $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                foreach ($resultadoItems as $key => $values) {
                    $keys = array(
                        'value',
                        'data',
                    );
                    $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
                }
                echo '{"suggestions":' . json_encode($resultado) . '}';
                break;

            case 'consultaPortatiles':

                $cadenaSql = $this->sql->getCadenaSql('consultarEquipo', $_REQUEST['query']);

                $resultadoItems = $esteRecursoDBPR->ejecutarAcceso($cadenaSql, "busqueda");

                foreach ($resultadoItems as $key => $values) {
                    $keys = array(
                        'value',
                        'data',
                    );
                    $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
                }
                echo '{"suggestions":' . json_encode($resultado) . '}';
                break;

            case 'consultaInformacionPortatiles':
                $cadenaSql = $this->sql->getCadenaSql('consultarInformacionEquipo', $_REQUEST['valor']);

                $informacionEquipo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                foreach ($informacionEquipo as $key => $value) {

                    $equipo[$key] = trim($value);

                }

                echo json_encode($equipo);

                break;

        }
    }
}

$miProcesarAjax = new procesarAjax($this->sql);

exit;
