<?php

namespace gestionBeneficiarios\gestionFirmaDocumentos\entidad;

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
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        switch ($_REQUEST['funcion']) {

            case 'consultaBeneficiarios':

                $cadenaSql = $this->sql->getCadenaSql('consultarBeneficiariosPotenciales');

                $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                foreach ($resultadoItems as $key => $values) {
                    $keys = array(
                        'value',
                        'data',
                    );
                    $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
                }
                echo '{"suggestions":' . json_encode($resultado) . '}';

                break;

            case 'consultaFirma':

                if (iconv_strlen($_REQUEST['value']) != 5) {
                    echo "Error";
                    exit;
                }

                $cadenaSql = $this->sql->getCadenaSql('consultarFirma', $_REQUEST['value']);

                $firma = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($firma) {
                    $url_firma = $this->miConfigurador->configuracion['host'];

                    $url_firma .= $this->miConfigurador->configuracion['site'];

                    $url_firma .= $firma[0]['ruta_archivo'];

                    $url_firma .= $firma[0]['nombre_archivo'];

                    echo json_encode($url_firma);

                } else {
                    echo "Error";
                    exit;

                }

                break;

        }

    }
}
$miProcesarAjax = new procesarAjax($this->sql);
exit();
