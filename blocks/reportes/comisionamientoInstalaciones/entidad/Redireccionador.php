<?php
namespace reportes\comisionamientoInstalaciones\entidad;
if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador {
    public static function redireccionar($opcion, $valor = "") {

        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "SinResultado":

                $variable = 'pagina=comisionamientoInstalaciones';
                $variable .= '&mensaje=SinResultado';
                break;

            case "archivoGenerado":
                $variable = 'pagina=comisionamientoInstalaciones';
                $variable .= '&mensaje=archivoGenerado';
                $variable .= "&archivo=" . $valor['rutaUrl'];
                break;

            case "errorProceso":

                $variable = 'pagina=comisionamientoInstalaciones';
                $variable .= '&mensaje=errorProceso';
                break;

            case "exitoProceso":
                $variable = 'pagina=comisionamientoInstalaciones';
                $variable .= '&mensaje=exitoProceso';
                $variable .= "&identificacion_proceso=" . $valor;
                break;

            case "ErrorEliminarProceso":
                $variable = 'pagina=comisionamientoInstalaciones';
                $variable .= '&mensaje=errorEliminarProceso';

                break;

            case "ExitoEliminarProceso":
                $variable = 'pagina=comisionamientoInstalaciones';
                $variable .= '&mensaje=exitoEliminarProceso';

                break;

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
