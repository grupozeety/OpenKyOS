<?php

namespace reportes\plantillaFamiliares\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador {
    public static function redireccionar($opcion, $valor = "") {
        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "ErrorFormatoArchivo":
                $variable = 'pagina=plantillaFamiliares';
                $variable .= '&mensajeModal=errorFormatoArchivo';
                break;

            case "ErrorArchivoNoValido":
                $variable = 'pagina=plantillaFamiliares';
                $variable .= '&mensajeModal=errorArchivoNoValido';
                break;

            case "ErrorCargarArchivo":
                $variable = 'pagina=plantillaFamiliares';
                $variable .= '&mensajeModal=errorCargarArchivo';
                break;

            case "ErrorNoCargaInformacionHojaCalculo":
                $variable = 'pagina=plantillaFamiliares';
                $variable .= '&mensajeModal=errorCargarInformacion';
                break;

            case "ErrorInformacionCargar":
                $variable = 'pagina=plantillaFamiliares';
                $variable .= '&mensajeModal=errorInformacionCargar';
                $variable .= '&log=' . $valor;
                break;

            case "ExitoInformacion":
                $variable = 'pagina=plantillaFamiliares';
                $variable .= '&mensajeModal=exitoInformacion';
                break;

            case "ErrorCreacion":
                $variable = 'pagina=plantillaFamiliares';
                $variable .= '&mensajeModal=errorCreacion';
                break;

            case "ExitoRegistro":
                $variable = 'pagina=plantillaFamiliares';
                $variable .= '&mensajeModal=exitoRegistro';
                break;

            case "ExitoActualizacion":
                $variable = 'pagina=plantillaFamiliares';
                $variable .= '&mensajeModal=exitoActualizacion';
                break;

            case "ErrorRegistroProceso":
                $variable = 'pagina=plantillaFamiliares';
                $variable .= '&mensajeModal=errorRegistroProceso';
                break;

            case "ErrorActualizacion":
                $variable = 'pagina=plantillaFamiliares';
                $variable .= '&mensajeModal=errorActualizacion';
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

        echo "<script>location.replace('" . $redireccion . "')</script>";

        exit();
    }
}
?>
