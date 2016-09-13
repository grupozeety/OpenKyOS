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

                if ($resultadoContratos) {

                    foreach ($resultadoContratos as $key => $valor) {

                        $resultadoFinal[] = array(
                            'numeroContrato' => "<center>" . $valor['numero_contrato'] . "</center>",
                            'identificacionBeneficiario' => "<center>" . $valor['identificacion'] . "</center>",
                            'nombreBeneficiario' => "<center>" . $valor['nombre_beneficiario'] . "</center>",
                            'opcion' => "<center>Aprobar Contrato</center>",
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
