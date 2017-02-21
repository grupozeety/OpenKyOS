<?php
namespace reportes\estadoBeneficiarios\entidad;

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
                                    'municipio' => $valor['municipio'],
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

                                $pc_contratos = ($valor['contratos'] / $valor['beneficiarios_meta']) * 100;
                                $color_contrato = $this->colorCelda($pc_contratos);

                                $pc_accPortatil = ($valor['asignacion_portatiles'] / $valor['beneficiarios_meta']) * 100;
                                $color_pc_accPortatil = $this->colorCelda($pc_accPortatil);

                                $pc_accServicio = ($valor['asignacion_servicios'] / $valor['beneficiarios_meta']) * 100;
                                $color_pc_accServicio = $this->colorCelda($pc_accServicio);

                                $pc_activacion = ($valor['activacion'] / $valor['beneficiarios_meta']) * 100;
                                $color_pc_activacion = $this->colorCelda($pc_activacion);

                                $pc_revision = ($valor['revision'] / $valor['beneficiarios_meta']) * 100;
                                $color_pc_revision = $this->colorCelda($pc_revision);

                                $pc_aprobacion = ($valor['aprobacion'] / $valor['beneficiarios_meta']) * 100;
                                $color_pc_aprobacion = $this->colorCelda($pc_aprobacion);

                                $resultadoFinal[] = array(
                                    'municipio' => $valor['municipio'],
                                    'proyecto' => $link,
                                    'beneficiarios_meta' => "&nbsp;" . $valor['beneficiarios_meta'],
                                    'beneficiarios_sistema' => "&nbsp;" . $valor['beneficiarios_sistema'],
                                    'contratos' => "<div style='background-color:" . $color_contrato . "'>" . $valor['contratos'] . "</div>",
                                    'accPortatil' => "<div style='background-color:" . $color_pc_accPortatil . "'>" . $valor['asignacion_portatiles'] . "</div>",
                                    'accServicio' => "<div style='background-color:" . $color_pc_accServicio . "'>" . $valor['asignacion_servicios'] . "</div>",
                                    'activacion' => "<div style='background-color:" . $color_pc_activacion . "'>" . $valor['activacion'] . "</div>",
                                    'revision' => "<div style='background-color:" . $color_pc_revision . "'>" . $valor['revision'] . "</div>",
                                    'aprobacion' => "<div style='background-color:" . $color_pc_aprobacion . "'>" . $valor['aprobacion'] . "</div>",

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

                        $cadenaSql = $this->sql->getCadenaSql('consultarDocumentos', $valor['id_beneficiario']);
                        $documento = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

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
                        $link1 = ($documento) ? "<a  target='_blank' href='" . $urlConsultarRequisitos . "'>Ver</a>" : "&nbsp;";

                        // Variables para Con
                        $cadenaACodificar = "pagina=gestionRequisitos";
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
                        $link2 = ($documento) ? "<a  target='_blank' href='" . $urlConsultarRequisitos . "'>Ver</a>" : "&nbsp;";

                        $resultadoFinal[] = array(
                            'beneficiario' => $valor['beneficiario'],
                            'contratacion' => $link1,
                            'comisionamiento' => $link2,
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

    public function colorCelda($valor)
    {

        if ($valor >= 0 && $valor <= 20) {
            $color = "#F08080";
        } else if ($valor > 20 && $valor <= 50) {
            $color = "#f3aa51";
        } else if ($valor > 50 && $valor <= 80) {
            $color = "#f0ed80";
        } else if ($valor > 80 && $valor <= 99) {
            $color = "#b0e6c8";
        } else if ($valor > 99) {
            $color = "#0d7b3e";
        }

        return $color;
    }
}
$miProcesarAjax = new procesarAjax($this->sql);
exit;
