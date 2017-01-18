<?php
namespace reportes\beneficiariosRegistrados\entidad;
if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador {
    public static function redireccionar($opcion, $valor = "") {

        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "SinResultado":

                $variable = 'pagina=beneficiariosRegistrados';
                $variable .= '&mensaje=SinResultado';
                break;

            case "archivoGenerado":
                $variable = 'pagina=beneficiariosRegistrados';
                $variable .= '&mensaje=archivoGenerado';
                $variable .= "&archivo=" . $valor['rutaUrl'];
                break;

            case "errorProceso":

                $variable = 'pagina=beneficiariosRegistrados';
                $variable .= '&mensaje=errorProceso';
                break;

            case "exitoProceso":
                $variable = 'pagina=beneficiariosRegistrados';
                $variable .= '&mensaje=exitoProceso';
                $variable .= "&identificacion_proceso=" . $valor;
                break;

            case "ErrorEliminarProceso":
                $variable = 'pagina=beneficiariosRegistrados';
                $variable .= '&mensaje=errorEliminarProceso';

                break;

            case "ExitoEliminarProceso":
                $variable = 'pagina=beneficiariosRegistrados';
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
