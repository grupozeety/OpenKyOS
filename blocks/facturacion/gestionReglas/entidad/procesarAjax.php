<?php
namespace facturacion\gestionReglas\entidad;
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

        case 'consultaParticular':

            $cadenaSql = $this->sql->getCadenaSql('consultaParticular');

            $reglas = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            if ($reglas) {
                foreach ($reglas as $key => $valor) {


                    /*

                    { data :"numero_regla" },
                    { data :"descripcion" },
                    { data :"formula" },
                    { data :"identificador_formula" },
                    { data :"actualizar" },
                    { data :"eliminar" }


                    0 => string '1' (length=1)
                    'id_regla' => string '1' (length=1)
                    1 => string 'Calculo Intereses' (length=17)
                    'decripcion' => string 'Calculo Intereses' (length=17)
                    2 => string '15*vm' (length=5)
                    'formula' => string '15*vm' (length=5)
                    3 => string 't' (length=1)
                    'estado_registro' => string 't' (length=1)
                    4 => string '2017-01-31 01:25:06.544182' (length=26)
                    'fecha_registro' => string '2017-01-31 01:25:06.544182' (length=26)
                    5 => string 'Int' (length=3)
                    'identificador' => string 'Int' (length=3)
                    */


                    $resultadoFinal[] = array(
                    'numero_regla' => $valor['id_regla'],
                    'descripcion' => $valor['decripcion'],
                    'formula' => $valor['formula'],
                    'identificador_formula' => $valor['identificador'],
                    'actualizar' => " ",
                    'eliminar' => " ",

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
        } else if ($valor >= 21 && $valor <= 50) {
            $color = "#f3aa51";
        } else if ($valor >= 51 && $valor <= 80) {
            $color = "#f0ed80";
        } else if ($valor >= 81 && $valor <= 99) {
            $color = "#b0e6c8";
        } else if ($valor >= 100) {
            $color = "#0d7b3e";
        }

        return $color;
    }
}
$miProcesarAjax = new procesarAjax($this->sql);
exit;
?>
