<?php

namespace gestionCapacitaciones\apropiacion\entidad;

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
                $variable = 'pagina=apropiacion';
                $variable .= '&mensaje=exitoRegistro';
                $variable .= '&idActividad=' . $valor;
                break;

            case 'ErrorAsociacionActividadBeneficiario':
                $variable = 'pagina=apropiacion';
                $variable .= '&mensaje=errorAsociacionActividadBeneficiario';
                $variable .= '&idActividad=' . $valor;
                break;

            case 'ErrorAsociacionActividad':
                $variable = 'pagina=apropiacion';
                $variable .= '&mensaje=errorAsociacionActividad';
                break;

            case "ErrorRegistro":
                $variable = 'pagina=apropiacion';
                $variable .= '&mensaje=errorRegistro';
                break;

            case 'ExitoActualizacion':
                $variable = 'pagina=apropiacion';
                $variable .= '&mensaje=exitoActualizacion';
                break;

            case "ErrorActualizacion":
                $variable = 'pagina=apropiacion';
                $variable .= '&mensaje=errorActualizacion';
                break;

            case 'ExitoEliminar':
                $variable = 'pagina=apropiacion';
                $variable .= '&mensaje=exitoEliminar';
                break;

            case "ErrorEliminar":
                $variable = 'pagina=apropiacion';
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
