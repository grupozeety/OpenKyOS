<?php
namespace reportes\informacionBeneficiarios\entidad;
if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador {
    public static function redireccionar($opcion, $valor = "") {

        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "SinResultado":

                $variable = 'pagina=informacionBeneficiarios';
                $variable .= '&mensaje=SinResultado';
                break;

            case "archivoGenerado":
                $variable = 'pagina=informacionBeneficiarios';
                $variable .= '&mensaje=archivoGenerado';
                $variable .= "&archivo=" . $valor['rutaUrl'];
                break;

            case "errorProceso":

                $variable = 'pagina=informacionBeneficiarios';
                $variable .= '&mensaje=errorProceso';
                break;

            case "exitoProceso":
                $variable = 'pagina=informacionBeneficiarios';
                $variable .= '&mensaje=exitoProceso';
                $variable .= "&identificacion_proceso=" . $valor;
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

        echo "<script>location.replace('    " . $redireccion . "')</script>";

        exit();
    }
}
?>
