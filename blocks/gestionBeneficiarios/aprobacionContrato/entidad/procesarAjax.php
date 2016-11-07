<?php
namespace gestionBeneficiarios\aprobacionContrato\entidad;
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

            case 'consultarContratos':

                $cadenaSql = $this->sql->getCadenaSql('consultarContratos');
                //echo $cadenaSql;exit;
                $resultadoContratos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                // URL base
                $url = $this->miConfigurador->getVariableConfiguracion("host");
                $url .= $this->miConfigurador->getVariableConfiguracion("site");
                $url .= "/index.php?";

                if ($resultadoContratos) {

                    foreach ($resultadoContratos as $key => $valor) {

                        $arreglo = array(
                            'perfil_beneficiario' => $valor['tipo_beneficiario'],
                            'id_beneficiario' => $valor['identificador_beneficiario'],

                        );
                        $cadenaSql = $this->sql->getCadenaSql('consultarValidacionRequisitos', $arreglo);
                        $requisitos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                        if ($requisitos) {
                            foreach ($requisitos as $key => $value) {

                                if ((is_null($value['nombre_documento']) || $value['comisionador'] == 'f' || $value['supervisor'] == 'f' || $value['analista'] == 'f')) {
                                    $noAprobar = true;
                                }

                            }
                        }

                        // Variables
                        $cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
                        $cadenaACodificar .= "&opcion=aprobarContrato";
                        $cadenaACodificar .= "&id_contrato=" . $valor["identificador_contrato"];
                        $cadenaACodificar .= "&numero_contrato=" . $valor["numero_contrato"];
                        $cadenaACodificar .= "&nombre_beneficiario=" . $valor["nombre_beneficiario"];
                        $cadenaACodificar .= "&identificacion_beneficiario=" . $valor["identificacion"];
                        $cadenaACodificar .= "&id_beneficiario=" . $valor["identificador_beneficiario"];

                        // Codificar las variables
                        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
                        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

                        // URL Aprobar Contratp
                        $urlAprobarContrato = $url . $cadena;
                        $archivoContrato = (is_null($valor['nombre_documento_contrato'])) ? " " : "<center><a href='" . $valor['ruta_documento_contrato'] . "' target='_blank' >" . $valor['nombre_documento_contrato'] . "</a></center>";

                        if (isset($noAprobar) && $noAprobar == true) {

                            $estado_contrato = "<center>Existen Documentos por Verificar</center>";
                        } else {

                            $estado_contrato = "<center><b>" . $valor['estado_contrato'] . "</b></center>";

                        }

                        $resultadoFinal[] = array(
                            'numeroContrato' => "<center>" . $valor['numero_contrato'] . "</center>",
                            'urbanizacion' => "<center>" . $valor['proyecto'] . "</center>",
                            'identificacionBeneficiario' => "<center>" . $valor['identificacion'] . "</center>",
                            'nombreBeneficiario' => "<center>" . $valor['nombre_beneficiario'] . "</center>",
                            'archivoContrato' => $archivoContrato,
                            'opcion' => $estado_contrato,
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
