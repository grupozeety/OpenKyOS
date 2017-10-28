<?php
namespace gestionCapacitaciones\apropiacion\entidad;

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

        $conexion = "otunWs";

        $this->esteRecursoDBOtunWS = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";

        $esteBloque = $this->miConfigurador->configuracion['esteBloque'];

        switch ($_REQUEST['funcion']) {

            case 'consultarInformacionApropiacion':

                $cadenaSql = $this->sql->getCadenaSql('consultarInformacionApropiacion', $_REQUEST['idActividad']);

                $informacion = $this->esteRecursoDBOtunWS->ejecutarAcceso($cadenaSql, "busqueda")[0];

                echo json_encode($informacion);

                break;

            case 'consultarActividad':

                $cadenaSql = $this->sql->getCadenaSql('consultarActividad');

                $resultadoItems = $this->esteRecursoDBOtunWS->ejecutarAcceso($cadenaSql, "busqueda");

                if ($resultadoItems) {
                    foreach ($resultadoItems as $key => $values) {
                        $keys = array(
                            'value',
                            'data',
                        );
                        $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));

                    }

                } else {

                    $resultado[0] = array(
                        'value' => 'Nueva Actividad a Registrar',
                        'data' => null,
                    );

                }

                echo '{"suggestions":' . json_encode($resultado) . '}';

                break;

            case 'consultaInformacionBeneficiarios':

                $cadenaSql = $this->sql->getCadenaSql('consultarInformacionBeneficiario', $_REQUEST['beneficiario']);

                $informacion = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda")[0];

                echo json_encode($informacion);

                break;

            case 'consultaBeneficiarios':

                $cadenaSql = $this->sql->getCadenaSql('consultarBeneficiariosPotenciales');

                $resultadoItems = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                foreach ($resultadoItems as $key => $values) {
                    $keys = array(
                        'value',
                        'data',
                    );
                    $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
                }
                echo '{"suggestions":' . json_encode($resultado) . '}';

                break;

            case 'consultaParticular':
                $cadenaSql = $this->sql->getCadenaSql('consultaParticular');
                $periodos = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($periodos) {
                    foreach ($periodos as $key => $valor) {
                        {
                            $valorCodificado = "pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                            $valorCodificado .= "&opcion=actualizarPeriodo";
                            $valorCodificado .= "&id_periodo=" . $valor['id_periodo'];
                        }

                        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
                        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valorCodificado, $enlace);

                        $urlActualizarPeriodo = $url . $cadena;

                        {
                            $valorCodificado = "pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                            $valorCodificado .= "&action=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                            $valorCodificado .= "&bloque=" . $esteBloque['nombre'];
                            $valorCodificado .= "&bloqueGrupo=" . $esteBloque["grupo"];
                            $valorCodificado .= "&opcion=eliminarPeriodo";
                            $valorCodificado .= "&id_periodo=" . $valor['id_periodo'];

                        }

                        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
                        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valorCodificado, $enlace);

                        $urlEliminarPeriodo = $url . $cadena;

                        $resultadoFinal[] = array(
                            'ident' => $valor['id_periodo'],
                            'unidad' => $valor['descripcion'],
                            'valor' => $valor['valor'],
                            'actualizar' => "<b><a href='" . $urlActualizarPeriodo . "'><IMG  src='theme/basico/img/update.ico'  width='25' height='25' ></a></b>",
                            'eliminar' => "<b><a href='" . $urlEliminarPeriodo . "'><IMG  src='theme/basico/img/delete.ico'  width='25' height='25' ></a></b>",

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

    public function colorCelda($valor)
    {

        if ($valor >= 0 && $valor <= 20) {
            $color = "#F08080";
        } elseif ($valor >= 21 && $valor <= 50) {
            $color = "#f3aa51";
        } elseif ($valor >= 51 && $valor <= 80) {
            $color = "#f0ed80";
        } elseif ($valor >= 81 && $valor <= 99) {
            $color = "#b0e6c8";
        } elseif ($valor >= 100) {
            $color = "#0d7b3e";
        }

        return $color;
    }
}
$miProcesarAjax = new procesarAjax($this->sql);
exit();
