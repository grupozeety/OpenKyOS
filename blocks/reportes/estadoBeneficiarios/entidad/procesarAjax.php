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

                switch ($_REQUEST['tipo']) {

                case 'porcentaje':
                        $cadenaSql = $this->sql->getCadenaSql('consultaGeneralBeneficiariosPorcentaje', $_REQUEST['metas']);
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
                                    'beneficiarios_meta' => "&nbsp;" . $valor['beneficiarios_meta'],
                                    'beneficiarios_sistema' => "&nbsp;" . $valor['beneficiarios_sistema'],
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
                                "recordsTotal":'         . $total . ',
                                "recordsFiltered":'         . $total . ',
                                "data":'         . $resultado . '}';
                        } else {

                            $resultado = '{
                                "recordsTotal":0 ,
                                "recordsFiltered":0 ,
                                "data": 0 }'        ;
                        }
                        break;

                case 'numerico':
                        $cadenaSql = $this->sql->getCadenaSql('consultaGeneralBeneficiariosNumerico', $_REQUEST['metas']);

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
                                    'beneficiarios_meta' => "&nbsp;" . $valor['beneficiarios_meta'],
                                    'beneficiarios_sistema' => "&nbsp;" . $valor['beneficiarios_sistema'],
                                    'contratos' => "&nbsp;" . $valor['contratos'],
                                    'accPortatil' => "&nbsp;" . $valor['asignacion_portatiles'],
                                    'accServicio' => "&nbsp;" . $valor['asignacion_servicios'],
                                    'activacion' => "&nbsp;" . $valor['activacion'],
                                    'revision' => "&nbsp;" . $valor['revision'],
                                    'aprobacion' => "&nbsp;" . $valor['aprobacion'],

                                );
                            }

                            $total = count($resultadoFinal);

                            $resultado = json_encode($resultadoFinal);

                            $resultado = '{
                                "recordsTotal":'         . $total . ',
                                "recordsFiltered":'         . $total . ',
                                "data":'         . $resultado . '}';
                        } else {

                            $resultado = '{
                                "recordsTotal":0 ,
                                "recordsFiltered":0 ,
                                "data": 0 }'        ;
                        }
                        break;
                }

                //                var_dump($_REQUEST);
                //              var_dump($procesos);exit;

                echo $resultado;

                break;

            case 'consultaParticular':

                $cadenaSql = $this->sql->getCadenaSql('consultaParticularBeneficiarios', $_REQUEST['id_proyecto']);

                $procesos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($procesos) {
                    foreach ($procesos as $key => $valor) {

                        // Variables para Con
                        $cadenaACodificar = "pagina=generacionContrato";
                        //$cadenaACodificar .= "&bloqueNombre=" . $esteBloque["nombre"];
                        //$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque["grupo"];
                        $cadenaACodificar .= "&opcion=validarRequisitos";
                        $cadenaACodificar .= "&proceso=verificarRequisitos";
                        $cadenaACodificar .= "&id_beneficiario=" . $valor['id_beneficiario'];

                        // Codificar las variables
                        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
                        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

                        // URL Consultar Proyectos
                        $urlConsultarRequisitos = $url . $cadena;
                        $link = "<a  target='_blank' href='" . $urlConsultarRequisitos . "'>" . $valor['beneficiario'] . "</a>";

                        $resultadoFinal[] = array(
                            'beneficiario' => $link,
                            'contrato' => $valor['contrato'],
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

        }
    }
}

$miProcesarAjax = new procesarAjax($this->sql);
exit;
?>


