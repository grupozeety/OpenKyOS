<?php
namespace reportes\informacionBeneficiarios\entidad;
class procesarAjax {
    public $miConfigurador;
    public $sql;
    public function __construct($sql) {
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

            case 'consultarProcesos':

                $cadenaSql = $this->sql->getCadenaSql('consultarProceso');
                $procesos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($procesos) {
                    foreach ($procesos as $key => $valor) {

                        $archivo = (is_null($valor['nombre_archivo'])) ? " " : "<center><a href='" . $valor['ruta_relativa_archivo'] . "' target='_blank' >" . $valor['nombre_archivo'] . "</a></center>";

                        $resultadoFinal[] = array(
                            'id_proceso' => "<center>" . $valor['id_proceso'] . "</center>",
                            'descripcion' => "<center>" . $valor['descripcion'] . "</center>",
                            'estado' => "<center>" . $valor['estado'] . "</center>",
                            'porcentaje_estado' => "<center>" . $valor['porcentaje_estado'] . "</center>",
                            'archivo' => "<center>" . $archivo . "</center>",
                        );
                    }

                    $total = count($resultadoFinal);

                    $resultado = json_encode($resultadoFinal);

                    $resultado = '{
                                "recordsTotal":'     . $total . ',
                                "recordsFiltered":'     . $total . ',
                                "data":'     . $resultado . '}';
                } else {

                    $resultado = '{
                                "recordsTotal":0 ,
                                "recordsFiltered":0 ,
                                "data": 0 }'    ;
                }
                echo $resultado;

                break;

        }
    }
}

$miProcesarAjax = new procesarAjax($this->sql);

?>
