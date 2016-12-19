<?php
namespace reportes\estadoBeneficiarios\entidad;
class procesarAjax {
    public $miConfigurador;
    public $sql;
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");

        $this->sql = $sql;

        switch ($_REQUEST['funcion']) {

            case 'consultaGeneral':
                var_dump($_REQUEST);
                exit;

                $cadenaSql = $this->sql->getCadenaSql('consultaGeneralBeneficiarios');
                $procesos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($procesos) {
                    foreach ($procesos as $key => $valor) {

                        /*
                    // Variables para Con
                    $cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
                    $cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
                    $cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
                    $cadenaACodificar .= "&opcion=consultaParticular";
                    $cadenaACodificar .= "&proyecto=EL RECUERDO";
                    $cadenaACodificar .= "&id_proyecto=11";

                    // Codificar las variables
                    $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
                    $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

                    // URL Consultar Proyectos
                    $urlConsultarParticularEnlace = $url . $cadena;
                    echo $urlConsultarParticularEnlace;
                     */

                        $archivo = (is_null($valor['ruta_archivo'])) ? " " : "<center><a href='" . $valor['ruta_archivo'] . "' target='_blank' >" . $valor['nombre_ruta_archivo'] . "</a></center>";

                        $resultadoFinal[] = array(
                            'proceso' => $valor['id_proceso'],
                            'estado' => $valor['estado'],
                            'archivo' => $archivo,
                            'num_inicial' => $valor['parametro_inicio'],
                            'num_final' => $valor['parametro_fin'],
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

            case 'consultaParticular':
                var_dump($_REQUEST);
                exit;

                $cadenaSql = $this->sql->getCadenaSql('consultaParticularBeneficiarios');
                $procesos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($procesos) {
                    foreach ($procesos as $key => $valor) {

                        $archivo = (is_null($valor['ruta_archivo'])) ? " " : "<center><a href='" . $valor['ruta_archivo'] . "' target='_blank' >" . $valor['nombre_ruta_archivo'] . "</a></center>";

                        $resultadoFinal[] = array(
                            'proceso' => $valor['id_proceso'],
                            'estado' => $valor['estado'],
                            'archivo' => $archivo,
                            'num_inicial' => $valor['parametro_inicio'],
                            'num_final' => $valor['parametro_fin'],
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
exit;
?>


