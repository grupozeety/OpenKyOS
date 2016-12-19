<?php
namespace reportes\estadoBeneficiarios\entidad;
class procesarAjax {
    public $miConfigurador;
    public $sql;
    public function __construct($sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->ruta = $this->miConfigurador->getVariableConfiguracion("rutaBloque");

        $this->sql = $sql;

        $conexion = "interoperacion";

        //$conexion = "produccion";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";

        $esteBloque = $this->miConfigurador->configuracion['esteBloque'];

        switch ($_REQUEST['funcion']) {

            case 'consultaGeneral':
                //var_dump($_REQUEST);
                //exit;
                $cadenaSql = $this->sql->getCadenaSql('consultaGeneralBeneficiarios');

                $procesos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($procesos) {
                    foreach ($procesos as $key => $valor) {

                        // Variables para Con
                        $cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
                        $cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
                        $cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
                        $cadenaACodificar .= "&opcion=consultaParticular";
                        $cadenaACodificar .= "&proyecto=" . $valor['proyecto'];
                        $cadenaACodificar .= "&id_proyecto=" . $valor['id_proyecto'];

                        // Codificar las variables
                        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
                        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

                        // URL Consultar Proyectos
                        $urlConsultarParticularEnlace = $url . $cadena;
                        $link = "<a  target='_blank' href='" . $urlConsultarParticularEnlace . "'>" . $valor['proyecto'] . "</a>";

                        $resultadoFinal[] = array(
                            'proyecto' => $link,
                            'beneficiarios' => "#" . $valor['beneficiarios'],
                            'preventas' => $valor['preventas'],
                            'ventas' => $valor['ventas'],
                            'accPortatil' => $valor['asignacion_portatiles'],
                            'accServicio' => $valor['asignacion_servicios'],
                            'activacion' => $valor['activacion'],
                            'revision' => $valor['revision'],
                            'aprobacion' => $valor['aprobacion'],

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

                $cadenaSql = $this->sql->getCadenaSql('consultaParticularBeneficiarios', $_REQUEST['id_proyecto']);
                $procesos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($procesos) {
                    foreach ($procesos as $key => $valor) {

                        $resultadoFinal[] = array(
                            'beneficiario' => $valor['beneficiario'],
                            'contrato' => $valor['contratos'],
                            'accPortatil' => $valor['portatiles_asignados'],
                            'accServicio' => $valor['servicios_asignados'],
                            'activacion' => $valor['nactivacion'],
                            'revision' => $valor['nrevision'],
                            'aprobacion' => $valor['naprobacion'],
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


