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

                $resultadoContratos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                // URL base
                $url = $this->miConfigurador->getVariableConfiguracion("host");
                $url .= $this->miConfigurador->getVariableConfiguracion("site");
                $url .= "/index.php?";

                if ($resultadoContratos) {

                    foreach ($resultadoContratos as $key => $valor) {

                        {

                            $arreglo = array(
                                'perfil_beneficiario' => $valor['tipo_beneficiario'],
                                'id_beneficiario' => $valor['id_beneficiario'],

                            );

                            $cadenaSql = $this->sql->getCadenaSql('consultarValidacionRequisitos', $arreglo);
                            $requisitos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                            $cadenaSql = $this->sql->getCadenaSql('consultaInformacionBeneficiario', $valor['id_beneficiario']);
                            $beneficiario = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                            $cadenaSql = $this->sql->getCadenaSql('consultaContratoInfo', $valor['id_beneficiario']);
                            $contrato = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                            if ($requisitos) {
                                foreach ($requisitos as $value) {

                                    $value['comisionador'] = ($value['comisionador'] == 't') ? 1 : (($value['analista'] == 'f') ? 0 : NULL);
                                    $value['supervisor'] = ($value['supervisor'] == 't') ? 1 : (($value['analista'] == 'f') ? 0 : NULL);
                                    $value['analista'] = ($value['analista'] == 't') ? 1 : (($value['analista'] == 'f') ? 0 : NULL);

                                    $resultado = $value['comisionador'] * $value['supervisor'] * $value['analista'];

                                    if ($beneficiario['minvi'] == 't' && $resultado && !is_null($value['nombre_documento'])) {

                                        switch ($value['tipologia_documento']) {
                                        case '99':
                                                $cambiarEstadoCB = true;

                                                $contrato['comisionador'] = ($contrato['comisionador'] == 't') ? 1 : (($contrato['analista'] == 'f') ? 0 : NULL);

                                                $contrato['supervisor'] = ($contrato['supervisor'] == 't') ? 1 : (($contrato['analista'] == 'f') ? 0 : NULL);

                                                $contrato['analista'] = ($contrato['analista'] == 't') ? 1 : (($contrato['analista'] == 'f') ? 0 : NULL);

                                                $resultadoContrato = $contrato['comisionador'] * $contrato['supervisor'] * $contrato['analista'];
                                                if ($resultadoContrato) {

                                                    $cambiarEstadoCN = true;
                                                }
                                                break;

                                        case '124':
                                                $cambiarEstadoCNI = true;

                                                $contrato['comisionador'] = ($contrato['comisionador'] == 't') ? 1 : (($contrato['analista'] == 'f') ? 0 : NULL);

                                                $contrato['supervisor'] = ($contrato['supervisor'] == 't') ? 1 : (($contrato['analista'] == 'f') ? 0 : NULL);

                                                $contrato['analista'] = ($contrato['analista'] == 't') ? 1 : (($contrato['analista'] == 'f') ? 0 : NULL);

                                                $resultadoContrato = $contrato['comisionador'] * $contrato['supervisor'] * $contrato['analista'];
                                                if ($resultadoContrato) {

                                                    $cambiarEstadoCN = true;
                                                }
                                                break;

                                        }

                                    }

                                    if ($beneficiario['minvi'] == 'f' && $resultado && !is_null($value['nombre_documento'])) {
                                        $cambiarEstado = true;

                                    } else {
                                        $EstadoFaltante = true;
                                    }

                                }

                                if (isset($cambiarEstadoCB) && isset($cambiarEstadoCNI) && isset($cambiarEstadoCN) && $beneficiario['minvi'] == 't') {

                                    $estado_contrato = "<center><b>" . $valor['estado_contrato'] . "</b></center>";

                                } elseif (!isset($EstadoFaltante) && $beneficiario['minvi'] == 'f') {

                                    $estado_contrato = "<center><b>" . $valor['estado_contrato'] . "</b></center>";

                                } else {

                                    $estado_contrato = "<center>Existen Documentos por Verificar</center>";

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
