<?php
namespace gestionBeneficiarios\generarContratosMasivos\entidad;
if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador {
    public static function redireccionar($opcion, $valor = "") {

        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "ErrorFormatoArchivo":
                $variable = 'pagina=generarContratosMasivos';
                $variable .= '&mensajeModal=errorFormatoArchivo';
                break;

            case "ErrorArchivoNoValido":
                $variable = 'pagina=generarContratosMasivos';
                $variable .= '&mensajeModal=errorArchivoNoValido';
                break;

            case "ErrorCargarArchivo":
                $variable = 'pagina=generarContratosMasivos';
                $variable .= '&mensajeModal=errorCargarArchivo';
                break;

            case "ErrorNoCargaInformacionHojaCalculo":
                $variable = 'pagina=generarContratosMasivos';
                $variable .= '&mensajeModal=errorCargarInformacion';
                break;

            case "ErrorInformacionCargar":
                $variable = 'pagina=generarContratosMasivos';
                $variable .= '&mensajeModal=errorInformacionCargar';
                $variable .= '&log=' . $valor;
                break;

            case "ExitoInformacion":
                $variable = 'pagina=generarContratosMasivos';
                $variable .= '&mensajeModal=exitoInformacion';
                break;

            case "ErrorCreacionContratos":
                $variable = 'pagina=generarContratosMasivos';
                $variable .= '&mensajeModal=errorCreacionContratos';
                break;

            default:
                $variable = '';

        }
        foreach ($_REQUEST as $clave => $valor) {
            unset($_REQUEST[$clave]);
        }

        $url = $miConfigurador->configuracion["host"] . $miConfigurador->configuracion["site"] . "/index.php?";
        $enlace = $miConfigurador->configuracion['enlace'];
        $variable = $miConfigurador->fabricaConexiones->crypto->codificar($variable);
        $_REQUEST[$enlace] = $enlace . '=' . $variable;
        $redireccion = $url . $_REQUEST[$enlace];

        echo "<script>location.replace('" . $redireccion . "')</script>";

        exit();
    }
}
?>
