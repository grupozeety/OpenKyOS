<?php

namespace gestionBeneficiarios\gestionEstadoBeneficiarios\entidad;

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

            case 'consultarProcesos':

                $cadenaSql = $this->sql->getCadenaSql('consultarBeneficiarios');
                $procesos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($procesos) {
                    foreach ($procesos as $key => $valor) {
                        $resultadoFinal[] = array(
                            'id_beneficiario' => "<center>" . $valor['id_beneficiario'] . "</center>",
                            'identificacion' => "<center>" . $valor['identificacion'] . "</center>",
                            'nombre' => "<center>" . $valor['nombre_beneficiario'] . "</center>",
                            'estado_interventoria' => "<center>" . $valor['estado_interventoria'] . "</center>",
                            'estado_sistema' => "<center>" . $valor['estado_beneficiario'] . "</center>",
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
