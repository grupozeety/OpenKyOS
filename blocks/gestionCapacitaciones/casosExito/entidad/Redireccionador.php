<?php

namespace gestionCapacitaciones\casosExito\entidad;

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
                $variable = 'pagina=competenciasTIC';
                $variable .= '&mensaje=exitoRegistro';
                $variable .= '&idActividad=' . $valor;

                break;

            case "ErrorRegistro":
                $variable = 'pagina=competenciasTIC';
                $variable .= '&mensaje=errorRegistro';
                break;

            case "ErrorAsociacionActividad":
                $variable = 'pagina=competenciasTIC';
                $variable .= '&mensaje=errorAsociacionActividad';
                $variable .= '&idActividad=' . $valor;
                break;

            //____________________________________________________
            case 'ExitoActualizacion':
                $variable = 'pagina=competenciasTIC';
                $variable .= '&mensaje=exitoActualizacion';
                break;

            case "ErrorActualizacion":
                $variable = 'pagina=competenciasTIC';
                $variable .= '&mensaje=errorActualizacion';
                break;

            case 'ExitoEliminar':
                $variable = 'pagina=competenciasTIC';
                $variable .= '&mensaje=exitoEliminar';
                break;

            case "ErrorEliminar":
                $variable = 'pagina=competenciasTIC';
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
