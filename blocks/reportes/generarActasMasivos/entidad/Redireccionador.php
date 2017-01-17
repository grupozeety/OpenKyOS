<?php
namespace reportes\generarActasMasivos\entidad;
if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador {
    public static function redireccionar($opcion, $valor = "") {

        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "ErrorFormatoArchivo":
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=errorFormatoArchivo';
                break;

            case "ErrorArchivoNoValido":
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=errorArchivoNoValido';
                break;

            case "ErrorCargarArchivo":
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=errorCargarArchivo';
                break;

            case "ErrorNoCargaInformacionHojaCalculo":
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=errorCargarInformacion';
                break;

            case "ErrorInformacionCargar":
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=errorInformacionCargar';
                $variable .= '&log=' . $valor;
                break;

            case "ExitoInformacion":
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=exitoInformacion';
                break;

            case "ErrorCreacion":
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=errorCreacion';
                break;

            case "ExitoRegistroProceso":
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=exitoRegistroProceso';
                $variable .= '&proceso=' . $valor;
                break;

            case "ErrorRegistroProceso":
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=errorRegistroProceso';
                break;

            case "ExitoRegistroActas":
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=exitoRegistroActas';
                break;

            case 'ExitoActualizacionActas':
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=exitoActualizacionActas';
                break;

            case "ErrorEliminarProceso":
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=errorEliminarProceso';
                break;

            case 'ExitoEliminarProceso':
                $variable = 'pagina=generarActasMasivos';
                $variable .= '&mensajeModal=exitoEliminarProceso';
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
