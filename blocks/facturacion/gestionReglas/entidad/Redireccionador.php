<?php

namespace facturacion\gestionReglas\entidad;

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
                $variable = 'pagina=gestionReglas';
                $variable .= '&mensaje=exitoRegistro';
                break;

            case "ErrorRegistro":
                $variable = 'pagina=gestionReglas';
                $variable .= '&mensaje=errorRegistro';
                break;


            case 'ExitoActualizacion':
                $variable = 'pagina=gestionReglas';
                $variable .= '&mensaje=exitoActualizacion';
                break;

            case "ErrorActualizacion":
                $variable = 'pagina=gestionReglas';
                $variable .= '&mensaje=errorActualizacion';
                break;

            case 'ExitoEliminar':
                $variable = 'pagina=gestionReglas';
                $variable .= '&mensaje=exitoEliminar';
                break;

            case "ErrorEliminar":
                $variable = 'pagina=gestionReglas';
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
