<?php
namespace cambioClave\entidad;

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
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        switch ($_REQUEST['funcion']) {

            case 'consultarFacturas':

                $cadenaSql = $this->sql->getCadenaSql('consultaInformacionFacturacion', $_REQUEST['id_beneficiario']);
                $informacionFactura = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($informacionFactura) {
                    foreach ($informacionFactura as $key => $valor) {

                        $resultadoFinal[] = array(
                            'fecha_factura' => "<center>" . $valor['fecha_factura'] . "</center>",
                            'periodo_facturado' => "<center>" . $valor['id_ciclo'] . "</center>",
                            'valor_factura' => "<center><b>$ " . number_format($valor['total_factura'], 2) . "</b></center>",
                        );
                    }

                    $total = count($resultadoFinal);

                    $resultado = json_encode($resultadoFinal);

                    $resultado = '{
                                "recordsTotal":' . $total . ',
                                "recordsFiltered":' . $total . ',
                                "data":' . $resultado . '}';
                } else {

                    $resultado = '{
                                "recordsTotal":0 ,
                                "recordsFiltered":0 ,
                                "data": 0 }';
                }
                echo $resultado;

                break;

        }
    }
}

$miProcesarAjax = new procesarAjax($this->sql);
