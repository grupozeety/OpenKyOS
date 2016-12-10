<?php
namespace gestionBeneficiarios\generarContratosMasivos\entidad;
class procesarAjax {
    public $miConfigurador;
    public $sql;
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");

        $this->sql = $sql;
        //Conexion a Base de Datos
        $conexion = "interoperacion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        switch ($_REQUEST['funcion']) {

            case 'consultarProcesos':

                $cadenaSql = $this->sql->getCadenaSql('consultarProceso');
                $procesos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($procesos) {
                    foreach ($procesos as $key => $valor) {

                        $archivo = (is_null($valor['ruta_archivo'])) ? " " : "<center><a href='" . $valor['ruta_archivo'] . "' target='_blank' >" . $valor['nombre_ruta_archivo'] . "</a></center>";

                        $resultadoFinal[] = array(
                            'proceso' => "<center>" . $valor['id_proceso'] . "</center>",
                            'estado' => "<center>" . $valor['estado'] . "</center>",
                            'archivo' => "<center>" . $archivo . "</center>",
                            'num_inicial' => "<center>" . $valor['parametro_inicio'] . "</center>",
                            'num_final' => "<center>" . $valor['parametro_fin'] . "</center>",
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

            case 'ejecutarProcesos':
                include_once "ejecutarProcesos.php";
                break;

        }
    }
}

$miProcesarAjax = new procesarAjax($this->sql);
exit;
?>
