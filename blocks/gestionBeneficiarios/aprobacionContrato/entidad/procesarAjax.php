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
                        // Variables
                        $cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
                        $cadenaACodificar .= "&opcion=aprobarContrato";
                        $cadenaACodificar .= "&id_contrato=" . $valor["identificador_contrato"];
                        $cadenaACodificar .= "&numero_contrato=" . $valor["numero_contrato"];
                        $cadenaACodificar .= "&nombre_beneficiario=" . $valor["nombre_beneficiario"];
                        $cadenaACodificar .= "&identificacion_beneficiario=" . $valor["identificacion"];

                        // Codificar las variables
                        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
                        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

                        // URL Aprobar Contratp
                        $urlAprobarContrato = $url . $cadena;

                        $estado_contrato = ($valor['estado_contrato'] == 'Borrador') ? "<center><b><a href='" . $urlAprobarContrato . "'  >Por Aprobar Contrato</a></b></center>" : "<center><b>" . $valor['estado_contrato'] . "</b></center>";

                        $resultadoFinal[] = array(
                            'numeroContrato' => "<center>" . $valor['numero_contrato'] . "</center>",
                            'urbanizacion' => "<center>" . $valor['urbanizacion'] . "</center>",
                            'identificacionBeneficiario' => "<center>" . $valor['identificacion'] . "</center>",
                            'nombreBeneficiario' => "<center>" . $valor['nombre_beneficiario'] . "</center>",
                            'opcion' => $estado_contrato,
                        );

                    }

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
