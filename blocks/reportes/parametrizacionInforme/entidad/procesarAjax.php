<?php
namespace reportes\parametrizacionInforme\entidad;
class procesarAjax {
    public $miConfigurador;
    public $sql;
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");

        $this->sql = $sql;

        $conexion = "openproject";
        $esteRecursoDBOP = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexion = "estructura";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        switch ($_REQUEST['funcion']) {

            case 'consultarProyectos':

                switch ($_REQUEST['tipo_proyecto']) {
                case 'core':
                        $cadenaSql = $this->sql->getCadenaSql('consultarProyectosCore');
                        $resultadoItems = $esteRecursoDBOP->ejecutarAcceso($cadenaSql, "busqueda");
                        break;

                case 'cabecera':
                        $cadenaSql = $this->sql->getCadenaSql('consultarProyectosCabecera');
                        $resultadoItems = $esteRecursoDBOP->ejecutarAcceso($cadenaSql, "busqueda");
                        break;

                case 'hfc':
                        $cadenaSql = $this->sql->getCadenaSql('consultarProyectosHfc');
                        $resultadoItems = $esteRecursoDBOP->ejecutarAcceso($cadenaSql, "busqueda");
                        break;

                case 'wman':
                        $cadenaSql = $this->sql->getCadenaSql('consultarProyectosWman');
                        $resultadoItems = $esteRecursoDBOP->ejecutarAcceso($cadenaSql, "busqueda");
                        break;

                }

                foreach ($resultadoItems as $key => $values) {
                    $keys = array(
                        'value',
                        'data',
                    );
                    $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
                }
                echo '{"suggestions":' . json_encode($resultado) . '}';

                break;

            case 'consultarActividades':
                $cadenaSql = $this->sql->getCadenaSql('consultarActividades');
                $resultadoItems = $esteRecursoDBOP->ejecutarAcceso($cadenaSql, "busqueda");

                foreach ($resultadoItems as $key => $values) {
                    $keys = array(
                        'value',
                        'data',
                    );
                    $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
                }
                echo '{"suggestions":' . json_encode($resultado) . '}';

                break;

            case 'consultarCampos':
                $cadenaSql = $this->sql->getCadenaSql('consultarCamposGeneral');
                $campos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                $campos = base64_encode(json_encode($campos));

                echo $campos;

                break;
            case 'consultarParametrizacion':
                $cadenaSql = $this->sql->getCadenaSql('consultarParametrizacion');
                $proyectos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                foreach ($proyectos as $key => $value) {
                    $cadenaSql = $this->sql->getCadenaSql('consultarNombreProyecto', $value['id_proyecto']);
                    $resultado = $esteRecursoDBOP->ejecutarAcceso($cadenaSql, "busqueda");

                    $proyectos[$key]['nombre_proyecto'] = $resultado[0][0];
                }

                foreach ($proyectos as $key => $valor) {
                    $resultadoFinal[] = array(
                        'tipo_proyecto' => "<center>" . $valor['tipo_proyecto'] . "</center>",
                        'id_proyecto' => "<center>" . $valor['id_proyecto'] . "</center>",
                        'nombre_proyecto' => "<center>" . $valor['nombre_proyecto'] . "</center>",

                    );
                }

                $total = count($resultadoFinal);

                $resultado = json_encode($resultadoFinal);

                $resultado = '{
                "recordsTotal":'     . $total . ',
                "recordsFiltered":'     . $total . ',
                "data":'     . $resultado . '}';

                echo $resultado;

                break;

        }
    }
}

$miProcesarAjax = new procesarAjax($this->sql);

?>
