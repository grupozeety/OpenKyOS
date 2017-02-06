<?php

namespace facturacion\gestionPeriodos\entidad;

if (!isset($GLOBALS["autorizado"])) {
    include "index.php";
    exit();
}
class Redireccionador
{

    public static function redireccionar($opcion, $valor = "")
    {
        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {
            case 'ExitoRegistro':
                $variable = 'pagina=gestionPeriodos';
                $variable .= '&mensaje=exitoRegistro';
                break;

            case "ErrorRegistro":
                $variable = 'pagina=gestionPeriodos';
                $variable .= '&mensaje=errorRegistro';
                break;

            case 'ExitoActualizacion':
                $variable = 'pagina=gestionPeriodos';
                $variable .= '&mensaje=exitoActualizacion';
                break;

            case "ErrorActualizacion":
                $variable = 'pagina=gestionPeriodos';
                $variable .= '&mensaje=errorActualizacion';
                break;

            case 'ExitoEliminar':
                $variable = 'pagina=gestionPeriodos';
                $variable .= '&mensaje=exitoEliminar';
                break;

            case "ErrorEliminar":
                $variable = 'pagina=gestionPeriodos';
                $variable .= '&mensaje=errorEliminar';
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
