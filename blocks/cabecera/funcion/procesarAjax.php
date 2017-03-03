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
            case 'consultarCabecera':

                $cadenaSql = $this->sql->getCadenaSql('consultarCabecera');

                $resultado = $this->esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                foreach ($resultado as $key => $value) {

                    {

                        $valorCodificado = "actionBloque=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                        $valorCodificado = "pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                        $valorCodificado .= "&opcion=actualizacion";
                        $valorCodificado .= "&id=" . $value['id_cabecera'];
                    }

                    $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
                    $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($valorCodificado, $enlace);

                    $urlActualizar = $url . $cadena;

                    $resultadoFinal[] = array(
                        'codigo_cabecera' => $value['codigo_cabecera'],
                        'descripcion' => $value['descripcion'],
                        'departamento' => $value['departamento'],
                        'municipio' => $value['municipio'],
                        'urbanizacion' => $value['urbanizacion'],
                        'actualizacion' => "<center><b><a href='" . $urlActualizar . "'><IMG  src='theme/basico/img/update.ico'  width='25' height='25' ></a></b></center>",

                    );
                }

                $total = count($resultadoFinal);

                $resultado = json_encode($resultadoFinal);

                $resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
                "data":' . $resultado . '}';

                echo $resultado;

                break;
        }
    }

}
$miProcesarAjax = new procesarAjax($this->sql);
exit();
